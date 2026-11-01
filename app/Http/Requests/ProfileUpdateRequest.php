<?php
namespace App\Http\Requests;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class ProfileUpdateRequest extends FormRequest {
 public function rules(): array {
  return [
   'username'=>['required','string','max:50',Rule::unique(User::class,'username')->ignore($this->user()->id)],
   'email'=>['nullable','string','lowercase','email','max:100',Rule::unique(User::class,'email')->ignore($this->user()->id)],
   'phone_number'=>['nullable','regex:/^[0-9]{10}$/'],
  ];
 }
 public function attributes(): array {return ['username'=>'اسم المستخدم','email'=>'البريد الإلكتروني','phone_number'=>'رقم الهاتف'];}
}
