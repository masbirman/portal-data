<?php $__env->startSection('title', 'Dashboard - Portal Data AN-TKA Disdik Sulteng'); ?>

<?php $__env->startSection('content'); ?>
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('public-dashboard.dashboard-stats');

$__html = app('livewire')->mount($__name, $__params, 'lw-188716163-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('public.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/public/dashboard.blade.php ENDPATH**/ ?>