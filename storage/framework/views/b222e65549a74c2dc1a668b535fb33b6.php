<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'code',
    'message',
    'source' => null,
    'details' => null,
    'trace' => null,
    'showDetails' => false,
    'identifier' => null,
    'errorCode' => null,
    'documentationUrl' => null,
    'originalUrl' => null,
    'previousUrl' => null,
    'actions' => [],
    'showBackButton' => true,
    'showReloadButton' => true,
    'showHomeButton' => true,
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
    'code',
    'message',
    'source' => null,
    'details' => null,
    'trace' => null,
    'showDetails' => false,
    'identifier' => null,
    'errorCode' => null,
    'documentationUrl' => null,
    'originalUrl' => null,
    'previousUrl' => null,
    'actions' => [],
    'showBackButton' => true,
    'showReloadButton' => true,
    'showHomeButton' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div>
    <?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <div style="display: flex; align-items: center; gap: 1.5rem; max-width: 48rem; width: 100%; padding: 0 1rem;">
                
                <div style="flex: 0 0 auto;">
                    <?php echo module_image(
                        'module://media-store/Resources/Assets/logo/numerimondes-builder.svg',
                        'inline',
                        'width: 64px; height: 64px;',
                        ''
                    ); ?>

                </div>

                
                <div style="flex: 1; text-align: left; margin-top: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <h2 style="font-size: 1.875rem; font-weight: 800; margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;">
                            <?php echo e(__('Error')); ?> <?php echo e($code); ?> <?php echo e(__('Occurred')); ?>

                        </h2>
                    </div>
                    <!--[if BLOCK]><![endif]--><?php if($source): ?>
                        <p style="font-family: 'Courier New', monospace; font-size: 0.875rem; margin: 0.5rem 0 0 0; opacity: 0.8; word-break: break-word;">
                            <?php echo e($source); ?>

                        </p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>

            
            <!--[if BLOCK]><![endif]--><?php if($identifier): ?>
                <div style="margin-top: 2rem; max-width: 48rem; width: 100%; padding: 0 1rem;">
                    <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <div style="text-align: center;">
                            <p style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">
                                <?php echo e(__('Reference this identifier when contacting support')); ?>:
                            </p>
                            <div style="display: flex; justify-content: center; margin-bottom: 0.5rem; align-items: center; gap: 0.5rem;">
                                <div
                                    class="error-ref-badge"
                                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background: rgba(59, 130, 246, 0.1); border: 1px solid var(--primary-600); border-radius: 0.5rem; font-weight: 600; font-size: 0.855rem; cursor: pointer; max-width: 100%;"
                                    id="error-ref-<?php echo e($identifier); ?>"
                                    :title="($errorCode ? $errorCode . '-' : '') . $identifier"
                                >
                                    <code style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; max-width: 200px;">
                                        <?php echo e($errorCode ? $errorCode . '-' : ''); ?><?php echo e($identifier); ?>

                                    </code>
                                    <button
                                        type="button"
                                        class="copy-ref-btn"
                                        data-reference="<?php echo e($errorCode ? $errorCode . '-' . $identifier : $identifier); ?>"
                                        style="background: none; border: none; cursor: pointer; padding: 0; display: flex; align-items: center; opacity: 0.7; transition: opacity 0.2s; flex-shrink: 0;"
                                        aria-label="<?php echo e(__('Copy reference')); ?>"
                                    >
                                        <svg style="width: 16px; height: 16px; color: var(--primary-800);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <p style="font-size: 0.75rem; margin: 0; opacity: 0.7;">
                                <?php echo e(__('This has been logged for troubleshooting')); ?>

                            </p>
                        </div>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
                </div>

                <script>
                    document.querySelectorAll('.copy-ref-btn').forEach(button => {
                        button.addEventListener('click', function(e) {
                            e.preventDefault();
                            const reference = this.getAttribute('data-reference');
                            copyToClipboard(reference);
                        });
                    });

                    function copyToClipboard(text) {
                        if (navigator.clipboard && window.isSecureContext) {
                            navigator.clipboard.writeText(text).then(() => {
                                showNotification('success', text);
                            }).catch(() => {
                                fallbackCopy(text);
                            });
                        } else {
                            fallbackCopy(text);
                        }
                    }

                    function fallbackCopy(text) {
                        const textarea = document.createElement('textarea');
                        textarea.value = text;
                        document.body.appendChild(textarea);
                        textarea.select();
                        try {
                            document.execCommand('copy');
                            showNotification('success', text);
                        } catch {
                            showNotification('danger', text);
                        }
                        document.body.removeChild(textarea);
                    }

                    function showNotification(type, reference) {
                        const title = type === 'success' ? '<?php echo e(__('Copied to clipboard')); ?>' : '<?php echo e(__('Copy failed')); ?>';

                        if (window.Livewire) {
                            window.Livewire.dispatch('notificationSent', {
                                notification: {
                                    title: title,
                                    body: reference,
                                    status: type,
                                    duration: 3000,
                                    format: 'filament',
                                }
                            });
                        }
                    }
                </script>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <div style="animation-delay: 0.2s; margin-top: 1.3rem; margin-bottom: 1.3rem; padding: 0 1rem;">
            <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                <div>
                    <p style="font-size: 0.875rem; margin-top: 0; opacity: 0.9; line-height: 1.6;">
                        <?php echo e($message); ?>

                    </p>

                    
                    <!--[if BLOCK]><![endif]--><?php if(!empty($actions)): ?>
                        <div style="margin-top: 1rem; display: flex; flex-wrap: wrap; gap: 0.75rem;">
                            <?php
                                $actionCount = count($actions);
                            ?>

                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $actionType = $action['type'] ?? 'link';
                                    $actionColor = $action['color'] ?? 'primary';
                                    $actionLabel = $action['label'] ?? __('Action');
                                    $actionHref = $action['href'] ?? null;
                                    $fullWidth = $actionCount === 1;
                                ?>

                                <!--[if BLOCK]><![endif]--><?php if($actionType === 'link' && !empty($actionHref)): ?>
                                    <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['href' => $actionHref,'color' => $actionColor,'size' => 'sm','icon' => 'heroicon-o-arrow-top-right-on-square','tag' => 'a','target' => '_blank','style' => $fullWidth ? 'width: 100%;' : '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($actionHref),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($actionColor),'size' => 'sm','icon' => 'heroicon-o-arrow-top-right-on-square','tag' => 'a','target' => '_blank','style' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fullWidth ? 'width: 100%;' : '')]); ?>
                                        <?php echo e($actionLabel); ?>

                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <!--[if BLOCK]><![endif]--><?php if(collect($actions)->where('description', '!=', null)->isNotEmpty()): ?>
                            <div style="margin-top: 1rem; padding: 1rem; background: rgba(59, 130, 246, 0.1); border-left: 4px solid rgb(59, 130, 246); border-radius: 0.5rem;">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <!--[if BLOCK]><![endif]--><?php if(!empty($action['description'])): ?>
                                        <p style="font-size: 0.875rem; margin-bottom: 0.5rem; margin-top: 0;">
                                            <strong><?php echo e($action['label']); ?>:</strong> <?php echo e($action['description']); ?>

                                        </p>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    
                    <!--[if BLOCK]><![endif]--><?php if($documentationUrl): ?>
                        <div style="margin-top: 1.5rem;">
                            <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['href' => $documentationUrl,'color' => 'gray','size' => 'sm','icon' => 'heroicon-o-book-open','outlined' => true,'tag' => 'a','target' => '_blank']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($documentationUrl),'color' => 'gray','size' => 'sm','icon' => 'heroicon-o-book-open','outlined' => true,'tag' => 'a','target' => '_blank']); ?>
                                <?php echo e(__('View Documentation')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    
                    <!--[if BLOCK]><![endif]--><?php if($showDetails && $details): ?>
                        <div style="margin-top: 1.5rem;">
                            <details style="cursor: pointer;">
                                <summary style="font-weight: 600; padding: 0.75rem; background: rgba(59, 130, 246, 0.08); border-radius: 0.5rem; list-style: none; display: flex; align-items: center; gap: 0.5rem; user-select: none;">
                                    <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span style="font-size: 0.875rem;"><?php echo e(__('Technical Details')); ?></span>
                                </summary>

                                <div style="margin-top: 1rem; padding: 1rem; background: rgba(59, 130, 246, 0.08); border-radius: 0.5rem;">
                                    <p style="font-weight: 600; margin: 0 0 0.75rem 0; font-size: 0.875rem;"><?php echo e(__('Details')); ?>:</p>
                                    <code style="font-family: 'Courier New', monospace; font-size: 0.8rem; display: block; word-break: break-word; white-space: pre-wrap; overflow-x: auto;"><?php echo e($details); ?></code>
                                </div>

                                <!--[if BLOCK]><![endif]--><?php if($trace): ?>
                                    <div style="margin-top: 1rem; padding: 1rem; background: rgba(59, 130, 246, 0.08); border-radius: 0.5rem;">
                                        <p style="font-weight: 600; margin: 0 0 0.75rem 0; font-size: 0.875rem;"><?php echo e(__('Stack Trace')); ?>:</p>
                                        <pre style="font-family: 'Courier New', monospace; font-size: 0.75rem; overflow-x: auto; white-space: pre-wrap; word-wrap: break-word; margin: 0;"><?php echo e($trace); ?></pre>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </details>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
        </div>

        
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; max-width: 48rem; width: 100%; padding: 0 1rem; margin-top: 1.5rem; flex-wrap: wrap;">
            <div style="display: flex; gap: 0.5rem;">
                <!--[if BLOCK]><![endif]--><?php if($showBackButton && $previousUrl): ?>
                    <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['href' => $previousUrl,'color' => 'gray','icon' => 'heroicon-o-arrow-left','size' => 'sm','outlined' => true,'tag' => 'a']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($previousUrl),'color' => 'gray','icon' => 'heroicon-o-arrow-left','size' => 'sm','outlined' => true,'tag' => 'a']); ?>
                        <?php echo e(__('Back')); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <!--[if BLOCK]><![endif]--><?php if($showReloadButton && $originalUrl): ?>
                    <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['href' => $originalUrl,'color' => 'success','icon' => 'heroicon-o-arrow-path','size' => 'sm','tag' => 'a']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($originalUrl),'color' => 'success','icon' => 'heroicon-o-arrow-path','size' => 'sm','tag' => 'a']); ?>
                        <?php echo e(__('Reload Origin')); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <!--[if BLOCK]><![endif]--><?php if($showHomeButton): ?>
                    <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['href' => '/','color' => 'primary','icon' => 'heroicon-o-home','size' => 'sm','outlined' => true,'tag' => 'a']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/','color' => 'primary','icon' => 'heroicon-o-home','size' => 'sm','outlined' => true,'tag' => 'a']); ?>
                        <?php echo e(__('Panel Home')); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>

        
        <div style="margin-top: 2rem; padding: 0 1rem; max-width: 24rem; margin-left: auto; margin-right: auto;">
            <?php if (isset($component)) { $__componentOriginal388e1416f496c833c11c2ba7d86d1f07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal388e1416f496c833c11c2ba7d86d1f07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.theme-switcher.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::theme-switcher'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal388e1416f496c833c11c2ba7d86d1f07)): ?>
<?php $attributes = $__attributesOriginal388e1416f496c833c11c2ba7d86d1f07; ?>
<?php unset($__attributesOriginal388e1416f496c833c11c2ba7d86d1f07); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal388e1416f496c833c11c2ba7d86d1f07)): ?>
<?php $component = $__componentOriginal388e1416f496c833c11c2ba7d86d1f07; ?>
<?php unset($__componentOriginal388e1416f496c833c11c2ba7d86d1f07); ?>
<?php endif; ?>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>

    <?php echo $__env->make('base::error-page-styling', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/SafeErrorPage/Resources/Views/error-page.blade.php ENDPATH**/ ?>