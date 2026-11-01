<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class BankAccount extends Model {protected $fillable=['bank_name','account_holder','account_number','iban','is_active'];protected $casts=['is_active'=>'boolean'];}
