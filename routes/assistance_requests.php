<?php
use App\Http\Controllers\BeneficiaryAssistanceRequestController;
use App\Http\Controllers\Admin\AssistanceRequestController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/beneficiary/assistance-requests', [BeneficiaryAssistanceRequestController::class, 'index'])->name('beneficiary.requests.index');
    Route::get('/beneficiary/assistance-requests/create', [BeneficiaryAssistanceRequestController::class, 'create'])->name('beneficiary.requests.create');
    Route::post('/beneficiary/assistance-requests', [BeneficiaryAssistanceRequestController::class, 'store'])->name('beneficiary.requests.store');
    Route::get('/admin/assistance-requests/{id}', [AssistanceRequestController::class, 'show'])->whereNumber('id')->name('admin.requests.show');
});
