<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AnalyticsController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export.csv', [AnalyticsController::class, 'export'])->name('dashboard.export');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
