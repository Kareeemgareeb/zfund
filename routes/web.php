<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $projects = \App\Models\CharitableProject::where('status','ACTIVE')->where('target_budget','>',0)->whereColumn('collected_amount','<','target_budget')->latest()->take(6)->get();
    $approvedRequests = \App\Models\AssistanceRequest::where('status','APPROVED')->whereRaw('COALESCE(approved_amount, requested_amount) > 0')->withSum(['transactions as collected_amount'=>fn($q)=>$q->where('status','SUCCESSFUL')],'amount')->get()->filter(fn($item) => (float)($item->collected_amount ?? 0) < (float)($item->approved_amount ?? $item->requested_amount))->take(6);
    $stats = ['projects'=>\App\Models\CharitableProject::where('status','ACTIVE')->count(),'confirmed'=>\App\Models\Transaction::where('status','SUCCESSFUL')->sum('amount'),'beneficiaries'=>\App\Models\Beneficiary::count()];
    return view('welcome', compact('projects', 'approvedRequests','stats'));
});

Route::get('/dashboard', function () {
    $user = Illuminate\Support\Facades\Auth::user()->load('beneficiary.fileRenewals');
    return view('dashboard', compact('user'));
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/calculator', function () {
    return view('zakat.calculator');
})->name('calculator');


use App\Http\Controllers\FileRenewalController;
Route::post('/beneficiary/renew-file', [FileRenewalController::class, 'submit'])->middleware('auth');


use App\Http\Controllers\BeneficiaryController;

Route::middleware('auth')->group(function () {
    Route::get('/beneficiary/apply', [BeneficiaryController::class, 'create'])->name('beneficiary.apply');
    Route::post('/beneficiary/apply', [BeneficiaryController::class, 'store'])->name('beneficiary.apply.store');
});
// Review access is checked inside the controller against admin/case_officer roles.
Route::patch('/admin/file-renewals/{renewal}/review', [FileRenewalController::class, 'review'])
    ->middleware('auth')->name('file-renewals.review');

// Prices: public read, admin-only edit and update.
Route::get('/zakat/metal-prices', [\App\Http\Controllers\ZakatMetalPriceController::class, 'latest'])->name('zakat.prices');
Route::middleware('auth')->group(function () {
 Route::get('/admin/zakat-prices', [\App\Http\Controllers\ZakatMetalPriceController::class, 'edit'])->name('admin.zakat-prices.edit');
 Route::post('/admin/zakat-prices', [\App\Http\Controllers\ZakatMetalPriceController::class, 'update'])->name('admin.zakat-prices.update');
});


// Phase 3: guarded administration routes. All checks occur before controller invocation.
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/assistance-requests', function () {
        abort_unless(auth()->user()->hasRole('admin'), 403);
        return app(\App\Http\Controllers\Admin\AssistanceRequestController::class)->index(request());
    })->name('admin.requests.index');

    Route::patch('/assistance-requests/{id}/status', function (\Illuminate\Http\Request $request, $id) {
        abort_unless(auth()->user()->hasRole('admin'), 403);
        return app(\App\Http\Controllers\Admin\AssistanceRequestController::class)->updateStatus($request, $id);
    })->name('admin.requests.updateStatus');
});

// دورة طلبات الإعانة
require __DIR__.'/assistance_requests.php';

// دورة طلبات الإعانة


// مراجعة ملفات المستفيدين
require __DIR__.'/beneficiary_reviews.php';

// إدارة تجديد الملفات السنوي - تستخدم جدول file_renewals الموجود دون أي ترحيلات.
Route::middleware('auth')->group(function () {
    Route::get('/admin/file-renewals', [\App\Http\Controllers\FileRenewalController::class, 'index'])->name('admin.file-renewals.index');
    Route::post('/admin/file-renewals/{renewal}/decision', [\App\Http\Controllers\FileRenewalController::class, 'reviewFromForm'])->name('admin.file-renewals.decision');
});

// المرحلة 15: إدارة وعرض المشاريع الخيرية.
require __DIR__.'/charitable_projects.php';

// المرحلة 17: تخصيص التبرعات الناجحة لطلبات الإعانة المعتمدة.
require __DIR__.'/assistance_donations.php';

// المرحلة 18: الحوالات المصرفية.
require __DIR__.'/bank_transfers.php';

// نظام الإشعارات الداخلية (In-App Notifications)
require __DIR__.'/notifications.php';

Route::get('/assistance-opportunities',[\App\Http\Controllers\PublicAssistanceController::class,'index'])->name('assistance.public.index');

// Public detail pages only expose approved cases and active projects.
Route::get('/assistance-opportunities/{assistance}', [\App\Http\Controllers\PublicDetailController::class, 'assistance'])->whereNumber('assistance')->name('assistance.public.show');
