<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\PelaksanaanAsesmen;
use App\Models\JenjangPendidikan;

class JenjangChart extends Component
{
    public function render()
    {
        $data = JenjangPendidikan::withCount('sekolah')
            ->having('sekolah_count', '>', 0)
            ->get()
            ->map(function ($jenjang) {
                return [
                    'nama' => $jenjang->nama,
                    'count' => $jenjang->sekolah_count
                ];
            });

        return view('livewire.public.jenjang-chart', compact('data'));
    }
}
