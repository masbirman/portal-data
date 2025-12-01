<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\SiklusAsesmen;
use App\Models\PelaksanaanAsesmen;
use App\Models\Wilayah;

class WilayahTrendChart extends Component
{
    public $selectedWilayah = '';

    public function render()
    {
        $wilayahs = Wilayah::orderBy('urutan')->orderBy('nama')->get();
        $years = SiklusAsesmen::whereHas('pelaksanaanAsesmen')->orderBy('tahun')->pluck('tahun')->toArray();

        $chartData = $this->getChartData($years);

        return view('livewire.public.wilayah-trend-chart', compact('wilayahs', 'chartData', 'years'));
    }

    public function getChartData($years)
    {
        if (empty($this->selectedWilayah)) {
            return null;
        }

        $cacheKey = 'wilayah_trend_' . $this->selectedWilayah;

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($years) {
            $categories = [];
            $seriesSekolah = [];
            $seriesPeserta = [];
            $seriesKeikutsertaan = [];

            foreach ($years as $tahun) {
                $categories[] = $tahun;

                $query = PelaksanaanAsesmen::whereHas('siklusAsesmen', function ($q) use ($tahun) {
                    $q->where('tahun', $tahun);
                })->whereHas('sekolah', function ($q) {
                    $q->where('wilayah_id', $this->selectedWilayah);
                });

                // Jumlah Sekolah
                $seriesSekolah[] = (clone $query)->count();

                // Total Peserta
                $seriesPeserta[] = (clone $query)->sum('jumlah_peserta');

                // Keikutsertaan (Avg Partisipasi)
                $avgLiterasi = (clone $query)->avg('partisipasi_literasi') ?? 0;
                $avgNumerasi = (clone $query)->avg('partisipasi_numerasi') ?? 0;
                $seriesKeikutsertaan[] = round(($avgLiterasi + $avgNumerasi) / 2, 1);
            }

            return [
                'categories' => $categories,
                'series' => [
                    ['name' => 'Jumlah Sekolah', 'data' => $seriesSekolah],
                    ['name' => 'Jumlah Peserta', 'data' => $seriesPeserta],
                    ['name' => 'Keikutsertaan (%)', 'data' => $seriesKeikutsertaan]
                ]
            ];
        });
    }

    public function updatedSelectedWilayah()
    {
        // Clear cache when wilayah changes to get fresh data
    }
}
