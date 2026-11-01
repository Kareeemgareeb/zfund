<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $fillable = [
        'user_id', 'file_number', 'full_name', 'gender', 'birth_date',
        'marital_status', 'family_members_count', 'city', 'address',
        'bank_name', 'bank_account_no', 'case_description', 'eligibility_status', 'next_renewal_due'
    ];

    protected function casts(): array {
        return ['birth_date' => 'date', 'next_renewal_due' => 'date'];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function assistanceRequests() { return $this->hasMany(AssistanceRequest::class); }
    public function fileRenewals() { return $this->hasMany(FileRenewal::class); }
}
