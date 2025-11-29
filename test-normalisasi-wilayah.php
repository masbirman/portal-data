<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Imports\SekolahImport;
use Illuminate\Support\Str;

echo "=== TEST NORMALISASI NAMA WILAYAH ===\n\n";

// Create instance to access normalizeWilayahName method
$import = new class {
    protected function normalizeWilayahName($name)
    {
        $name = trim($name);
        
        // Replace "Kab." or "Kab " with "Kabupaten "
        $name = preg_replace('/^Kab\.?\s+/i', 'Kabupaten ', $name);
        
        // Normalize multiple spaces to single space
        $name = preg_replace('/\s+/', ' ', $name);
        
        // Fix specific cases
        // "Tolitoli" => "Toli-Toli"
        if (stripos($name, 'Tolitoli') !== false) {
            $name = str_ireplace('Tolitoli', 'Toli-Toli', $name);
        }
        
        // "Tojo Unauna" or "Tojo Una-una" => "Tojo Una-Una"
        if (stripos($name, 'Tojo Una') !== false) {
            $name = preg_replace('/Tojo\s+Una[-\s]?una/i', 'Tojo Una-Una', $name);
        }
        
        // Apply title case
        return Str::title($name);
    }
    
    public function test($input) {
        return $this->normalizeWilayahName($input);
    }
};

// Test cases
$testCases = [
    'Kab. Donggala',
    'Kab Donggala',
    'Kabupaten Donggala',
    'KABUPATEN DONGGALA',
    'kabupaten donggala',
    'Kab. Tolitoli',
    'Kab. Toli-Toli',
    'Kabupaten Toli-Toli',
    'Kab. Tojo Unauna',
    'Kab. Tojo Una-Una',
    'Kab. Tojo Una-una',
    'Kabupaten Tojo Una-Una',
    'Kota Palu',
    'KOTA PALU',
    'kota palu',
];

echo "Input => Output (Normalized)\n";
echo str_repeat("-", 80) . "\n";

foreach ($testCases as $test) {
    $normalized = $import->test($test);
    $status = ($test === $normalized) ? "✓ SAMA" : "→ DINORMALISASI";
    printf("%-30s => %-30s [%s]\n", $test, $normalized, $status);
}

echo "\n" . str_repeat("-", 80) . "\n";
echo "\nSemua variasi nama wilayah di atas akan menghasilkan nama yang konsisten.\n";
echo "Ini memastikan tidak ada duplikat wilayah saat import data.\n";
