<?php

namespace App\Livewire\Public;

use App\Models\JenjangPendidikan;
use App\Models\PelaksanaanAsesmen;
use App\Models\SiklusAsesmen;
use App\Models\Wilayah;
use Livewire\Component;

class WilayahTrendChart extends Component
{
    public $selectedWilayah = 'all';
    public $selectedJenjang = '';

    public function render()
    {
        $wilayahs = Wilayah::orderBy('urutan')->orderBy('nama')->get();
        $jenjangs = JenjangPendidikan::orderBy('id')->get();
        $years = SiklusAsesmen::whereHas('pelaksanaanAsesmen')->orderBy('tahun')->pluck('tahun')->toArray();

        $chartData = $this->getChartData($years);

        if ($this->selectedWilayah === 'all') {
            $selectedWilayahName = 'Semua Kota/Kabupaten';
        } else {
            $selectedWilayahName = Wilayah::find($this->selectedWilayah)?->nama ?? '-';
        }

        if ($this->selectedJenjang === 'all') {
            $selectedJenjangName = 'Semua Jenjang';
        } elseif ($this->selectedJenjang) {
            $selectedJenjangName = JenjangPendidikan::find($this->selectedJenjang)?->nama ?? '-';
        } else {
            $selectedJenjangName = null;
        }

        return view('livewire.public.wilayah-trend-chart', compact('wilayahs', 'jenjangs', 'chartData', 'years', 'selectedWilayahName', 'selectedJenjangName'));
    }

    public function getChartData($years)
    {
        // Harus pilih jenjang dulu
        if (empty($this->selectedJenjang)) {
            return null;
        }

        $cacheKey = 'wilayah_trend_' . ($this->selectedWilayah ?: 'all') . '_' . ($this->selectedJenjang ?: 'all');

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($years) {
            $categories = [];
            $seriesSekolah = [];
            $seriesPeserta = [];
            $seriesKeikutsertaan = [];

            foreach ($years as $tahun) {
                $categories[] = $tahun;

                $query = PelaksanaanAsesmen::whereHas('siklusAsesmen', function ($q) use ($tahun) {
                    $q->where('tahun', $tahun);
                });

                // Build filter untuk sekolah
                $query->whereHas('sekolah', function ($q) {
                    // Filter wilayah jika bukan "all"
                    if ($this->selectedWilayah && $this->selectedWilayah !== 'all') {
                        $q->where('wilayah_id', $this->selectedWilayah);
                    }
                    // Filter jenjang jika bukan "all"
                    if ($this->selectedJenjang && $this->selectedJenjang !== 'all') {
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
