

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'module' => null,
    'module_path' => null,
    'path' => null,
    'element' => null,
    'scope' => null,
    'recursive' => false,
    'types' => 'css,js',
    'exclude' => '',
    'inline' => true,
    'once' => true,
    'return' => 'auto',
    'force_type' => null
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
    'module' => null,
    'module_path' => null,
    'path' => null,
    'element' => null,
    'scope' => null,
    'recursive' => false,
    'types' => 'css,js',
    'exclude' => '',
    'inline' => true,
    'once' => true,
    'return' => 'auto',
    'force_type' => null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Cache UIQuery instance to avoid repeated instantiation
    static $UIQueryInstance = null;
    if ($UIQueryInstance === null) {
        $UIQueryInstance = app(\Webkernel\Aptitudes\Base\Resources\Views\components\UIQuery\UIQuery::class);
    }
    $UIQuery = $UIQueryInstance;

    // Parse asset types (cache parsed types)
    static $parsedTypesCache = [];
    $typesKey = $types . '|' . $exclude;
    if (!isset($parsedTypesCache[$typesKey])) {
        $parsedTypesCache[$typesKey] = [
            'types' => array_map('trim', explode(',', $types)),
            'exclude' => $exclude ? array_map('trim', explode(',', $exclude)) : []
        ];
    }
    $assetTypes = $parsedTypesCache[$typesKey]['types'];
    $excludeList = $parsedTypesCache[$typesKey]['exclude'];

    // Extract all attributes for asset rendering
    $assetAttributes = array_merge($attributes->getAttributes(), [
        'return' => $return
    ]);

    $output = '';
    $moduleName = $module ? strtoupper($module) : 'no-module';

    try {
        // Create cache key for expensive operations
        $cacheKey = md5(serialize([
            'path' => $path,
            'module' => $module,
            'element' => $element,
            'scope' => $scope,
            'recursive' => $recursive,
            'types' => $types,
            'exclude' => $exclude,
            'inline' => $inline,
            'once' => $once,
            'return' => $return,
            'force_type' => $force_type,
            'attributes' => $assetAttributes
        ]));

        static $outputCache = [];

        // Check cache first (only if $once is true)
        if ($once && isset($outputCache[$cacheKey])) {
            $output = $outputCache[$cacheKey];
        } else {
            // Determine rendering mode based on props priority
            if ($path) {
                // Mode 1: Specific asset by path (highest priority)
                $output = $UIQuery->renderAssetByPath($path, $assetAttributes, $force_type);
            }
            elseif ($module && $element) {
                // Mode 2: Module element (logo, icon, etc.)
                $output = $UIQuery->renderModuleElement($module, $element, $assetAttributes);
            }
            elseif ($module) {
                // Mode 3: Module assets (with optional scoping)
                $output = $UIQuery->renderModuleAssets(
                    $module,
                    $scope,
                    $recursive,
                    $assetTypes,
                    $excludeList,
                    $inline,
                    $once
                );
            }
            else {
                throw new Exception('UIQuery requires either path, or module, or module+element parameters');
            }

            // Cache the output if $once is true
            if ($once) {
                $outputCache[$cacheKey] = $output;
            }
        }
    } catch (Exception $e) {
        $output = '<div class="uiquery-error" style="color: #dc3545; background: #f8d7da; border: 1px solid #f5c6cb; padding: 8px 12px; border-radius: 4px; font-family: monospace; font-size: 12px; display: inline-block;">UIQuery Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
?>

<?php echo "<!--[@UIQuery:{$moduleName}:start]-->"; ?>

<?php echo $output; ?>

<?php echo "<!--[@UIQuery:{$moduleName}:end]-->"; ?>

<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/Base/Resources/Views/components/UIQuery/index.blade.php ENDPATH**/ ?>