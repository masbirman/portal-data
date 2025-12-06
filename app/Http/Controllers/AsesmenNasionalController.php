<?php

namespace App\Http\Controllers;

use App\Models\PelaksanaanAsesmen;
use App\Models\Sekolah;
use App\Models\SiklusAsesmen;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsesmenNasionalController extends Controller
{
    /**
     * Tampilkan halaman agregat per tahun
     */
    public function index($tahun)
    {
        // Validasi tahun
        $siklus = SiklusAsesmen::where('tahun', $tahun)->first();

        if (!$siklus) {
            abort(404, 'Data asesmen untuk tahun ' . $tahun . ' tidak ditemukan.');
        }

        return view('public.asesmen-nasional', [
            'tahun' => $tahun,
            'siklus' => $siklus
        ]);
    }

    /**
     * Tampilkan halaman detail wilayah
     */
    public function wilayah($tahun, $wilayahId)
    {
        // Validasi tahun
        $siklus = SiklusAsesmen::where('tahun', $tahun)->first();

        if (!$siklus) {
            abort(404, 'Data asesmen untuk tahun ' . $tahun . ' tidak ditemukan.');
        }

        // Validasi wilayah
        $wilayah = Wilayah::find($wilayahId);

        if (!$wilayah) {
            abort(404, 'Wilayah tidak ditemukan.');
        }

        return view('public.wilayah-detail', [
            'tahun' => $tahun,
            'siklus' => $siklus,
            'wilayah' => $wilayah
        ]);
    }

    /**
     * Tampilkan halaman peta visualisasi
     */
    public function peta($tahun)
    {
        // Validasi tahun
        $siklus = SiklusAsesmen::where('tahun', $tahun)->first();

        if (!$siklus) {
            abort(404, 'Data asesmen untuk tahun ' . $tahun . ' tidak ditemukan.');
        }

        // Ambil data wilayah
        $wilayahList = Wilayah::all();

        // Ambil statistik pelaksanaan asesmen per wilayah (bypass global scope dengan query langsung)
        $pelaksanaanStats = DB::table('pelaksanaan_asesmen')
            ->select(
                'wilayah_id',
                DB::raw('SUM(jumlah_peserta) as total_peserta'),
                DB::raw("SUM(CASE WHEN status_pelaksanaan = 'Mandiri' THEN 1 ELSE 0 END) as status_mandiri"),
                DB::raw("SUM(CASE WHEN status_pelaksanaan = 'Menumpang' THEN 1 ELSE 0 END) as status_menumpang"),
                DB::raw("SUM(CASE WHEN moda_pelaksanaan = 'Online' THEN 1 ELSE 0 END) as moda_online"),
                DB::raw("SUM(CASE WHEN moda_pelaksanaan = 'Semi Online' THEN 1 ELSE 0 END) as moda_semi_online")
            )
            ->where('siklus_asesmen_id', $siklus->id)
            ->groupBy('wilayah_id')
            ->get()
            ->keyBy('wilayah_id');

        // Ambil jumlah sekolah per wilayah untuk tahun ini
        $sekolahCounts = DB::table('sekolah')
            ->select('wilayah_id', DB::raw('COUNT(*) as total_sekolah'))
            ->whereRaw("JSON_CONTAINS(tahun, '\"$tahun\"')")
            ->groupBy('wilayah_id')
            ->get()
            ->keyBy('wilayah_id');

        // Gabungkan data
        $wilayahData = $wilayahList->map(function ($wilayah) use ($pelaksanaanStats, $sekolahCounts) {
            $stats = $pelaksanaanStats->get($wilayah->id);
            $sekolah = $sekolahCounts->get($wilayah->id);

            return [
                'id' => $wilayah->id,
                'nama' => $wilayah->nama,
                'kode_wilayah' => $wilayah->kode_wilayah,
                'logo' => $wilayah->logo,
                'latitude' => $wilayah->latitude,
                'longitude' => $wilayah->longitude,
                'total_sekolah' => $sekolah->total_sekolah ?? 0,
                'total_peserta' => $stats->total_peserta ?? 0,
                // Status Pelaksanaan
                'status_mandiri' => $stats->status_mandiri ?? 0,
                'status_menumpang' => $stats->status_menumpang ?? 0,
                // Moda Pelaksanaan
                'moda_online' => $stats->moda_online ?? 0,
                'moda_semi_online' => $stats->moda_semi_online ?? 0,
            ];
        });

        // Ambil tahun yang tersedia
        $availableYears = SiklusAsesmen::orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();

        return view('public.peta-data', [
            'tahun' => $tahun,
            'siklus' => $siklus,
            'wilayahData' => $wilayahData,
            'availableYears' => $availableYears
        ]);
    }
}
