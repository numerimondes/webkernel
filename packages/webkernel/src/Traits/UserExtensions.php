<?php

namespace Webkernel\Traits;

use Exception;
use Log;
use Webkernel\Models\Language;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait UserExtensions
{
    protected $extraFillable = [
        'username', 'mobile', 'whatsapp', 'user_lang', 'timezone',
        'forceChangePassword', 'is_active', 'is_banned', 'created_by',
        'belongs_to', 'marketing_callable', 'marketing_whatsappable', 'marketing_smsable',
    ];

    protected $cachedFillable = null;

    // Méthode pour fusionner dynamiquement les éléments à "fillable"
    public function getFillable()
    {
        return array_merge(parent::getFillable(), $this->extraFillable);
    }

 /**
     * Définition des casts pour certains champs supplémentaires
     *
     * @return array
     */
    public function getCasts(): array
    {
        return array_merge(parent::getCasts(), [
            'forceChangePassword' => 'boolean',
            'is_active' => 'boolean',
            'is_banned' => 'boolean',
        ]);
    }

    // Méthode booted pour gérer la génération automatique du username
    protected static function booted()
    {
        static::creating(function ($user) {
            if (!$user->username) {
                $baseUsername = strtolower(str_replace(' ', '', $user->name));
                $user->username = $baseUsername;
                if (self::where('username', $user->username)->exists()) {
                    $user->username = $baseUsername . '_' . time();
                }
            }

            $user->created_by = $user->created_by ?? $user->id;
        });
    }

    // Relations avec les utilisateurs créateurs et auto-inscrits
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    public function selfRegisteredUsers(): HasMany
    {
        return $this->hasMany(self::class, 'created_by');
    }

    //public function sessions(): HasMany
    //{
    //    return $this->hasMany(Session::class);
    //}
//
    //public function historySessions(): HasMany
    //{
    //    return $this->hasMany(HistorySession::class);
    //}

    // Méthode pour obtenir le fuseau horaire de l'utilisateur
    public static function getUserTimezone(): string
    {
        return auth()->check() ? auth()->user()->timezone ?? config('app.timezone') : config('app.timezone');
    }

    // Récupérer le code de langue actuel de l'utilisateur
    public function getCurrentUserLanguageCode(): string
    {
        try {
            $language = Language::find($this->user_lang);
            return $language ? $language->code : 'en';
        } catch (Exception $e) {
            Log::error("Erreur lors de la récupération de la langue de l'utilisateur : " . $e->getMessage());
            return 'en';
        }
    }
}
