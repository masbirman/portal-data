<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PelaksanaanAsesmenTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['SMA', 'Kota Palu', '40201234', 'SMA Negeri 1 Palu', 'Negeri', 50, 'Mandiri', 'Online', 100, 100, 'Ruang Lab', 'Budi Santoso', 'Ahmad Yani'],
        ];
    }

    public function headings(): array
    {
        return [
            'jenjang',
            'kota_kabupaten',
            'kode_sekolah',
            'nama_sekolah',
            'status_sekolah',
            'jumlah_peserta',
            'status_pelaksanaan',
            'moda_pelaksanaan',
            'partisipasi_literasi',
            'partisipasi_numerasi',
            'tempat_pelaksanaan',
            'nama_penanggung_jawab',
            'nama_proktor',
        ];
    }
}
