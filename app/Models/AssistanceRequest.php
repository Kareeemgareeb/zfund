<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssistanceRequest extends Model
{
    protected $fillable = [
        'request_code', 'beneficiary_id', 'category', 'requested_amount', 'approved_amount', 'status', 'description'
    ];

    public function transactions() { return $this->hasMany(Transaction::class); }
    public function beneficiary() { return $this->belongsTo(Beneficiary::class); }
    public function documents() { return $this->hasMany(RequestDocument::class); }
    public function disbursements() { return $this->hasMany(ProjectDisbursement::class); }
}
