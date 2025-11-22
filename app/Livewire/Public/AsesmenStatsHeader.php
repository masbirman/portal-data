<?php

namespace App\Livewire\Public;

use App\Models\PelaksanaanAsesmen;
use App\Models\JenjangPendidikan;
use App\Models\Sekolah;
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
        // Define custom order for jenjang
        $jenjangOrder = ['SMA', 'SMK', 'SMP', 'SD', 'SMALB', 'SMPLB', 'SDLB', 'PAKET C', 'PAKET B', 'PAKET A'];
        
        // Ambil semua jenjang untuk memastikan key array lengkap
        $jenjangs = JenjangPendidikan::all();
        
        // Inisialisasi stats dengan 0
        $stats = [];
        foreach ($jenjangs as $jenjang) {
            $stats[$jenjang->nama] = 0;
        }

        // Query Sekolah berdasarkan tahun yang dipilih
        $sekolahQuery = Sekolah::query()
            ->whereJsonContains('tahun', (string) $this->tahun);

        // Jika ada filter wilayah
        if ($this->wilayahId) {
            $sekolahQuery->where('wilayah_id', $this->wilayahId);
        }

        // Ambil data dan hitung per jenjang
        $sekolahCounts = $sekolahQuery->get()->groupBy(function ($item) {
            return $item->jenjangPendidikan->nama ?? 'Lainnya';
        })->map->count();

        // Merge hasil hitungan ke array stats
        foreach ($sekolahCounts as $nama => $count) {
            $stats[$nama] = $count;
        }

        // Sort stats according to custom order
        $sortedStats = [];
        foreach ($jenjangOrder as $jenjang) {
            if (isset($stats[$jenjang])) {
                $sortedStats[$jenjang] = $stats[$jenjang];
            }
        }
        
        // Add any remaining jenjang not in the custom order
        foreach ($stats as $jenjang => $count) {
            if (!isset($sortedStats[$jenjang])) {
                $sortedStats[$jenjang] = $count;
            }
        }

        return view('livewire.public.asesmen-stats-header', ['stats' => $sortedStats]);
    }
}
