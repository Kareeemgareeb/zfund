<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BeneficiaryReviewController;
Route::middleware('auth')->prefix('admin')->group(function () {
 Route::get('/beneficiaries', [BeneficiaryReviewController::class, 'index'])->name('admin.beneficiaries.index');
 Route::get('/beneficiaries/{beneficiary}', [BeneficiaryReviewController::class, 'show'])->name('admin.beneficiaries.show');
 Route::patch('/beneficiaries/{beneficiary}/review', [BeneficiaryReviewController::class, 'update'])->name('admin.beneficiaries.review');
});
