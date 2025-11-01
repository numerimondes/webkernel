{{--
/**
 * Dynamic Asset Component
 *
 * This component dynamically loads and displays assets from local files or remote URLs.
 * It supports images, text files, and other document types with flexible error handling.
 *
 * Usage Examples:
 * <x-dynamic-asset path="app/module/assets/logo.png" width="32" height="32" alt="Logo" />
 * <x-dynamic-asset path="https://example.com/image.png" error="quiet" />
 * <x-dynamic-asset path="document.pdf" onFail="downloadable" />
 * <x-dynamic-asset path="style.css" return="raw" />
 *
 * File Location: resources/views/components/dynamic-asset.blade.php
 *
 * @param string|null $path - File path (local or remote URL)
 * @param string|null $asset - Alias for path
 * @param string|null $file - Alias for path
 * @param string|null $src - Alias for path
 * @param string|null $type - Force file extension (png, jpg, etc.)
 * @param string|null $width - Image width attribute
 * @param string|null $height - Image height attribute
 * @param string|null $alt - Image alt text
 * @param string|null $class - CSS classes
 * @param string $error - Error display mode: "quiet" (console only) or "loud" (visible)
 * @param string $onFail - Fallback for unreadable files: "none" or "downloadable"
 * @param string $return - Return type: "auto", "image", "text", "raw", "download"
 */
--}}

@props([
    'path' => null,
    'asset' => null,
    'file' => null,
    'type' => null,
    'src' => null,
    'width' => null,
    'height' => null,
    'alt' => null,
    'class' => null,
    'error' => 'quiet',
    'onFail' => 'none',
    'return' => 'auto'
])

@php
    // Initialize variables
    $filePath = null;
    $errorMessage = null;
    $debugMessage = null;
    $isRemoteUrl = false;
    $actualPath = null;
    $actualExtension = null;
    $mimeType = null;
    $base64Data = null;
    $fileContent = null;

    // Determine file path from various prop options
    $filePath = $path ?? $asset ?? $file ?? $src;

    // Build path from remaining attributes if no explicit path provided
    if (!$filePath) {
        $segments = [];
        foreach ($attributes->getAttributes() as $key => $value) {
            if (!in_array($key, ['path', 'asset', 'file', 'src', 'width', 'height', 'alt', 'class', 'type', 'error', 'onFail', 'return'])) {
                $segments[] = $value;
            }
        }
        $filePath = implode('/', $segments);
    }

    // Validate that a path was provided
    if (!$filePath) {
        $errorMessage = 'No file path provided';
        $debugMessage = 'Dynamic Asset Error: No file path provided. Called from: ' . request()->url();
    } else {
        // Check if path is a remote URL
        $isRemoteUrl = filter_var($filePath, FILTER_VALIDATE_URL) !== false;

        if ($isRemoteUrl) {
            // Handle remote URLs
            try {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 10,
                        'user_agent' => 'Laravel Dynamic Asset Component'
                    ]
                ]);
                $fileContent = file_get_contents($filePath, false, $context);

                if ($fileContent === false) {
                    throw new Exception('Failed to fetch remote file');
                }

                $actualPath = $filePath;
                $actualExtension = pathinfo(parse_url($filePath, PHP_URL_PATH), PATHINFO_EXTENSION);
                $debugMessage = 'Remote asset loaded successfully: ' . $filePath . ' (size: ' . strlen($fileContent) . ' bytes)';

            } catch (Exception $e) {
                $errorMessage = 'Remote asset not loaded: ' . $filePath;
                $debugMessage = 'Dynamic Asset Error: Remote asset not loaded: ' . $filePath . '. Called from: ' . request()->url() . '. Error: ' . $e->getMessage();
            }
        } else {
            // Handle local files

            // Convert app/ prefix to absolute path
            if (strpos($filePath, 'app/') === 0) {
                $filePath = app_path(substr($filePath, 4));
            }

            // Supported file extensions
            $extensions = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'webp', 'pdf', 'txt', 'css', 'js', 'json', 'xml'];

            // Check if file has extension and exists
            if (pathinfo($filePath, PATHINFO_EXTENSION)) {
                if (file_exists($filePath)) {
                    $actualPath = $filePath;
                    $actualExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                }
            } else {
                // Try with specified type first
                if ($type && file_exists($filePath . '.' . $type)) {
                    $actualPath = $filePath . '.' . $type;
                    $actualExtension = $type;
                } else {
                    // Try all extensions
                    foreach ($extensions as $ext) {
                        $testPath = $filePath . '.' . $ext;
                        if (file_exists($testPath)) {
                            $actualPath = $testPath;
                            $actualExtension = $ext;
                            break;
                        }
                    }
                }
            }

            if ($actualPath) {
                try {
                    $fileContent = file_get_contents($actualPath);
                    if ($fileContent === false) {
                        throw new Exception('Failed to read file');
                    }
                    $debugMessage = 'Local asset loaded successfully: ' . $actualPath . ' (size: ' . strlen($fileContent) . ' bytes)';
                } catch (Exception $e) {
                    $errorMessage = 'Asset not loaded: ' . $actualPath;
                    $debugMessage = 'Dynamic Asset Error: Asset not loaded: ' . $actualPath . '. Called from: ' . request()->url() . '. Error: ' . $e->getMessage();
                }
            } else {
                $errorMessage = 'Asset not found: ' . $filePath;
                $debugMessage = 'Dynamic Asset Error: Asset not found: ' . $filePath . '. Called from: ' . request()->url() . '. Tested extensions: ' . implode(', ', $extensions);
            }
        }

        // Process file content if loaded successfully
        if ($fileContent !== null && $actualExtension) {
            // Determine MIME type
            $mimeTypes = [
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'ico' => 'image/x-icon',
                'webp' => 'image/webp',
                'pdf' => 'application/pdf',
                'txt' => 'text/plain',
                'css' => 'text/css',
                'js' => 'application/javascript',
                'json' => 'application/json',
                'xml' => 'application/xml',
            ];
            $mimeType = $mimeTypes[$actualExtension] ?? 'application/octet-stream';

            // Encode to base64 for data URL
            $base64Data = base64_encode($fileContent);
        }
    }

    // Special handling for url return type - return early with just the URL
    if ($return === 'url') {
        if ($errorMessage) {
            // Return empty string if there's an error
            echo '';
            return;
        }

        if ($isRemoteUrl) {
            // For remote URLs, return the URL directly
            echo $actualPath;
            return;
        } else {
            // For local files, return data URL
            echo 'data:' . $mimeType . ';base64,' . $base64Data;
            return;
        }
    }
@endphp

{{-- Debug console output --}}
<script>
    @if($debugMessage)
        console.log("{{ $debugMessage }}");
    @endif
</script>

{{-- Handle errors based on error mode --}}
@if($errorMessage)
    @if($error === 'loud')
        <div class="dynamic-asset-error" style="color: #dc3545; background: #f8d7da; border: 1px solid #f5c6cb; padding: 8px 12px; border-radius: 4px; font-family: monospace; font-size: 12px; display: inline-block;">
            ⚠️ {{ $errorMessage }}
        </div>
    @endif
    {{-- Quiet mode: only console log (already handled above) --}}

{{-- Success: Display content based on return type and content --}}
@elseif($fileContent !== null && $mimeType && $base64Data)

    @php
        $isImage = strpos($mimeType, 'image/') === 0;
        $isText = in_array($mimeType, ['text/plain', 'text/css', 'application/javascript', 'application/json', 'application/xml']);
        $displayType = $return;

        // Auto-detect display type if not specified
        if ($return === 'auto') {
            if ($isImage && ($width || $height || $alt)) {
                $displayType = 'image';
            } elseif ($isText) {
                $displayType = 'text';
            } elseif ($isImage) {
                $displayType = 'image';
            } else {
                $displayType = ($onFail === 'downloadable') ? 'download' : 'raw';
            }
        }
    @endphp

    @if($displayType === 'image' && $isImage)
        <img
            src="data:{{ $mimeType }};base64,{{ $base64Data }}"
            @if($width) width="{{ $width }}" @endif
            @if($height) height="{{ $height }}" @endif
            @if($alt) alt="{{ $alt }}" @else alt="Dynamic Asset" @endif
            @if($class) class="{{ $class }}" @endif
            loading="lazy"
        />

    @elseif($displayType === 'text' && $isText)
        <pre class="dynamic-asset-text {{ $class }}" style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 12px; border-radius: 6px; overflow-x: auto; font-family: 'Courier New', monospace; font-size: 13px; line-height: 1.4; margin: 0;">{{ $fileContent }}</pre>

    @elseif($displayType === 'raw')
        {!! $fileContent !!}

    @elseif($displayType === 'download' || ($onFail === 'downloadable'))
        @if(class_exists('Filament\Support\Facades\FilamentIcon'))
            {{-- Use Filament button if available --}}
            <x-filament::button
                tag="a"
                :href="'data:' . $mimeType . ';base64,' . $base64Data"
                download="{{ basename($actualPath ?? 'file.' . $actualExtension) }}"
                size="sm"
                color="primary"
                icon="heroicon-m-arrow-down-tray"
            >
                Download {{ strtoupper($actualExtension) }}
            </x-filament::button>
        @else
            {{-- Fallback button --}}
            <a
                href="data:{{ $mimeType }};base64,{{ $base64Data }}"
                download="{{ basename($actualPath ?? 'file.' . $actualExtension) }}"
                class="inline-flex items-center px-3 py-2 text-xs font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 {{ $class }}"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download {{ strtoupper($actualExtension) }}
            </a>
        @endif

    @else
        {{-- Default fallback for unknown display types --}}
        @if($isImage)
            <img src="data:{{ $mimeType }};base64,{{ $base64Data }}" alt="Asset" style="max-width: 100%; height: auto;" />
        @else
            <span class="dynamic-asset-unknown" style="color: #6c757d; font-style: italic;">{{ ucfirst($actualExtension) }} file loaded</span>
        @endif
    @endif

@endif
