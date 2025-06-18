<?php

namespace Webkernel\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Schema;

class Language extends Model
{
    protected $table = 'webkernel_lang';

    protected $fillable = [
        'code',
        'ISO',
        'label',
        'is_active',
        'belongs_to',
    ];

    public $timestamps = true;

    // 🔗 Lié aux utilisateurs (si tu utilises ce lien)
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_lang');
    }

    // 🔗 Lié aux traductions
    public function translations(): HasMany
    {
        return $this->hasMany(LanguageTranslation::class, 'lang');
    }

    public static function getActiveLanguages()
    {
        if (!Schema::hasTable('webkernel_lang')) {
            return null;
        }

        return self::where('is_active', true)->get();
    }
    public function languageTranslations()
    {
        return $this->hasMany(LanguageTranslation::class, 'lang');
    }
}

