<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\SiklusAsesmen;
use App\Models\PelaksanaanAsesmen;

class TrendChart extends Component
{
    public function render()
    {
        // Get years that have data
        $years = SiklusAsesmen::whereHas('pelaksanaanAsesmen')->orderBy('tahun')->get();

        $categories = [];
        $seriesPeserta = [];
        $seriesKeikutsertaan = [];

        foreach ($years as $year) {
            $categories[] = $year->tahun;

            $query = PelaksanaanAsesmen::whereHas('siklusAsesmen', function ($q) use ($year) {
                $q->where('tahun', $year->tahun);
            });

            // Total Peserta
            $seriesPeserta[] = (clone $query)->sum('jumlah_peserta');

            // Keikutsertaan (Avg Partisipasi)
            $avgLiterasi = (clone $query)->avg('partisipasi_literasi') ?? 0;
            $avgNumerasi = (clone $query)->avg('partisipasi_numerasi') ?? 0;
            $seriesKeikutsertaan[] = round(($avgLiterasi + $avgNumerasi) / 2, 1);
        }

        $chartData = [
            'categories' => $categories,
            'series' => [
                [
                    'name' => 'Peserta Asesmen',
                    'data' => $seriesPeserta
                ],
                [
                    'name' => 'Keikutsertaan (%)',
                    'data' => $seriesKeikutsertaan
                ]
            ]
        ];

        return view('livewire.public.trend-chart', compact('chartData'));
    }
}
