<?php

namespace App\Http\Controllers;

use App\Models\PelaksanaanAsesmen;
use App\Models\SiklusAsesmen;
use App\Models\JenjangPendidikan;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class PublicDashboardController extends Controller
{
    public function landing()
    {
        $stats = $this->getOverviewStats();
        
        return view('public.landing', compact('stats'));
    }

    public function dashboard()
    {
        $stats = $this->getOverviewStats();
        $years = SiklusAsesmen::orderBy('tahun', 'desc')->pluck('tahun', 'id');
        
        return view('public.dashboard', compact('stats', 'years'));
    }

    private function getOverviewStats()
    {
        return [
            'total_sekolah' => PelaksanaanAsesmen::distinct('sekolah_id')->count(),
            'total_peserta' => PelaksanaanAsesmen::sum('jumlah_peserta'),
            'total_wilayah' => Wilayah::whereHas('pelaksanaanAsesmen')->count(),
            'avg_partisipasi_literasi' => round(PelaksanaanAsesmen::avg('partisipasi_literasi'), 1),
            'avg_partisipasi_numerasi' => round(PelaksanaanAsesmen::avg('partisipasi_numerasi'), 1),
        ];
    }
}
