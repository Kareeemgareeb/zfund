<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\ZakatCalculatorController;

Route::post('/zakat/calculate', [ZakatCalculatorController::class, 'calculate']);

use App\Http\Controllers\FileRenewalController;

// Beneficiary Route
Route::post('/beneficiary/renew-file', [FileRenewalController::class, 'submit'])->middleware('auth:sanctum');

// Admin Route
Route::post('/admin/renewals/{renewal}/review', [FileRenewalController::class, 'review'])->middleware('auth:sanctum');
