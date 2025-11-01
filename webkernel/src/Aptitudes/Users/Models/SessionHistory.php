<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $session_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $login_at
 * @property \Illuminate\Support\Carbon|null $logout_at
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SessionHistory active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SessionHistory inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SessionHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SessionHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SessionHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SessionHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SessionHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SessionHistory whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SessionHistory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SessionHistory whereLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SessionHistory whereLogoutAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SessionHistory whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SessionHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SessionHistory whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SessionHistory whereUserId($value)
 * @mixin \Eloquent
 */
class SessionHistory extends Model
{
    protected $table = 'session_history';

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'login_at',
        'logout_at',
        'is_active',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour les sessions actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les sessions inactives
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
