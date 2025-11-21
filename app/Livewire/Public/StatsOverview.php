<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\PelaksanaanAsesmen;
use App\Models\Wilayah;

class StatsOverview extends Component
{
    public function render()
    {
        $stats = [
            'total_sekolah' => PelaksanaanAsesmen::distinct('sekolah_id')->count(),
            'total_peserta' => PelaksanaanAsesmen::sum('jumlah_peserta'),
            'total_wilayah' => Wilayah::whereHas('pelaksanaanAsesmen')->count(),
            'avg_partisipasi' => round((PelaksanaanAsesmen::avg('partisipasi_literasi') + PelaksanaanAsesmen::avg('partisipasi_numerasi')) / 2, 1),
        ];

        return view('livewire.public.stats-overview', compact('stats'));
    }
}
