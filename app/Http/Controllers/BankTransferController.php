<?php
namespace App\Http\Controllers;
use App\Models\{BankAccount,BankTransferSubmission,CharitableProject,AssistanceRequest};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class BankTransferController extends Controller {
 public function create(Request $request){$type=$request->query('type');$id=(int)$request->query('id');$target=$type==='project'?CharitableProject::where('status','ACTIVE')->where('target_budget','>',0)->whereColumn('collected_amount','<','target_budget')->findOrFail($id):($type==='assistance'?AssistanceRequest::where('status','APPROVED')->findOrFail($id):abort(404));$accounts=BankAccount::where('is_active',true)->get();return view('donations.bank_transfer',compact('type','target','accounts'));}
 public function confirmation(Request $request){
  $confirmation=$request->session()->get('bank_transfer_confirmation');
  if(!$confirmation)return redirect('/');
  return view('donations.bank_confirmation',compact('confirmation'));
 }
 public function store(Request $request){$data=$request->validate(['type'=>'required|in:project,assistance','target_id'=>'required|integer','bank_account_id'=>'required|integer|exists:bank_accounts,id','donor_name'=>'required|string|max:150','donor_phone'=>'nullable|string|max:40','amount'=>'required|numeric|min:1|max:9999999999.99','transfer_reference'=>'required|string|max:100','receipt'=>'required|file|mimes:jpg,jpeg,png,pdf|max:5120']);
  $account=BankAccount::where('is_active',true)->findOrFail($data['bank_account_id']);
  if($data['type']==='project') {
   $project=CharitableProject::where('status','ACTIVE')->where('target_budget','>',0)->whereColumn('collected_amount','<','target_budget')->findOrFail($data['target_id']);
   if(round((float)$data['amount'],2)>round((float)$project->target_budget-(float)$project->collected_amount,2)) throw ValidationException::withMessages(['amount'=>'المبلغ المدخل يتجاوز المبلغ المتبقي لتمويل المشروع.']);
  } else {
   $assistance=AssistanceRequest::where('status','APPROVED')->findOrFail($data['target_id']);
   $approved=(float)($assistance->approved_amount ?: $assistance->requested_amount);
   $collected=(float)$assistance->transactions()->where('status','SUCCESSFUL')->sum('amount');
   if($approved<=0 || round((float)$data['amount'],2)>round($approved-$collected,2)) throw ValidationException::withMessages(['amount'=>'المبلغ المدخل يتجاوز التمويل المتبقي لطلب الإعانة.']);
  }
  $data['transfer_reference']=trim($data['transfer_reference']);
  $duplicate=BankTransferSubmission::where('bank_account_id',$account->id)->where('transfer_reference',$data['transfer_reference'])->exists();if($duplicate)throw ValidationException::withMessages(['transfer_reference'=>'رقم هذه الحوالة مستخدم بالفعل لحوالة قيد المراجعة أو معتمدة.']);
  $path=$request->file('receipt')->store('bank-transfer-receipts','local');
  try {BankTransferSubmission::create(['user_id'=>auth()->id(),'bank_account_id'=>$account->id,'charitable_project_id'=>$data['type']==='project'?$data['target_id']:null,'assistance_request_id'=>$data['type']==='assistance'?$data['target_id']:null,'donor_name'=>$data['donor_name'],'donor_phone'=>$data['donor_phone']??null,'amount'=>$data['amount'],'transfer_reference'=>$data['transfer_reference'],'receipt_path'=>$path,'bank_name_snapshot'=>$account->bank_name,'account_holder_snapshot'=>$account->account_holder,'account_number_snapshot'=>$account->account_number,'iban_snapshot'=>$account->iban]);}catch(\Throwable $e){\Illuminate\Support\Facades\Storage::disk('local')->delete($path);throw $e;}
  return redirect()->route('donations.bank.confirmation')->with('bank_transfer_confirmation',[
   'reference'=>'ZT-'.str_pad((string)BankTransferSubmission::where('bank_account_id',$account->id)->where('transfer_reference',$data['transfer_reference'])->value('id'),8,'0',STR_PAD_LEFT),
   'amount'=>number_format((float)$data['amount'],2,'.',''),
   'target'=>$data['type']==='project'?'مشروع خيري':'طلب إعانة معتمد',
   'message'=>'تم استلام طلب الحوالة وهو قيد المراجعة. لن يُحتسب المبلغ إلا بعد التحقق من وصوله.'
  ]);
 }
}
