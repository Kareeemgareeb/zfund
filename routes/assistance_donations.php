<?php
use App\Http\Controllers\Admin\AssistanceDonationController;
use Illuminate\Support\Facades\Route;
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/assistance-donations', [AssistanceDonationController::class, 'index'])->name('admin.assistance-donations.index');
    Route::post('/assistance-donations/allocate', [AssistanceDonationController::class, 'allocate'])->name('admin.assistance-donations.allocate');
});
