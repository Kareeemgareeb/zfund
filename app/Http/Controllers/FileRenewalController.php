<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileRenewalService;
use App\Models\FileRenewal;
use Illuminate\Support\Facades\Auth;
use App\Models\Beneficiary;

class FileRenewalController extends Controller
{
    protected $renewalService;

    public function __construct(FileRenewalService $renewalService)
    {
        $this->renewalService = $renewalService;
    }

    /**
     * Endpoint for Beneficiaries to submit their annual renewal.
     */
    public function submit(Request $request)
    {
        abort_unless(Auth::user()->hasRole('beneficiary'), 403);
        $request->validate([
            'beneficiary_notes' => 'nullable|string|max:1000'
        ]);

        // Authenticated user must have a beneficiary profile
        $beneficiary = Auth::user()->beneficiary;
        
        if (!$beneficiary) {
            return response()->json(['error' => 'لا يوجد ملف مستفيد مرتبط بحسابك.'], 403);
        }

        try {
            $renewal = $this->renewalService->submitRenewal($beneficiary, $request->beneficiary_notes);
            
            return response()->json([
                'success' => true,
                'message' => 'تم إرسال طلب تجديد ملفك إلى الإدارة بنجاح.',
                'data' => $renewal
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function index(Request $request)
    {
        abort_unless(Auth::user()->hasRole('admin') || Auth::user()->hasRole('case_officer'), 403);
        $status = $request->query('status', 'PENDING');
        if (!in_array($status, ['ALL', 'PENDING', 'APPROVED', 'REJECTED'], true)) $status = 'PENDING';
        $sort = $request->query('sort', 'date_desc');
        if (!in_array($sort, ['date_desc', 'date_asc', 'name_asc', 'name_desc'], true)) $sort = 'date_desc';
        $renewals = FileRenewal::with(['beneficiary', 'reviewer'])
            ->when($status !== 'ALL', fn ($q) => $q->where('status', $status))
            ->when(str_starts_with($sort, 'name_'), function ($q) use ($sort) {
                $q->orderBy(
                    Beneficiary::select('full_name')->whereColumn('beneficiaries.id', 'file_renewals.beneficiary_id')->limit(1),
                    $sort === 'name_asc' ? 'asc' : 'desc'
                );
            }, function ($q) use ($sort) {
                $q->orderBy('submission_date', $sort === 'date_asc' ? 'asc' : 'desc');
            })->orderByDesc('id')->paginate(15)->withQueryString();
        return view('admin.file-renewals.index', compact('renewals', 'status', 'sort'));
    }

    public function reviewFromForm(Request $request, FileRenewal $renewal)
    {
        abort_unless(Auth::user()->hasRole('admin') || Auth::user()->hasRole('case_officer'), 403);
        $validated = $request->validate(['status' => 'required|in:APPROVED,REJECTED']);
        if ($renewal->status !== 'PENDING') {
            return back()->withErrors(['status' => 'تمت مراجعة هذا الطلب مسبقًا.']);
        }
        $this->renewalService->reviewRenewal($renewal, $validated['status'], Auth::id());
        return redirect()->route('admin.file-renewals.index')->with('status', 'تم حفظ قرار مراجعة التجديد بنجاح.');
    }

    /**
     * Endpoint for Administrators to Approve or Reject a renewal.
     */
    public function review(Request $request, FileRenewal $renewal)
    {
        abort_unless(Auth::user()->hasRole('admin') || Auth::user()->hasRole('case_officer'), 403);
        abort_unless($renewal->status === 'PENDING', 409, 'تمت مراجعة الطلب مسبقاً');
        $request->validate([
            'status' => 'required|in:APPROVED,REJECTED'
        ]);

        // Assuming Route Middleware ensures only Admins reach this method
        $adminId = Auth::id(); 

        $updatedRenewal = $this->renewalService->reviewRenewal($renewal, $request->status, $adminId);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة طلب التجديد بنجاح.',
            'data' => $updatedRenewal
        ]);
    }
}
