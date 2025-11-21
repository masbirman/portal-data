<?php

namespace App\Http\Controllers;

use App\Models\SiklusAsesmen;
use App\Models\Wilayah;
use App\Models\JenjangPendidikan;
use App\Models\PelaksanaanAsesmen;
use Illuminate\Http\Request;

class AsesmenController extends Controller
{
    public function index()
    {
        $latestSiklus = SiklusAsesmen::orderBy('tahun', 'desc')->first();

        if ($latestSiklus) {
            return redirect()->route('asesmen.rekap', ['tahun' => $latestSiklus->tahun]);
        }

        return view('asesmen.no-data');
    }

    public function rekap($tahun)
    {
        $siklus = SiklusAsesmen::where('tahun', $tahun)->firstOrFail();
        $allSiklus = SiklusAsesmen::orderBy('tahun', 'desc')->get();
        $wilayahs = Wilayah::orderBy('id')->get(); // Order by ID as requested
        $jenjangs = JenjangPendidikan::all();

        // Aggregation
        $stats = [];
        foreach ($wilayahs as $wilayah) {
            $stats[$wilayah->id] = [];
            foreach ($jenjangs as $jenjang) {
                $count = PelaksanaanAsesmen::where('siklus_asesmen_id', $siklus->id)
                    ->where('wilayah_id', $wilayah->id)
                    ->whereHas('sekolah', function ($query) use ($jenjang) {
                        $query->where('jenjang_pendidikan_id', $jenjang->id);
                    })
                    ->count();
                $stats[$wilayah->id][$jenjang->id] = $count;
            }
        }

        return view('asesmen.rekap', compact('siklus', 'allSiklus', 'wilayahs', 'jenjangs', 'stats'));
    }

    public function detail($tahun, $wilayah_id)
    {
        $siklus = SiklusAsesmen::where('tahun', $tahun)->firstOrFail();
        $allSiklus = SiklusAsesmen::orderBy('tahun', 'desc')->get();
        $wilayah = Wilayah::findOrFail($wilayah_id);
        $jenjangs = JenjangPendidikan::all();

        // Get Data
        $data = PelaksanaanAsesmen::with(['sekolah.jenjangPendidikan'])
            ->where('siklus_asesmen_id', $siklus->id)
            ->where('wilayah_id', $wilayah->id)
            ->get();

        // Calculate Scorecards
        $scorecards = [];
        foreach ($jenjangs as $jenjang) {
            $scorecards[$jenjang->nama] = $data->filter(function ($item) use ($jenjang) {
                return $item->sekolah->jenjang_pendidikan_id == $jenjang->id;
            })->count();
        }
        $scorecards['Total'] = $data->count();

        return view('asesmen.detail', compact('siklus', 'allSiklus', 'wilayah', 'jenjangs', 'data', 'scorecards'));
    }
}
