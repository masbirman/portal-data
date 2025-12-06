<?php

namespace App\Services;

use App\Models\Sekolah;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SchoolMatchingService
{
    protected int $threshold;

    public function __construct(int $threshold = 80)
    {
        $this->threshold = $threshold;
    }

    /**
     * Normalize school name for better matching
     */
    public function normalizeName(string $name): string
    {
        $name = Str::upper($name);

        // Standardize common variations
        $replacements = [
            'SMA NEGERI' => 'SMAN',
            'SMP NEGERI' => 'SMPN',
            'SD NEGERI' => 'SDN',
            'SMK NEGERI' => 'SMKN',
            'SD INPRES' => 'SD INPRES',
            'SLB NEGERI' => 'SLBN',
            'SEKOLAH DASAR' => 'SD',
            'SEKOLAH MENENGAH PERTAMA' => 'SMP',
            'SEKOLAH MENENGAH ATAS' => 'SMA',
            'SEKOLAH MENENGAH KEJURUAN' => 'SMK',
        ];

        foreach ($replacements as $search => $replace) {
            $name = str_replace($search, $replace, $name);
        }

        // Remove extra spaces
        $name = preg_replace('/\s+/', ' ', $name);

        return trim($name);
    }

    /**
     * Calculate similarity between two school names
     */
    public function calculateSimilarity(string $name1, string $name2): float
    {
        $normalized1 = $this->normalizeName($name1);
        $normalized2 = $this->normalizeName($name2);

        // Try exact match first
        if ($normalized1 === $normalized2) {
            return 100.0;
        }

        // Use similar_text for percentage
        similar_text($normalized1, $normalized2, $percent);

        return $percent;
    }


    /**
     * Find best match for a school name in the database
     */
    public function findMatch(string $scrapedName, int $wilayahId, ?int $jenjangId = null): ?array
    {
        $query = Sekolah::where('wilayah_id', $wilayahId);

        if ($jenjangId) {
            $query->where('jenjang_pendidikan_id', $jenjangId);
        }

        $schools = $query->get();

        $bestMatch = null;
        $bestScore = 0;

        foreach ($schools as $school) {
            $score = $this->calculateSimilarity($scrapedName, $school->nama);

            if ($score > $bestScore && $score >= $this->threshold) {
                $bestScore = $score;
                $bestMatch = [
                    'school' => $school,
                    'score' => $score,
                ];
            }
        }

        return $bestMatch;
    }

    /**
     * Map bentuk_pendidikan from scraping to jenjang_pendidikan_id
     */
    public function mapJenjangId(string $bentukPendidikan): ?int
    {
        $mapping = [
            'SD' => 4,
            'SMP' => 3,
            'SMA' => 1,
            'SMK' => 2,
            'SDLB' => 7,
            'SMPLB' => 6,
            'SMALB' => 5,
            'SLB' => null, // Need to determine from name
            'PKBM' => null, // Could be Paket A (10), B (9), or C (8)
        ];

        return $mapping[$bentukPendidikan] ?? null;
    }

    /**
     * Map status_sekolah from scraping to database format
     */
    public function mapStatus(string $status): string
    {
        return $status === 'NEGERI' ? 'Negeri' : 'Swasta';
    }

    /**
     * Get threshold
     */
    public function getThreshold(): int
    {
        return $this->threshold;
    }

    /**
     * Set threshold
     */
    public function setThreshold(int $threshold): self
    {
        $this->threshold = $threshold;
        return $this;
    }
}
