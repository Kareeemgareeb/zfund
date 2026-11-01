<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['transaction_ref', 'donor_id', 'charitable_project_id', 'assistance_request_id', 'amount', 'payment_gateway', 'status'];

    public function donor() { return $this->belongsTo(Donor::class); }
    public function project() { return $this->belongsTo(CharitableProject::class, 'charitable_project_id'); }
    public function assistanceRequest() { return $this->belongsTo(AssistanceRequest::class); }
    public function receipt() { return $this->hasOne(Receipt::class); }
}
