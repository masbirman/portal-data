<?php

namespace App\Livewire\Public;

use App\Models\JenjangPendidikan;
use App\Models\PelaksanaanAsesmen;
use App\Models\SiklusAsesmen;
use App\Models\Wilayah;
use Livewire\Component;

class WilayahTrendChart extends Component
{
    public $selectedTahun = '';
    public $selectedWilayah = 'all';
    public $selectedJenjang = 'all';

    public function mount()
    {
        // Set default tahun ke tahun terakhir yang ada datanya
        $latestYear = SiklusAsesmen::whereHas('pelaksanaanAsesmen')
            ->orderBy('tahun', 'desc')
            ->first();

        if ($latestYear) {
            $this->selectedTahun = $latestYear->tahun;
        }
    }

    public function render()
    {
        $wilayahs = Wilayah::orderBy('urutan')->orderBy('nama')->get();
        $jenjangs = JenjangPendidikan::orderBy('id')->get();
        $years = SiklusAsesmen::whereHas('pelaksanaanAsesmen')->orderBy('tahun', 'desc')->pluck('tahun')->toArray();

        $chartData = $this->getChartData();

        // Nama untuk display
        if ($this->selectedWilayah === 'all') {
            $selectedWilayahName = 'Semua Kota/Kabupaten';
        } elseif ($this->selectedWilayah) {
            $selectedWilayahName = Wilayah::find($this->selectedWilayah)?->nama ?? '-';
        } else {
            $selectedWilayahName = null;
        }

        if ($this->selectedJenjang === 'all') {
            $selectedJenjangName = 'Semua Jenjang';
        } elseif ($this->selectedJenjang) {
            $selectedJenjangName = JenjangPendidikan::find($this->selectedJenjang)?->nama ?? '-';
        } else {
            $selectedJenjangName = null;
        }

        return view('livewire.public.wilayah-trend-chart', compact(
            'wilayahs', 'jenjangs', 'years', 'chartData',
            'selectedWilayahName', 'selectedJenjangName'
        ));
    }

    public function getChartData()
    {
        // Harus pilih tahun, wilayah, dan jenjang
        if (empty($this->selectedTahun) || empty($this->selectedWilayah) || empty($this->selectedJenjang)) {
            return null;
        }

        $cacheKey = 'wilayah_stats_' . $this->selectedTahun . '_' . $this->selectedWilayah . '_' . $this->selectedJenjang;

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () {
            $query = PelaksanaanAsesmen::whereHas('siklusAsesmen', function ($q) {
                $q->where('tahun', $this->selectedTahun);
            });

            // Build filter untuk sekolah
            $query->whereHas('sekolah', function ($q) {
                if ($this->selectedWilayah && $this->selectedWilayah !== 'all') {
                    $q->where('wilayah_id', $this->selectedWilayah);
                }
                if ($this->selectedJenjang && $this->selectedJenjang !== 'all') {
                    $q->where('jenjang_pendidikan_id', $this->selectedJenjang);
                }
            });

            $jumlahSekolah = (clone $query)->count();
            $jumlahPeserta = (clone $query)->sum('jumlah_peserta');
            $avgLiterasi = (clone $query)->avg('partisipasi_literasi') ?? 0;
            $avgNumerasi = (clone $query)->avg('partisipasi_numerasi') ?? 0;
            $keikutsertaan = round(($avgLiterasi + $avgNumerasi) / 2, 1);

            // Data untuk chart status dan moda
            $statusData = (clone $query)
                ->selectRaw('status_pelaksanaan, count(*) as total')
                ->groupBy('status_pelaksanaan')
                ->pluck('total', 'status_pelaksanaan')
                ->toArray();

            $modaData = (clone $query)
                ->selectRaw('moda_pelaksanaan, count(*) as total')
                ->groupBy('moda_pelaksanaan')
                ->pluck('total', 'moda_pelaksanaan')
                ->toArray();

            // Data trend antar tahun
            $trendData = $this->getTrendData();

            return [
                'jumlah_sekolah' => $jumlahSekolah,
                'jumlah_peserta' => $jumlahPeserta,
                'keikutsertaan' => $keikutsertaan,
                'status' => $statusData,
                'moda' => $modaData,
                'trend' => $trendData,
            ];
        });
    }

    private function getTrendData()
    {
        $years = SiklusAsesmen::whereHas('pelaksanaanAsesmen')->orderBy('tahun')->pluck('tahun')->toArray();

        $categories = [];
        $seriesSekolah = [];
        $seriesPeserta = [];
        $seriesKeikutsertaan = [];

        foreach ($years as $tahun) {
            $categories[] = $tahun;

            $query = PelaksanaanAsesmen::whereHas('siklusAsesmen', function ($q) use ($tahun) {
                $q->where('tahun', $tahun);
            });

            $query->whereHas('sekolah', function ($q) {
                if ($this->selectedWilayah && $this->selectedWilayah !== 'all') {
                    $q->where('wilayah_id', $this->selectedWilayah);
                }
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
            'sekolah' => $seriesSekolah,
            'peserta' => $seriesPeserta,
            'keikutsertaan' => $seriesKeikutsertaan,
        ];
    }
}
