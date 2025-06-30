@php
    use Illuminate\Support\Str;
    $languages = \Webkernel\Models\Language::getActiveLanguages();
    $currentLang = auth()->check() ? auth()->user()->user_lang : (session('locale') ?? config('app.locale'));
    $currentLangLabel = $languages->firstWhere('code', $currentLang)->label ?? $currentLang;

    $method = 'method_replace'; // or 'method_cssinline'

    $processSvg = function($svgContent) use ($method) {
        if (! $svgContent) return '';
        if ($method === 'method_replace') {
            $svgContent = preg_replace('/(width|height)="[^"]*"/i', '', $svgContent);
            $svgContent = preg_replace(
                '/<svg(.*?)>/',
                '<svg$1 class="fi-icon fi-size-md" xmlns="http://www.w3.org/2000/svg" fill="currentColor" aria-hidden="true">',
                $svgContent
            );
        } elseif ($method === 'method_cssinline') {
            $svgContent = preg_replace(
                '/<svg(.*?)>/',
                '<svg$1 style="height:20px; width:20px;" class="fi-icon fi-size-md" xmlns="http://www.w3.org/2000/svg" fill="currentColor" aria-hidden="true">',
                $svgContent
            );
        }
        return $svgContent;
    };

    $path = base_path('packages/webkernel/src/public/assets/flags/language/' . $currentLang . '.svg');
    $svgContent = file_exists($path) ? file_get_contents($path) : '';
    $svgContent = $processSvg($svgContent);

    $languageSVG = function($languageCode) use ($processSvg) {
        $path = base_path('packages/webkernel/src/public/assets/flags/language/' . $languageCode . '.svg');
        $svgContent = file_exists($path) ? file_get_contents($path) : '';
        return $processSvg($svgContent);
    };
@endphp

<div x-data="{
    selectedLang: '{{ $currentLang }}',
    selectedLangLabel: '{{ $currentLangLabel }}',
    changeLang(lang) {
        this.selectedLang = lang;
        window.location.href = '{{ url('lang') }}' + '/' + lang;
    },
}">
    <x-filament::dropdown teleport placement="bottom-start">
        <x-slot name="trigger">
            <div class="fi-dropdown-list p-1">
                <div class="flex items-center justify-center h-8 w-8 rounded-md transition-colors hover:bg-gray-50 dark:hover:bg-white/5">
                    {!! $svgContent !!}
                </div>
            </div>
        </x-slot>

        <x-filament::dropdown.header class="font-semibold text-gray-900 dark:text-gray-100" icon="heroicon-c-language">
            {{ lang('available_languages') }}
        </x-filament::dropdown.header>

        <x-filament::dropdown.list class="w-40 max-h-60 overflow-y-auto fi-dropdown-list">
            @foreach ($languages as $language)
                <a
                    href="javascript:void(0)"
                    @click.prevent="changeLang('{{ $language->code }}')"
                    wire:key="language-{{ $language->code }}"
                    class="fi-dropdown-list-item fi-ac-grouped-action cursor-pointer whitespace-nowrap flex items-center gap-2"
                    :class="selectedLang === '{{ $language->code }}' ? 'bg-gray-100 dark:bg-gray-700' : ''"
                >
                    {!! $languageSVG($language->code) !!}
                    <span class="fi-dropdown-list-item-label">{{ $language->label }}</span>
                    <span x-show="selectedLang === '{{ $language->code }}'">
                        <x-filament::icon name="heroicon-m-check" class="h-4 w-4 text-primary-500" />
                    </span>
                </a>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
