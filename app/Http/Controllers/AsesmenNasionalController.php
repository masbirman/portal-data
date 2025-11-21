<?php

namespace App\Http\Controllers;

use App\Models\SiklusAsesmen;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class AsesmenNasionalController extends Controller
{
    /**
     * Tampilkan halaman agregat per tahun
     */
    public function index($tahun)
    {
        // Validasi tahun
        $siklus = SiklusAsesmen::where('tahun', $tahun)->first();
        
        if (!$siklus) {
            abort(404, 'Data asesmen untuk tahun ' . $tahun . ' tidak ditemukan.');
        }

        return view('public.asesmen-nasional', [
            'tahun' => $tahun,
            'siklus' => $siklus
        ]);
    }

    /**
     * Tampilkan halaman detail wilayah
     */
    public function wilayah($tahun, $wilayahId)
    {
        // Validasi tahun
        $siklus = SiklusAsesmen::where('tahun', $tahun)->first();
        
        if (!$siklus) {
            abort(404, 'Data asesmen untuk tahun ' . $tahun . ' tidak ditemukan.');
        }

        // Validasi wilayah
        $wilayah = Wilayah::find($wilayahId);
        
        if (!$wilayah) {
            abort(404, 'Wilayah tidak ditemukan.');
        }

        return view('public.wilayah-detail', [
            'tahun' => $tahun,
            'siklus' => $siklus,
            'wilayah' => $wilayah
        ]);
    }
}
