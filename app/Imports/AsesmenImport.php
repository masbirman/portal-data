<?php

namespace App\Imports;

use App\Models\JenjangPendidikan;
use App\Models\PelaksanaanAsesmen;
use App\Models\Sekolah;
use App\Models\Wilayah;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class AsesmenImport implements ToCollection, WithHeadingRow
{
    protected $siklusAsesmenId;

    public function __construct($siklusAsesmenId)
    {
        $this->siklusAsesmenId = $siklusAsesmenId;
    }

    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // 1. Find or Create Wilayah
            // Assuming column header is 'kota_kabupaten'
            $wilayahName = $row['kota_kabupaten'] ?? null;
            if (!$wilayahName) continue; // Skip if no wilayah

            $wilayah = Wilayah::firstOrCreate(['nama' => $wilayahName]);

            // 2. Find or Create Jenjang Pendidikan
            // Assuming column header is 'jenjang'
            $jenjangName = $row['jenjang'] ?? null;
            if (!$jenjangName) continue;

            // Try to find existing jenjang by nama or kode
            $jenjang = JenjangPendidikan::where('nama', $jenjangName)
                ->orWhere('kode', $jenjangName)
                ->first();
            
            if (!$jenjang) {
                $jenjang = JenjangPendidikan::create([
                    'kode' => $jenjangName,
                    'nama' => $jenjangName,
                ]);
            }

            // 3. Find or Create Sekolah
            // Assuming column header is 'kode_sekolah' and 'nama_sekolah'
            $kodeSekolah = $row['kode_sekolah'] ?? null;
            $namaSekolah = $row['nama_sekolah'] ?? null;
            
            if (!$kodeSekolah || !$namaSekolah) continue;

            $sekolah = Sekolah::updateOrCreate(
                ['kode_sekolah' => $kodeSekolah],
                [
                    'nama' => $namaSekolah,
                    'wilayah_id' => $wilayah->id,
                    'jenjang_pendidikan_id' => $jenjang->id,
                    'tahun' => [], // Default empty array for tahun
                ]
            );

            // 4. Create Pelaksanaan Asesmen
            PelaksanaanAsesmen::updateOrCreate(
                [
                    'siklus_asesmen_id' => $this->siklusAsesmenId,
                    'sekolah_id' => $sekolah->id,
                ],
                [
                    'jumlah_peserta' => $row['jumlah_peserta'] ?? null,
                    'wilayah_id' => $wilayah->id,
                    'status_pelaksanaan' => $row['status_pelaksanaan'] ?? 'Mandiri',
                    'moda_pelaksanaan' => $row['moda_pelaksanaan'] ?? 'Online',
                    'partisipasi_literasi' => $row['partisipasi_literasi'] ?? 0,
                    'partisipasi_numerasi' => $row['partisipasi_numerasi'] ?? 0,
                    'tempat_pelaksanaan' => $row['tempat_pelaksanaan'] ?? '-',
                    'nama_penanggung_jawab' => $row['nama_penanggung_jawab'] ?? '-',
                    'nama_proktor' => $row['nama_proktor'] ?? '-',
                ]
            );
        }
    }
}
