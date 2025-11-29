<?php

namespace App\Imports;

use App\Models\JenjangPendidikan;
use App\Models\Sekolah;
use App\Models\Wilayah;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SekolahImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    protected $siklusAsesmenId;
    protected $wilayahCache = [];
    protected $jenjangCache = [];

    public function __construct($siklusAsesmenId)
    {
        $this->siklusAsesmenId = $siklusAsesmenId;
        // Increase time limit to prevent timeout
        set_time_limit(0);
        ini_set('memory_limit', '512M');
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        // Pre-load Wilayah and Jenjang to avoid N+1 queries
        $this->preloadCache();

        foreach ($rows as $row) {
            // 1. Find or Create Wilayah
            $wilayahName = trim($row['kota_kabupaten'] ?? '');
            if (!$wilayahName) continue;

            $wilayahId = $this->getWilayahId($wilayahName);

            // 2. Find or Create Jenjang Pendidikan
            $jenjangName = $row['jenjang'] ?? null;
            if (!$jenjangName) continue;

            $jenjangId = $this->getJenjangId($jenjangName);

            // 3. Find or Create Sekolah
            $kodeSekolah = $row['kode_sekolah'] ?? null;
            $namaSekolah = $row['nama_sekolah'] ?? null;
            $statusSekolah = $row['status_sekolah'] ?? null;
            $tahunBaru = (string) ($row['tahun'] ?? null);

            if (!$kodeSekolah || !$namaSekolah) continue;

            $sekolah = Sekolah::firstOrNew(['kode_sekolah' => $kodeSekolah]);

            $tahunExisting = $sekolah->tahun ?? [];

            if ($tahunBaru && !in_array($tahunBaru, $tahunExisting)) {
                $tahunExisting[] = $tahunBaru;
                sort($tahunExisting);
            }

            $sekolah->nama = $namaSekolah;
            $sekolah->status_sekolah = $statusSekolah;
            $sekolah->tahun = $tahunExisting;
            $sekolah->wilayah_id = $wilayahId;
            $sekolah->jenjang_pendidikan_id = $jenjangId;
            $sekolah->save();
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    protected function preloadCache()
    {
        // Cache all Wilayah: normalized lowercase name -> id
        $this->wilayahCache = Wilayah::all()->mapWithKeys(function ($item) {
            // Normalize the wilayah name from database before caching
            $normalizedName = $this->normalizeWilayahName($item->nama);
            return [strtolower($normalizedName) => $item->id];
        })->toArray();

        // Cache all Jenjang: code -> id AND name -> id
        $jenjangs = JenjangPendidikan::all();
        foreach ($jenjangs as $jenjang) {
            $this->jenjangCache['kode'][$jenjang->kode] = $jenjang->id;
            $this->jenjangCache['nama'][$jenjang->nama] = $jenjang->id;
        }
    }

    protected function getWilayahId($name)
    {
        // Normalize wilayah name to prevent duplicates
        $normalizedName = $this->normalizeWilayahName($name);
        $lowerName = strtolower($normalizedName);
        
        if (isset($this->wilayahCache[$lowerName])) {
            return $this->wilayahCache[$lowerName];
        }

        // Create new if not found
        $wilayah = Wilayah::create(['nama' => $normalizedName]);
        $this->wilayahCache[$lowerName] = $wilayah->id;
        return $wilayah->id;
    }

    /**
     * Normalize wilayah name to prevent duplicates
     * - Replace "Kab." with "Kabupaten"
     * - Replace "Kab " with "Kabupaten "
     * - Trim and normalize spaces
     * - Title case
     */
    protected function normalizeWilayahName($name)
    {
        $name = trim($name);
        
        // Replace "Kab." or "Kab " with "Kabupaten "
        $name = preg_replace('/^Kab\.?\s+/i', 'Kabupaten ', $name);
        
        // Normalize multiple spaces to single space
        $name = preg_replace('/\s+/', ' ', $name);
        
        // Fix specific cases
        // "Tolitoli" => "Toli-Toli"
        if (stripos($name, 'Tolitoli') !== false) {
            $name = str_ireplace('Tolitoli', 'Toli-Toli', $name);
        }
        
        // "Tojo Unauna" or "Tojo Una-una" => "Tojo Una-Una"
        if (stripos($name, 'Tojo Una') !== false) {
            $name = preg_replace('/Tojo\s+Una[-\s]?una/i', 'Tojo Una-Una', $name);
        }
        
        // Apply title case
        return Str::title($name);
    }

    protected function getJenjangId($name)
    {
        // Try by code
        if (isset($this->jenjangCache['kode'][$name])) {
            return $this->jenjangCache['kode'][$name];
        }
        // Try by name
        if (isset($this->jenjangCache['nama'][$name])) {
            return $this->jenjangCache['nama'][$name];
        }

        // Create new
        $jenjang = JenjangPendidikan::create([
            'kode' => $name,
            'nama' => $name
        ]);
        
        $this->jenjangCache['kode'][$name] = $jenjang->id;
        $this->jenjangCache['nama'][$name] = $jenjang->id;
        
        return $jenjang->id;
    }
}
