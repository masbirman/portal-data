<?php

namespace App\Http\Controllers;

use App\Models\SiklusAsesmen;
use App\Models\Wilayah;
use App\Models\PelaksanaanAsesmen;
use Illuminate\Http\Request;

class PetaDataController extends Controller
{
    /**
     * Tampilkan halaman peta data
     */
    public function index($tahun = null)
    {
        // Default ke tahun terbaru jika tidak ada parameter
        if (!$tahun) {
            $tahun = SiklusAsesmen::max('tahun') ?? date('Y');
        }

        // Validasi tahun
        $siklus = SiklusAsesmen::where('tahun', $tahun)->first();

        // Ambil data wilayah dengan statistik
        $wilayahData = Wilayah::all()->map(function ($wilayah) use ($tahun) {
            $pelaksanaan = PelaksanaanAsesmen::where('wilayah_id', $wilayah->id)
                ->whereHas('siklusAsesmen', function ($q) use ($tahun) {
                    $q->where('tahun', $tahun);
                })
                ->get();

            $totalSekolah = $pelaksanaan->count();
            $totalPeserta = $pelaksanaan->sum('jumlah_peserta');
            
            // Hitung status
            $statusMandiri = $pelaksanaan->where('status_pelaksanaan', 'mandiri')->count();
            $statusMenumpang = $pelaksanaan->where('status_pelaksanaan', 'menumpang')->count();

            return [
                'id' => $wilayah->id,
                'nama' => $wilayah->nama,
                'latitude' => $wilayah->latitude,
                'longitude' => $wilayah->longitude,
                'total_sekolah' => $totalSekolah,
                'total_peserta' => $totalPeserta,
                'status_mandiri' => $statusMandiri,
                'status_menumpang' => $statusMenumpang,
            ];
        });

        // Ambil tahun yang tersedia
        $availableYears = SiklusAsesmen::distinct()->pluck('tahun')->sort()->values();

        return view('public.peta-data', [
            'tahun' => $tahun,
            'siklus' => $siklus,
            'wilayahData' => $wilayahData,
            'availableYears' => $availableYears,
        ]);
    }
}
