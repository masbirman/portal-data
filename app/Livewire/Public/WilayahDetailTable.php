<?php

namespace App\Livewire\Public;

use App\Models\PelaksanaanAsesmen;
use App\Models\JenjangPendidikan;
use App\Models\Sekolah;
use Livewire\Component;
use Livewire\WithPagination;

class WilayahDetailTable extends Component
{
    use WithPagination;

    public $tahun;
    public $wilayahId;
    public $jenjangIdFilter = 'all'; // Filter by jenjang ID
    public $search = '';
    public $perPage = 10;

    public function mount($tahun, $wilayahId)
    {
        $this->tahun = $tahun;
        $this->wilayahId = $wilayahId;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingJenjangIdFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function setJenjangFilter($jenjangId)
    {
        $this->jenjangIdFilter = $jenjangId;
        $this->resetPage();
    }

    public function render()
    {
        // Define custom order for jenjang
        $jenjangOrder = ['SMA', 'SMK', 'SMP', 'SD', 'SMALB', 'SMPLB', 'SDLB', 'PAKET C', 'PAKET B', 'PAKET A'];

        // Get all unique jenjang
        $allJenjang = JenjangPendidikan::all()->unique('nama');

        // Sort jenjang according to custom order
        $jenjangList = collect();
        foreach ($jenjangOrder as $nama) {
            $jenjang = $allJenjang->firstWhere('nama', $nama);
            if ($jenjang) {
                $jenjangList->push($jenjang);
            }
        }

        // Add any remaining jenjang not in the custom order
        foreach ($allJenjang as $jenjang) {
            if (!$jenjangList->contains('nama', $jenjang->nama)) {
                $jenjangList->push($jenjang);
            }
        }

        // Query data sekolah
        $query = Sekolah::query()
            ->with(['jenjangPendidikan', 'wilayah'])
            ->with(['pelaksanaanAsesmen' => function ($q) {
                $q->whereHas('siklusAsesmen', function ($q2) {
                    $q2->where('tahun', $this->tahun);
                });
            }])
            ->whereJsonContains('tahun', (string) $this->tahun)
            ->where('wilayah_id', $this->wilayahId);

        // Filter by jenjang
        if ($this->jenjangIdFilter !== 'all') {
            $query->where('jenjang_pendidikan_id', $this->jenjangIdFilter);
        }

        // Search by nama sekolah
        if ($this->search) {
            $query->where('nama', 'like', '%' . $this->search . '%');
        }

        $chartStats = $this->getChartStats();

        return view('livewire.public.wilayah-detail-table', [
            'data' => $query->orderBy('id')->paginate($this->perPage),
            'chartStats' => $chartStats,
            'jenjangList' => $jenjangList
        ]);
    }

    public function getChartStats()
    {
        $query = PelaksanaanAsesmen::query()
            ->whereHas('siklusAsesmen', function ($q) {
                $q->where('tahun', $this->tahun);
            })
            ->whereHas('sekolah', function ($q) {
                $q->where('wilayah_id', $this->wilayahId);

                if ($this->jenjangIdFilter !== 'all') {
                    $q->where('jenjang_pendidikan_id', $this->jenjangIdFilter);
                }

                if ($this->search) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                }
            });

        $statusStats = (clone $query)
            ->selectRaw('status_pelaksanaan, count(*) as count')
            ->groupBy('status_pelaksanaan')
            ->pluck('count', 'status_pelaksanaan')
            ->toArray();

        $modaStats = (clone $query)
            ->selectRaw('moda_pelaksanaan, count(*) as count')
            ->groupBy('moda_pelaksanaan')
            ->pluck('count', 'moda_pelaksanaan')
            ->toArray();

        return [
            'status' => $statusStats,
            'moda' => $modaStats,
        ];
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\WilayahDetailExport(
                $this->tahun,
                $this->wilayahId,
                $this->jenjangIdFilter,
                $this->search
            ),
            'data-sekolah-' . $this->tahun . '.xlsx'
        );
    }
}
