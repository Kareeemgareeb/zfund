<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AssistanceRequestController extends Controller
{
    private function authorizeAdmin(): void { abort_unless(auth()->user()?->hasRole('admin'), 403); }

    public function index(Request $request)
    {
        $this->authorizeAdmin();
        $sort = $request->query('sort', 'date_desc');
        if (!in_array($sort, ['date_desc', 'date_asc', 'name_asc', 'name_desc'], true)) $sort = 'date_desc';
        $query = AssistanceRequest::with('beneficiary');
        if (str_starts_with($sort, 'name_')) {
            $query->orderBy(
                \App\Models\Beneficiary::select('full_name')->whereColumn('beneficiaries.id', 'assistance_requests.beneficiary_id')->limit(1),
                $sort === 'name_asc' ? 'asc' : 'desc'
            )->orderByDesc('assistance_requests.id');
        } else {
            $query->orderBy('created_at', $sort === 'date_asc' ? 'asc' : 'desc')->orderByDesc('id');
        }
        $requests = $query->paginate(10)->withQueryString();
        return view('admin.assistance_requests.index', compact('requests'));
    }

    public function show($id)
    {
        $this->authorizeAdmin();
        $item = AssistanceRequest::with('beneficiary.user')->findOrFail($id);
        return view('admin.assistance_requests.show', compact('item'));
    }

    public function updateStatus(Request $request, $id)
    {
        $this->authorizeAdmin();
        $data = $request->validate(['status' => ['required','in:UNDER_REVIEW,APPROVED,REJECTED']]);
        $item = AssistanceRequest::findOrFail($id);
        if (in_array($item->status, ['REJECTED','DISBURSED'], true)) {
            throw ValidationException::withMessages(['status'=>'هذا الطلب مغلق ولا يمكن تعديل حالته.']);
        }
        if ($data['status'] === 'APPROVED' && $item->status !== 'UNDER_REVIEW') {
            throw ValidationException::withMessages(['status'=>'يجب نقل الطلب إلى قيد المراجعة قبل الموافقة.']);
        }
        $item->update(['status'=>$data['status']]);

        if ($item->beneficiary?->user) {
            $item->beneficiary->user->notify(new \App\Notifications\AssistanceRequestStatusNotification(
                $item,
                $data['status']
            ));
        }

        return back()->with('success','تم تحديث حالة الطلب بنجاح.');
    }
}
