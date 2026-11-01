<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class BankTransferSubmission extends Model {protected $fillable=['user_id','bank_account_id','charitable_project_id','assistance_request_id','donor_name','donor_phone','amount','transfer_reference','receipt_path','bank_name_snapshot','account_holder_snapshot','account_number_snapshot','iban_snapshot'];public function account(){return $this->belongsTo(BankAccount::class,'bank_account_id');}public function project(){return $this->belongsTo(CharitableProject::class,'charitable_project_id');}public function assistance(){return $this->belongsTo(AssistanceRequest::class,'assistance_request_id');}}
