<?php

namespace App\Livewire;

use App\Models\JenjangPendidikan;
use App\Models\Sekolah;
use App\Models\SiklusAsesmen;
use App\Models\Wilayah;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class SchoolDirectory extends Component
{
    use WithPagination;

    // Default jenjang: SMA (id=1)
    private const DEFAULT_JENJANG_ID = 1;

    // Jenis data options
    public const JENIS_DATA = [
        'asesmen_nasional' => 'Asesmen Nasional',
        'tes_kemampuan_akademik' => 'Tes Kemampuan Akademik',
        'survei_lingkungan_belajar' => 'Survei Lingkungan Belajar',
    ];

    public string $search = '';
    public ?int $wilayahId = null;
    public ?int $jenjangId = self::DEFAULT_JENJANG_ID;
    public ?string $status = null;
    public ?int $tahun = null;
    public ?string $jenisData = null;
    public int $perPage = 12;

    protected $queryString = [
        'search' => ['except' => ''],
        'wilayahId' => ['except' => null],
        'jenjangId' => ['except' => self::DEFAULT_JENJANG_ID],
        'status' => ['except' => null],
        'tahun' => ['except' => null],
        'jenisData' => ['except' => null],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingWilayahId(): void
    {
        $this->resetPage();
    }

    public function updatingJenjangId(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingTahun(): void
    {
        $this->resetPage();
    }

    public function updatingJenisData(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function getSchoolsProperty(): LengthAwarePaginator
    {
        return $this->buildSchoolsQuery()->paginate($this->perPage);
    }

    /**
     * Build search pattern that handles abbreviations and multiple spaces
     * Returns array of LIKE patterns to search
     */
    protected function buildSearchPatterns(string $term): array
    {
        $term = strtoupper(trim($term));
        $patterns = [];

        // Map abbreviations to full names
        $expansions = [
            'SMAN' => 'SMA NEGERI',
            'SMPN' => 'SMP NEGERI',
            'SDN' => 'SD NEGERI',
            'SMKN' => 'SMK NEGERI',
            'SLBN' => 'SLB NEGERI',
        ];

        // Expand abbreviations in search term
        $expandedTerm = $term;
        foreach ($expansions as $abbr => $full) {
            if (str_contains($term, $abbr)) {
                $expandedTerm = str_replace($abbr, $full, $term);
                break;
            }
        }

        // Split into words and create pattern with % between words
        // This handles multiple spaces in database
        $words = preg_split('/\s+/', $expandedTerm);
        $pattern = '%' . implode('%', $words) . '%';
        $patterns[] = $pattern;

        // Also add original term pattern if different
        if ($expandedTerm !== $term) {
            $origWords = preg_split('/\s+/', $term);
            $patterns[] = '%' . implode('%', $origWords) . '%';
        }

        return array_unique($patterns);
    }

    /**
     * Build the schools query with all filters applied.
     * This method is public to allow testing of filter logic.
     */
    public function buildSchoolsQuery()
    {
        $query = Sekolah::withoutGlobalScopes()
            ->with([
                'wilayah' => fn($q) => $q->withoutGlobalScopes(),
                'jenjangPendidikan'
            ]);

        // Search filter - by name, kode_sekolah, or npsn
        if (!empty($this->search)) {
            $searchPatterns = $this->buildSearchPatterns($this->search);
            $originalSearch = $this->search;

            $query->where(function ($q) use ($searchPatterns, $originalSearch) {
                // Search by name with patterns (handles abbreviations and multiple spaces)
                foreach ($searchPatterns as $pattern) {
                    $q->orWhere('nama', 'like', $pattern);
                }
                // Also search by kode_sekolah and npsn
                $q->orWhere('kode_sekolah', 'like', '%' . $originalSearch . '%')
                  ->orWhere('npsn', 'like', '%' . $originalSearch . '%');
            });
        }

        // Wilayah filter
        if (!empty($this->wilayahId)) {
            $query->where('wilayah_id', $this->wilayahId);
        }

        // Jenjang filter
        if (!empty($this->jenjangId)) {
            $query->where('jenjang_pendidikan_id', $this->jenjangId);
        }

        // Status filter (Negeri/Swasta)
        if (!empty($this->status)) {
            $query->where('status_sekolah', $this->status);
        }

        // Tahun filter - only show schools that have pelaksanaan_asesmen in selected year
        if (!empty($this->tahun)) {
            $query->whereHas('pelaksanaanAsesmen.siklusAsesmen', function ($q) {
                $q->where('tahun', $this->tahun);
            });
        }

        // Order by jenjang first (SMA=1, SMK=2, SMP=3, SD=4, etc), then by name
        return $query->orderBy('jenjang_pendidikan_id')->orderBy('nama');
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->wilayahId = null;
        $this->jenjangId = self::DEFAULT_JENJANG_ID;
        $this->status = null;
        $this->tahun = null;
        $this->jenisData = null;
        $this->resetPage();
    }

    public function render(): View
    {
        $schools = $this->schools;

        // Get available years from siklus_asesmen
        $tahunOptions = SiklusAsesmen::orderByDesc('tahun')
            ->pluck('tahun')
            ->unique()
            ->mapWithKeys(fn($tahun) => [$tahun => $tahun]);

        // Get total count of all schools (without filters)
        $totalAllSchools = Sekolah::withoutGlobalScopes()->count();

        return view('livewire.school-directory', [
            'schools' => $schools,
            'wilayahOptions' => Wilayah::withoutGlobalScopes()->orderBy('nama')->pluck('nama', 'id'),
            'jenjangOptions' => JenjangPendidikan::pluck('nama', 'id'),
            'tahunOptions' => $tahunOptions,
            'jenisDataOptions' => self::JENIS_DATA,
            'totalSchools' => $schools->total(),
            'totalAllSchools' => $totalAllSchools,
        ]);
    }
}
