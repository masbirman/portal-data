<?php

namespace App\Exports;

use App\Models\PelaksanaanAsesmen;
use App\Models\Sekolah;
use App\Models\SiklusAsesmen;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class DataRequestExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $dataType;
    protected $tahun;
    protected $wilayahId;
    protected $jenjangPendidikanId;

    public function __construct($dataType, $tahun, $wilayahId, $jenjangPendidikanId)
    {
        $this->dataType = $dataType;
        $this->tahun = $tahun;
        $this->wilayahId = $wilayahId;
        $this->jenjangPendidikanId = $jenjangPendidikanId;
    }

    public function collection()
    {
        switch ($this->dataType) {
            case 'asesmen_nasional':
                $siklusAsesmen = SiklusAsesmen::where('tahun', $this->tahun)->first();
                if (!$siklusAsesmen) {
                    return collect([]);
                }

                $query = PelaksanaanAsesmen::where('siklus_asesmen_id', $siklusAsesmen->id);

                // Filter wilayah (null = semua wilayah)
                if ($this->wilayahId != null) {
                    $query->where('wilayah_id', $this->wilayahId);
                }

                // Filter jenjang (null = semua jenjang)
                if ($this->jenjangPendidikanId != null) {
                    $query->whereHas('sekolah', function ($q) {
                        $q->where('jenjang_pendidikan_id', $this->jenjangPendidikanId);
                    });
                }

                return $query->with(['sekolah.jenjangPendidikan', 'wilayah'])->get();

            case 'survei_lingkungan_belajar':
                return collect([]);

            case 'tes_kemampuan_akademik':
                return collect([]);

            default:
                return collect([]);
        }
    }

    public function headings(): array
    {
        switch ($this->dataType) {
            case 'asesmen_nasional':
                return [
                    'Nama Sekolah',
                    'Kode Sekolah',
                    'Jenjang',
                    'Kota/Kabupaten',
                    'Status Sekolah',
                    'Peserta',
                    'Status Pelaksanaan',
                    'Moda Pelaksanaan',
                    'Partisipasi Literasi (%)',
                    'Partisipasi Numerasi (%)',
                    'Tempat Pelaksanaan',
                    'Penanggung Jawab',
                    'Proktor',
                    'Keterangan',
                ];

            case 'survei_lingkungan_belajar':
                return ['NPSN', 'Nama Sekolah', 'Jenjang', 'Wilayah'];

            case 'tes_kemampuan_akademik':
                return ['NPSN', 'Nama Sekolah', 'Jenjang', 'Wilayah'];

            default:
                return [];
        }
    }

    public function map($row): array
    {
        switch ($this->dataType) {
            case 'asesmen_nasional':
                return [
                    $row->sekolah->nama ?? '-',
                    $row->sekolah->kode_sekolah ?? '-',
                    $row->sekolah->jenjangPendidikan->nama ?? '-',
                    $row->wilayah->nama ?? '-',
                    $row->sekolah->status_sekolah ?? '-',
                    $row->jumlah_peserta ?? '-',
                    $row->status_pelaksanaan ?? '-',
                    $row->moda_pelaksanaan ?? '-',
                    $row->partisipasi_literasi ?? '-',
                    $row->partisipasi_numerasi ?? '-',
                    $row->tempat_pelaksanaan ?? '-',
                    $row->nama_penanggung_jawab ?? '-',
                    $row->nama_proktor ?? '-',
                    $row->keterangan ?? '-',
                ];

            case 'survei_lingkungan_belajar':
                return [
                    $row->npsn ?? '-',
                    $row->nama ?? '-',
                    $row->jenjangPendidikan->nama ?? '-',
                    $row->wilayah->nama ?? '-'
                ];

            case 'tes_kemampuan_akademik':
                return [
                    $row->npsn ?? '-',
                    $row->nama ?? '-',
                    $row->jenjangPendidikan->nama ?? '-',
                    $row->wilayah->nama ?? '-'
                ];

            default:
                return [];
        }
    }

    public function title(): string
    {
        $jenjang = $this->jenjangPendidikanId == null ? 'Semua Jenjang' : (\App\Models\JenjangPendidikan::find($this->jenjangPendidikanId)->nama ?? '');
        return ucfirst(str_replace('_', ' ', $this->dataType)) . ' ' . $this->tahun . ' ' . $jenjang;
    }
}
