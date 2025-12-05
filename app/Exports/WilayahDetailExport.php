<?php

namespace App\Exports;

use App\Models\Sekolah;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WilayahDetailExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $tahun;
    protected $wilayahId;
    protected $jenjangIdFilter;
    protected $search;

    public function __construct($tahun, $wilayahId, $jenjangIdFilter, $search)
    {
        $this->tahun = $tahun;
        $this->wilayahId = $wilayahId;
        $this->jenjangIdFilter = $jenjangIdFilter;
        $this->search = $search;
    }

    public function query()
    {
        $query = Sekolah::query()
            ->with(['jenjangPendidikan', 'wilayah'])
            ->with(['pelaksanaanAsesmen' => function ($q) {
                $q->whereHas('siklusAsesmen', function ($q2) {
                    $q2->where('tahun', $this->tahun);
                });
            }])
            ->whereJsonContains('tahun', (string) $this->tahun)
            ->where('wilayah_id', $this->wilayahId);

        if ($this->jenjangIdFilter !== 'all') {
            $query->where('jenjang_pendidikan_id', $this->jenjangIdFilter);
        }

        if ($this->search) {
            $query->where('nama', 'like', '%' . $this->search . '%');
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Nama Sekolah',
            'Kota/Kabupaten',
            'Status Sekolah',
            'Jumlah Peserta',
            'Status Pelaksanaan',
            'Moda Pelaksanaan',
            'Partisipasi Literasi (%)',
            'Partisipasi Numerasi (%)',
            'Tempat Pelaksanaan',
            'Penanggung Jawab',
            'Proktor',
            'Keterangan',
        ];
    }

    public function map($sekolah): array
    {
        $asesmen = $sekolah->pelaksanaanAsesmen->first();

        return [
            $sekolah->nama,
            $sekolah->wilayah->nama ?? '-',
            $sekolah->status_sekolah ?? '-',
            $asesmen->jumlah_peserta ?? '-',
            $asesmen->status_pelaksanaan ?? '-',
            $asesmen->moda_pelaksanaan ?? '-',
            $asesmen ? ((float)$asesmen->partisipasi_literasi == 0 ? '0' : ($asesmen->partisipasi_literasi == 100 ? '100' : number_format($asesmen->partisipasi_literasi, 2))) : '-',
            $asesmen ? ((float)$asesmen->partisipasi_numerasi == 0 ? '0' : ($asesmen->partisipasi_numerasi == 100 ? '100' : number_format($asesmen->partisipasi_numerasi, 2))) : '-',
            $asesmen->tempat_pelaksanaan ?? '-',
            $asesmen->nama_penanggung_jawab ?? '-',
            $asesmen->nama_proktor ?? '-',
            $asesmen->keterangan ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
