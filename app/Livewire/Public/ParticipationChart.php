<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\PelaksanaanAsesmen;
use App\Models\SiklusAsesmen;

class ParticipationChart extends Component
{
    public function render()
    {
        $data = SiklusAsesmen::with('pelaksanaanAsesmen')
            ->orderBy('tahun')
            ->get()
            ->map(function ($siklus) {
                return [
                    'tahun' => $siklus->tahun,
                    'literasi' => round($siklus->pelaksanaanAsesmen->avg('partisipasi_literasi'), 1),
                    'numerasi' => round($siklus->pelaksanaanAsesmen->avg('partisipasi_numerasi'), 1),
                ];
            });

        return view('livewire.public.participation-chart', compact('data'));
    }
}
