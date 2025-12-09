<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cleanup duplicate wilayah entries by merging them to canonical names.
     */
    public function up(): void
    {
        // Get all wilayah to find duplicates
        $wilayahs = DB::table('wilayah')->get();
        
        // Build mapping of canonical names to IDs
        $canonicalMap = [];
        $duplicates = [];
        
        foreach ($wilayahs as $wilayah) {
            $normalizedName = $this->normalizeWilayahName($wilayah->nama);
            
            if (!isset($canonicalMap[$normalizedName])) {
                $canonicalMap[$normalizedName] = $wilayah->id;
            } else {
                // This is a duplicate
                $duplicates[$wilayah->id] = $canonicalMap[$normalizedName];
            }
        }
        
        // Merge duplicates
        foreach ($duplicates as $duplicateId => $canonicalId) {
            // Update sekolah
            DB::table('sekolah')
                ->where('wilayah_id', $duplicateId)
                ->update(['wilayah_id' => $canonicalId]);
            
            // Update pelaksanaan_asesmen
            DB::table('pelaksanaan_asesmen')
                ->where('wilayah_id', $duplicateId)
                ->update(['wilayah_id' => $canonicalId]);
            
            // Update user_wilayah if exists
            if (DB::getSchemaBuilder()->hasTable('user_wilayah')) {
                DB::table('user_wilayah')
                    ->where('wilayah_id', $duplicateId)
                    ->update(['wilayah_id' => $canonicalId]);
            }
            
            // Update download_requests if exists
            if (DB::getSchemaBuilder()->hasTable('download_requests')) {
                DB::table('download_requests')
                    ->where('wilayah_id', $duplicateId)
                    ->update(['wilayah_id' => $canonicalId]);
            }
            
            // Delete duplicate wilayah
            DB::table('wilayah')->where('id', $duplicateId)->delete();
        }
    }
    
    /**
     * Normalize wilayah name for comparison
     */
    protected function normalizeWilayahName(string $name): string
    {
        $name = trim($name);
        
        // Remove "Kab." or "Kabupaten" prefix
        $name = preg_replace('/^(Kab\\.?|Kabupaten)\\s*/i', '', $name);
        
        $lowerName = strtolower($name);
        
        // Normalize Toli-Toli variants
        if (preg_match('/toli[\s\-]*toli/i', $lowerName)) {
            return 'toli-toli';
        }
        
        // Normalize Tojo Una-Una variants
        if (preg_match('/tojo[\s\-]*una[\s\-]*una/i', $lowerName)) {
            return 'tojo una-una';
        }
        
        return strtolower(trim($name));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse - data has been merged
    }
};
