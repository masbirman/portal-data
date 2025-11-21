<?php

namespace App\Livewire\Public;

use App\Models\Wilayah;
use App\Models\JenjangPendidikan;
use App\Models\PelaksanaanAsesmen;
use Livewire\Component;
use Livewire\WithPagination;

class WilayahAggregateTable extends Component
{
    use WithPagination;
    
    public $tahun;
    public $search = '';
    
    public function mount($tahun)
    {
        $this->tahun = $tahun;
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        $jenjangList = JenjangPendidikan::all();
        
        // Get all wilayah dengan data agregat
        $wilayahData = Wilayah::when($this->search, function($query) {
                $query->where('nama', 'like', '%' . $this->search . '%');
            })
            ->orderBy('nama')
            ->paginate(15);
        
        // Untuk setiap wilayah, hitung jumlah sekolah per jenjang
        foreach ($wilayahData as $wilayah) {
            $wilayah->stats = [];
            
            foreach ($jenjangList as $jenjang) {
                $count = PelaksanaanAsesmen::whereHas('siklusAsesmen', function($q) {
                        $q->where('tahun', $this->tahun);
                    })
                    ->where('wilayah_id', $wilayah->id)
                    ->whereHas('sekolah', function($q) use ($jenjang) {
                        $q->where('jenjang_pendidikan_id', $jenjang->id);
                    })
                    ->distinct('sekolah_id')
                    ->count('sekolah_id');
                
                $wilayah->stats[$jenjang->nama] = $count;
            }
        }
        
        return view('livewire.public.wilayah-aggregate-table', [
            'wilayahData' => $wilayahData,
            'jenjangList' => $jenjangList
        ]);
    }
}
