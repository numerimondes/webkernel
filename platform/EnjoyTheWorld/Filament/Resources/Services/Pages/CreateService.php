<?php

declare(strict_types=1);

namespace Platform\EnjoyTheWorld\Filament\Resources\Services\Pages;

use Filament\Resources\Pages\CreateRecord;
use Platform\EnjoyTheWorld\Filament\Resources\Services\ServiceResource;
use Platform\EnjoyTheWorld\Models\Service;

class CreateService extends CreateRecord
{
  protected static string $resource = ServiceResource::class;
  /**
   * Get breadcrumb
   *
   * @return string
   */
  public function getBreadcrumb(): string
  {
    return __('Create');
  }

  /**
   * Get title
   *
   * @return string
   */
  public function getTitle(): string
  {
    return __('Create Service');
  }

  /**
   * Mutate form data before create
   *
   * @param array<string, mixed> $data
   * @return array<string, mixed>
   */
  protected function mutateFormDataBeforeCreate(array $data): array
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

    // Store translations for after create
    $this->translationsToSync = $translations;

    // Ensure provider_id is set from the relation manager owner
    if (!isset($data['provider_id']) && $this->getOwnerRecord()) {
      $data['provider_id'] = $this->getOwnerRecord()->getKey();
    }

    return $data;
  }

  /**
   * Translations to sync
   *
   * @var array<int, array<string, string>>
   */
  protected array $translationsToSync = [];

  /**
   * After create hook
   *
   * @return void
   */
  protected function afterCreate(): void
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

      $record->translations()->create([
        'language_code' => $translationData['language_code'],
        'title' => $translationData['title'],
        'description' => $translationData['description'],
      ]);
    }
  }

  /**
   * Get owner record (provider)
   *
   * @return \Illuminate\Database\Eloquent\Model|null
   */
  protected function getOwnerRecord(): ?\Illuminate\Database\Eloquent\Model
  {
    // This will be set by the relation manager
    return $this->ownerRecord ?? null;
  }
}
