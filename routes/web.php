<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AsesmenController;
use App\Http\Controllers\PublicDashboardController;

// Public Dashboard Routes
Route::get('/', [PublicDashboardController::class, 'landing'])->name('public.landing');
Route::get('/dashboard', [PublicDashboardController::class, 'dashboard'])->name('public.dashboard');

// Legacy routes (keep for backward compatibility)
Route::get('/old', [AsesmenController::class, 'index'])->name('home');
Route::get('/asesmen/{tahun}', [AsesmenController::class, 'rekap'])->name('asesmen.rekap');
Route::get('/asesmen/{tahun}/wilayah/{wilayah_id}', [AsesmenController::class, 'detail'])->name('asesmen.detail');
