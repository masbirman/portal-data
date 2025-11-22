<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SekolahTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['SMA', 'Kota Palu', '40201234', 'SMA Negeri 1 Palu', 'Negeri', '2024'],
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
            'tahun',
        ];
    }
}
