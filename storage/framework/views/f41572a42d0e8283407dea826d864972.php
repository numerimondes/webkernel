<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'inFooter' => false,
    'fontSize' => '98%',
    'type_user_datetime' => 'icon_time',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'inFooter' => false,
    'fontSize' => '98%',
    'type_user_datetime' => 'icon_time',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
$userTimezone = CurrentUserTimezoneName();
$userTimezoneDisplay = CurrentUserTimezone();
$modes = [
    'icon_only' => ['date' => false, 'time' => false, 'icon' => true, 'timezone' => false],
    'icon_time' => ['date' => false, 'time' => true, 'icon' => true, 'timezone' => false],
    'time_only' => ['date' => false, 'time' => true, 'icon' => false, 'timezone' => false],
    'time_date' => ['date' => true, 'time' => true, 'icon' => false, 'timezone' => false],
    'full' => ['date' => true, 'time' => true, 'icon' => false, 'timezone' => true],
];
$type_user_datetime = is_string($type_user_datetime) ? $type_user_datetime : 'icon_time';
$display = $modes[$type_user_datetime] ?? $modes['icon_time'];
$dateOpt = $display['date'] ? ", dateStyle: 'short'" : '';
$timeOpt = $display['time'] ? ", timeStyle: 'medium'" : '';
$formatOpts = trim($timeOpt . $dateOpt, ', ');
?>
<div x-data="{
    time: new Date().toLocaleString('fr-FR', { timeZone: '<?php echo e($userTimezone); ?>', hour12: false<?php echo e($formatOpts ? ', ' . $formatOpts : ''); ?> }),
    init() {
        setInterval(() => {
            this.time = new Date().toLocaleString('fr-FR', { timeZone: '<?php echo e($userTimezone); ?>', hour12: false<?php echo e($formatOpts ? ', ' . $formatOpts : ''); ?> });
        }, 1000);
    }
}"
class="whitespace-nowrap <?php echo e(!$inFooter ? ($display['icon'] ? 'inline-flex items-center gap-1.5' : 'text-gray-700 dark:text-white force-inter-ltr') : ''); ?>"
style="<?php echo e($inFooter ? '' : 'margin-right:9px; '); ?>font-size: <?php echo e($fontSize); ?>; font-variant-numeric: tabular-nums;"
<?php if($display['icon']): ?>
    x-tooltip="'<?php echo e($userTimezoneDisplay); ?> - ' + time"
<?php elseif(!$display['timezone']): ?>
    x-tooltip="'<?php echo e($userTimezoneDisplay); ?>'"
<?php endif; ?>>
    <!--[if BLOCK]><![endif]--><?php if($display['icon']): ?>
        <?php if (isset($component)) { $__componentOriginalf0029cce6d19fd6d472097ff06a800a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf0029cce6d19fd6d472097ff06a800a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon-button','data' => ['color' => 'gray','icon' => \Filament\Support\Icons\Heroicon::OutlinedClock,'iconSize' => 'lg','badge' => '','class' => 'fi-topbar-database-notifications-btn','style' => 'display: inline-block !important; vertical-align: middle;']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'gray','icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Filament\Support\Icons\Heroicon::OutlinedClock),'icon-size' => 'lg','badge' => '','class' => 'fi-topbar-database-notifications-btn','style' => 'display: inline-block !important; vertical-align: middle;']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf0029cce6d19fd6d472097ff06a800a1)): ?>
<?php $attributes = $__attributesOriginalf0029cce6d19fd6d472097ff06a800a1; ?>
<?php unset($__attributesOriginalf0029cce6d19fd6d472097ff06a800a1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf0029cce6d19fd6d472097ff06a800a1)): ?>
<?php $component = $__componentOriginalf0029cce6d19fd6d472097ff06a800a1; ?>
<?php unset($__componentOriginalf0029cce6d19fd6d472097ff06a800a1); ?>
<?php endif; ?>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><?php if($display['time']): ?>
        <span x-text="time" class="inline-block" style="display: inline-block !important; vertical-align: middle;"></span>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><?php if($display['timezone'] && !$inFooter): ?>
        <span class="inline-block"><?php echo e($userTimezoneDisplay); ?></span>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>

<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/Users/Resources/Views/currentuserdatetime.blade.php ENDPATH**/ ?>