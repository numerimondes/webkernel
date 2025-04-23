<?php

namespace Webkernel\Models;

use Illuminate\Support\Collection;
use Filament\Forms\Components\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
class LanguageTranslation extends Model
{
    protected $table = 'webkernel_lang_words';

    protected $fillable = [
        'lang',
        'lang_ref',
        'translation',
        'app',
        'theme',
        'belongs_to',
    ];
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public $timestamps = true;

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'lang');
    }

    public static function getTranslation(string $key, string $locale): ?string
    {
        $language = Language::where('code', $locale)->first();

        if (!$language) {
            return null;
        }

        $entry = self::where('lang_ref', $key)
            ->where('lang', $language->id)
            ->first();

        return $entry?->translation;
    }

    // App\Models\LanguageTranslation.php
    public static function getTranslationsForKey(string $key): Collection
    {
        return self::where('lang_ref', $key)
            ->with('language')
            ->get()
            ->keyBy('lang');
    }


    public function isDeletable(): bool
    {
        // Vérifie si created_at est bien défini
        if (is_null($this->created_at)) {
            return true; // Peut être supprimé si la date est absente
        }

        // Assure-toi que created_at est bien un Carbon
        $createdAt = $this->created_at instanceof Carbon
            ? $this->created_at
            : Carbon::parse($this->created_at);

        // Vérifie si l'enregistrement a été créé dans la dernière minute
        return $createdAt->diffInMinutes(now()) <= 1;
    }

}
