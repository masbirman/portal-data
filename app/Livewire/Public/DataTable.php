<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PelaksanaanAsesmen;
use App\Models\SiklusAsesmen;
use App\Models\JenjangPendidikan;
use App\Models\Wilayah;

class DataTable extends Component
{
    use WithPagination;

    public $search = '';
    public $filterTahun = '';
    public $filterJenjang = '';
    public $filterWilayah = '';
    public $perPage = 10;

    protected $queryString = ['search', 'filterTahun', 'filterJenjang', 'filterWilayah'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = PelaksanaanAsesmen::with(['siklusAsesmen', 'sekolah.jenjangPendidikan', 'wilayah']);

        // Apply filters
        if ($this->search) {
            $query->whereHas('sekolah', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterTahun) {
            $query->where('siklus_asesmen_id', $this->filterTahun);
        }

        if ($this->filterJenjang) {
            $query->whereHas('sekolah', function ($q) {
                $q->where('jenjang_pendidikan_id', $this->filterJenjang);
            });
        }

        if ($this->filterWilayah) {
            $query->where('wilayah_id', $this->filterWilayah);
        }

        $data = $query->latest('id')->paginate($this->perPage);

        // Get filter options
        $tahunOptions = SiklusAsesmen::orderBy('tahun', 'desc')->pluck('tahun', 'id');
        $jenjangOptions = JenjangPendidikan::pluck('nama', 'id');
        $wilayahOptions = Wilayah::orderBy('nama')->pluck('nama', 'id');

        return view('livewire.public.data-table', [
            'data' => $data,
            'tahunOptions' => $tahunOptions,
            'jenjangOptions' => $jenjangOptions,
            'wilayahOptions' => $wilayahOptions,
        ]);
    }
}
