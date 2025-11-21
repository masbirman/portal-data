<?php

namespace App\Livewire\Public;

use App\Models\PelaksanaanAsesmen;
use App\Models\JenjangPendidikan;
use Livewire\Component;

class AsesmenStatsHeader extends Component
{
    public $tahun;
    public $wilayahId = null; // Optional: jika untuk wilayah tertentu saja
    
    public function mount($tahun, $wilayahId = null)
    {
        $this->tahun = $tahun;
        $this->wilayahId = $wilayahId;
    }
    
    public function render()
    {
        // Ambil semua jenjang
        $jenjangList = JenjangPendidikan::all();
        
        $stats = [];
        
        foreach ($jenjangList as $jenjang) {
            $query = PelaksanaanAsesmen::whereHas('siklusAsesmen', function($q) {
                $q->where('tahun', $this->tahun);
            })
            ->whereHas('sekolah', function($q) use ($jenjang) {
                $q->where('jenjang_pendidikan_id', $jenjang->id);
                
                // Filter by wilayah jika ada
                if ($this->wilayahId) {
                    $q->where('wilayah_id', $this->wilayahId);
                }
            });
            
            $stats[$jenjang->nama] = $query->distinct('sekolah_id')->count('sekolah_id');
        }
        
        return view('livewire.public.asesmen-stats-header', [
            'stats' => $stats
        ]);
    }
}
