<?php

namespace App\Livewire\Public;

use App\Models\JenjangPendidikan;
use App\Models\PelaksanaanAsesmen;
use App\Models\SiklusAsesmen;
use App\Models\Wilayah;
use Livewire\Component;

class WilayahTrendChart extends Component
{
    public $selectedWilayah = '';
    public $selectedJenjang = '';

    public function render()
    {
        $wilayahs = Wilayah::orderBy('urutan')->orderBy('nama')->get();
        $jenjangs = JenjangPendidikan::orderBy('id')->get();
        $years = SiklusAsesmen::whereHas('pelaksanaanAsesmen')->orderBy('tahun')->pluck('tahun')->toArray();

        $chartData = $this->getChartData($years);
        $selectedWilayahName = $this->selectedWilayah ? Wilayah::find($this->selectedWilayah)?->nama : null;
        $selectedJenjangName = $this->selectedJenjang ? JenjangPendidikan::find($this->selectedJenjang)?->nama : 'Semua Jenjang';

        return view('livewire.public.wilayah-trend-chart', compact('wilayahs', 'jenjangs', 'chartData', 'years', 'selectedWilayahName', 'selectedJenjangName'));
    }

    public function getChartData($years)
    {
        if (empty($this->selectedWilayah)) {
            return null;
        }

        $cacheKey = 'wilayah_trend_' . $this->selectedWilayah . '_' . ($this->selectedJenjang ?: 'all');

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
                    if ($this->selectedJenjang) {
                        $q->where('jenjang_pendidikan_id', $this->selectedJenjang);
                    }
                });

                $seriesSekolah[] = (clone $query)->count();
                $seriesPeserta[] = (clone $query)->sum('jumlah_peserta');

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
}
