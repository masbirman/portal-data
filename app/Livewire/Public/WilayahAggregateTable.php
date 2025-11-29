<?php

namespace App\Livewire\Public;

use App\Models\Wilayah;
use App\Models\JenjangPendidikan;
use App\Models\PelaksanaanAsesmen;
use App\Models\Sekolah;
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

        // Get paginated wilayah
        $wilayahData = Wilayah::orderBy('urutan', 'asc')
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%');
            })
            ->paginate(15);

        // Transform the collection to add stats
        $wilayahData->getCollection()->transform(function ($wilayah) use ($jenjangList) {
            $stats = [];

            // Get all schools for this wilayah and year
            $schools = Sekolah::where('wilayah_id', $wilayah->id)
                ->whereJsonContains('tahun', (string) $this->tahun)
                ->with('jenjangPendidikan')
                ->get();

            // Group by jenjang name
            $countsByJenjang = $schools->groupBy(function ($school) {
                return $school->jenjangPendidikan->nama ?? 'Lainnya';
            })->map->count();

            foreach ($jenjangList as $jenjang) {
                $stats[$jenjang->nama] = $countsByJenjang[$jenjang->nama] ?? 0;
            }

            // Attach stats to the wilayah object
            $wilayah->stats = $stats;

            return $wilayah;
        });

        return view('livewire.public.wilayah-aggregate-table', [
            'wilayahData' => $wilayahData,
            'jenjangList' => $jenjangList
        ]);
    }
}
