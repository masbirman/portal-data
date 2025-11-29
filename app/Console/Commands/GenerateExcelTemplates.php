<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class GenerateExcelTemplates extends Command
{
    protected $signature = 'templates:generate';
    protected $description = 'Generate Excel templates for imports';

    public function handle()
    {
        $this->generateSekolahTemplate();
        $this->generatePelaksanaanAsesmenTemplate();

        $this->info('Templates generated successfully!');
    }

    private function generateSekolahTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['jenjang', 'kota_kabupaten', 'kode_sekolah', 'nama_sekolah', 'status_sekolah', 'tahun'];
        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $headerStyle = $sheet->getStyle('A1:F1');
        $headerStyle->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('4472C4');

        // Example data
        $exampleData = [
            ['SMA', 'Kota Palu', '40201234', 'SMA Negeri 1 Palu', 'Negeri', '2024'],
            ['SMP', 'Donggala', '40301567', 'SMP Swasta Al-Azhar', 'Swasta', '2024'],
        ];
        $sheet->fromArray($exampleData, null, 'A2');

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/templates/template_sekolah.xlsx'));
    }

    private function generatePelaksanaanAsesmenTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
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
            'nama_proktor'
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $headerStyle = $sheet->getStyle('A1:M1');
        $headerStyle->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('4472C4');

        // Example data
        $exampleData = [
            ['SMA', 'Kota Palu', '40201234', 'SMA Negeri 1 Palu', 'Negeri', 50, 'Mandiri', 'Online', 98.5, 95.0, 'SMA Negeri 1 Palu', 'Budi Santoso, S.Pd', 'Andi Wijaya'],
            ['SMP', 'Donggala', '40301567', 'SMP Swasta Al-Azhar', 'Swasta', 45, 'Menumpang', 'Semi Online', 87.3, 82.1, 'SMA Negeri 1 Banawa', 'Siti Aminah, S.Pd', 'Rudi Hartono'],
        ];
        $sheet->fromArray($exampleData, null, 'A2');

        // Auto-size columns
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/templates/template_pelaksanaan_asesmen.xlsx'));
    }
}
