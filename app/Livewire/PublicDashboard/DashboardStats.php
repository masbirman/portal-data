<?php

namespace App\Livewire\PublicDashboard;

use Livewire\Component;
use App\Models\PelaksanaanAsesmen;
use App\Models\Wilayah;
use App\Models\JenjangPendidikan;
use App\Models\SiklusAsesmen;
use Illuminate\Support\Facades\DB;

class DashboardStats extends Component
{
    public $selectedYear;
    public $selectedWilayah;
    public $selectedJenjang;
    public $activeTab = 'status'; // status, moda

    public function mount()
    {
        $this->selectedYear = SiklusAsesmen::max('tahun') ?? date('Y');
    }

    public function getFiltersProperty()
    {
        return [
            'years' => SiklusAsesmen::orderBy('tahun', 'desc')->pluck('tahun', 'tahun'),
            'wilayahs' => Wilayah::orderBy('nama')->pluck('nama', 'id'),
            'jenjangs' => JenjangPendidikan::orderBy('nama')->pluck('nama', 'id'),
        ];
    }

    public function getStatsProperty()
    {
        $query = PelaksanaanAsesmen::query()
            ->with(['sekolah.jenjangPendidikan', 'wilayah'])
            ->whereHas('siklusAsesmen', function ($q) {
                $q->where('tahun', $this->selectedYear);
            });

        if ($this->selectedWilayah) {
            $query->where('wilayah_id', $this->selectedWilayah);
        }

        if ($this->selectedJenjang) {
            $query->whereHas('sekolah', function ($q) {
                $q->where('jenjang_pendidikan_id', $this->selectedJenjang);
            });
        }

        $data = $query->get();

        // Group by Jenjang
        $grouped = $data->groupBy(function ($item) {
            return $item->sekolah->jenjangPendidikan->nama ?? 'Lainnya';
        });

        $charts = [];
        $tableStatus = [];
        $tableModa = [];

        $allJenjangs = JenjangPendidikan::orderBy('nama')->get();

        // Custom Sort Order
        $customOrder = ['SMA', 'SMK', 'SMP', 'SD', 'SMALB', 'SMPLB', 'SDLB', 'PAKET C', 'PAKET B', 'PAKET A'];
        
        $sortedJenjangs = $allJenjangs->sortBy(function ($jenjang) use ($customOrder) {
            $index = array_search(strtoupper($jenjang->nama), $customOrder);
            return $index === false ? 999 : $index;
        });

        foreach ($sortedJenjangs as $jenjang) {
            $items = $grouped->get($jenjang->nama, collect());
            
            // Status Counts
            $mandiri = $items->filter(fn($i) => strtoupper($i->status_pelaksanaan) === 'MANDIRI')->count();
            $menumpang = $items->filter(fn($i) => strtoupper($i->status_pelaksanaan) === 'MENUMPANG')->count();
            $belumStatus = $items->filter(fn($i) => !in_array(strtoupper($i->status_pelaksanaan), ['MANDIRI', 'MENUMPANG']))->count();

            // Moda Counts
            $online = $items->filter(fn($i) => strtoupper($i->moda_pelaksanaan) === 'ONLINE')->count();
            $semi = $items->filter(fn($i) => in_array(strtoupper($i->moda_pelaksanaan), ['SEMI ONLINE', 'SEMI_ONLINE']))->count();
            $belumModa = $items->filter(fn($i) => !in_array(strtoupper($i->moda_pelaksanaan), ['ONLINE', 'SEMI ONLINE', 'SEMI_ONLINE']))->count();

            // Chart Data (Exclude Belum)
            $charts[$jenjang->nama] = [
                'status' => [$mandiri, $menumpang],
                'moda' => [$online, $semi],
            ];

            // Table Data
            $tableStatus[] = [
                'jenjang' => $jenjang->nama,
                'mandiri' => $mandiri,
                'menumpang' => $menumpang,
                'belum' => $belumStatus,
                'total' => $mandiri + $menumpang + $belumStatus
            ];

            $tableModa[] = [
                'jenjang' => $jenjang->nama,
                'online' => $online,
                'semi' => $semi,
                'belum' => $belumModa,
                'total' => $online + $semi + $belumModa
            ];
        }

        // --- Regional Recap Data (Ignores Wilayah Filter) ---
        $queryWilayah = PelaksanaanAsesmen::query()
            ->with(['wilayah'])
            ->whereHas('siklusAsesmen', function ($q) {
                $q->where('tahun', $this->selectedYear);
            });

        if ($this->selectedJenjang) {
            $queryWilayah->whereHas('sekolah', function ($q) {
                $q->where('jenjang_pendidikan_id', $this->selectedJenjang);
            });
        }

        $dataWilayah = $queryWilayah->get();
        $groupedWilayah = $dataWilayah->groupBy(function ($item) {
            return $item->wilayah->nama ?? 'Lainnya';
        });

        $tableWilayah = [];
        $allWilayahs = Wilayah::orderBy('id')->get();

        foreach ($allWilayahs as $wilayah) {
            $items = $groupedWilayah->get($wilayah->nama, collect());

            // Status Counts
            $mandiri = $items->filter(fn($i) => strtoupper($i->status_pelaksanaan) === 'MANDIRI')->count();
            $menumpang = $items->filter(fn($i) => strtoupper($i->status_pelaksanaan) === 'MENUMPANG')->count();
            $belumStatus = $items->filter(fn($i) => !in_array(strtoupper($i->status_pelaksanaan), ['MANDIRI', 'MENUMPANG']))->count();

            // Moda Counts
            $online = $items->filter(fn($i) => strtoupper($i->moda_pelaksanaan) === 'ONLINE')->count();
            $semi = $items->filter(fn($i) => in_array(strtoupper($i->moda_pelaksanaan), ['SEMI ONLINE', 'SEMI_ONLINE']))->count();
            $belumModa = $items->filter(fn($i) => !in_array(strtoupper($i->moda_pelaksanaan), ['ONLINE', 'SEMI ONLINE', 'SEMI_ONLINE']))->count();

            $tableWilayah[] = [
                'wilayah' => $wilayah->nama,
                'status' => [
                    'mandiri' => $mandiri,
                    'menumpang' => $menumpang,
                    'belum' => $belumStatus
                ],
                'moda' => [
                    'online' => $online,
                    'semi' => $semi,
                    'belum' => $belumModa
                ]
            ];
        }

        // Grand Totals for Header
        $totalSekolah = $data->unique('sekolah_id')->count();
        
        $totalMandiri = $data->filter(fn($i) => strtoupper($i->status_pelaksanaan) === 'MANDIRI')->unique('sekolah_id')->count();
        $totalMenumpang = $data->filter(fn($i) => strtoupper($i->status_pelaksanaan) === 'MENUMPANG')->unique('sekolah_id')->count();
        $totalBelumStatus = $totalSekolah - $totalMandiri - $totalMenumpang;

        $totalOnline = $data->filter(fn($i) => strtoupper($i->moda_pelaksanaan) === 'ONLINE')->unique('sekolah_id')->count();
        $totalSemi = $data->filter(fn($i) => in_array(strtoupper($i->moda_pelaksanaan), ['SEMI ONLINE', 'SEMI_ONLINE']))->unique('sekolah_id')->count();
        $totalBelumModa = $totalSekolah - $totalOnline - $totalSemi;

        return [
            'charts' => $charts,
            'tableStatus' => $tableStatus,
            'tableModa' => $tableModa,
            'tableWilayah' => $tableWilayah,
            'totals' => [
                'sekolah' => $totalSekolah,
                'status' => [
                    'mandiri' => $totalMandiri,
                    'menumpang' => $totalMenumpang,
                    'belum' => $totalBelumStatus
                ],
                'moda' => [
                    'online' => $totalOnline,
                    'semi' => $totalSemi,
                    'belum' => $totalBelumModa
                ]
            ]
        ];
    }

    public function render()
    {
        return view('livewire.public-dashboard.dashboard-stats');
    }
}
