<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Concerns;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\HtmlString;
use Livewire\Component as LivewireComponent;

/**
 * Base trait for all permission matrix tabs
 * Provides common functionality for checkbox lists and select all toggles
 */
trait InteractsWithTabs
{
  /**
   * Get select all toggle for specific tab
   */
  public static function getSelectAllToggle(string $category): Toggle
  {
    return Toggle::make("select_all_{$category}")
      ->label('Select All ' . ucfirst($category))
      ->helperText(fn(): HtmlString => new HtmlString("Toggle to select or deselect all {$category} privileges"))
      ->live()
      ->afterStateUpdated(function (LivewireComponent $livewire, Set $set, bool $state) use ($category): void {
        static::toggleAllPrivileges($livewire, $set, $state, $category);
      })
      ->inline(false)
      ->columnSpanFull();
  }

  /**
   * Get checkbox list grid for specific category
   *
   * @return array<int, Grid>
   */
  public static function getCheckboxListGrid(string $category): array
  {
    $privileges = static::getPrivileges();

    if (empty($privileges)) {
      return [];
    }

    $options = collect($privileges)
      ->mapWithKeys(function (array $privilege): array {
        return [$privilege['key'] => $privilege['description'] ?? $privilege['key']];
      })
      ->toArray();

    return [
      Grid::make()
        ->schema([
          CheckboxList::make("privileges_{$category}")
            ->options($options)
            ->hiddenLabel()
            ->searchable(count($options) > 10)
            ->live()
            ->afterStateUpdated(function (LivewireComponent $livewire, Set $set) use ($category): void {
              static::updateSelectAllState($livewire, $set, $category);
            })
            ->bulkToggleable()
            ->gridDirection('row')
            ->columns(2)
            ->columnSpanFull()
            ->dehydrated(fn(?array $state): bool => !blank($state)),
        ])
        ->columns(1),
    ];
  }

  /**
   * Toggle all privileges for a specific tab
   */
  protected static function toggleAllPrivileges(
    LivewireComponent $livewire,
    Set $set,
    bool $state,
    string $category,
  ): void {
    $fieldName = "privileges_{$category}";
    $checkboxComponent = collect($livewire->form->getFlatComponents())->first(
      fn($component): bool => $component instanceof CheckboxList && $component->getName() === $fieldName,
    );

    if (!$checkboxComponent) {
      return;
    }

    if ($state) {
      $set($fieldName, array_keys($checkboxComponent->getOptions()));
    } else {
      $set($fieldName, []);
    }
  }

  /**
   * Update select all toggle for specific tab
   */
  protected static function updateSelectAllState(LivewireComponent $livewire, Set $set, string $category): void
  {
    $fieldName = "privileges_{$category}";
    $checkboxComponent = collect($livewire->form->getFlatComponents())->first(
      fn($component): bool => $component instanceof CheckboxList && $component->getName() === $fieldName,
    );

    if (!$checkboxComponent) {
      return;
    }

    $allOptions = count(array_keys($checkboxComponent->getOptions()));
    $selectedOptions = count(collect($checkboxComponent->getState())->values()->unique()->toArray());

    if ($allOptions > 0 && $allOptions === $selectedOptions) {
      $set("select_all_{$category}", true);
    } else {
      $set("select_all_{$category}", false);
    }
  }

  /**
   * Get separator image configuration for the current tab
   * Returns array with path, width, and height configuration
   *
   * Example return value:
   * [
   *   'path' => public_path('images/assets/svg/resource_separator.svg'),
   *   'width' => 350,
   *   'height' => 'auto',
   * ]
   *
   * @return array<string, mixed>
   */
  abstract public static function getSeparatorConfig(): array;

  /**
   * Get privileges for current tab
   *
   * @return array<int, array<string, string>>
   */
  abstract public static function getPrivileges(): array;
}
