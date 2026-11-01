<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    protected $fillable = ['user_id', 'donor_name', 'phone_number', 'email', 'is_anonymous'];

    public function user() { return $this->belongsTo(User::class); }
    public function calculations() { return $this->hasMany(ZakatCalculation::class); }
    public function transactions() { return $this->hasMany(Transaction::class); }
}
