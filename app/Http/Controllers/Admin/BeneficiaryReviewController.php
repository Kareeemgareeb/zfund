<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BeneficiaryReviewController extends Controller
{
    private function guard(): void { abort_unless(auth()->user()?->hasRole('admin'), 403); }

    public function index(Request $request)
    {
        $this->guard();
        $status = $request->query('status');
        $sort = $request->query('sort', 'date_desc');
        if (!in_array($sort, ['date_desc', 'date_asc', 'name_asc', 'name_desc'], true)) $sort = 'date_desc';
        $query = Beneficiary::with('user');
        if (str_starts_with($sort, 'name_')) {
            $query->orderBy('full_name', $sort === 'name_asc' ? 'asc' : 'desc')->orderByDesc('id');
        } else {
            $query->orderBy('created_at', $sort === 'date_asc' ? 'asc' : 'desc')->orderByDesc('id');
        }
        if (in_array($status, ['PENDING','VERIFIED','INELIGIBLE'], true)) $query->where('eligibility_status', $status);
        return view('admin.beneficiaries.index', ['files' => $query->paginate(12)->withQueryString(), 'status' => $status, 'sort' => $sort]);
    }

    public function show(Beneficiary $beneficiary)
    {
        $this->guard();
        $beneficiary->load('user');
        return view('admin.beneficiaries.show', compact('beneficiary'));
    }

    public function update(Request $request, Beneficiary $beneficiary)
    {
        $this->guard();
        $data = $request->validate([
            'decision' => ['required', Rule::in(['VERIFIED','INELIGIBLE'])],
            'review_note' => ['required_if:decision,INELIGIBLE','nullable','string','max:1500'],
        ], [
            'decision.required' => 'اختر قرار المراجعة.',
            'decision.in' => 'القرار غير صالح.',
            'review_note.required_if' => 'يجب كتابة سبب عدم الاعتماد.',
            'review_note.max' => 'يجب ألا تتجاوز الملاحظات 1500 حرف.',
        ]);
        DB::transaction(function () use ($beneficiary, $data) {
            $locked = Beneficiary::whereKey($beneficiary->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->eligibility_status === 'PENDING', 409, 'هذا الملف تمت مراجعته بالفعل.');
            $locked->eligibility_status = $data['decision'];
            $locked->review_note = $data['review_note'] ?? null;
            $locked->reviewed_by = auth()->id();
            $locked->reviewed_at = now();
            $locked->save();

            if ($locked->user) {
                $locked->user->notify(new \App\Notifications\BeneficiaryReviewNotification(
                    $locked,
                    $data['decision'],
                    $data['review_note'] ?? null
                ));
            }
        });
        return redirect()->route('admin.beneficiaries.show', $beneficiary)->with('success', 'تم حفظ قرار مراجعة الملف.');
    }
}
