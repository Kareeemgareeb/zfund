<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
class BankAccountController extends Controller {
 private function guard(){abort_unless(auth()->user()?->hasRole('admin'),403);}
 public function index(){$this->guard();$accounts=BankAccount::latest()->get();return view('admin.bank_accounts.index',compact('accounts'));}
 public function store(Request $request){$this->guard();$data=$request->validate(['bank_name'=>'required|string|max:150','account_holder'=>'required|string|max:150','account_number'=>'required|string|max:100','iban'=>'nullable|string|max:100']);BankAccount::create($data+['is_active'=>true]);return back()->with('status','تمت إضافة الحساب.');}
 public function update(Request $request,BankAccount $account){$this->guard();$data=$request->validate(['bank_name'=>'required|string|max:150','account_holder'=>'required|string|max:150','account_number'=>'required|string|max:100','iban'=>'nullable|string|max:100','is_active'=>'required|boolean']);$account->update($data);return back()->with('status','تم تحديث الحساب.');}
}
