<?php

namespace App\Imports;

use App\Models\JenjangPendidikan;
use App\Models\Sekolah;
use App\Models\Wilayah;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SekolahImport implements ToCollection, WithHeadingRow
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
            $wilayahName = $row['kota_kabupaten'] ?? null;
            if (!$wilayahName) continue; // Skip if no wilayah

            $wilayah = Wilayah::firstOrCreate(['nama' => $wilayahName]);

            // 2. Find or Create Jenjang Pendidikan
            $jenjangName = $row['jenjang'] ?? null;
            if (!$jenjangName) continue;

            $jenjang = JenjangPendidikan::firstOrCreate(
                ['kode' => $jenjangName],
                ['nama' => $jenjangName] // Default nama to kode if new
            );

            // 3. Find or Create Sekolah
            $kodeSekolah = $row['kode_sekolah'] ?? null;
            $namaSekolah = $row['nama_sekolah'] ?? null;
            $tahunBaru = (string) ($row['tahun'] ?? null);
            
            if (!$kodeSekolah || !$namaSekolah) continue;

            $sekolah = Sekolah::firstOrNew(['kode_sekolah' => $kodeSekolah]);
            
            // Ambil tahun yang sudah ada (array), atau array kosong jika baru
            $tahunExisting = $sekolah->tahun ?? [];
            
            // Jika tahun baru tidak kosong dan belum ada di array, tambahkan
            if ($tahunBaru && !in_array($tahunBaru, $tahunExisting)) {
                $tahunExisting[] = $tahunBaru;
                // Sort tahun agar rapi (opsional)
                sort($tahunExisting);
            }

            $sekolah->nama = $namaSekolah;
            $sekolah->tahun = $tahunExisting; // Simpan sebagai array
            $sekolah->wilayah_id = $wilayah->id;
            $sekolah->jenjang_pendidikan_id = $jenjang->id;
            $sekolah->save();
        }
    }
}
