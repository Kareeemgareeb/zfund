<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileRenewal extends Model
{
    protected $fillable = [
        'beneficiary_id', 'renewal_year', 'submission_date', 'status', 'beneficiary_notes', 'reviewed_by'
    ];
    
    protected function casts(): array {
        return ['submission_date' => 'date'];
    }

    public function beneficiary() { return $this->belongsTo(Beneficiary::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
}
