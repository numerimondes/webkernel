@php
    use Illuminate\Support\Str;
    $isMobile = Str::contains(request()->header('User-Agent'), 'Mobile');
    $languages = \Webkernel\Models\Language::getActiveLanguages();
    $currentLang = auth()->check() ? auth()->user()->user_lang : (session('locale') ?? config('app.locale'));
    $currentLangLabel = $languages->firstWhere('code', $currentLang)->label ?? $currentLang;
    $path = base_path('packages/webkernel/src/public/assets/flags/language/' . $currentLang . '.svg');
    $svgContent = file_exists($path) ? file_get_contents($path) : '';
    if ($svgContent) {
        $svgContent = preg_replace('/<svg(.*?)>/', '<svg$1 class="h-4 w-4 object-contain">', $svgContent);
    }
    $languageSVG = function($languageCode) {
        $path = base_path('packages/webkernel/src/public/assets/flags/language/' . $languageCode . '.svg');
        $svgContent = file_exists($path) ? file_get_contents($path) : '';
        if ($svgContent) {
            $svgContent = preg_replace('/<svg(.*?)>/', '<svg$1 class="h-4 w-4 object-contain">', $svgContent);
        }
        return $svgContent;
    };
    $responsiveMaxHeight = 'max-h-48 sm:max-h-60 md:max-h-72 lg:max-h-80 xl:max-h-96';
@endphp
<!-- WebKernel Language Provider by www.numerimondes.com -->
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
        <x-filament::dropdown.header class="font-semibold" icon="heroicon-c-language">
            {{ lang('available_languages') }}
        </x-filament::dropdown.header>
        <x-filament::dropdown.list class="w-40 {{ $responsiveMaxHeight }} overflow-y-auto">
            @foreach ($languages as $language)
                <x-filament::dropdown.list.item
                    x-bind:class="selectedLang === '{{ $language->code }}' ? 'force-inter bg-gray-100 dark:bg-gray-700' : ''"
                    @click="changeLang('{{ $language->code }}')" tag="button" wire:key="language-{{ $language->code }}">
                    <div class="flex items-center justify-between w-full">
                        <div class="flex items-center gap-2 force-inter">
                            {!! $languageSVG($language->code) !!}
                            <span>{{ $language->label }}</span>
                        </div>
                        <template x-if="selectedLang === '{{ $language->code }}'">
                            <x-filament::icon name="heroicon-m-check" class="h-4 w-4 text-primary-500" />
                        </template>
                    </div>
                </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
