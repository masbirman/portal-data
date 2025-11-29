

<?php $__env->startSection('title', 'Asesmen Nasional ' . $tahun . ' - Portal Data AN-TKA Disdik Sulteng'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Asesmen Nasional Tahun <?php echo e($tahun); ?></h1>
                <p class="text-gray-600">Jumlah Satuan Pendidikan Pelaksanaan Asesmen Nasional Tahun <?php echo e($tahun); ?></p>
            </div>
        </div>
    </div>

    <!-- Stats Header -->
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('public.asesmen-stats-header', ['tahun' => $tahun]);

$__html = app('livewire')->mount($__name, $__params, 'lw-112259814-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

    <!-- Info Update -->
    <div class="flex items-center justify-center mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2 flex items-center space-x-2">
            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
            </svg>
            <span class="text-sm text-blue-800">
                <strong>Update:</strong> <?php echo e(now()->locale('id')->translatedFormat('F Y')); ?> (data terkini)
            </span>
        </div>
    </div>

    <!-- Section Title -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Data Satuan Pendidikan Kota/Kabupaten Per Jenjang</h2>
        <p class="text-gray-600">Klik pada baris untuk melihat detail per sekolah</p>
    </div>

    <!-- Wilayah Aggregate Table -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('public.wilayah-aggregate-table', ['tahun' => $tahun]);

$__html = app('livewire')->mount($__name, $__params, 'lw-112259814-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>

    <!-- Footer Info -->
    
<?php $__env->stopSection(); ?>


<?php echo $__env->make('public.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/public/asesmen-nasional.blade.php ENDPATH**/ ?>