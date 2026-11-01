<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = ['transaction_id', 'receipt_number', 'pdf_path'];
    public function transaction() { return $this->belongsTo(Transaction::class); }
}
