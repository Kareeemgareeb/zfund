<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BankTransferController;
use App\Http\Controllers\Admin\{BankAccountController,BankTransferReviewController};
Route::get('/donate/bank-transfer',[BankTransferController::class,'create'])->name('donations.bank.create');
Route::get('/donate/bank-transfer/confirmation',[BankTransferController::class,'confirmation'])->name('donations.bank.confirmation');
Route::post('/donate/bank-transfer',[BankTransferController::class,'store'])->middleware('throttle:5,1')->name('donations.bank.store');
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function(){
 Route::get('/bank-accounts',[BankAccountController::class,'index'])->name('bank-accounts.index');
 Route::post('/bank-accounts',[BankAccountController::class,'store'])->name('bank-accounts.store');
 Route::put('/bank-accounts/{account}',[BankAccountController::class,'update'])->name('bank-accounts.update');
 Route::get('/bank-transfers/assistance/{assistance}', function (\App\Models\AssistanceRequest $assistance) {
  abort_unless(auth()->user()?->hasRole('admin'),403);
  $collected = \App\Models\Transaction::where('assistance_request_id',$assistance->id)->where('status','SUCCESSFUL')->sum('amount');
  return view('admin.bank_transfers.assistance_detail',compact('assistance','collected'));
 })->name('bank-transfers.assistance-detail');
 Route::get('/bank-transfers',[BankTransferReviewController::class,'index'])->name('bank-transfers.index');
 Route::get('/bank-transfers/{transfer}/receipt',[BankTransferReviewController::class,'receipt'])->name('bank-transfers.receipt');
 Route::post('/bank-transfers/{transfer}/decision',[BankTransferReviewController::class,'decision'])->name('bank-transfers.decision');
});

Route::middleware('auth')->group(function(){
 Route::get('/my-donations',[\App\Http\Controllers\DonorPaymentHistoryController::class,'index'])->name('donations.history');
 Route::get('/my-donations/{transfer}/receipt',[\App\Http\Controllers\DonorPaymentHistoryController::class,'receipt'])->name('donations.history.receipt');
});
