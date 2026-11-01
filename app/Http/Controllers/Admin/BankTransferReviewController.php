<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{BankTransferSubmission,Donor,Transaction,CharitableProject,User};
use App\Notifications\BankTransferDecisionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Storage};
use Illuminate\Validation\ValidationException;
class BankTransferReviewController extends Controller {
 private function guard(){abort_unless(auth()->user()?->hasRole('admin'),403);}
 public function index(Request $request){$this->guard();$status=$request->query('status','PENDING');if(!in_array($status,['PENDING','APPROVED','REJECTED','ALL'],true))$status='PENDING';$query=BankTransferSubmission::with(['account','project','assistance'])->latest('id');if($status!=='ALL')$query->where('status',$status);$transfers=$query->paginate(20);return view('admin.bank_transfers.index',compact('transfers','status'));}
 public function receipt(BankTransferSubmission $transfer){$this->guard();abort_unless(Storage::disk('local')->exists($transfer->receipt_path),404);return Storage::disk('local')->response($transfer->receipt_path, basename($transfer->receipt_path), ['X-Content-Type-Options'=>'nosniff','Content-Security-Policy'=>"default-src 'none'; style-src 'unsafe-inline'; sandbox"]);}
 public function decision(Request $request,BankTransferSubmission $transfer){$this->guard();$data=$request->validate(['decision'=>'required|in:APPROVED,REJECTED','review_note'=>'required_if:decision,REJECTED|nullable|string|max:1000']);DB::transaction(function()use($transfer,$data){$transfer=BankTransferSubmission::whereKey($transfer->id)->lockForUpdate()->firstOrFail();if($transfer->status!=='PENDING')throw ValidationException::withMessages(['decision'=>'تمت مراجعة هذه الحوالة بالفعل.']);if($data['decision']==='APPROVED'){
   if(BankTransferSubmission::where('bank_account_id',$transfer->bank_account_id)->where('transfer_reference',$transfer->transfer_reference)->where('status','APPROVED')->exists())throw ValidationException::withMessages(['decision'=>'تم اعتماد رقم هذه الحوالة مسبقًا.']);
   if($transfer->charitable_project_id){$project=CharitableProject::whereKey($transfer->charitable_project_id)->lockForUpdate()->firstOrFail();if($project->status!=='ACTIVE' || (float)$project->target_budget<=0)throw ValidationException::withMessages(['decision'=>'المشروع غير متاح لاستقبال التبرعات.']);
   if(round((float)$transfer->amount,2)>round((float)$project->target_budget-(float)$project->collected_amount,2))throw ValidationException::withMessages(['decision'=>'لا يمكن اعتماد الحوالة: قيمتها تتجاوز التمويل المتبقي للمشروع. يمكن رفضها بعد التحقق من رد المبلغ للمتبرع.']);}
   else { $assistance=\App\Models\AssistanceRequest::whereKey($transfer->assistance_request_id)->lockForUpdate()->firstOrFail();if($assistance->status!=='APPROVED')throw ValidationException::withMessages(['decision'=>'طلب الإعانة لم يعد معتمدًا.']);
   $approved=(float)($assistance->approved_amount ?: $assistance->requested_amount);
   $collected=(float)$assistance->transactions()->where('status','SUCCESSFUL')->sum('amount');
   if($approved<=0 || round((float)$transfer->amount,2)>round($approved-$collected,2))throw ValidationException::withMessages(['decision'=>'لا يمكن اعتماد الحوالة: قيمتها تتجاوز التمويل المتبقي لطلب الإعانة.']); }
   if(Transaction::where('transaction_ref','BT-'.str_pad((string)$transfer->id,10,'0',STR_PAD_LEFT))->exists())throw ValidationException::withMessages(['decision'=>'هذه الحوالة لها معاملة مالية مسجلة بالفعل.']);
   // One donor profile per registered user; multiple transfers share that profile.
   // Guest transfers have no user_id and keep their own donor record.
   $donor = $transfer->user_id
       ? Donor::firstOrCreate(
           ['user_id' => $transfer->user_id],
           ['donor_name' => $transfer->donor_name, 'phone_number' => $transfer->donor_phone, 'is_anonymous' => false]
       )
       : Donor::create([
           'user_id' => null,
           'donor_name' => $transfer->donor_name,
           'phone_number' => $transfer->donor_phone,
           'is_anonymous' => false,
       ]);
   $transaction=Transaction::create(['transaction_ref'=>'BT-'.str_pad((string)$transfer->id,10,'0',STR_PAD_LEFT),'donor_id'=>$donor->id,'charitable_project_id'=>$transfer->charitable_project_id,'assistance_request_id'=>$transfer->assistance_request_id,'amount'=>$transfer->amount,'payment_gateway'=>'BANK_TRANSFER','status'=>'SUCCESSFUL']);
   if($transfer->charitable_project_id){$project->increment('collected_amount',$transfer->amount);$project->refresh();if($project->target_budget>0 && $project->collected_amount >= $project->target_budget && $project->status==='ACTIVE')$project->update(['status'=>'FULLY_FUNDED']);}
   $transfer->transaction_id=$transaction->id;
  }
  $transfer->status=$data['decision'];$transfer->review_note=$data['review_note']??null;$transfer->reviewed_by=auth()->id();$transfer->reviewed_at=now();$transfer->save();
  if($transfer->user_id){
      $donorUser = User::find($transfer->user_id);
      if($donorUser){
          $donorUser->notify(new BankTransferDecisionNotification($transfer, $data['decision'], $data['review_note'] ?? null));
      }
  }
 });return back()->with('status','تم حفظ القرار بنجاح.');}
}
