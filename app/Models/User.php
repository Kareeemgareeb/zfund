<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'national_id', 'username', 'email', 'phone_number', 'password', 'status'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array {
        return ['password' => 'hashed'];
    }

    public function roles() { return $this->belongsToMany(Role::class); }
    public function beneficiary() { return $this->hasOne(Beneficiary::class); }
    public function donor() { return $this->hasOne(Donor::class); }
    
    public function hasRole($roleName) {
        return $this->roles()->where('name', $roleName)->exists();
    }
}
