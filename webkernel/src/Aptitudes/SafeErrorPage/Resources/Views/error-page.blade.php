@props([
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
])

<div>
    <x-filament-panels::page>
        {{-- Error Header Section --}}
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <div style="display: flex; align-items: center; gap: 1.5rem; max-width: 48rem; width: 100%; padding: 0 1rem;">
                {{-- Logo --}}
                <div style="flex: 0 0 auto;">
                    {!! module_image(
                        'module://media-store/Resources/Assets/logo/numerimondes-builder.svg',
                        'inline',
                        'width: 64px; height: 64px;',
                        ''
                    ) !!}
                </div>

                {{-- Title + Code Badge --}}
                <div style="flex: 1; text-align: left; margin-top: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <h2 style="font-size: 1.875rem; font-weight: 800; margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;">
                            {{ __('Error') }} {{ $code }} {{ __('Occurred') }}
                        </h2>
                    </div>
                    @if($source)
                        <p style="font-family: 'Courier New', monospace; font-size: 0.875rem; margin: 0.5rem 0 0 0; opacity: 0.8; word-break: break-word;">
                            {{ $source }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- Error Reference Badge & Copy Notification --}}
            @if($identifier)
                <div style="margin-top: 2rem; max-width: 48rem; width: 100%; padding: 0 1rem;">
                    <x-filament::section>
                        <div style="text-align: center;">
                            <p style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">
                                {{ __('Reference this identifier when contacting support') }}:
                            </p>
                            <div style="display: flex; justify-content: center; margin-bottom: 0.5rem; align-items: center; gap: 0.5rem;">
                                <div
                                    class="error-ref-badge"
                                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background: rgba(59, 130, 246, 0.1); border: 1px solid var(--primary-600); border-radius: 0.5rem; font-weight: 600; font-size: 0.855rem; cursor: pointer; max-width: 100%;"
                                    id="error-ref-{{ $identifier }}"
                                    :title="($errorCode ? $errorCode . '-' : '') . $identifier"
                                >
                                    <code style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; max-width: 200px;">
                                        {{ $errorCode ? $errorCode . '-' : '' }}{{ $identifier }}
                                    </code>
                                    <button
                                        type="button"
                                        class="copy-ref-btn"
                                        data-reference="{{ $errorCode ? $errorCode . '-' . $identifier : $identifier }}"
                                        style="background: none; border: none; cursor: pointer; padding: 0; display: flex; align-items: center; opacity: 0.7; transition: opacity 0.2s; flex-shrink: 0;"
                                        aria-label="{{ __('Copy reference') }}"
                                    >
                                        <svg style="width: 16px; height: 16px; color: var(--primary-800);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <p style="font-size: 0.75rem; margin: 0; opacity: 0.7;">
                                {{ __('This has been logged for troubleshooting') }}
                            </p>
                        </div>
                    </x-filament::section>
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
                        const title = type === 'success' ? '{{ __('Copied to clipboard') }}' : '{{ __('Copy failed') }}';

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
            @endif
        </div>

        {{-- Error Message Section --}}
        <div style="animation-delay: 0.2s; margin-top: 1.3rem; margin-bottom: 1.3rem; padding: 0 1rem;">
            <x-filament::section>
                <div>
                    <p style="font-size: 0.875rem; margin-top: 0; opacity: 0.9; line-height: 1.6;">
                        {{ $message }}
                    </p>

                    {{-- Custom Actions --}}
                    @if(!empty($actions))
                        <div style="margin-top: 1rem; display: flex; flex-wrap: wrap; gap: 0.75rem;">
                            @php
                                $actionCount = count($actions);
                            @endphp

                            @foreach($actions as $action)
                                @php
                                    $actionType = $action['type'] ?? 'link';
                                    $actionColor = $action['color'] ?? 'primary';
                                    $actionLabel = $action['label'] ?? __('Action');
                                    $actionHref = $action['href'] ?? null;
                                    $fullWidth = $actionCount === 1;
                                @endphp

                                @if($actionType === 'link' && !empty($actionHref))
                                    <x-filament::button
                                        :href="$actionHref"
                                        :color="$actionColor"
                                        size="sm"
                                        icon="heroicon-o-arrow-top-right-on-square"
                                        tag="a"
                                        target="_blank"
                                        :style="$fullWidth ? 'width: 100%;' : ''"
                                    >
                                        {{ $actionLabel }}
                                    </x-filament::button>
                                @endif
                            @endforeach
                        </div>

                        @if(collect($actions)->where('description', '!=', null)->isNotEmpty())
                            <div style="margin-top: 1rem; padding: 1rem; background: rgba(59, 130, 246, 0.1); border-left: 4px solid rgb(59, 130, 246); border-radius: 0.5rem;">
                                @foreach($actions as $action)
                                    @if(!empty($action['description']))
                                        <p style="font-size: 0.875rem; margin-bottom: 0.5rem; margin-top: 0;">
                                            <strong>{{ $action['label'] }}:</strong> {{ $action['description'] }}
                                        </p>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endif

                    {{-- Documentation Link --}}
                    @if($documentationUrl)
                        <div style="margin-top: 1.5rem;">
                            <x-filament::button
                                :href="$documentationUrl"
                                color="gray"
                                size="sm"
                                icon="heroicon-o-book-open"
                                outlined
                                tag="a"
                                target="_blank"
                            >
                                {{ __('View Documentation') }}
                            </x-filament::button>
                        </div>
                    @endif

                    {{-- Technical Details Toggle --}}
                    @if($showDetails && $details)
                        <div style="margin-top: 1.5rem;">
                            <details style="cursor: pointer;">
                                <summary style="font-weight: 600; padding: 0.75rem; background: rgba(59, 130, 246, 0.08); border-radius: 0.5rem; list-style: none; display: flex; align-items: center; gap: 0.5rem; user-select: none;">
                                    <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span style="font-size: 0.875rem;">{{ __('Technical Details') }}</span>
                                </summary>

                                <div style="margin-top: 1rem; padding: 1rem; background: rgba(59, 130, 246, 0.08); border-radius: 0.5rem;">
                                    <p style="font-weight: 600; margin: 0 0 0.75rem 0; font-size: 0.875rem;">{{ __('Details') }}:</p>
                                    <code style="font-family: 'Courier New', monospace; font-size: 0.8rem; display: block; word-break: break-word; white-space: pre-wrap; overflow-x: auto;">{{ $details }}</code>
                                </div>

                                @if($trace)
                                    <div style="margin-top: 1rem; padding: 1rem; background: rgba(59, 130, 246, 0.08); border-radius: 0.5rem;">
                                        <p style="font-weight: 600; margin: 0 0 0.75rem 0; font-size: 0.875rem;">{{ __('Stack Trace') }}:</p>
                                        <pre style="font-family: 'Courier New', monospace; font-size: 0.75rem; overflow-x: auto; white-space: pre-wrap; word-wrap: break-word; margin: 0;">{{ $trace }}</pre>
                                    </div>
                                @endif
                            </details>
                        </div>
                    @endif
                </div>
            </x-filament::section>
        </div>

        {{-- Navigation Bar --}}
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; max-width: 48rem; width: 100%; padding: 0 1rem; margin-top: 1.5rem; flex-wrap: wrap;">
            <div style="display: flex; gap: 0.5rem;">
                @if($showBackButton && $previousUrl)
                    <x-filament::button
                        :href="$previousUrl"
                        color="gray"
                        icon="heroicon-o-arrow-left"
                        size="sm"
                        outlined
                        tag="a"
                    >
                        {{ __('Back') }}
                    </x-filament::button>
                @endif
            </div>

            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                @if($showReloadButton && $originalUrl)
                    <x-filament::button
                        :href="$originalUrl"
                        color="success"
                        icon="heroicon-o-arrow-path"
                        size="sm"
                        tag="a"
                    >
                        {{ __('Reload Origin') }}
                    </x-filament::button>
                @endif

                @if($showHomeButton)
                    <x-filament::button
                        href="/"
                        color="primary"
                        icon="heroicon-o-home"
                        size="sm"
                        outlined
                        tag="a"
                    >
                        {{ __('Panel Home') }}
                    </x-filament::button>
                @endif
            </div>
        </div>

        {{-- Theme Switcher --}}
        <div style="margin-top: 2rem; padding: 0 1rem; max-width: 24rem; margin-left: auto; margin-right: auto;">
            <x-filament-panels::theme-switcher />
        </div>
    </x-filament-panels::page>

    @include('base::error-page-styling')
</div>
