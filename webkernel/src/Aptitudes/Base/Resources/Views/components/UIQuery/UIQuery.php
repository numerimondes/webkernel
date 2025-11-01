<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Base\Resources\Views\components\UIQuery;

use Webkernel\Arcanes\ModuleAssetService;
use Exception;

class UIQuery
{
  private ModuleAssetService $moduleAssetService;
  private array $loadedAssets = [];

  public function __construct(ModuleAssetService $moduleAssetService)
  {
    $this->moduleAssetService = $moduleAssetService;
  }

  /**
   * Render assets for a specific module with optional path scoping
   */
  public function renderModuleAssets(
    string $moduleId,
    ?string $scopePath = null,
    bool $recursive = false,
    array $assetTypes = ['css', 'js'],
    array $exclude = [],
    bool $inline = true,
    bool $once = true,
  ): string {
    if ($scopePath !== null) {
      return $this->renderScopedModuleAssets($moduleId, $scopePath, $recursive, $assetTypes, $exclude, $inline, $once);
    }

    return $this->renderFullModuleAssets($moduleId, $assetTypes, $exclude, $inline, $once);
  }

  /**
   * Render a specific asset file by path
   */
  public function renderAssetByPath(string $path, array $attributes = [], ?string $forceType = null): string
  {
    $assetData = $this->loadAssetContent($path, $forceType);

    if (isset($assetData['error'])) {
      return $this->renderError($assetData['error']);
    }

    return $this->renderAssetContent($assetData, $attributes);
  }

  /**
   * Render a module element (logo, icon, etc.)
   */
  public function renderModuleElement(string $moduleId, string $elementName, array $attributes = []): string
  {
    $elementPath = $this->moduleAssetService->getModuleElement($moduleId, $elementName);

    if (!$elementPath) {
      return $this->renderError("Element '{$elementName}' not found in module '{$moduleId}'");
    }

    $attributes = $this->applyElementDefaults($elementName, $attributes);
    return $this->renderAssetByPath($elementPath, $attributes);
  }

  /**
   * Render scoped module assets from a specific subdirectory
   */
  private function renderScopedModuleAssets(
    string $moduleId,
    string $scopePath,
    bool $recursive,
    array $assetTypes,
    array $exclude,
    bool $inline,
    bool $once,
  ): string {
    $output = '';

    foreach ($assetTypes as $assetType) {
      if (!in_array($assetType, ['css', 'js'])) {
        continue;
      }

      $files = $this->moduleAssetService->getModuleAssetsFromPath($moduleId, $assetType, $scopePath, $recursive);
      $filteredFiles = $this->filterAssets($files, $exclude);

      foreach ($filteredFiles as $file) {
        $output .= $this->renderSingleFile($file, $assetType, $inline, $once);
      }
    }

    return $output;
  }

  /**
   * Render all module assets (css, js, elements)
   */
  private function renderFullModuleAssets(
    string $moduleId,
    array $assetTypes,
    array $exclude,
    bool $inline,
    bool $once,
  ): string {
    $moduleAssets = $this->moduleAssetService->getModuleAssets($moduleId);

    if (!$moduleAssets) {
      return $this->renderError("Module assets not found: {$moduleId}");
    }

    $output = '';

    foreach ($assetTypes as $assetType) {
      if (!isset($moduleAssets[$assetType])) {
        continue;
      }

      $files = $this->filterAssets($moduleAssets[$assetType], $exclude);

      foreach ($files as $file) {
        $output .= $this->renderSingleFile($file, $assetType, $inline, $once);
      }
    }

    return $output;
  }

  /**
   * Render a single file with proper HTML tags
   */
  private function renderSingleFile(string $filePath, string $type, bool $inline, bool $once): string
  {
    $onceKey = $type . '-' . md5($filePath);

    if ($once && in_array($onceKey, $this->loadedAssets)) {
      return '';
    }

    if ($once) {
      $this->loadedAssets[] = $onceKey;
    }

    if (!file_exists($filePath)) {
      return '';
    }

    if ($inline) {
      $content = file_get_contents($filePath);
      if ($content === false) {
        return '';
      }

      $minified = $this->minifyContent($content, $type);

      return match ($type) {
        'css' => '<style>' . $minified . '</style>' . PHP_EOL,
        'js' => '<script>' . $minified . '</script>' . PHP_EOL,
        default => '',
      };
    }

    $publicPath = str_replace(public_path(), '', $filePath);
    return match ($type) {
      'css' => '<link rel="stylesheet" href="' . asset($publicPath) . '">' . PHP_EOL,
      'js' => '<script src="' . asset($publicPath) . '"></script>' . PHP_EOL,
      default => '',
    };
  }

  /**
   * Load asset content from local or remote path
   */
  private function loadAssetContent(string $path, ?string $forceType = null): array
  {
    $isRemoteUrl = filter_var($path, FILTER_VALIDATE_URL) !== false;

    if ($isRemoteUrl) {
      return $this->loadRemoteAsset($path);
    }

    return $this->loadLocalAsset($path, $forceType);
  }

  /**
   * Load remote asset via HTTP
   */
  private function loadRemoteAsset(string $url): array
  {
    try {
      $context = stream_context_create([
        'http' => [
          'timeout' => 5,
          'user_agent' => 'WebKernel UIQuery Component',
        ],
      ]);

      $content = file_get_contents($url, false, $context);
      if ($content === false) {
        throw new Exception('Failed to fetch remote file');
      }

      $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);

      return [
        'content' => $content,
        'extension' => $extension ?: 'bin',
        'path' => $url,
        'mime_type' => $this->getMimeType($extension ?: 'bin'),
        'is_remote' => true,
      ];
    } catch (Exception $e) {
      return ['error' => 'Remote asset failed to load: ' . $url];
    }
  }

  /**
   * Load local asset from filesystem
   */
  private function loadLocalAsset(string $path, ?string $forceType = null): array
  {
    // Handle app/ prefix
    if (str_starts_with($path, 'app/')) {
      $path = app_path(substr($path, 4));
    }
    // Handle relative paths from project root
    elseif (!str_starts_with($path, '/') && !str_starts_with($path, 'http')) {
      $path = base_path($path);
    }

    $supportedExtensions = [
      'images' => ['png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'webp'],
      'documents' => ['pdf', 'txt', 'json', 'xml'],
      'styles' => ['css'],
      'scripts' => ['js'],
    ];

    $allExtensions = array_merge(...array_values($supportedExtensions));

    $actualPath = null;
    $actualExtension = null;

    // If path has extension, try direct access
    $pathExtension = pathinfo($path, PATHINFO_EXTENSION);
    if ($pathExtension && file_exists($path)) {
      $actualPath = $path;
      $actualExtension = $pathExtension;
    } else {
      // Try with force type first
      if ($forceType && file_exists($path . '.' . $forceType)) {
        $actualPath = $path . '.' . $forceType;
        $actualExtension = $forceType;
      } else {
        // Try all supported extensions
        foreach ($allExtensions as $ext) {
          $testPath = $path . '.' . $ext;
          if (file_exists($testPath)) {
            $actualPath = $testPath;
            $actualExtension = $ext;
            break;
          }
        }
      }
    }

    if (!$actualPath) {
      return ['error' => 'Asset not found: ' . $path];
    }

    $content = file_get_contents($actualPath);
    if ($content === false) {
      return ['error' => 'Asset not readable: ' . $actualPath];
    }

    return [
      'content' => $content,
      'extension' => $actualExtension,
      'path' => $actualPath,
      'mime_type' => $this->getMimeType($actualExtension),
      'is_remote' => false,
    ];
  }

  /**
   * Render asset content based on type and attributes
   */
  private function renderAssetContent(array $assetData, array $attributes): string
  {
    $returnType = $attributes['return'] ?? 'auto';
    $extension = $assetData['extension'];
    $mimeType = $assetData['mime_type'];

    // Auto-detect return type if not specified
    if ($returnType === 'auto') {
      $returnType = $this->detectReturnType($extension, $mimeType, $attributes);
    }

    return match ($returnType) {
      'image' => $this->renderImage($assetData, $attributes),
      'inline_css' => $this->renderInlineStyle($assetData),
      'inline_js' => $this->renderInlineScript($assetData),
      'link_css' => $this->renderLinkedStyle($assetData),
      'link_js' => $this->renderLinkedScript($assetData),
      'text' => $this->renderText($assetData, $attributes),
      'download' => $this->renderDownload($assetData, $attributes),
      'data_url' => $this->renderAsDataUrl($assetData),
      'favicon' => $this->renderFavicon($assetData, $attributes),
      'raw' => $assetData['content'],
      default => $this->renderGeneric($assetData, $attributes),
    };
  }

  /**
   * Detect appropriate return type based on file extension and attributes
   */
  private function detectReturnType(string $extension, string $mimeType, array $attributes): string
  {
    // Image detection
    if (str_starts_with($mimeType, 'image/')) {
      return 'image';
    }

    // CSS/JS detection
    if ($extension === 'css') {
      return 'inline_css';
    }

    if ($extension === 'js') {
      return 'inline_js';
    }

    // Text files
    if (in_array($extension, ['txt', 'json', 'xml'])) {
      return 'text';
    }

    // Default to download for other types
    return 'download';
  }

  /**
   * Render image with proper attributes
   */
  private function renderImage(array $assetData, array $attributes): string
  {
    $isSvg = $assetData['extension'] === 'svg';

    // Handle inline SVG with style/class injection or if any SVG-specific attributes are present
    if (
      $isSvg &&
      ($attributes['inject_style'] ??
        ($attributes['inject-style'] ??
          ($attributes['inject_class'] ??
            ($attributes['inject-class'] ?? isset($attributes['width']) || isset($attributes['height'])))))
    ) {
      return $this->renderInlineSvg($assetData, $attributes);
    }

    $dataUrl = $this->renderAsDataUrl($assetData);
    $attrs = ['src="' . $dataUrl . '"', 'loading="lazy"'];

    // Add standard image attributes
    foreach (['width', 'height', 'alt'] as $attr) {
      if (isset($attributes[$attr])) {
        $attrs[] = $attr . '="' . htmlspecialchars($attributes[$attr]) . '"';
      }
    }

    // Add CSS class and style
    if ($class = $this->buildClass($attributes)) {
      $attrs[] = 'class="' . htmlspecialchars($class) . '"';
    }

    if ($style = $this->buildStyle($attributes)) {
      $attrs[] = 'style="' . htmlspecialchars($style) . '"';
    }

    return '<img ' . implode(' ', $attrs) . '>';
  }

  /**
   * Render inline SVG with attribute injection
   */
  private function renderInlineSvg(array $assetData, array $attributes): string
  {
    $svgContent = $assetData['content'];
    $svgAttributes = [];

    // Add CSS classes
    if ($class = $this->buildClass($attributes)) {
      $svgAttributes[] = 'class="' . htmlspecialchars($class) . '"';
    }

    // Build style attributes
    $styleAttributes = [];

    // Add injected styles (for fill, stroke, etc.)
    $injectStyle = $this->buildStyle($attributes);
    if ($injectStyle) {
      $styleAttributes[] = $injectStyle;
    }

    // Add width/height as CSS if provided
    foreach (['width', 'height'] as $attr) {
      if (isset($attributes[$attr])) {
        $value = $attributes[$attr];
        // Don't add 'px' if value already has units or is a number that should be unitless
        if (is_numeric($value)) {
          $styleAttributes[] = $attr . ': ' . $value . 'px';
        } else {
          $styleAttributes[] = $attr . ': ' . $value;
        }
      }
    }

    if (!empty($styleAttributes)) {
      $svgAttributes[] = 'style="' . htmlspecialchars(implode('; ', $styleAttributes)) . '"';
    }

    // Add width/height as attributes as well (for better compatibility)
    foreach (['width', 'height'] as $attr) {
      if (isset($attributes[$attr])) {
        $svgAttributes[] = $attr . '="' . htmlspecialchars($attributes[$attr]) . '"';
      }
    }

    $attributesString = !empty($svgAttributes) ? ' ' . implode(' ', $svgAttributes) : '';

    return preg_replace('/<svg([^>]*)>/', '<svg$1' . $attributesString . '>', $svgContent, 1);
  }

  /**
   * Render inline CSS
   */
  private function renderInlineStyle(array $assetData): string
  {
    $minified = $this->minifyContent($assetData['content'], 'css');
    return '<style>' . $minified . '</style>';
  }

  /**
   * Render inline JavaScript
   */
  private function renderInlineScript(array $assetData): string
  {
    $minified = $this->minifyContent($assetData['content'], 'js');
    return '<script>' . $minified . '</script>';
  }

  /**
   * Render linked CSS
   */
  private function renderLinkedStyle(array $assetData): string
  {
    if ($assetData['is_remote']) {
      return '<link rel="stylesheet" href="' . $assetData['path'] . '">';
    }

    $publicPath = str_replace(public_path(), '', $assetData['path']);
    return '<link rel="stylesheet" href="' . asset($publicPath) . '">';
  }

  /**
   * Render linked JavaScript
   */
  private function renderLinkedScript(array $assetData): string
  {
    if ($assetData['is_remote']) {
      return '<script src="' . $assetData['path'] . '"></script>';
    }

    $publicPath = str_replace(public_path(), '', $assetData['path']);
    return '<script src="' . asset($publicPath) . '"></script>';
  }

  /**
   * Render text content with proper formatting
   */
  private function renderText(array $assetData, array $attributes): string
  {
    $class = $this->buildClass($attributes);
    $style = $this->buildStyle($attributes);

    $preStyle =
      'background: #f8f9fa; border: 1px solid #dee2e6; padding: 12px; border-radius: 6px; ' .
      'overflow-x: auto; font-family: monospace; font-size: 13px; line-height: 1.4; margin: 0;';

    if ($style) {
      $preStyle .= ' ' . $style;
    }

    return '<pre class="uiquery-text ' .
      $class .
      '" style="' .
      $preStyle .
      '">' .
      htmlspecialchars($assetData['content']) .
      '</pre>';
  }

  /**
   * Render download link
   */
  private function renderDownload(array $assetData, array $attributes): string
  {
    $dataUrl = $this->renderAsDataUrl($assetData);
    $filename = basename($assetData['path'] ?? 'file.' . $assetData['extension']);
    $extension = strtoupper($assetData['extension']);

    $class = $this->buildClass($attributes);
    $style = $this->buildStyle($attributes);

    $defaultClass =
      'inline-flex items-center px-3 py-2 text-xs font-medium text-white bg-blue-600 ' .
      'border border-transparent rounded-md hover:bg-blue-700 focus:outline-none ' .
      'focus:ring-2 focus:ring-offset-2 focus:ring-blue-500';

    $finalClass = trim($defaultClass . ' ' . $class);

    return '<a href="' .
      $dataUrl .
      '" download="' .
      $filename .
      '" class="' .
      $finalClass .
      '"' .
      ($style ? ' style="' . $style . '"' : '') .
      '>' .
      '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">' .
      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" ' .
      'd="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>' .
      '</svg>Download ' .
      $extension .
      '</a>';
  }

  /**
   * Render generic content based on type
   */
  private function renderGeneric(array $assetData, array $attributes): string
  {
    $extension = $assetData['extension'];

    return match ($extension) {
      'css' => $this->renderInlineStyle($assetData),
      'js' => $this->renderInlineScript($assetData),
      default => $this->renderText($assetData, $attributes),
    };
  }

  /**
   * Generate data URL for asset
   */
  private function renderAsDataUrl(array $assetData): string
  {
    if ($assetData['is_remote']) {
      return $assetData['path'];
    }

    return 'data:' . $assetData['mime_type'] . ';base64,' . base64_encode($assetData['content']);
  }

  /**
   * Render favicon link tag with base64 data URL
   */
  private function renderFavicon(array $assetData, array $attributes): string
  {
    $dataUrl = $this->renderAsDataUrl($assetData);
    $type = $attributes['type'] ?? $assetData['mime_type'];
    $sizes = $attributes['sizes'] ?? null;

    $attrs = ['rel="icon"', 'type="' . htmlspecialchars($type) . '"', 'href="' . $dataUrl . '"'];

    if ($sizes) {
      $attrs[] = 'sizes="' . htmlspecialchars($sizes) . '"';
    }

    return '<link ' . implode(' ', $attrs) . '>';
  }

  /**
   * Filter assets by exclusion list
   */
  private function filterAssets(array $assets, array $exclude): array
  {
    if (empty($exclude)) {
      return $assets;
    }

    return array_filter($assets, function (string $assetPath) use ($exclude): bool {
      $filename = basename($assetPath);
      foreach ($exclude as $excludeFile) {
        if (str_contains($filename, $excludeFile)) {
          return false;
        }
      }
      return true;
    });
  }

  /**
   * Apply default attributes for known elements
   */
  private function applyElementDefaults(string $element, array $attributes): array
  {
    if (!isset($attributes['width']) && !isset($attributes['height'])) {
      $defaults = [
        'logo' => ['width' => '150', 'height' => '50'],
        'icon' => ['width' => '32', 'height' => '32'],
        'banner' => ['width' => '800', 'height' => '200'],
        'avatar' => ['width' => '64', 'height' => '64'],
        'thumbnail' => ['width' => '128', 'height' => '128'],
      ];

      if (isset($defaults[$element])) {
        $attributes = array_merge($defaults[$element], $attributes);
      }
    }

    if (!isset($attributes['alt'])) {
      $attributes['alt'] = ucfirst($element);
    }

    return $attributes;
  }

  /**
   * Build CSS class string from attributes
   */
  private function buildClass(array $attributes): string
  {
    $classes = [];
    if ($class = $attributes['class'] ?? null) {
      $classes[] = $class;
    }
    // Support both inject_class and inject-class (Laravel converts hyphens to underscores)
    if ($injectClass = $attributes['inject_class'] ?? ($attributes['inject-class'] ?? null)) {
      $classes[] = $injectClass;
    }
    return trim(implode(' ', $classes));
  }

  /**
   * Build CSS style string from attributes
   */
  private function buildStyle(array $attributes): string
  {
    $styles = [];
    if ($style = $attributes['style'] ?? null) {
      $styles[] = $style;
    }
    // Support both inject_style and inject-style (Laravel converts hyphens to underscores)
    if ($injectStyle = $attributes['inject_style'] ?? ($attributes['inject-style'] ?? null)) {
      $styles[] = $injectStyle;
    }
    return trim(implode(' ', $styles));
  }

  /**
   * Get MIME type for file extension
   */
  private function getMimeType(string $extension): string
  {
    $mimeTypes = [
      // Images
      'png' => 'image/png',
      'jpg' => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'gif' => 'image/gif',
      'svg' => 'image/svg+xml',
      'ico' => 'image/x-icon',
      'webp' => 'image/webp',
      // Documents
      'pdf' => 'application/pdf',
      'txt' => 'text/plain',
      'json' => 'application/json',
      'xml' => 'application/xml',
      // Styles and Scripts
      'css' => 'text/css',
      'js' => 'application/javascript',
    ];

    return $mimeTypes[$extension] ?? 'application/octet-stream';
  }

  /**
   * Minify CSS or JS content
   */
  private function minifyContent(string $content, string $type): string
  {
    if ($type === 'css') {
      // Remove comments
      $content = preg_replace('/\/\*.*?\*\//s', '', $content);
      // Collapse whitespace
      $content = preg_replace('/\s+/', ' ', $content);
      // Remove spaces around CSS syntax
      $content = preg_replace('/\s*([{}:;,>+~])\s*/', '$1', $content);
      return trim($content);
    }

    if ($type === 'js') {
      // Remove block comments
      $content = preg_replace('/\/\*.*?\*\//s', '', $content);
      // Remove line comments
      $content = preg_replace('/\/\/.*$/m', '', $content);
      // Collapse whitespace
      $content = preg_replace('/\s+/', ' ', $content);
      // Remove spaces around JS operators
      $content = preg_replace('/\s*([{}();,=+\-*\/])\s*/', '$1', $content);
      return trim($content);
    }

    return $content;
  }

  /**
   * Render error message
   */
  private function renderError(string $message): string
  {
    return '<div class="uiquery-error" style="color: #dc3545; background: #f8d7da; border: 1px solid #f5c6cb; ' .
      'padding: 8px 12px; border-radius: 4px; font-family: monospace; font-size: 12px; display: inline-block;">' .
      'UIQuery Error: ' .
      htmlspecialchars($message) .
      '</div>';
  }
}
