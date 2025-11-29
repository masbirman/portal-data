<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jenjang => $jumlah): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg shadow-lg p-4 text-white text-center transform hover:scale-105 transition-transform duration-200">
            <h3 class="text-sm font-semibold mb-1 uppercase tracking-wide opacity-90"><?php echo e($jenjang); ?></h3>
            <p class="text-3xl font-bold"><?php echo e($jumlah); ?></p>
            <p class="text-xs mt-1 opacity-75">Satuan Pendidikan</p>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH /var/www/resources/views/livewire/public/asesmen-stats-header.blade.php ENDPATH**/ ?>