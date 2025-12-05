<?php

namespace Tests\Unit\Services;

use App\Models\JenjangPendidikan;
use App\Models\PelaksanaanAsesmen;
use App\Models\Sekolah;
use App\Models\SiklusAsesmen;
use App\Models\Wilayah;
use App\Services\SchoolStatisticsService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Property-based tests for SchoolStatisticsService
 * 
 * These tests verify the correctness properties defined in the design document
 * by generating random test data and verifying properties hold across all inputs.
 */
class SchoolStatisticsServiceTest extends TestCase
{
    use DatabaseTransactions;

    private SchoolStatisticsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SchoolStatisticsService();
    }

    /**
     * **Feature: school-directory, Property 7: Statistics calculation - Total peserta**
     * 
     * *For any* school, the displayed total_peserta SHALL equal the sum of 
     * jumlah_peserta from all related pelaksanaan_asesmen records.
     * 
     * **Validates: Requirements 4.2**
     */
    #[Test]
    public function property_7_total_peserta_equals_sum_of_assessment_participants(): void
    {
        // Run 100 iterations with random data
        for ($i = 0; $i < 100; $i++) {
            // Generate random test data
            $wilayah = Wilayah::factory()->create();
            $jenjang = JenjangPendidikan::factory()->create();
            $siklus = SiklusAsesmen::factory()->create();
            
            $sekolah = Sekolah::factory()->create([
                'wilayah_id' => $wilayah->id,
                'jenjang_pendidikan_id' => $jenjang->id,
            ]);

            // Generate random number of assessments (0-5)
            $assessmentCount = fake()->numberBetween(0, 5);
            $expectedTotal = 0;

            for ($j = 0; $j < $assessmentCount; $j++) {
                $jumlahPeserta = fake()->numberBetween(10, 500);
                $expectedTotal += $jumlahPeserta;

                PelaksanaanAsesmen::factory()->create([
                    'sekolah_id' => $sekolah->id,
                    'wilayah_id' => $wilayah->id,
                    'siklus_asesmen_id' => $siklus->id,
                    'jumlah_peserta' => $jumlahPeserta,
                ]);
            }

            // Get statistics from service
            $statistics = $this->service->getStatistics($sekolah);

            // Property: total_peserta equals sum of all jumlah_peserta
            $this->assertEquals(
                $expectedTotal,
                $statistics['total_peserta'],
                "Property 7 failed: total_peserta ({$statistics['total_peserta']}) should equal sum of jumlah_peserta ({$expectedTotal})"
            );
        }
    }

    /**
     * **Feature: school-directory, Property 8: Statistics calculation - Averages**
     * 
     * *For any* school with assessment data, the displayed avg_literasi and avg_numerasi 
     * SHALL equal the arithmetic mean of partisipasi_literasi and partisipasi_numerasi 
     * from all related pelaksanaan_asesmen records.
     * 
     * **Validates: Requirements 4.3**
     */
    #[Test]
    public function property_8_averages_equal_arithmetic_mean_of_participation(): void
    {
        // Run 100 iterations with random data
        for ($i = 0; $i < 100; $i++) {
            // Generate random test data
            $wilayah = Wilayah::factory()->create();
            $jenjang = JenjangPendidikan::factory()->create();
            $siklus = SiklusAsesmen::factory()->create();
            
            $sekolah = Sekolah::factory()->create([
                'wilayah_id' => $wilayah->id,
                'jenjang_pendidikan_id' => $jenjang->id,
            ]);

            // Generate at least 1 assessment for meaningful average
            $assessmentCount = fake()->numberBetween(1, 5);
            $literasiValues = [];
            $numerasiValues = [];

            for ($j = 0; $j < $assessmentCount; $j++) {
                $literasi = fake()->randomFloat(2, 0, 100);
                $numerasi = fake()->randomFloat(2, 0, 100);
                $literasiValues[] = $literasi;
                $numerasiValues[] = $numerasi;

                PelaksanaanAsesmen::factory()->create([
                    'sekolah_id' => $sekolah->id,
                    'wilayah_id' => $wilayah->id,
                    'siklus_asesmen_id' => $siklus->id,
                    'partisipasi_literasi' => $literasi,
                    'partisipasi_numerasi' => $numerasi,
                ]);
            }

            // Calculate expected averages
            $expectedAvgLiterasi = round(array_sum($literasiValues) / count($literasiValues), 2);
            $expectedAvgNumerasi = round(array_sum($numerasiValues) / count($numerasiValues), 2);

            // Get statistics from service
            $statistics = $this->service->getStatistics($sekolah);

            // Property: avg_literasi equals arithmetic mean
            $this->assertEquals(
                $expectedAvgLiterasi,
                $statistics['avg_literasi'],
                "Property 8 failed: avg_literasi ({$statistics['avg_literasi']}) should equal mean ({$expectedAvgLiterasi})"
            );

            // Property: avg_numerasi equals arithmetic mean
            $this->assertEquals(
                $expectedAvgNumerasi,
                $statistics['avg_numerasi'],
                "Property 8 failed: avg_numerasi ({$statistics['avg_numerasi']}) should equal mean ({$expectedAvgNumerasi})"
            );
        }
    }

    /**
     * **Feature: school-directory, Property 9: Nearby schools - Same wilayah**
     * 
     * *For any* school, all schools in the "Sekolah Sekitar" list SHALL have 
     * the same wilayah_id as the current school.
     * 
     * **Validates: Requirements 5.1**
     */
    #[Test]
    public function property_9_nearby_schools_have_same_wilayah(): void
    {
        // Run 100 iterations with random data
        for ($i = 0; $i < 100; $i++) {
            // Create multiple wilayahs
            $targetWilayah = Wilayah::factory()->create();
            $otherWilayah = Wilayah::factory()->create();
            $jenjang = JenjangPendidikan::factory()->create();

            // Create target school
            $targetSekolah = Sekolah::factory()->create([
                'wilayah_id' => $targetWilayah->id,
                'jenjang_pendidikan_id' => $jenjang->id,
            ]);

            // Create schools in same wilayah
            $sameWilayahCount = fake()->numberBetween(0, 7);
            for ($j = 0; $j < $sameWilayahCount; $j++) {
                Sekolah::factory()->create([
                    'wilayah_id' => $targetWilayah->id,
                    'jenjang_pendidikan_id' => $jenjang->id,
                ]);
            }

            // Create schools in different wilayah
            $differentWilayahCount = fake()->numberBetween(0, 5);
            for ($j = 0; $j < $differentWilayahCount; $j++) {
                Sekolah::factory()->create([
                    'wilayah_id' => $otherWilayah->id,
                    'jenjang_pendidikan_id' => $jenjang->id,
                ]);
            }

            // Get nearby schools
            $nearbySchools = $this->service->getNearbySchools($targetSekolah);

            // Property: All nearby schools have same wilayah_id
            foreach ($nearbySchools as $school) {
                $this->assertEquals(
                    $targetWilayah->id,
                    $school->wilayah_id,
                    "Property 9 failed: Nearby school wilayah_id ({$school->wilayah_id}) should equal target wilayah_id ({$targetWilayah->id})"
                );
            }
        }
    }

    /**
     * **Feature: school-directory, Property 10: Nearby schools - Exclusion and limit**
     * 
     * *For any* school, the "Sekolah Sekitar" list SHALL NOT contain the current school 
     * AND SHALL contain at most 5 schools.
     * 
     * **Validates: Requirements 5.2**
     */
    #[Test]
    public function property_10_nearby_schools_excludes_current_and_respects_limit(): void
    {
        // Run 100 iterations with random data
        for ($i = 0; $i < 100; $i++) {
            // Create wilayah and jenjang
            $wilayah = Wilayah::factory()->create();
            $jenjang = JenjangPendidikan::factory()->create();

            // Create target school
            $targetSekolah = Sekolah::factory()->create([
                'wilayah_id' => $wilayah->id,
                'jenjang_pendidikan_id' => $jenjang->id,
            ]);

            // Create random number of schools in same wilayah (0-10)
            $schoolCount = fake()->numberBetween(0, 10);
            for ($j = 0; $j < $schoolCount; $j++) {
                Sekolah::factory()->create([
                    'wilayah_id' => $wilayah->id,
                    'jenjang_pendidikan_id' => $jenjang->id,
                ]);
            }

            // Get nearby schools
            $nearbySchools = $this->service->getNearbySchools($targetSekolah);

            // Property: Current school is NOT in the list
            $nearbyIds = $nearbySchools->pluck('id')->toArray();
            $this->assertNotContains(
                $targetSekolah->id,
                $nearbyIds,
                "Property 10 failed: Nearby schools should not contain the current school (id: {$targetSekolah->id})"
            );

            // Property: List contains at most 5 schools
            $this->assertLessThanOrEqual(
                5,
                $nearbySchools->count(),
                "Property 10 failed: Nearby schools count ({$nearbySchools->count()}) should be at most 5"
            );
        }
    }
}
