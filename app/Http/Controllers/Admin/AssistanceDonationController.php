<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssistanceDonationController extends Controller
{
    private function guard(): void { abort_unless(auth()->user()?->hasRole('admin'), 403); }

    public function index()
    {
        $this->guard();
        $requests = AssistanceRequest::where('status', 'APPROVED')
            ->withSum(['transactions as collected_amount' => fn ($q) => $q->where('status', 'SUCCESSFUL')], 'amount')
            ->orderByDesc('id')->get();
        $available = Transaction::where('status', 'SUCCESSFUL')
            ->whereNull('charitable_project_id')->whereNull('assistance_request_id')
            ->orderByDesc('id')->limit(100)->get();
        $recent = Transaction::with('assistanceRequest')->whereNotNull('assistance_request_id')
            ->orderByDesc('id')->limit(30)->get();
        return view('admin.assistance_donations.index', compact('requests','available','recent'));
    }

    public function allocate(Request $request)
    {
        $this->guard();
        $data = $request->validate([
            'transaction_id' => ['required','integer','exists:transactions,id'],
            'assistance_request_id' => ['required','integer','exists:assistance_requests,id'],
        ]);
        DB::transaction(function () use ($data) {
            $donation = Transaction::whereKey($data['transaction_id'])->lockForUpdate()->firstOrFail();
            $assistance = AssistanceRequest::whereKey($data['assistance_request_id'])->lockForUpdate()->firstOrFail();
            if ($donation->status !== 'SUCCESSFUL' || $donation->charitable_project_id !== null || $donation->assistance_request_id !== null) {
                throw ValidationException::withMessages(['transaction_id'=>'التبرع غير ناجح أو مخصص مسبقًا لجهة أخرى.']);
            }
            if ($assistance->status !== 'APPROVED') {
                throw ValidationException::withMessages(['assistance_request_id'=>'يمكن تخصيص التبرعات لطلبات الإعانة المعتمدة فقط.']);
            }
            $donation->assistance_request_id = $assistance->id;
            $donation->save();
        });
        return back()->with('status', 'تم ربط التبرع بطلب الإعانة بنجاح.');
    }
}
