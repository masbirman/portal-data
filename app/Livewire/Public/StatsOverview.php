<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\SiklusAsesmen;
use App\Models\PelaksanaanAsesmen;

class StatsOverview extends Component
{
    public function render()
    {
        // Get the latest year that actually has data
        $latestYear = SiklusAsesmen::whereHas('pelaksanaanAsesmen')->max('tahun') ?? date('Y');

        // Cache key based on latest year to invalidate when new data comes
        $cacheKey = 'stats_overview_public_' . ($latestYear ?? 'default');
        
        $stats = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($latestYear) {
            $query = PelaksanaanAsesmen::whereHas('siklusAsesmen', function ($q) use ($latestYear) {
                $q->where('tahun', $latestYear);
            });

            $totalSekolah = (clone $query)->distinct('sekolah_id')->count();
            $totalPeserta = (clone $query)->sum('jumlah_peserta');
            
            $mandiriCount = (clone $query)->whereRaw('UPPER(status_pelaksanaan) = ?', ['MANDIRI'])->distinct('sekolah_id')->count();
            $kemandirian = $totalSekolah > 0 ? ($mandiriCount / $totalSekolah) * 100 : 0;

            $avgLiterasi = (clone $query)->avg('partisipasi_literasi') ?? 0;
            $avgNumerasi = (clone $query)->avg('partisipasi_numerasi') ?? 0;
            $keikutsertaan = ($avgLiterasi + $avgNumerasi) / 2;

            return [
                'total_sekolah' => $totalSekolah,
                'total_peserta' => $totalPeserta,
                'kemandirian' => round($kemandirian, 1),
                'keikutsertaan' => round($keikutsertaan, 1),
            ];
        });

        return view('livewire.public.stats-overview', compact('stats', 'latestYear'));
    }
}
