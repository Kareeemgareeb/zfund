<?php

use App\Http\Controllers\Admin\CharitableProjectController;
use App\Http\Controllers\CharitableProjectPublicController;
use Illuminate\Support\Facades\Route;

Route::get('/projects', [CharitableProjectPublicController::class, 'index'])->name('projects.index');
Route::get('/projects/{project}', [\App\Http\Controllers\PublicDetailController::class, 'project'])->whereNumber('project')->name('projects.show');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/projects', [CharitableProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [CharitableProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [CharitableProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [CharitableProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [CharitableProjectController::class, 'update'])->name('projects.update');
});
