<?php
namespace Webkernel\Filament\Pages;

use BackedEnum;
use Filament\Actions;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Webkernel\Models\PlatformSetting;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Concerns\InteractsWithForms;
use UnitEnum;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) PlatformSetting::count();
    }

    public static function getNavigationGroup(): ?string
    {
        return lang('system_menu_core_settings');
    }
    protected string $view = 'webkernel::filament.pages.setting';
    public static function getNavigationLabel(): string
    {
        return 'Settings';
    }

    protected static ?int $navigationSort = 1;

    protected array $slugToCategory = [];
    protected ?Collection $allSettingsData = null;

    public ?array $data = [];
    public string $search = '';
    public string $navigation_style = 'sidebar';
    public int $sections_per_row = 1;
    public int $fields_per_section = 2;

    public function getTitle(): string|Htmlable
    {
        return 'Platform Settings';
    }

    public function mount(): void
    {
        $this->fillForm();
        $this->form->fill($this->data);
    }

    protected function fillForm(): void
    {
        $tenant = Filament::getTenant();
        $tenantId = $tenant ? $tenant->id : 1;

        $allSettings = PlatformSetting::where('tenant_id', $tenantId)
            ->where('value->is_editable', true)
            ->orderBy('category')
            ->orderByRaw("JSON_EXTRACT(value, '$.display_order') ASC")
            ->get();

        // Store all settings for search functionality
        $this->allSettingsData = $allSettings;

        $grouped = $allSettings->groupBy('category');
        $mergedSettings = [];

        foreach ($grouped as $category => $settings) {
            foreach ($settings as $setting) {
                $settingData = is_string($setting->value) ? json_decode($setting->value, true) : $setting->value;
                $value = $settingData['value'] ?? null;

                if (is_array($value) && isset($value[0]['sub_key'])) {
                    foreach ($value as $subSetting) {
                        $subKey = $setting->key . '_' . $subSetting['sub_key'];
                        $mergedSettings[$category][$subKey] = $subSetting['value'] ?? null;
                    }
                } else {
                    $mergedSettings[$category][$setting->key] = $value;
                }
            }
        }

        $this->data = $mergedSettings;
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                $this->getSearchSection(),
                $this->getSettingsTabs(),
            ])
            ->statePath('data');
    }

    protected function getSearchSection(): Section
    {
        $categoriesCount = $this->allSettingsData ? $this->allSettingsData->groupBy('category')->count() : 1;

        return Section::make()
            ->schema([
                Grid::make(4)
                    ->schema([
                        TextInput::make('search')
                            ->label('Search Settings')
                            ->placeholder('Type to search...')
                            ->live(debounce: 300)
                            ->prefixIcon('heroicon-m-magnifying-glass')
                            ->columnSpan(2)
                            ->afterStateUpdated(fn () => $this->refreshSettingsDisplay()),

                        Select::make('navigation_style')
                            ->label('Layout Style')
                            ->options([
                                'sidebar' => 'Sidebar Navigation',
                                'horizontal' => 'Horizontal Tabs',
                                'grid' => 'Grid Layout',
                            ])
                            ->default('sidebar')
                            ->live()
                            ->selectablePlaceholder(false)
                            ->afterStateUpdated(fn () => $this->refreshSettingsDisplay()),

                        Select::make('sections_per_row')
                            ->label('Sections per Row')
                            ->options(array_combine(
                                range(1, min($categoriesCount, 3)),
                                range(1, min($categoriesCount, 3))
                            ))
                            ->default(1)
                            ->live()
                            ->selectablePlaceholder(false)
                            ->visible(fn () => $this->navigation_style === 'grid')
                            ->afterStateUpdated(fn () => $this->refreshSettingsDisplay()),
                    ]),

                Grid::make(2)
                    ->schema([
                        Select::make('fields_per_section')
                            ->label('Fields per Section')
                            ->options([
                                1 => '1 Column',
                                2 => '2 Columns',
                            ])
                            ->default(2)
                            ->live()
                            ->selectablePlaceholder(false)
                            ->visible(fn () => in_array($this->navigation_style, ['grid', 'horizontal']))
                            ->afterStateUpdated(fn () => $this->refreshSettingsDisplay()),
                    ])
                    ->visible(fn () => in_array($this->navigation_style, ['grid', 'horizontal'])),
            ])
            ->columnSpanFull()
            ->compact();
    }

    public function refreshSettingsDisplay(): void
    {
        // This method will be called when search or layout options change
        // The form will automatically re-render with the new settings
    }

    protected function getSettingsTabs(): Tabs|Grid
    {
        $tenant = Filament::getTenant();
        $tenantId = $tenant ? $tenant->id : 1;

        $allSettings = PlatformSetting::where('tenant_id', $tenantId)
            ->where('value->is_editable', true)
            ->orderBy('category')
            ->orderByRaw("JSON_EXTRACT(value, '$.display_order') ASC")
            ->get();

        $grouped = $allSettings->groupBy('category');

        // Filter by search if provided
        if ($this->search) {
            $grouped = $this->filterSettingsBySearch($grouped);
        }

        if ($this->navigation_style === 'grid') {
            return $this->getGridLayout($grouped);
        }

        return $this->getTabsLayout($grouped);
    }

    protected function filterSettingsBySearch(Collection $grouped): Collection
    {
        $searchLower = mb_strtolower($this->search);
        $filtered = collect();

        foreach ($grouped as $category => $settings) {
            $matchingSettings = $settings->filter(function ($setting) use ($searchLower, $category) {
                $settingData = is_string($setting->value) ? json_decode($setting->value, true) : $setting->value;
                $label = $settingData['label_key'] ?? Str::of($setting->key)->title()->replace('_', ' ');
                $key = $setting->key;

                $labelLower = mb_strtolower($label);
                $keyLower = mb_strtolower($key);
                $categoryLower = mb_strtolower($category);

                return str_contains($labelLower, $searchLower) ||
                       str_contains($keyLower, $searchLower) ||
                       str_contains($categoryLower, $searchLower);
            });

            if ($matchingSettings->isNotEmpty()) {
                $filtered->put($category, $matchingSettings);
            }
        }

        return $filtered;
    }

    protected function getGridLayout(Collection $grouped): Grid
    {
        $sections = [];

        foreach ($grouped as $category => $settings) {
            $sections[] = Section::make($this->getCategoryLabel($category))
                ->description('Configure ' . strtolower($this->getCategoryLabel($category)) . ' settings')
                ->icon($this->getCategoryIcon($category))
                ->schema([
                    Grid::make($this->fields_per_section)
                        ->schema($this->generateFieldsForCategory($settings))
                ])
                ->collapsible()
                ->persistCollapsed()
                ->compact();
        }

        return Grid::make($this->sections_per_row)
            ->schema($sections)
            ->columnSpanFull();
    }

    protected function getTabsLayout(Collection $grouped): Tabs
    {
        $tabs = [];
        $this->slugToCategory = [];

        foreach ($grouped as $category => $settings) {
            $slug = $this->slugifyCategory($category);
            $this->slugToCategory[$slug] = $category;

            $tabs[] = Tab::make($slug)
                ->label($this->getCategoryLabel($category))
                ->icon($this->getCategoryIcon($category))
                ->schema([
                    Fieldset::make($this->getCategoryLabel($category))
                        ->schema([
                            Grid::make($this->fields_per_section)
                                ->schema($this->generateFieldsForCategory($settings))
                        ])
                ]);
        }

        return Tabs::make('settings_tabs')
            ->tabs($tabs)
            ->persistTabInQueryString('tab')
            ->columnSpanFull();
    }

    protected function slugifyCategory(string $category): string
    {
        // Replace :: with - and make it URL-friendly
        $slug = str_replace('::', '-', $category);
        $slug = str_replace('_', '-', $slug);
        $slug = strtolower($slug);
        // Remove any special characters except hyphens
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        // Remove multiple consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        // Remove leading/trailing hyphens
        return trim($slug, '-');
    }

    protected function deslugifyCategory(string $slug): ?string
    {
        return $this->slugToCategory[$slug] ?? null;
    }

    protected function generateFieldsForCategory(Collection $settings): array
    {
        $fields = [];

        foreach ($settings as $setting) {
            $settingData = is_string($setting->value) ? json_decode($setting->value, true) : $setting->value;
            $value = $settingData['value'] ?? null;
            $label = $settingData['label_key'] ?? Str::of($setting->key)->title()->replace('_', ' ');
            $key = $setting->key;

            // Search filtering is now handled in filterSettingsBySearch method
            // No need to filter here again

            if (is_array($value) && isset($value[0]['sub_key'])) {
                foreach ($value as $subSetting) {
                    $fields[] = $this->generateSubField($setting, $subSetting);
                }
            } else {
                $fields[] = $this->generateField($setting, $settingData);
            }
        }

        return $fields;
    }

    protected function generateSubField(PlatformSetting $setting, array $subSetting)
    {
        $label = $subSetting['label_key'] ?? Str::of($setting->key . '_' . $subSetting['sub_key'])->title()->replace('_', ' ');
        $name = "{$setting->category}.{$setting->key}_{$subSetting['sub_key']}";
        $type = $subSetting['type'] ?? 'string';
        $required = $subSetting['required'] ?? false;
        $constraints = $subSetting['constraints'] ?? [];
        $description = $subSetting['description_key'] ?? null;

        return $this->createFieldByType($name, $label, $type, $constraints, $required, $description);
    }

    protected function generateField(PlatformSetting $setting, array $settingData)
    {
        $label = $settingData['label_key'] ?? Str::of($setting->key)->title()->replace('_', ' ');
        $name = "{$setting->category}.{$setting->key}";
        $type = $settingData['type'] ?? 'string';
        $required = $settingData['required'] ?? false;
        $constraints = $settingData['constraints'] ?? [];
        $description = $settingData['description_key'] ?? null;

        $field = $this->createFieldByType($name, $label, $type, $constraints, $required, $description);

        if ($setting->key === 'PLATFORM_LICENCE') {
            $field = $field->password()->revealable(true);
        }

        return $field;
    }

    protected function createFieldByType(string $name, string $label, string $type, array $constraints, bool $required, ?string $description = null)
{
    if ($type === 'number') {
        // Critères pour choisir Slider ou TextInput
        $hasRange = isset($constraints['min']) && isset($constraints['max']);
        $rangeSize = $hasRange ? ($constraints['max'] - $constraints['min']) : null;

        // Par exemple, si on a un min/max défini et la plage est raisonnable, on fait un Slider
        if ($hasRange && $rangeSize !== null && $rangeSize <= 1000) {
            $field = Slider::make($name)
                ->label($label)
                ->range(minValue: $constraints['min'], maxValue: $constraints['max'])
                ->required($required)
                ->fillTrack()
                ->pips(density: 5)
                ->step($constraints['step'] ?? 1);
        } else {
            // Sinon TextInput numérique
            $field = TextInput::make($name)
                ->label($label)
                ->numeric()
                ->required($required)
                ->minValue($constraints['min'] ?? null)
                ->maxValue($constraints['max'] ?? null)
                ->step($constraints['step'] ?? 1);
        }
    } else {
        $field = match ($type) {
            'string' => TextInput::make($name)
                ->label($label)
                ->required($required)
                ->maxLength($constraints['max_length'] ?? 255)
                ->placeholder($constraints['placeholder'] ?? null),

            'text' => Textarea::make($name)
                ->label($label)
                ->required($required)
                ->rows($constraints['rows'] ?? 3)
                ->maxLength($constraints['max_length'] ?? 1000)
                ->placeholder($constraints['placeholder'] ?? null),

            'slider' => Slider::make($name)
                ->label($label)
                ->range(minValue: $constraints['min'] ?? 0, maxValue: $constraints['max'] ?? 100)
                ->required($required)
                ->fillTrack()
                ->pips(density: 5)
                ->step($constraints['step'] ?? 1),

            'boolean' => Toggle::make($name)
                ->label($label)
                ->required($required)
                ->inline(false),

            'color' => ColorPicker::make($name)
                ->label($label)
                ->required($required)
                ->placeholder($constraints['placeholder'] ?? '#000000'),

            'select' => Select::make($name)
                ->label($label)
                ->options(array_combine($constraints['options'] ?? [], $constraints['options'] ?? []))
                ->required($required)
                ->searchable($constraints['searchable'] ?? true)
                ->placeholder('Select an option'),

            'image' => FileUpload::make($name)
                ->label($label)
                ->image()
                ->directory('settings')
                ->disk($constraints['disk'] ?? 'public')
                ->visibility('public')
                ->maxSize($constraints['max_size'] ?? 2048)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                ->required($required),

            'file' => FileUpload::make($name)
                ->label($label)
                ->directory('settings')
                ->disk($constraints['disk'] ?? 'public')
                ->visibility('public')
                ->maxSize($constraints['max_size'] ?? 10240)
                ->acceptedFileTypes($constraints['accepted_types'] ?? [])
                ->required($required),

            default => TextInput::make($name)
                ->label($label)
                ->required($required)
                ->maxLength(255),
        };
    }

    if ($description) {
        $field->helperText($description);
    }

    return $field;
}

    public function updatedSearch(): void
    {
        // This will trigger a re-render of the form with filtered settings
    }

    public function updatedNavigationStyle(): void
    {
        // This will trigger a re-render with the new layout style
    }

    public function updatedSectionsPerRow(): void
    {
        // This will trigger a re-render with the new grid layout
    }

    public function updatedFieldsPerSection(): void
    {
        // This will trigger a re-render with the new field layout
    }

    protected function checkLicence(string $licence): bool
    {
        return true;
    }

    public function save(): void
    {
        $tenant = Filament::getTenant();
        $tenantId = $tenant ? $tenant->id : 1;
        $state = $this->form->getState();

        try {
            foreach ($state as $category => $categorySettings) {
                if (is_array($categorySettings)) {
                    foreach ($categorySettings as $key => $value) {
                        if ($key === 'PLATFORM_LICENCE' && !$this->checkLicence($value)) {
                            Notification::make()
                                ->title('Invalid License')
                                ->body('The license key provided is not valid.')
                                ->danger()
                                ->duration(5000)
                                ->send();
                            return;
                        }

                        if (str_contains($key, '_') && $this->isSubKeySetting($key, $tenantId)) {
                            $this->updateSubKeySetting($key, $value, $tenantId);
                        } else {
                            $this->updateSetting($key, $value, $tenantId);
                        }
                    }
                }
            }

            Notification::make()
                ->title('Settings Saved')
                ->body('All settings have been updated successfully.')
                ->success()
                ->duration(5000)
                ->send();

        } catch (\Exception $e) {
            \Log::error('Error saving settings:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->title('Save Error')
                ->body('An error occurred while saving: ' . $e->getMessage())
                ->danger()
                ->duration(10000)
                ->send();
        }
    }

    protected function updateSetting(string $key, $newValue, int $tenantId): void
    {
        $setting = PlatformSetting::where('tenant_id', $tenantId)->where('key', $key)->first();
        if (!$setting) {
            return;
        }

        $settingData = is_string($setting->value) ? json_decode($setting->value, true) : $setting->value;
        $settingData['value'] = $newValue;

        $setting->update([
            'value' => $settingData,
            'edited_by' => auth()->id(),
        ]);

        if (method_exists($setting, 'clearCache')) {
            $setting->clearCache();
        }
    }

    protected function isSubKeySetting(string $key, int $tenantId): bool
    {
        $parts = explode('_', $key);
        if (count($parts) < 2) {
            return false;
        }

        $subKey = array_pop($parts);
        $parentKey = implode('_', $parts);

        $parentSetting = PlatformSetting::where('tenant_id', $tenantId)->where('key', $parentKey)->first();
        if (!$parentSetting) {
            return false;
        }

        $settingData = is_string($parentSetting->value) ? json_decode($parentSetting->value, true) : $parentSetting->value;
        $value = $settingData['value'] ?? null;

        return is_array($value) && isset($value[0]['sub_key']);
    }

    protected function updateSubKeySetting(string $key, $value, int $tenantId): void
    {
        $parts = explode('_', $key);
        $subKey = array_pop($parts);
        $parentKey = implode('_', $parts);

        $parentSetting = PlatformSetting::where('tenant_id', $tenantId)->where('key', $parentKey)->first();
        if (!$parentSetting) {
            return;
        }

        $settingData = is_string($parentSetting->value) ? json_decode($parentSetting->value, true) : $parentSetting->value;

        if (isset($settingData['value']) && is_array($settingData['value'])) {
            foreach ($settingData['value'] as &$subSetting) {
                if (($subSetting['sub_key'] ?? null) === $subKey) {
                    $subSetting['value'] = $value;
                    break;
                }
            }

            $parentSetting->update([
                'value' => $settingData,
                'edited_by' => auth()->id(),
            ]);

            if (method_exists($parentSetting, 'clearCache')) {
                $parentSetting->clearCache();
            }
        }
    }

    protected function getCategoryLabel(string $category): string
    {
        $labels = [
            'general' => 'General',
            'branding' => 'Branding',
            'email' => 'Email',
            'security' => 'Security',
            'notifications' => 'Notifications',
            'api' => 'API',
            'analytics' => 'Analytics',
            'social' => 'Social Media',
            'payment' => 'Payment',
            'system' => 'System',
        ];

        return $labels[$category] ?? Str::of($category)->title()->replace('_', ' ')->replace('::', ' ');
    }

    protected function getCategoryIcon(string $category): string
    {
        $icons = [
            'general' => 'heroicon-o-cog-6-tooth',
            'branding' => 'heroicon-o-paint-brush',
            'email' => 'heroicon-o-envelope',
            'security' => 'heroicon-o-shield-check',
            'notifications' => 'heroicon-o-bell',
            'api' => 'heroicon-o-code-bracket',
            'analytics' => 'heroicon-o-chart-bar',
            'social' => 'heroicon-o-share',
            'payment' => 'heroicon-o-credit-card',
            'system' => 'heroicon-o-server',
        ];

        return $icons[$category] ?? 'heroicon-o-folder';
    }

    protected function getActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Save Settings')
                ->color('primary')
                ->icon('heroicon-o-check')
                ->action('save'),
        ];
    }
}
