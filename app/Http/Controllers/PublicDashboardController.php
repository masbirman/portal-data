<?php

namespace App\Http\Controllers;

use App\Models\PelaksanaanAsesmen;
use App\Models\SiklusAsesmen;
use App\Models\JenjangPendidikan;
use App\Models\Wilayah;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class PublicDashboardController extends Controller
{
    public function landing()
    {
        // Prevent session locking so other requests aren't blocked
        session_write_close();
        // Allow longer execution time for the initial cache generation
        ini_set('max_execution_time', 300);

        $stats = $this->getOverviewStats();
        
        return view('public.landing', compact('stats'));
    }

    public function dashboard()
    {
        return view('public.dashboard');
    }

    public function peta($tahun = null)
    {
        return view('public.peta', compact('tahun'));
    }

    private function getOverviewStats()
    {
        return \Illuminate\Support\Facades\Cache::remember('overview_stats_landing', 3600, function () {
            return [
                'total_sekolah' => PelaksanaanAsesmen::distinct('sekolah_id')->count(),
                'total_peserta' => PelaksanaanAsesmen::sum('jumlah_peserta'),
                'total_wilayah' => Wilayah::whereHas('pelaksanaanAsesmen')->count(),
                'avg_partisipasi_literasi' => round(PelaksanaanAsesmen::avg('partisipasi_literasi'), 1),
                'avg_partisipasi_numerasi' => round(PelaksanaanAsesmen::avg('partisipasi_numerasi'), 1),
            ];
        });
    }
}
