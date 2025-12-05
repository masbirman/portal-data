<?php

namespace App\Livewire;

use App\Models\JenjangPendidikan;
use App\Models\Sekolah;
use App\Models\Wilayah;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class SchoolDirectory extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $wilayahId = null;
    public ?int $jenjangId = null;
    public ?string $status = null;
    public int $perPage = 12;

    protected $queryString = [
        'search' => ['except' => ''],
        'wilayahId' => ['except' => null],
        'jenjangId' => ['except' => null],
        'status' => ['except' => null],
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

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function getSchoolsProperty(): LengthAwarePaginator
    {
        return $this->buildSchoolsQuery()->paginate($this->perPage);
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

        // Search filter - by name or kode_sekolah
        if (!empty($this->search)) {
            $searchTerm = $this->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%')
                  ->orWhere('kode_sekolah', 'like', '%' . $searchTerm . '%');
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

        return $query->orderBy('nama');
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->wilayahId = null;
        $this->jenjangId = null;
        $this->status = null;
        $this->resetPage();
    }

    public function render(): View
    {
        $schools = $this->schools;

        return view('livewire.school-directory', [
            'schools' => $schools,
            'wilayahOptions' => Wilayah::withoutGlobalScopes()->orderBy('nama')->pluck('nama', 'id'),
            'jenjangOptions' => JenjangPendidikan::pluck('nama', 'id'),
            'totalSchools' => $schools->total(),
        ]);
    }
}
