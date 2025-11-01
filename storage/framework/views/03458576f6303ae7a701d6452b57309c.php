<?php
use Webkernel\Aptitudes\I18n\Resources\Views\components\LanguageSelector\LanguageSelector;

$language_selector = new LanguageSelector([]);
$isTopbar = filament()->getDatabaseNotificationsPosition() === \Filament\Enums\DatabaseNotificationsPosition::Topbar;
$isSidebarCollapsibleOnDesktop = filament()->isSidebarCollapsibleOnDesktop();

$iconConfig = [
    'topbar'   => ['size' => 20, 'class' => 'fi-btn-size-lg fi-icon-btn fi-icon-btn-size-lg'],
    'sidebar'  => ['size' => 20, 'class' => 'p-1'],
    'dropdown' => ['size' => 16, 'class' => '']
];

$currentContext = $isTopbar ? 'topbar' : 'sidebar';
$triggerIconSize = $iconConfig[$currentContext]['size'];
$triggerIconClass = $iconConfig[$currentContext]['class'];
$dropdownIconSize = $iconConfig['dropdown']['size'];
?>

<style>
.svg-color-grayed {
    color: var(--gray-400);
}
.dark .svg-color-grayed {
    color: var(--gray-500);
}
</style>

<div
    x-data="{
        selectedLang: '<?php echo e($language_selector->currentCode); ?>',
        isLoading: false,
        changeLang(lang) {
            if (this.isLoading) return;
            this.isLoading = true;
            this.selectedLang = lang;

            // Navigate to language switch route
            window.location.href = '<?php echo e($language_selector->changeUrl); ?>/' + lang;
        }
    }"
    id="<?php echo e($language_selector->getId()); ?>"
>
    <?php if (isset($component)) { $__componentOriginal22ab0dbc2c6619d5954111bba06f01db = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22ab0dbc2c6619d5954111bba06f01db = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.dropdown.index','data' => ['teleport' => true,'placement' => 'bottom-start']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['teleport' => true,'placement' => 'bottom-start']); ?>
         <?php $__env->slot('trigger', null, []); ?> 
            <!--[if BLOCK]><![endif]--><?php if($isTopbar): ?>
                
                <button
                    type="button"
                    class="fi-topbar-database-notifications-btn <?php echo e($triggerIconClass); ?>"
                    aria-label="<?php echo e(lang('Available Languages')); ?>"
                    x-bind:disabled="isLoading"
                >
                    <!--[if BLOCK]><![endif]--><?php if($language_selector->showFlags && $language_selector->currentLanguage): ?>
                        <?php if (isset($component)) { $__componentOriginala84d2a67a9c5d677ec82079b753ef825 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala84d2a67a9c5d677ec82079b753ef825 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'base::components.UIQuery.index','data' => ['path' => 'webkernel/src/Aptitudes/I18n/Resources/Assets/flags/language/'.e($language_selector->currentLanguage->code).'.svg','injectClass' => 'fi-icon fi-size-lg','injectStyle' => 'height: '.e($triggerIconSize).'px; width: '.e($triggerIconSize).'px;','width' => ''.e($triggerIconSize).'','height' => ''.e($triggerIconSize).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('base::UIQuery'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['path' => 'webkernel/src/Aptitudes/I18n/Resources/Assets/flags/language/'.e($language_selector->currentLanguage->code).'.svg','inject-class' => 'fi-icon fi-size-lg','inject-style' => 'height: '.e($triggerIconSize).'px; width: '.e($triggerIconSize).'px;','width' => ''.e($triggerIconSize).'','height' => ''.e($triggerIconSize).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala84d2a67a9c5d677ec82079b753ef825)): ?>
<?php $attributes = $__attributesOriginala84d2a67a9c5d677ec82079b753ef825; ?>
<?php unset($__attributesOriginala84d2a67a9c5d677ec82079b753ef825); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala84d2a67a9c5d677ec82079b753ef825)): ?>
<?php $component = $__componentOriginala84d2a67a9c5d677ec82079b753ef825; ?>
<?php unset($__componentOriginala84d2a67a9c5d677ec82079b753ef825); ?>
<?php endif; ?>
                    <?php else: ?>
                        <?php echo e(svg('heroicon-o-language', 'fi-icon fi-size-lg svg-color-grayed')); ?>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </button>
            <?php else: ?>
                
                <button
                    class="fi-sidebar-database-notifications-btn"
                    x-bind:disabled="isLoading"
                >
                    <span class="<?php echo e($triggerIconClass); ?>">
                        <!--[if BLOCK]><![endif]--><?php if($language_selector->showFlags && $language_selector->currentLanguage): ?>
                            <?php if (isset($component)) { $__componentOriginala84d2a67a9c5d677ec82079b753ef825 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala84d2a67a9c5d677ec82079b753ef825 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'base::components.UIQuery.index','data' => ['path' => 'webkernel/src/Aptitudes/I18n/Resources/Assets/flags/language/'.e($language_selector->currentLanguage->code).'.svg','injectClass' => 'fi-icon fi-size-lg','injectStyle' => 'height: '.e($triggerIconSize).'px; width: '.e($triggerIconSize).'px;','width' => ''.e($triggerIconSize).'','height' => ''.e($triggerIconSize).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('base::UIQuery'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['path' => 'webkernel/src/Aptitudes/I18n/Resources/Assets/flags/language/'.e($language_selector->currentLanguage->code).'.svg','inject-class' => 'fi-icon fi-size-lg','inject-style' => 'height: '.e($triggerIconSize).'px; width: '.e($triggerIconSize).'px;','width' => ''.e($triggerIconSize).'','height' => ''.e($triggerIconSize).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala84d2a67a9c5d677ec82079b753ef825)): ?>
<?php $attributes = $__attributesOriginala84d2a67a9c5d677ec82079b753ef825; ?>
<?php unset($__attributesOriginala84d2a67a9c5d677ec82079b753ef825); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala84d2a67a9c5d677ec82079b753ef825)): ?>
<?php $component = $__componentOriginala84d2a67a9c5d677ec82079b753ef825; ?>
<?php unset($__componentOriginala84d2a67a9c5d677ec82079b753ef825); ?>
<?php endif; ?>
                        <?php else: ?>
                            <?php echo e(svg('heroicon-o-language', 'fi-icon fi-size-lg svg-color-grayed')); ?>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </span>
                    <span
                        <?php if($isSidebarCollapsibleOnDesktop): ?>
                            x-show="$store.sidebar.isOpen"
                            x-transition:enter="fi-transition-enter"
                            x-transition:enter-start="fi-transition-enter-start"
                            x-transition:enter-end="fi-transition-enter-end"
                        <?php endif; ?>
                        class="fi-sidebar-database-notifications-btn-label"
                    >
                        <?php echo e(lang('Available Languages')); ?>

                    </span>
                </button>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
         <?php $__env->endSlot(); ?>

        <?php if (isset($component)) { $__componentOriginal7a83b62094aac4ed8d85f403cf23f250 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7a83b62094aac4ed8d85f403cf23f250 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.dropdown.header','data' => ['class' => 'font-semibold text-gray-900 dark:text-gray-100','icon' => 'heroicon-c-language']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::dropdown.header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'font-semibold text-gray-900 dark:text-gray-100','icon' => 'heroicon-c-language']); ?>
            <?php echo e(lang('Available Languages')); ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7a83b62094aac4ed8d85f403cf23f250)): ?>
<?php $attributes = $__attributesOriginal7a83b62094aac4ed8d85f403cf23f250; ?>
<?php unset($__attributesOriginal7a83b62094aac4ed8d85f403cf23f250); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7a83b62094aac4ed8d85f403cf23f250)): ?>
<?php $component = $__componentOriginal7a83b62094aac4ed8d85f403cf23f250; ?>
<?php unset($__componentOriginal7a83b62094aac4ed8d85f403cf23f250); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal66687bf0670b9e16f61e667468dc8983 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal66687bf0670b9e16f61e667468dc8983 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.dropdown.list.index','data' => ['class' => 'w-40 max-h-60 overflow-y-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::dropdown.list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-40 max-h-60 overflow-y-auto']); ?>
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $language_selector->languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a
                    href="javascript:void(0)"
                    @click.prevent="changeLang('<?php echo e($language->code); ?>')"
                    wire:key="language-<?php echo e($language->code); ?>"
                    class="fi-dropdown-list-item cursor-pointer whitespace-nowrap flex items-center gap-2 px-3 py-2 rounded-md transition-colors hover:bg-gray-100 dark:hover:bg-gray-700"
                    :class="{
                        'bg-gray-100 dark:bg-gray-700': selectedLang === '<?php echo e($language->code); ?>',
                        'opacity-50 pointer-events-none': isLoading
                    }"
                >
                    <!--[if BLOCK]><![endif]--><?php if($language_selector->showFlags): ?>
                        <?php if (isset($component)) { $__componentOriginala84d2a67a9c5d677ec82079b753ef825 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala84d2a67a9c5d677ec82079b753ef825 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'base::components.UIQuery.index','data' => ['path' => 'webkernel/src/Aptitudes/I18n/Resources/Assets/flags/language/'.e($language->code).'.svg','injectClass' => 'fi-icon','injectStyle' => 'height: '.e($dropdownIconSize).'px; width: '.e($dropdownIconSize).'px;','width' => ''.e($dropdownIconSize).'','height' => ''.e($dropdownIconSize).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('base::UIQuery'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['path' => 'webkernel/src/Aptitudes/I18n/Resources/Assets/flags/language/'.e($language->code).'.svg','inject-class' => 'fi-icon','inject-style' => 'height: '.e($dropdownIconSize).'px; width: '.e($dropdownIconSize).'px;','width' => ''.e($dropdownIconSize).'','height' => ''.e($dropdownIconSize).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala84d2a67a9c5d677ec82079b753ef825)): ?>
<?php $attributes = $__attributesOriginala84d2a67a9c5d677ec82079b753ef825; ?>
<?php unset($__attributesOriginala84d2a67a9c5d677ec82079b753ef825); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala84d2a67a9c5d677ec82079b753ef825)): ?>
<?php $component = $__componentOriginala84d2a67a9c5d677ec82079b753ef825; ?>
<?php unset($__componentOriginala84d2a67a9c5d677ec82079b753ef825); ?>
<?php endif; ?>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <span class="fi-dropdown-list-item-label flex-1">
                        <?php echo e($language->getDisplayName()); ?>

                    </span>
                    <span x-show="selectedLang === '<?php echo e($language->code); ?>'">
                        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['name' => 'heroicon-m-check','class' => 'h-4 w-4 text-primary-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'heroicon-m-check','class' => 'h-4 w-4 text-primary-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                    </span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="fi-dropdown-list-item text-gray-500 text-sm px-3 py-2">
                    <?php echo e(lang('No Available Languages')); ?>

                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal66687bf0670b9e16f61e667468dc8983)): ?>
<?php $attributes = $__attributesOriginal66687bf0670b9e16f61e667468dc8983; ?>
<?php unset($__attributesOriginal66687bf0670b9e16f61e667468dc8983); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal66687bf0670b9e16f61e667468dc8983)): ?>
<?php $component = $__componentOriginal66687bf0670b9e16f61e667468dc8983; ?>
<?php unset($__componentOriginal66687bf0670b9e16f61e667468dc8983); ?>
<?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22ab0dbc2c6619d5954111bba06f01db)): ?>
<?php $attributes = $__attributesOriginal22ab0dbc2c6619d5954111bba06f01db; ?>
<?php unset($__attributesOriginal22ab0dbc2c6619d5954111bba06f01db); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22ab0dbc2c6619d5954111bba06f01db)): ?>
<?php $component = $__componentOriginal22ab0dbc2c6619d5954111bba06f01db; ?>
<?php unset($__componentOriginal22ab0dbc2c6619d5954111bba06f01db); ?>
<?php endif; ?>
</div>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/I18n/Resources/Views/components/LanguageSelector/index.blade.php ENDPATH**/ ?>