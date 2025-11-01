<?php

use Webkernel\Arcanes\ModuleAssetService;

if (!function_exists('module_image')) {
  /**
   * Resolves a module path and returns either a base64 encoded image data URI or inline SVG.
   * This allows displaying images from non-public module directories.
   *
   * @param string $path The module path to the image (e.g., 'module://media-store/path/to/image.svg').
   * @param string $mode Display mode: 'base64' (default), 'inline' (for SVG only).
   * @param string $style Inline CSS styles to apply (only for inline SVG).
   * @param string $class CSS classes to apply (only for inline SVG).
   * @return string|null The base64 data URI, inline SVG markup, or null on error.
   */
  function module_image(string $path, string $mode = 'base64', string $style = '', string $class = ''): ?string
  {
    try {
      /** @var ModuleAssetService $assetService */
      $assetService = app(ModuleAssetService::class);
      $absolutePath = $assetService->resolveModulePath($path);

      if (!$absolutePath || !is_file($absolutePath)) {
        return null;
      }

      $fileContent = file_get_contents($absolutePath);
      if ($fileContent === false) {
        return null;
      }

      $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

      // Handle inline SVG mode
      if ($mode === 'inline' && $extension === 'svg') {
        return module_image_inline_svg($fileContent, $style, $class);
      }

      // Handle base64 mode (default)
      $mimeType = mime_content_type($absolutePath);
      if ($mimeType === false) {
        $mimeTypes = [
          'jpg' => 'image/jpeg',
          'jpeg' => 'image/jpeg',
          'png' => 'image/png',
          'gif' => 'image/gif',
          'svg' => 'image/svg+xml',
          'webp' => 'image/webp',
        ];
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
      }

      $base64Content = base64_encode($fileContent);
      return 'data:' . $mimeType . ';base64,' . $base64Content;
    } catch (\Exception $exception) {
      error_log('Error in module_image helper: ' . $exception->getMessage());
      return null;
    }
  }
}

if (!function_exists('module_image_inline_svg')) {
  /**
   * Process SVG content for inline display with optional style and class attributes.
   *
   * @param string $svgContent Raw SVG file content.
   * @param string $style Inline CSS styles.
   * @param string $class CSS classes.
   * @return string Processed SVG markup with injected attributes.
   */
  function module_image_inline_svg(string $svgContent, string $style = '', string $class = ''): string
  {
    // Remove XML declaration if present
    $svgContent = preg_replace('/<\?xml[^?]*\?>\s*/i', '', $svgContent);

    // Parse the SVG to inject style and class
    if (preg_match('/<svg([^>]*)>/i', $svgContent, $matches)) {
      $svgAttributes = $matches[1];
      $newAttributes = $svgAttributes;

      // Add or merge class attribute
      if (!empty($class)) {
        if (preg_match('/class=["\']([^"\']*)["\']/', $svgAttributes, $classMatch)) {
          $existingClass = $classMatch[1];
          $newAttributes = preg_replace(
            '/class=["\']([^"\']*)["\']/',
            'class="' . trim($existingClass . ' ' . $class) . '"',
            $newAttributes,
          );
        } else {
          $newAttributes .= ' class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '"';
        }
      }

      // Add or merge style attribute
      if (!empty($style)) {
        if (preg_match('/style=["\']([^"\']*)["\']/', $svgAttributes, $styleMatch)) {
          $existingStyle = $styleMatch[1];
          $separator = str_ends_with(trim($existingStyle), ';') ? '' : ';';
          $newAttributes = preg_replace(
            '/style=["\']([^"\']*)["\']/',
            'style="' . trim($existingStyle . $separator . $style) . '"',
            $newAttributes,
          );
        } else {
          $newAttributes .= ' style="' . htmlspecialchars($style, ENT_QUOTES, 'UTF-8') . '"';
        }
      }

      // Replace the original svg tag with the modified one
      $svgContent = preg_replace('/<svg[^>]*>/i', '<svg' . $newAttributes . '>', $svgContent, 1);
    }

    return $svgContent;
  }
}
