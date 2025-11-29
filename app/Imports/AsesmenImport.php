<?php

namespace App\Imports;

use App\Models\JenjangPendidikan;
use App\Models\PelaksanaanAsesmen;
use App\Models\Sekolah;
use App\Models\Wilayah;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

use Maatwebsite\Excel\Concerns\WithChunkReading;

class AsesmenImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    protected $siklusAsesmenId;
    protected $wilayahCache = [];
    protected $jenjangCache = [];
    protected $sekolahCache = [];

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
        $this->preloadCache();

        foreach ($rows as $row) {
            // Get Wilayah name
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

            if (!$kodeSekolah || !$namaSekolah) continue;

            $sekolahId = $this->getSekolahId($kodeSekolah, $namaSekolah, $statusSekolah, $wilayahId, $jenjangId);

            // 4. Create Pelaksanaan Asesmen
            PelaksanaanAsesmen::updateOrCreate(
                [
                    'siklus_asesmen_id' => $this->siklusAsesmenId,
                    'sekolah_id' => $sekolahId,
                ],
                [
                    'jumlah_peserta' => $row['jumlah_peserta'] ?? null,
                    'wilayah_id' => $wilayahId,
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

    public function chunkSize(): int
    {
        return 1000;
    }

    protected function preloadCache()
    {
        // Cache Wilayah: normalized lowercase name -> id
        $this->wilayahCache = Wilayah::all()->mapWithKeys(function ($item) {
            // Normalize the wilayah name from database before caching
            $normalizedName = $this->normalizeWilayahName($item->nama);
            return [strtolower($normalizedName) => $item->id];
        })->toArray();

        // Cache Jenjang
        $jenjangs = JenjangPendidikan::all();
        foreach ($jenjangs as $jenjang) {
            $this->jenjangCache['kode'][$jenjang->kode] = $jenjang->id;
            $this->jenjangCache['nama'][$jenjang->nama] = $jenjang->id;
        }

        // Cache Sekolah (only ID and Kode) to save memory
        // We assume kode_sekolah is unique enough for lookup
        $this->sekolahCache = Sekolah::pluck('id', 'kode_sekolah')->toArray();
    }

    protected function getWilayahId($name)
    {
        // Normalize wilayah name to prevent duplicates
        $normalizedName = $this->normalizeWilayahName($name);
        $lowerName = strtolower($normalizedName);
        
        if (isset($this->wilayahCache[$lowerName])) {
            return $this->wilayahCache[$lowerName];
        }

        $wilayah = Wilayah::create(['nama' => $normalizedName]);
        $this->wilayahCache[$lowerName] = $wilayah->id;
        return $wilayah->id;
    }

    /**
     * Normalize wilayah name to prevent duplicates
     * - Remove "Kabupaten" prefix but keep "Kota" for Kota Palu
     * - Normalize "Kab." or "Kab " variations
     * - Trim and normalize spaces
     * - Title case
     */
    protected function normalizeWilayahName($name)
    {
        $name = trim($name);
        
        // Special case: Keep "Kota Palu" as is
        if (stripos($name, 'Palu') !== false) {
            return 'Kota Palu';
        }
        
        // Remove "Kabupaten" prefix (including variations like "Kab." or "Kab ")
        $name = preg_replace('/^(Kabupaten|Kab\.?)\s+/i', '', $name);
        
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
        if (isset($this->jenjangCache['kode'][$name])) {
            return $this->jenjangCache['kode'][$name];
        }
        if (isset($this->jenjangCache['nama'][$name])) {
            return $this->jenjangCache['nama'][$name];
        }

        $jenjang = JenjangPendidikan::create([
            'kode' => $name,
            'nama' => $name
        ]);
        
        $this->jenjangCache['kode'][$name] = $jenjang->id;
        $this->jenjangCache['nama'][$name] = $jenjang->id;
        return $jenjang->id;
    }

    protected function getSekolahId($kode, $nama, $status, $wilayahId, $jenjangId)
    {
        if (isset($this->sekolahCache[$kode])) {
            // Update existing school details if needed (optional, but good for consistency)
            // For performance, we might skip update if not critical, but updateOrCreate does it anyway.
            // Here we just return ID to avoid query if we trust the cache.
            // However, if we need to update name/status, we should do it.
            // Let's stick to updateOrCreate logic but optimized.
            
            // Actually, to keep it simple and fast, let's just return the ID if exists.
            // If the user wants to update school names, they should use SekolahImport.
            // But AsesmenImport might also update school data. 
            // Let's do a lightweight update or just return ID.
            // The original code used updateOrCreate.
            
            // To be safe and match original behavior:
            $sekolah = Sekolah::find($this->sekolahCache[$kode]);
            if ($sekolah) {
                $sekolah->update([
                    'nama' => $nama,
                    'status_sekolah' => $status,
                    'wilayah_id' => $wilayahId,
                    'jenjang_pendidikan_id' => $jenjangId,
                ]);
                return $sekolah->id;
            }
        }

        // Create new
        $sekolah = Sekolah::create([
            'kode_sekolah' => $kode,
            'nama' => $nama,
            'status_sekolah' => $status,
            'wilayah_id' => $wilayahId,
            'jenjang_pendidikan_id' => $jenjangId,
            'tahun' => [],
        ]);

        $this->sekolahCache[$kode] = $sekolah->id;
        return $sekolah->id;
    }
}
