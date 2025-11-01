<?php

declare(strict_types=1);

namespace Platform\EnjoyTheWorld\Filament\Resources\Services\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Platform\EnjoyTheWorld\Filament\Resources\Services\ServiceResource;
use Platform\EnjoyTheWorld\Models\Service;

class EditService extends EditRecord
{
  protected static string $resource = ServiceResource::class;
  /**
   * Get breadcrumb
   *
   * @return string
   */
  public function getBreadcrumb(): string
  {
    return __('Edit');
  }

  /**
   * Get title
   *
   * @return string
   */
  public function getTitle(): string
  {
    return __('Edit Service');
  }

  /**
   * Get header actions
   *
   * @return array<int, \Filament\Actions\Action>
   */
  protected function getHeaderActions(): array
  {
    return [DeleteAction::make()->label(__('Delete'))->requiresConfirmation()];
  }

  /**
   * Mutate form data before fill
   *
   * @param array<string, mixed> $data
   * @return array<string, mixed>
   */
  protected function mutateFormDataBeforeFill(array $data): array
  {
    $record = $this->getRecord();

    if (!$record instanceof Service) {
      return $data;
    }

    // Load translations into individual fields
    $languages = ['en', 'fr', 'es', 'de', 'it'];

    foreach ($languages as $code) {
      $translation = $record->translation($code);

      if ($translation) {
        $data["translation_{$code}_title"] = $translation->title;
        $data["translation_{$code}_description"] = $translation->description;
      }
    }

    return $data;
  }

  /**
   * Mutate form data before save
   *
   * @param array<string, mixed> $data
   * @return array<string, mixed>
   */
  protected function mutateFormDataBeforeSave(array $data): array
  {
    // Extract translation fields
    $translations = [];
    $languages = ['en', 'fr', 'es', 'de', 'it'];

    foreach ($languages as $code) {
      $titleKey = "translation_{$code}_title";
      $descKey = "translation_{$code}_description";

      if (isset($data[$titleKey]) || isset($data[$descKey])) {
        $translations[] = [
          'language_code' => $code,
          'title' => $data[$titleKey] ?? '',
          'description' => $data[$descKey] ?? '',
        ];

        // Remove from main data array
        unset($data[$titleKey], $data[$descKey]);
      }
    }

    // Store translations for after save
    $this->translationsToSync = $translations;

    return $data;
  }

  /**
   * Translations to sync
   *
   * @var array<int, array<string, string>>
   */
  protected array $translationsToSync = [];

  /**
   * After save hook
   *
   * @return void
   */
  protected function afterSave(): void
  {
    $record = $this->getRecord();

    if (!$record instanceof Service || empty($this->translationsToSync)) {
      return;
    }

    foreach ($this->translationsToSync as $translationData) {
      // Skip empty translations
      if (empty($translationData['title']) && empty($translationData['description'])) {
        continue;
      }

      $record->translations()->updateOrCreate(
        [
          'language_code' => $translationData['language_code'],
        ],
        [
          'title' => $translationData['title'],
          'description' => $translationData['description'],
        ],
      );
    }
  }
}
