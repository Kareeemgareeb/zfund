<?php
namespace App\Http\Controllers;
use App\Models\BankTransferSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class DonorPaymentHistoryController extends Controller {
 public function index(Request $request){
  abort_unless($request->user()->hasRole('donor') && !$request->user()->hasRole('admin'),403);
  $data=$request->validate(['status'=>'nullable|in:PENDING,APPROVED,REJECTED,CANCELLED','from'=>'nullable|date','to'=>'nullable|date|after_or_equal:from','q'=>'nullable|string|max:100','project'=>'nullable|integer']);
  $base=BankTransferSubmission::where('user_id',$request->user()->id);
  $totals=(clone $base)->selectRaw('status, SUM(amount) AS total')->groupBy('status')->pluck('total','status');
  $count=(clone $base)->count();
  $projects=(clone $base)->whereNotNull('charitable_project_id')->with('project:id,title')->get()->pluck('project.title','charitable_project_id')->filter()->unique()->sort();
  $query=(clone $base)->with(['project:id,title','assistance:id,request_code,category']);
  if(!empty($data['status']))$query->where('status',$data['status']);
  if(!empty($data['from']))$query->whereDate('created_at','>=',$data['from']);
  if(!empty($data['to']))$query->whereDate('created_at','<=',$data['to']);
  if(!empty($data['project']))$query->where('charitable_project_id',$data['project']);
  if(!empty($data['q']))$query->where(function($q)use($data){$term=trim($data['q']);$q->where('transfer_reference','like','%'.$term.'%')->orWhere('id',ctype_digit(preg_replace('/^ZT-/i','',$term))?(int)preg_replace('/^ZT-/i','',$term):0)->orWhereHas('project',fn($p)=>$p->where('title','like','%'.$term.'%'))->orWhereHas('assistance',fn($a)=>$a->where('request_code','like','%'.$term.'%'));});
  $transfers=$query->latest()->paginate(12)->withQueryString();
  return view('donations.history',compact('transfers','totals','count','projects'));
 }
 public function receipt(Request $request,BankTransferSubmission $transfer){
  abort_unless($request->user()->hasRole('donor') && !$request->user()->hasRole('admin'),403);
  abort_unless($transfer->user_id!==null && (int)$transfer->user_id===(int)$request->user()->id,403);
  abort_unless(Storage::disk('local')->exists($transfer->receipt_path),404);
  return Storage::disk('local')->download($transfer->receipt_path,'receipt-'.$transfer->id.'.'.pathinfo($transfer->receipt_path,PATHINFO_EXTENSION));
 }
}
