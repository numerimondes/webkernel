<?php

namespace Webkernel\Observers;

use Webkernel\Models\LanguageTranslation;
use Illuminate\Support\Facades\Cache;

class LanguageTranslationObserver
{
    /**
     * Handle the LanguageTranslation "created" event.
     */
    public function created(LanguageTranslation $languageTranslation): void
    {
        Cache::forget('translations_' . $languageTranslation->key);
    }

    /**
     * Handle the LanguageTranslation "updated" event.
     */
    public function updated(LanguageTranslation $languageTranslation): void
    {
        Cache::forget('translations_' . $languageTranslation->key);
    }

    /**
     * Handle the LanguageTranslation "deleted" event.
     */
    public function deleted(LanguageTranslation $languageTranslation): void
    {
        Cache::forget('translations_' . $languageTranslation->key);
    }

    /**
     * Handle the LanguageTranslation "restored" event.
     */
    public function restored(LanguageTranslation $languageTranslation): void
    {
        Cache::forget('translations_' . $languageTranslation->key);
    }

    /**
     * Handle the LanguageTranslation "force deleted" event.
     */
    public function forceDeleted(LanguageTranslation $languageTranslation): void
    {
        Cache::forget('translations_' . $languageTranslation->key);
    }
}
