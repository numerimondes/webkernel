{{--
UIQuery - Unified Dynamic Asset Component

Clear Usage Examples:

1. Load module assets (CSS/JS):
   <x-base::UIQuery module="ui-module" />
   <x-base::UIQuery module="ui-module" types="css" />
   <x-base::UIQuery module="ui-module" types="js" />

2. Load scoped module assets from subdirectory:
   <x-base::UIQuery module="website-builder" scope="builder/V1" recursive />
   <x-base::UIQuery module="website-builder" scope="admin/dashboard" types="css" />

3. Load specific asset by path:
   <x-base::UIQuery path="app/assets/logo.png" width="150" height="50" alt="Company Logo" />
   <x-base::UIQuery path="https://cdn.example.com/styles.css" />

4. Load module element (predefined assets):
   <x-base::UIQuery module="ui-module" element="logo" width="100" />
   <x-base::UIQuery module="website-builder" element="icon" />

5. Regular image (non-SVG)
<x-base::UIQuery element="logo" module="website-builder" width="35" height="35" class="rounded" />

6. SVG with just classes
<x-base::UIQuery element="icon" module="ui-module" inject-class="text-blue-500 hover:text-blue-700" />

7. SVG with complex styling
<x-base::UIQuery element="logo" module="website-builder"
                inject-style="fill: currentColor; transition: all 0.3s ease;"
                inject-class="hover:scale-110 transform"
                width="50" height="50" />

8. Load specific asset by path
<x-base::UIQuery path="app/assets/icons/custom.svg"
                inject-style="stroke: #ff6b6b; stroke-width: 2;"
                width="24" height="24" />

9. Generate favicon with base64 data URL
<x-base::UIQuery path="webkernel/src/Aptitudes/Base/Resources/Assets/numerimondes.png" return="favicon" />
<x-base::UIQuery path="app/assets/logo.png" return="favicon" type="image/png" sizes="32x32" />

10. Get base64 data URL only
<x-base::UIQuery path="app/assets/image.jpg" return="data_url" />

@param string|null $module - Module ID for asset loading
@param string|null $path - Specific asset file path (local or remote URL)
@param string|null $element - Predefined element name (logo, icon, banner, etc.)
@param string|null $scope - Subdirectory path within module for scoped asset loading
@param bool $recursive - Whether to scan subdirectories recursively (default: false)
@param string $types - Asset types to include: "css", "js", "css,js" (default: "css,js")
@param string $exclude - Files to exclude (comma-separated filenames)
@param bool $inline - Whether to inline assets vs link them (default: true)
@param bool $once - Whether to load assets only once per request (default: true)
@param string $return - Return type: "auto", "image", "inline_css", "link_css", "text", "download", "data_url", "favicon"
@param string|null $force_type - Force file extension if path has no extension
--}}

@props([
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
])

@php
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
@endphp

{!! "<!--[@UIQuery:{$moduleName}:start]-->" !!}
{!! $output !!}
{!! "<!--[@UIQuery:{$moduleName}:end]-->" !!}
