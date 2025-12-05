<?php

namespace App\Services;

use App\Models\Sekolah;
use Illuminate\Support\Collection;

class SchoolStatisticsService
{
    /**
     * Get aggregated statistics for a school.
     *
     * @param Sekolah $sekolah
     * @return array{total_peserta: int, avg_literasi: float, avg_numerasi: float, total_asesmen: int}
     */
    public function getStatistics(Sekolah $sekolah): array
    {
        $assessments = $sekolah->pelaksanaanAsesmen()->get();

        if ($assessments->isEmpty()) {
            return [
                'total_peserta' => 0,
                'avg_literasi' => 0.0,
                'avg_numerasi' => 0.0,
                'total_asesmen' => 0,
            ];
        }

        $totalPeserta = $assessments->sum('jumlah_peserta');
        $avgLiterasi = $assessments->avg('partisipasi_literasi') ?? 0.0;
        $avgNumerasi = $assessments->avg('partisipasi_numerasi') ?? 0.0;

        return [
            'total_peserta' => (int) $totalPeserta,
            'avg_literasi' => round((float) $avgLiterasi, 2),
            'avg_numerasi' => round((float) $avgNumerasi, 2),
            'total_asesmen' => $assessments->count(),
        ];
    }

    /**
     * Get assessment history grouped by siklus for a school.
     *
     * @param Sekolah $sekolah
     * @return Collection
     */
    public function getAssessmentHistory(Sekolah $sekolah): Collection
    {
        return $sekolah->pelaksanaanAsesmen()
            ->with('siklusAsesmen')
            ->get()
            ->map(function ($assessment) {
                return [
                    'siklus' => $assessment->siklusAsesmen?->nama ?? 'Unknown',
                    'jumlah_peserta' => (int) $assessment->jumlah_peserta,
                    'partisipasi_literasi' => (float) $assessment->partisipasi_literasi,
                    'partisipasi_numerasi' => (float) $assessment->partisipasi_numerasi,
                ];
            });
    }

    /**
     * Get nearby schools in the same wilayah.
     *
     * @param Sekolah $sekolah
     * @param int $limit
     * @return Collection
     */
    public function getNearbySchools(Sekolah $sekolah, int $limit = 5): Collection
    {
        return Sekolah::withoutGlobalScopes()
            ->where('wilayah_id', $sekolah->wilayah_id)
            ->where('id', '!=', $sekolah->id)
            ->with([
                'wilayah' => fn($q) => $q->withoutGlobalScopes(),
                'jenjangPendidikan'
            ])
            ->limit($limit)
            ->get();
    }
}
