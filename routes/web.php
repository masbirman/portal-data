<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AsesmenController;
use App\Http\Controllers\AsesmenNasionalController;
use App\Http\Controllers\PublicDashboardController;

// Public Routes
Route::get('/', [PublicDashboardController::class, 'landing'])->name('public.landing');

// Asesmen Nasional Routes (New Structure)
Route::get('/asesmen-nasional/{tahun}', [AsesmenNasionalController::class, 'index'])->name('asesmen-nasional.index');
Route::get('/asesmen-nasional/{tahun}/wilayah/{wilayah}', [AsesmenNasionalController::class, 'wilayah'])->name('asesmen-nasional.wilayah');

// Legacy routes (keep for backward compatibility)
Route::get('/dashboard', [PublicDashboardController::class, 'dashboard'])->name('public.dashboard');
Route::get('/old', [AsesmenController::class, 'index'])->name('home');
Route::get('/asesmen/{tahun}', [AsesmenController::class, 'rekap'])->name('asesmen.rekap');
Route::get('/asesmen/{tahun}/wilayah/{wilayah_id}', [AsesmenController::class, 'detail'])->name('asesmen.detail');
