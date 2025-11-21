<?php

namespace App\Livewire\Public;

use App\Models\PelaksanaanAsesmen;
use App\Models\JenjangPendidikan;
use Livewire\Component;
use Livewire\WithPagination;

class WilayahDetailTable extends Component
{
    use WithPagination;
    
    public $tahun;
    public $wilayahId;
    public $jenjangFilter = 'all'; // Filter by jenjang
    public $search = '';
    
    public function mount($tahun, $wilayahId)
    {
        $this->tahun = $tahun;
        $this->wilayahId = $wilayahId;
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingJenjangFilter()
    {
        $this->resetPage();
    }
    
    public function setJenjangFilter($jenjang)
    {
        $this->jenjangFilter = $jenjang;
    }
    
    public function render()
    {
        $jenjangList = JenjangPendidikan::all();
        
        // Query data pelaksanaan
        $query = PelaksanaanAsesmen::with(['sekolah.jenjangPendidikan'])
            ->whereHas('siklusAsesmen', function($q) {
                $q->where('tahun', $this->tahun);
            })
            ->where('wilayah_id', $this->wilayahId);
        
        // Filter by jenjang
        if ($this->jenjangFilter !== 'all') {
            $query->whereHas('sekolah', function($q) {
                $q->whereHas('jenjangPendidikan', function($q2) {
                    $q2->where('nama', $this->jenjangFilter);
                });
            });
        }
        
        // Search by nama sekolah
        if ($this->search) {
            $query->whereHas('sekolah', function($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            });
        }
        
        $data = $query->orderBy('id')->paginate(10);
        
        return view('livewire.public.wilayah-detail-table', [
            'data' => $data,
            'jenjangList' => $jenjangList
        ]);
    }
}
