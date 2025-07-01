<?php

namespace Webkernel\Filament\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Webkernel\Models\PlatformSetting;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Support\Components\Component;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Concerns\InteractsWithForms;

trait SettingsPageGenerator
{
    /**
     * Get available categories for settings
     *
     * @return array<string>
     */
    public static function getAvailableCategories(): array
    {
        $tenant = filament()->getTenant();
        $tenantId = $tenant?->id ?? 1;

        return PlatformSetting::where('tenant_id', $tenantId)
            ->where('value->is_editable', true)
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * Generate settings pages for all categories
     *
     * @return array<array{class: string, route: string}>
     */
    public static function generateSettingsPages(): array
    {
        $categories = static::getAvailableCategories();
        $pages = [];

        foreach ($categories as $category) {
            $className = static::getCategoryClassName($category);

            if (!class_exists($className)) {
                static::createDynamicPageClass($className, $category);
            }

            $pages[] = [
                'class' => $className,
                'route' => '/' . Str::slug($category) . '-settings',
            ];
        }

        return $pages;
    }

    /**
     * Get the class name for a category
     *
     * @param string $category
     * @return string
     */
    public static function getCategoryClassName(string $category): string
    {
        return __NAMESPACE__ . '\\' . Str::studly($category) . 'Settings';
    }

    /**
     * Create a dynamic page class for a category
     *
     * @param string $className
     * @param string $category
     * @return void
     */
    public static function createDynamicPageClass(string $className, string $category): void
    {
        if (class_exists($className)) {
            return;
        }

        $shortClassName = class_basename($className);
        $isGeneral = $category === 'general';
        $slug = Str::slug($category) . '-settings';
        $icon = static::getCategoryIcon($category);
        $label = static::getCategoryLabel($category);
        $sortOrder = static::getCategorySortOrder($category);

        $classCode = <<<PHP
        namespace Webkernel\Filament\Pages;

        class {$shortClassName} extends BaseSettingsPage
        {
            public static ?string \$slug = '{$slug}';

            public static function getNavigationIcon(): ?string
            {
                return '{$icon}';
            }

            public static function getNavigationBadge(): ?string
            {
                \$tenant = filament()->getTenant();
                \$tenantId = \$tenant ? \$tenant->id : 1;
                return (string) PlatformSetting::where('tenant_id', \$tenantId)
                    ->where('category', '{$category}')
                    ->count();
            }

            public static function getNavigationLabel(): string
            {
                return '{$label}';
            }

            public static function getNavigationParentItem(): ?string
            {
                return 'Settings';
            }

            protected static ?int \$navigationSort = {$sortOrder};

            protected function getCategoryKey(): string
            {
                return '{$category}';
            }

            public static function canAccess(): bool
            {
                \$tenant = filament()->getTenant();
                \$tenantId = \$tenant ? \$tenant->id : 1;
                return PlatformSetting::where('tenant_id', \$tenantId)
                    ->where('category', '{$category}')
                    ->where('value->is_editable', true)
                    ->exists();
            }
        }
        PHP;

        // Validate class code before evaluation
        if (!empty($classCode)) {
            try {

            } catch (\ParseError $e) {
                \Log::error('Error creating dynamic page class:', [
                    'class' => $className,
                    'category' => $category,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }
}

abstract class BaseSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;
    use SettingsPageGenerator;

    protected string $view = 'webkernel::filament.pages.setting';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];
    public string $search = '';
    public int $fields_per_section = 2;

    protected ?Collection $categorySettingsData = null;

    abstract protected function getCategoryKey(): string;

    public static function getNavigationGroup(): ?string
    {
        return lang('system_menu_core_settings');
    }

    public function getTitle(): string|Htmlable
    {
        return static::getCategoryLabel($this->getCategoryKey()) . ' Settings';
    }

    public function mount(): void
    {
        $this->fillForm();
        $this->form->fill($this->data);
    }

    protected function fillForm(): void
    {
        $tenant = filament()->getTenant();
        $tenantId = $tenant?->id ?? 1;

        $categorySettings = PlatformSetting::where('tenant_id', $tenantId)
            ->where('category', $this->getCategoryKey())
            ->where('value->is_editable', true)
            ->orderByRaw("JSON_EXTRACT(value, '$.display_order') ASC")
            ->get();

        $this->categorySettingsData = $categorySettings;

        $mergedSettings = [];
        foreach ($categorySettings as $setting) {
            $settingData = is_string($setting->value) ? json_decode($setting->value, true) : $setting->value;
            if (!is_array($settingData)) {
                continue;
            }

            $value = $settingData['value'] ?? null;

            if (is_array($value) && isset($value[0]['sub_key'])) {
                foreach ($value as $subSetting) {
                    $subKey = $setting->key . '_' . ($subSetting['sub_key'] ?? '');
                    $mergedSettings[$subKey] = $subSetting['value'] ?? null;
                }
            } else {
                $mergedSettings[$setting->key] = $value;
            }
        }

        $this->data = $mergedSettings;
    }


    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                $this->getSearchSection(),
                $this->getSettingsSection(),
            ])
            ->statePath('data');
    }



    protected function getSearchSection(): Section
    {
        return Section::make()
            ->schema([
                Grid::make(3)
                    ->schema([
                        TextInput::make('search')
                            ->label(lang('Search Settings'))
                            ->placeholder(lang('Type to search...'))
                            ->live(debounce: 300)
                            ->prefixIcon('heroicon-m-magnifying-glass')
                            ->columnSpan(2)
                            ->afterStateUpdated(fn () => $this->refreshSettingsDisplay()),

                        Select::make('fields_per_section')
                            ->label(lang('Fields per Section'))
                            ->options([
                                1 => lang('1 Column'),
                                2 => lang('2 Columns'),
                                3 => lang('3 Columns'),
                            ])
                            ->default(2)
                            ->live()
                            ->selectablePlaceholder(false)
                            ->afterStateUpdated(fn () => $this->refreshSettingsDisplay()),
                    ]),
            ])
            ->columnSpanFull()
            ->compact();
    }

    public function refreshSettingsDisplay(): void
    {
        // Implementation can be added here if needed
    }

    protected function getSettingsSection(): Section
    {
        $settings = $this->categorySettingsData ?? collect();

        if ($this->search) {
            $settings = $this->filterSettingsBySearch($settings);
        }

        return Section::make(static::getCategoryLabel($this->getCategoryKey()))
            ->description(lang('Configure') . ' ' . strtolower(static::getCategoryLabel($this->getCategoryKey())) . ' ' . lang('settings'))
            ->icon(static::getCategoryIcon($this->getCategoryKey()))
            ->schema([
                Grid::make($this->fields_per_section)
                    ->schema($this->generateFieldsForCategory($settings))
            ])
            ->collapsible()
            ->persistCollapsed()
            ->compact()
            ->columnSpanFull();
    }

    protected function filterSettingsBySearch(Collection $settings): Collection
    {
        $searchLower = mb_strtolower($this->search);

        return $settings->filter(function ($setting) use ($searchLower) {
            $settingData = is_string($setting->value) ? json_decode($setting->value, true) : $setting->value;
            if (!is_array($settingData)) {
                return false;
            }

            $label = $settingData['label_key'] ?? Str::of($setting->key)->title()->replace('_', ' ');
            $key = $setting->key;

            return str_contains(mb_strtolower($label), $searchLower) ||
                   str_contains(mb_strtolower($key), $searchLower);
        });
    }

    /**
     * @param Collection<int, PlatformSetting> $settings
     * @return array<Component>
     */
    protected function generateFieldsForCategory(Collection $settings): array
    {
        $fields = [];

        foreach ($settings as $setting) {
            $settingData = is_string($setting->value) ? json_decode($setting->value, true) : $setting->value;
            if (!is_array($settingData)) {
                continue;
            }

            $value = $settingData['value'] ?? null;

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

    protected function generateSubField(PlatformSetting $setting, array $subSetting): Component
    {
        $label = $subSetting['label_key'] ?? Str::of($setting->key . '_' . ($subSetting['sub_key'] ?? ''))->title()->replace('_', ' ');
        $name = $setting->key . '_' . ($subSetting['sub_key'] ?? '');
        $type = $subSetting['type'] ?? 'string';
        $required = $subSetting['required'] ?? false;
        $constraints = $subSetting['constraints'] ?? [];
        $description = $subSetting['description_key'] ?? null;

        return $this->createFieldByType($name, $label, $type, $constraints, $required, $description);
    }

    protected function generateField(PlatformSetting $setting, array $settingData): Component
    {
        $label = $settingData['label_key'] ?? Str::of($setting->key)->title()->replace('_', ' ');
        $name = $setting->key;
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

    protected function createFieldByType(string $name, string $label, string $type, array $constraints, bool $required, ?string $description = null): Component
    {
        $field = match ($type) {
            'number' => $this->createNumberField($name, $label, $constraints, $required),
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
                ->minValue($constraints['min'] ?? 0)
                ->maxValue($constraints['max'] ?? 100)
                ->required($required)
                ->fillTrack()
                ->pips()
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
                ->options($constraints['options'] ?? [])
                ->required($required)
                ->searchable($constraints['searchable'] ?? true)
                ->placeholder(lang('Select an option')),
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

        if ($description) {
            $field->helperText($description);
        }

        return $field;
    }

    protected function createNumberField(string $name, string $label, array $constraints, bool $required): Component
    {
        $hasRange = isset($constraints['min']) && isset($constraints['max']);
        $rangeSize = $hasRange ? ($constraints['max'] - $constraints['min']) : null;

        if ($hasRange && $rangeSize !== null && $rangeSize <= 1000) {
            return Slider::make($name)
                ->label($label)
                ->minValue($constraints['min'])
                ->maxValue($constraints['max'])
                ->required($required)
                ->fillTrack()
                ->pips()
                ->step($constraints['step'] ?? 1);
        }

        return TextInput::make($name)
            ->label($label)
            ->numeric()
            ->required($required)
            ->minValue($constraints['min'] ?? null)
            ->maxValue($constraints['max'] ?? null)
            ->step($constraints['step'] ?? 1);
    }

    protected function checkLicence(string $licence): bool
    {
        return true; // Implement actual license checking logic
    }

    public function save(): void
    {
        $tenant = filament()->getTenant();
        $tenantId = $tenant?->id ?? 1;
        $state = $this->form->getState();

        try {
            foreach ($state as $key => $value) {
                if ($key === 'PLATFORM_LICENCE' && !$this->checkLicence($value)) {
                    Notification::make()
                        ->title(lang('Invalid License'))
                        ->body(lang('The license key provided is not valid.'))
                        ->danger()
                        ->persistent()
                        ->send();
                    return;
                }

                if (str_contains($key, '_') && $this->isSubKeySetting($key, $tenantId)) {
                    $this->updateSubKeySetting($key, $value, $tenantId);
                } else {
                    $this->updateSetting($key, $value, $tenantId);
                }
            }

            Notification::make()
                ->title(lang('Settings Saved'))
                ->body(static::getCategoryLabel($this->getCategoryKey()) . ' ' . lang('settings have been updated successfully.'))
                ->success()
                ->persistent()
                ->send();
        } catch (\Exception $e) {
            \Log::error('Error saving settings:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->title(lang('Save Error'))
                ->body(lang('An error occurred while saving: ') . $e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }

    protected function updateSetting(string $key, $newValue, int $tenantId): void
    {
        $setting = PlatformSetting::where('tenant_id', $tenantId)
            ->where('key', $key)
            ->where('category', $this->getCategoryKey())
            ->first();

        if (!$setting) {
            return;
        }

        $settingData = is_string($setting->value) ? json_decode($setting->value, true) : $setting->value;
        if (!is_array($settingData)) {
            return;
        }

        $settingData['value'] = $newValue;

        $settingOperations = [
            'value' => $settingData,
            'edited_by' => auth()->id(),
        ];

        $setting->update($settingOperations);

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

        $parentSetting = PlatformSetting::where('tenant_id', $tenantId)
            ->where('key', $parentKey)
            ->where('category', $this->getCategoryKey())
            ->first();

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

        $parentSetting = PlatformSetting::where('tenant_id', $tenantId)
            ->where('key', $parentKey)
            ->where('category', $this->getCategoryKey())
            ->first();

        if (!$parentSetting) {
            return;
        }

        $settingData = is_string($parentSetting->value) ? json_decode($parentSetting->value, true) : $parentSetting->value;
        if (!is_array($settingData) || !isset($settingData['value']) || !is_array($settingData['value'])) {
            return;
        }

        foreach ($settingData['value'] as &$subSetting) {
            if (($subSetting['sub_key'] ?? null) === $subKey) {
                $subSetting['value'] = $value;
                break;
            }
        }

        unset($subSetting); // Unset reference to avoid issues

        $parentSetting->update([
            'value' => $settingData,
            'edited_by' => auth()->id(),
        ]);

        if (method_exists($parentSetting, 'clearCache')) {
            $parentSetting->clearCache();
        }
    }

    /**
     * @return array<string, string>
     */
    public static function getCategoryLabels(): array
    {
        return [
            'general' => lang('General'),
            'branding' => lang('Branding'),
            'email' => lang('Email'),
            'security' => lang('Security'),
            'notifications' => lang('Notifications'),
            'api' => lang('API'),
            'analytics' => lang('Analytics'),
            'social' => lang('Social Media'),
            'payment' => lang('Payment'),
            'system' => lang('System'),
            'languages' => lang('Languages'),
        ];
    }

    public static function getCategoryLabel(string $category): string
    {
        return static::getCategoryLabels()[$category] ?? Str::of($category)->title()->replace('_', ' ')->replace('::', ' ');
    }

    /**
     * @return array<string, string>
     */
    public static function getCategoryIcons(): array
    {
        return [
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
            'languages' => 'heroicon-o-language',
        ];
    }

    public static function getCategoryIcon(string $category): string
    {
        return static::getCategoryIcons()[$category] ?? 'heroicon-o-folder';
    }

    /**
     * @return array<string, int>
     */
    public static function getCategorySortOrders(): array
    {
        return [
            'general' => 1,
            'branding' => 2,
            'email' => 3,
            'security' => 4,
            'notifications' => 5,
            'api' => 6,
            'analytics' => 7,
            'social' => 8,
            'payment' => 9,
            'system' => 10,
            'languages' => 11,
        ];
    }

    public static function getCategorySortOrder(string $category): int
    {
        return static::getCategorySortOrders()[$category] ?? 99;
    }

    /**
     * @return array<Actions\Action>
     */
    protected function getActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label(lang('Save Settings'))
                ->color('primary')
                ->icon('heroicon-o-check')
                ->action('save'),
        ];
    }
}

use Filament\Navigation\NavigationItem;

class Settings extends BaseSettingsPage
{
    protected static ?string $slug = 'settings';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function getNavigationBadge(): ?string
    {
        $tenant = filament()->getTenant();
        $tenantId = $tenant?->id ?? 1;
        return (string) PlatformSetting::where('tenant_id', $tenantId)
            ->where('category', 'general')
            ->count();
    }

    public static function getNavigationLabel(): string
    {
        return lang('Settings');
    }

    protected static ?int $navigationSort = 1;

    protected function getCategoryKey(): string
    {
        return 'general';
    }

    public static function canAccess(): bool
    {
        $tenant = filament()->getTenant();
        $tenantId = $tenant?->id ?? 1;
        return PlatformSetting::where('tenant_id', $tenantId)
            ->where('category', 'general')
            ->where('value->is_editable', true)
            ->exists();
    }

    /**
     * Register all pages, including dynamic ones
     *
     * @return array<string, string>
     */
    public static function getPages(): array
    {
        $pages = static::generateSettingsPages();
        $result = [];

        foreach ($pages as $page) {
            $result[$page['route']] = $page['class'];
        }

        $result['/settings'] = static::class;

        return $result;
    }

    /**
     * Register navigation items for dynamic pages
     *
     * @return array<NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        $navigationItems = [];
        $tenant = filament()->getTenant();
        $tenantId = $tenant?->id ?? 1;

        // Add the main Settings page
        $navigationItems[] = NavigationItem::make(static::getNavigationLabel())
            ->group(static::getNavigationGroup())
            ->icon(static::getNavigationIcon())
            ->badge(static::getNavigationBadge())
            ->sort(static::$navigationSort)
            ->url(route('filament.pages.' . static::$slug))
            ->isActiveWhen(fn () => request()->routeIs('filament.pages.' . static::$slug));

        // Add dynamic pages
        $pages = static::generateSettingsPages();
        foreach ($pages as $page) {
            $class = $page['class'];
            if (class_exists($class) && method_exists($class, 'canAccess') && $class::canAccess()) {
                $navigationItems[] = NavigationItem::make($class::getNavigationLabel())
                    ->group(static::getNavigationGroup())
                    ->icon($class::getNavigationIcon())
                    ->badge($class::getNavigationBadge())
                    ->sort($class::$navigationSort)
                    ->url(route('filament.pages.' . $class::$slug))
                    ->isActiveWhen(fn () => request()->routeIs('filament.pages.' . $class::$slug));
            }
        }

        return $navigationItems;
    }
}

// Dynamic class generation for categories
if (class_exists(PlatformSetting::class)) {
    try {
        $tenant = filament()->getTenant();
        $tenantId = $tenant?->id ?? 1;

        $categories = PlatformSetting::where('tenant_id', $tenantId)
            ->where('value->is_editable', true)
            ->distinct()
            ->pluck('category')
            ->filter()
            ->reject('general')
            ->values()
            ->toArray();

        foreach ($categories as $category) {
            $className = Str::studly($category) . 'Settings';
            $fullClassName = __NAMESPACE__ . '\\' . $className;

            if (!class_exists($fullClassName)) {
                BaseSettingsPage::createDynamicPageClass($fullClassName, $category);
            }
        }
    } catch (\Exception $e) {
        \Log::error('Error generating dynamic pages:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
