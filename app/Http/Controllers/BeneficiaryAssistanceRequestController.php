<?php
namespace App\Http\Controllers;

use App\Models\AssistanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BeneficiaryAssistanceRequestController extends Controller
{
    private function beneficiary(Request $request)
    {
        $user = $request->user();
        abort_unless($user->hasRole('beneficiary') && !$user->hasRole('admin') && !$user->hasRole('donor'), 403);
        return $user->beneficiary;
    }

    public function index(Request $request)
    {
        $beneficiary = $this->beneficiary($request);
        $query = $beneficiary ? AssistanceRequest::where('beneficiary_id', $beneficiary->id) : null;
        $statistics = [
            'count' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0,
            'requested' => 0, 'approved_amount' => 0, 'disbursed' => 0,
        ];
        if ($query) {
            $statistics['count'] = (clone $query)->count();
            $statistics['pending'] = (clone $query)->whereIn('status', ['SUBMITTED', 'UNDER_REVIEW'])->count();
            $statistics['approved'] = (clone $query)->whereIn('status', ['APPROVED', 'DISBURSED'])->count();
            $statistics['rejected'] = (clone $query)->where('status', 'REJECTED')->count();
            $statistics['requested'] = (float) (clone $query)->sum('requested_amount');
            $statistics['approved_amount'] = (float) (clone $query)->whereIn('status', ['APPROVED', 'DISBURSED'])->sum('approved_amount');
            $statistics['disbursed'] = (float) \App\Models\ProjectDisbursement::whereIn('assistance_request_id', (clone $query)->select('id'))->sum('amount');
        }
        $requests = $query ? $query->withSum('disbursements', 'amount')->latest()->paginate(10) : null;
        return view('beneficiary.assistance_requests.index', compact('beneficiary', 'requests', 'statistics'));
    }

    public function create(Request $request)
    {
        $beneficiary = $this->beneficiary($request);
        if (!$beneficiary) return redirect()->route('beneficiary.apply')->with('status', 'يرجى فتح ملف مستفيد أولاً.');
        if ($beneficiary->eligibility_status !== 'VERIFIED' || $beneficiary->next_renewal_due?->isPast()) return redirect()->route('beneficiary.requests.index')->with('status', 'لا يمكن تقديم طلب إعانة قبل اعتماد الملف والتأكد من سريان صلاحيته.');
        return view('beneficiary.assistance_requests.create', compact('beneficiary'));
    }

    public function store(Request $request)
    {
        $beneficiary = $this->beneficiary($request);
        if (!$beneficiary) return redirect()->route('beneficiary.apply');
        abort_unless($beneficiary->eligibility_status === 'VERIFIED' && !$beneficiary->next_renewal_due?->isPast(), 403, 'لا يمكن إرسال الطلب قبل اعتماد ملف ساري.');
        $data = $request->validate([
            'category' => ['required', 'in:ORPHAN_SPONSORSHIP,MARRIAGE_AID,FOOD_BASKET,MEDICAL_AID,EMERGENCY_DEBT,GENERAL_RELIEF'],
            'requested_amount' => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            'description' => ['required', 'string', 'min:20', 'max:3000'],
        ], [
            'category.required' => 'يرجى اختيار نوع الإعانة.',
            'category.in' => 'نوع الإعانة غير صحيح.',
            'requested_amount.required' => 'يرجى إدخال المبلغ المطلوب.',
            'requested_amount.numeric' => 'يجب أن يكون المبلغ رقمًا صحيحًا.',
            'requested_amount.gt' => 'يجب أن يكون المبلغ أكبر من صفر.',
            'description.required' => 'يرجى كتابة وصف الحالة.',
            'description.min' => 'يجب أن يتكون وصف الحالة من 20 حرفًا على الأقل.',
        ]);
        $createdItem = null;
        DB::transaction(function () use ($beneficiary, $data, &$createdItem) {
            $item = AssistanceRequest::create([
                'request_code' => 'TMP-'.bin2hex(random_bytes(10)),
                'beneficiary_id' => $beneficiary->id,
                'category' => $data['category'],
                'requested_amount' => $data['requested_amount'],
                'status' => 'SUBMITTED',
                'description' => $data['description'],
            ]);
            $item->update(['request_code' => 'SRT-REQ-'.str_pad((string)$item->id, 7, '0', STR_PAD_LEFT)]);
            $createdItem = $item;
        });

        if ($beneficiary->user && $createdItem) {
            $beneficiary->user->notify(new \App\Notifications\AssistanceRequestStatusNotification(
                $createdItem,
                'SUBMITTED'
            ));
        }
        return redirect()->route('beneficiary.requests.index')->with('success', 'تم إرسال طلب الإعانة بنجاح، ويمكنك متابعة حالته هنا.');
    }
}
