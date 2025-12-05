<?php

namespace Tests\Unit\Livewire;

use App\Livewire\SchoolDirectory;
use App\Models\JenjangPendidikan;
use App\Models\Sekolah;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Property-based tests for SchoolDirectory Livewire Component
 * 
 * These tests verify the correctness properties defined in the design document
 * by generating random test data and verifying properties hold across all inputs.
 */
class SchoolDirectoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * **Feature: school-directory, Property 2: Filter correctness - Search**
     * 
     * *For any* search term and school list, all schools returned by the search filter 
     * SHALL have either name or kode_sekolah containing the search term (case-insensitive).
     * 
     * **Validates: Requirements 2.1**
     */
    #[Test]
    public function property_2_search_filter_returns_schools_matching_name_or_kode(): void
    {
        // Run 100 iterations with random data
        for ($i = 0; $i < 100; $i++) {
            // Create test data
            $wilayah = Wilayah::factory()->create();
            $jenjang = JenjangPendidikan::factory()->create();
            
            // Create schools with various names and codes
            $schoolCount = fake()->numberBetween(5, 15);
            for ($j = 0; $j < $schoolCount; $j++) {
                Sekolah::factory()->create([
                    'wilayah_id' => $wilayah->id,
                    'jenjang_pendidikan_id' => $jenjang->id,
                ]);
            }

            // Generate a random search term (3-5 characters)
            $searchTerm = fake()->lexify('???');
            
            // Test the component
            $component = new SchoolDirectory();
            $component->search = $searchTerm;
            
            $results = $component->buildSchoolsQuery()->get();
            
            // Property: All returned schools must have name or kode_sekolah containing search term
            foreach ($results as $school) {
                $nameContains = stripos($school->nama, $searchTerm) !== false;
                $kodeContains = stripos($school->kode_sekolah, $searchTerm) !== false;
                
                $this->assertTrue(
                    $nameContains || $kodeContains,
                    "Property 2 failed: School '{$school->nama}' (kode: {$school->kode_sekolah}) does not contain search term '{$searchTerm}'"
                );
            }
        }
    }

    /**
     * **Feature: school-directory, Property 3: Filter correctness - Wilayah**
     * 
     * *For any* wilayah filter selection, all schools returned SHALL belong to the selected wilayah_id.
     * 
     * **Validates: Requirements 2.2**
     */
    #[Test]
    public function property_3_wilayah_filter_returns_only_schools_from_selected_wilayah(): void
    {
        // Run 100 iterations with random data
        for ($i = 0; $i < 100; $i++) {
            // Create multiple wilayahs
            $wilayah1 = Wilayah::factory()->create();
            $wilayah2 = Wilayah::factory()->create();
            $jenjang = JenjangPendidikan::factory()->create();
            
            // Create schools in different wilayahs
            $schoolsInWilayah1 = fake()->numberBetween(2, 5);
            $schoolsInWilayah2 = fake()->numberBetween(2, 5);
            
            for ($j = 0; $j < $schoolsInWilayah1; $j++) {
                Sekolah::factory()->create([
                    'wilayah_id' => $wilayah1->id,
                    'jenjang_pendidikan_id' => $jenjang->id,
                ]);
            }
            
            for ($j = 0; $j < $schoolsInWilayah2; $j++) {
                Sekolah::factory()->create([
                    'wilayah_id' => $wilayah2->id,
                    'jenjang_pendidikan_id' => $jenjang->id,
                ]);
            }

            // Test filtering by wilayah1
            $component = new SchoolDirectory();
            $component->wilayahId = $wilayah1->id;
            
            $results = $component->buildSchoolsQuery()->get();
            
            // Property: All returned schools must belong to selected wilayah
            foreach ($results as $school) {
                $this->assertEquals(
                    $wilayah1->id,
                    $school->wilayah_id,
                    "Property 3 failed: School wilayah_id ({$school->wilayah_id}) does not match filter ({$wilayah1->id})"
                );
            }
            
            // Verify we got the expected count
            $this->assertEquals(
                $schoolsInWilayah1,
                $results->count(),
                "Property 3 failed: Expected {$schoolsInWilayah1} schools, got {$results->count()}"
            );
        }
    }

    /**
     * **Feature: school-directory, Property 4: Filter correctness - Jenjang**
     * 
     * *For any* jenjang_pendidikan filter selection, all schools returned SHALL have 
     * the selected jenjang_pendidikan_id.
     * 
     * **Validates: Requirements 2.3**
     */
    #[Test]
    public function property_4_jenjang_filter_returns_only_schools_with_selected_jenjang(): void
    {
        // Run 100 iterations with random data
        for ($i = 0; $i < 100; $i++) {
            // Create multiple jenjangs
            $jenjang1 = JenjangPendidikan::factory()->create();
            $jenjang2 = JenjangPendidikan::factory()->create();
            $wilayah = Wilayah::factory()->create();
            
            // Create schools with different jenjangs
            $schoolsWithJenjang1 = fake()->numberBetween(2, 5);
            $schoolsWithJenjang2 = fake()->numberBetween(2, 5);
            
            for ($j = 0; $j < $schoolsWithJenjang1; $j++) {
                Sekolah::factory()->create([
                    'wilayah_id' => $wilayah->id,
                    'jenjang_pendidikan_id' => $jenjang1->id,
                ]);
            }
            
            for ($j = 0; $j < $schoolsWithJenjang2; $j++) {
                Sekolah::factory()->create([
                    'wilayah_id' => $wilayah->id,
                    'jenjang_pendidikan_id' => $jenjang2->id,
                ]);
            }

            // Test filtering by jenjang1
            $component = new SchoolDirectory();
            $component->jenjangId = $jenjang1->id;
            
            $results = $component->buildSchoolsQuery()->get();
            
            // Property: All returned schools must have selected jenjang
            foreach ($results as $school) {
                $this->assertEquals(
                    $jenjang1->id,
                    $school->jenjang_pendidikan_id,
                    "Property 4 failed: School jenjang_pendidikan_id ({$school->jenjang_pendidikan_id}) does not match filter ({$jenjang1->id})"
                );
            }
            
            // Verify we got the expected count
            $this->assertEquals(
                $schoolsWithJenjang1,
                $results->count(),
                "Property 4 failed: Expected {$schoolsWithJenjang1} schools, got {$results->count()}"
            );
        }
    }

    /**
     * **Feature: school-directory, Property 5: Filter correctness - Status**
     * 
     * *For any* status_sekolah filter selection (excluding "Semua"), all schools returned 
     * SHALL have the selected status_sekolah value.
     * 
     * **Validates: Requirements 2.4**
     */
    #[Test]
    public function property_5_status_filter_returns_only_schools_with_selected_status(): void
    {
        // Run 100 iterations with random data
        for ($i = 0; $i < 100; $i++) {
            // Create test data with unique wilayah to isolate from existing data
            $wilayah = Wilayah::factory()->create();
            $jenjang = JenjangPendidikan::factory()->create();
            
            // Create schools with different statuses
            $negeriCount = fake()->numberBetween(2, 5);
            $swastaCount = fake()->numberBetween(2, 5);
            
            for ($j = 0; $j < $negeriCount; $j++) {
                Sekolah::factory()->create([
                    'wilayah_id' => $wilayah->id,
                    'jenjang_pendidikan_id' => $jenjang->id,
                    'status_sekolah' => 'Negeri',
                ]);
            }
            
            for ($j = 0; $j < $swastaCount; $j++) {
                Sekolah::factory()->create([
                    'wilayah_id' => $wilayah->id,
                    'jenjang_pendidikan_id' => $jenjang->id,
                    'status_sekolah' => 'Swasta',
                ]);
            }

            // Test filtering by random status - also filter by wilayah to isolate test data
            $selectedStatus = fake()->randomElement(['Negeri', 'Swasta']);
            $expectedCount = $selectedStatus === 'Negeri' ? $negeriCount : $swastaCount;
            
            $component = new SchoolDirectory();
            $component->status = $selectedStatus;
            $component->wilayahId = $wilayah->id; // Filter by wilayah to isolate test data
            
            $results = $component->buildSchoolsQuery()->get();
            
            // Property: All returned schools must have selected status
            foreach ($results as $school) {
                $this->assertEquals(
                    $selectedStatus,
                    $school->status_sekolah,
                    "Property 5 failed: School status_sekolah ({$school->status_sekolah}) does not match filter ({$selectedStatus})"
                );
            }
            
            // Verify we got the expected count
            $this->assertEquals(
                $expectedCount,
                $results->count(),
                "Property 5 failed: Expected {$expectedCount} schools with status '{$selectedStatus}', got {$results->count()}"
            );
        }
    }

    /**
     * **Feature: school-directory, Property 6: School count matches filtered results**
     * 
     * *For any* combination of filters, the displayed school count SHALL equal 
     * the actual number of schools matching those filters.
     * 
     * **Validates: Requirements 1.5**
     */
    #[Test]
    public function property_6_school_count_matches_filtered_results(): void
    {
        // Run 100 iterations with random data
        for ($i = 0; $i < 100; $i++) {
            // Create multiple wilayahs and jenjangs
            $wilayah1 = Wilayah::factory()->create();
            $wilayah2 = Wilayah::factory()->create();
            $jenjang1 = JenjangPendidikan::factory()->create();
            $jenjang2 = JenjangPendidikan::factory()->create();
            
            // Create schools with various combinations
            $totalSchools = fake()->numberBetween(10, 20);
            for ($j = 0; $j < $totalSchools; $j++) {
                Sekolah::factory()->create([
                    'wilayah_id' => fake()->randomElement([$wilayah1->id, $wilayah2->id]),
                    'jenjang_pendidikan_id' => fake()->randomElement([$jenjang1->id, $jenjang2->id]),
                    'status_sekolah' => fake()->randomElement(['Negeri', 'Swasta']),
                ]);
            }

            // Apply random combination of filters
            $component = new SchoolDirectory();
            
            if (fake()->boolean(50)) {
                $component->wilayahId = fake()->randomElement([$wilayah1->id, $wilayah2->id]);
            }
            
            if (fake()->boolean(50)) {
                $component->jenjangId = fake()->randomElement([$jenjang1->id, $jenjang2->id]);
            }
            
            if (fake()->boolean(50)) {
                $component->status = fake()->randomElement(['Negeri', 'Swasta']);
            }
            
            // Get results and count
            $results = $component->buildSchoolsQuery()->get();
            $count = $component->buildSchoolsQuery()->count();
            
            // Property: Count must equal actual number of results
            $this->assertEquals(
                $results->count(),
                $count,
                "Property 6 failed: Count ({$count}) does not match actual results ({$results->count()})"
            );
        }
    }

    /**
     * **Feature: school-directory, Property 1: Card displays all required fields**
     * 
     * *For any* school in the database, when rendered as a Card_Sekolah, the output 
     * SHALL contain the school's name, kode_sekolah, wilayah name, status_sekolah, 
     * and jenjang_pendidikan name.
     * 
     * **Validates: Requirements 1.2**
     */
    #[Test]
    public function property_1_card_displays_all_required_fields(): void
    {
        // Run 100 iterations with random data
        for ($i = 0; $i < 100; $i++) {
            // Create test data
            $wilayah = Wilayah::factory()->create();
            $jenjang = JenjangPendidikan::factory()->create();
            
            $school = Sekolah::factory()->create([
                'wilayah_id' => $wilayah->id,
                'jenjang_pendidikan_id' => $jenjang->id,
            ]);

            // Test the Livewire component renders with all required fields
            $response = Livewire::test(SchoolDirectory::class)
                ->set('wilayahId', $wilayah->id)
                ->set('jenjangId', $jenjang->id);

            // Property: Card must display all required fields
            $response->assertSee($school->nama);
            $response->assertSee($school->kode_sekolah);
            $response->assertSee($wilayah->nama);
            $response->assertSee($school->status_sekolah);
            $response->assertSee($jenjang->nama);
        }
    }
}
