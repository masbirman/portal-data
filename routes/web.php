<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AsesmenController;
use App\Http\Controllers\AsesmenNasionalController;
use App\Http\Controllers\DownloadRequestController;
use App\Http\Controllers\PublicDashboardController;

// Public Routes
Route::get('/', [PublicDashboardController::class, 'landing'])->name('public.landing');

// Download Request Routes
Route::get('/request-download', [DownloadRequestController::class, 'index'])->name('download-request.index');
Route::post('/request-download', [DownloadRequestController::class, 'store'])->name('download-request.store');
Route::get('/request-download/success', [DownloadRequestController::class, 'success'])->name('download-request.success');
Route::get('/request-download/tracking', [\App\Http\Controllers\DownloadRequestTrackingController::class, 'index'])->name('download-request.tracking');
Route::post('/request-download/tracking', [\App\Http\Controllers\DownloadRequestTrackingController::class, 'check'])->name('download-request.tracking.check');
Route::get('/download/{token}', [DownloadRequestController::class, 'download'])->name('download-request.download');

// Asesmen Nasional Routes (New Structure)
Route::get('/asesmen-nasional/{tahun}', [AsesmenNasionalController::class, 'index'])->name('asesmen-nasional.index');
Route::get('/asesmen-nasional/{tahun}/wilayah/{wilayah}', [AsesmenNasionalController::class, 'wilayah'])->name('asesmen-nasional.wilayah');

// Legacy routes (keep for backward compatibility)
Route::get('/dashboard', [PublicDashboardController::class, 'dashboard'])->name('public.dashboard');
Route::get('/old', [AsesmenController::class, 'index'])->name('home');
Route::get('/asesmen/{tahun}', [AsesmenController::class, 'rekap'])->name('asesmen.rekap');
Route::get('/asesmen/{tahun}/wilayah/{wilayah_id}', [AsesmenController::class, 'detail'])->name('asesmen.detail');

// Test export route
Route::get('/test-export', function() {
    $data = \App\Models\Wilayah::orderBy('id')->get()->map(function($wilayah) {
        return [
            'wilayah' => $wilayah->nama,
            'status' => [
                'mandiri' => 0,
                'menumpang' => 0,
                'belum' => 0
            ],
            'moda' => [
                'online' => 0,
                'semi' => 0,
                'belum' => 0
            ]
        ];
    })->toArray();
    
    try {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\DashboardStatsExport($data, 2023), 
            'test.xlsx'
        );
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
