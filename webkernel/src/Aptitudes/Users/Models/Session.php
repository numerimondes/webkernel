<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * @property string $id
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string $payload
 * @property int $last_activity
 * @property-read string $browser
 * @property-read string $device
 * @property-read bool $is_current
 * @property-read string $last_activity_formatted
 * @property-read string $location
 * @property-read string $os
 * @property-read \App\Models\User|null $user
 * @method static Builder<static>|Session active()
 * @method static Builder<static>|Session byIp(string $ipAddress)
 * @method static Builder<static>|Session expired()
 * @method static Builder<static>|Session forUser(int $userId)
 * @method static Builder<static>|Session newModelQuery()
 * @method static Builder<static>|Session newQuery()
 * @method static Builder<static>|Session orderByActivity(string $direction = 'desc')
 * @method static Builder<static>|Session query()
 * @method static Builder<static>|Session recent(int $minutes = 60)
 * @method static Builder<static>|Session whereId($value)
 * @method static Builder<static>|Session whereIpAddress($value)
 * @method static Builder<static>|Session whereLastActivity($value)
 * @method static Builder<static>|Session wherePayload($value)
 * @method static Builder<static>|Session whereUserAgent($value)
 * @method static Builder<static>|Session whereUserId($value)
 * @mixin \Eloquent
 */
class Session extends Model
{
    protected $table = 'sessions';

    protected $fillable = [
        'id',
        'user_id',
        'ip_address',
        'user_agent',
        'payload',
        'last_activity',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'last_activity' => 'integer',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Boot the model
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }

    /**
     * Get active sessions for a user
     */
    public static function getActiveSessionsForUser(int $userId): \Illuminate\Support\Collection
    {
        $cacheKey = "user_sessions_{$userId}";

        return Cache::remember($cacheKey, 300, function () use ($userId) {
            return static::query()
                ->where('user_id', $userId)
                ->where('last_activity', '>', now()->subMinutes(config('session.lifetime', 120))->timestamp)
                ->orderBy('last_activity', 'desc')
                ->get();
        });
    }

    /**
     * Get current session
     */
    public static function getCurrentSession(): ?self
    {
        $sessionId = session()->getId();

        if (!$sessionId) {
            return null;
        }

        return static::query()
            ->where('id', $sessionId)
            ->first();
    }

    /**
     * Get session by ID
     */
    public static function getById(string $sessionId): ?self
    {
        return static::query()
            ->where('id', $sessionId)
            ->first();
    }

    /**
     * Check if session is active
     */
    public function isActive(): bool
    {
        $lifetime = config('session.lifetime', 120);
        return $this->last_activity > (now()->subMinutes($lifetime)->timestamp);
    }

    /**
     * Check if session is expired
     */
    public function isExpired(): bool
    {
        return !$this->isActive();
    }

    /**
     * Get session age in minutes
     */
    public function getAgeInMinutes(): int
    {
        return (int) floor((now()->timestamp - $this->last_activity) / 60);
    }

    /**
     * Get session age in human readable format
     */
    public function getAgeForHumans(): string
    {
        $minutes = $this->getAgeInMinutes();

        if ($minutes < 1) {
            return 'À l\'instant';
        } elseif ($minutes < 60) {
            return "Il y a {$minutes} minute" . ($minutes > 1 ? 's' : '');
        } elseif ($minutes < 1440) {
            $hours = (int) floor($minutes / 60);
            return "Il y a {$hours} heure" . ($hours > 1 ? 's' : '');
        } else {
            $days = (int) floor($minutes / 1440);
            return "Il y a {$days} jour" . ($days > 1 ? 's' : '');
        }
    }

    /**
     * Get device information from user agent
     */
    public function getDeviceInfo(): array
    {
        $userAgent = $this->user_agent ?? '';

        // Simple device detection
        $device = 'Unknown';
        $browser = 'Unknown';
        $os = 'Unknown';

        // OS Detection
        if (preg_match('/Windows NT (\d+\.\d+)/', $userAgent, $matches)) {
            $os = 'Windows ' . $matches[1];
        } elseif (preg_match('/Mac OS X (\d+[._]\d+)/', $userAgent, $matches)) {
            $os = 'macOS ' . str_replace('_', '.', $matches[1]);
        } elseif (preg_match('/Linux/', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android (\d+\.\d+)/', $userAgent, $matches)) {
            $os = 'Android ' . $matches[1];
        } elseif (preg_match('/iPhone OS (\d+[._]\d+)/', $userAgent, $matches)) {
            $os = 'iOS ' . str_replace('_', '.', $matches[1]);
        }

        // Browser Detection
        if (preg_match('/Chrome\/(\d+\.\d+)/', $userAgent, $matches)) {
            $browser = 'Chrome ' . $matches[1];
        } elseif (preg_match('/Firefox\/(\d+\.\d+)/', $userAgent, $matches)) {
            $browser = 'Firefox ' . $matches[1];
        } elseif (preg_match('/Safari\/(\d+\.\d+)/', $userAgent, $matches)) {
            $browser = 'Safari ' . $matches[1];
        } elseif (preg_match('/Edge\/(\d+\.\d+)/', $userAgent, $matches)) {
            $browser = 'Edge ' . $matches[1];
        }

        // Device Type
        if (preg_match('/Mobile|Android|iPhone/', $userAgent)) {
            $device = 'Mobile';
        } elseif (preg_match('/Tablet|iPad/', $userAgent)) {
            $device = 'Tablet';
        } else {
            $device = 'Desktop';
        }

        return [
            'device' => $device,
            'browser' => $browser,
            'os' => $os,
            'user_agent' => $userAgent,
        ];
    }

    /**
     * Get location information from IP address
     */
    public function getLocationInfo(): array
    {
        $ip = $this->ip_address;

        if (!$ip || $ip === '127.0.0.1' || $ip === '::1') {
            return [
                'country' => 'Local',
                'city' => 'Local',
                'ip' => $ip,
            ];
        }

        // You can integrate with IP geolocation services here
        // For now, return basic info
        return [
            'country' => 'Unknown',
            'city' => 'Unknown',
            'ip' => $ip,
        ];
    }

    /**
     * Terminate this session
     */
    public function terminate(): bool
    {
        return $this->delete();
    }

    /**
     * Terminate all sessions for a user except current one
     */
    public static function terminateAllForUser(int $userId, ?string $excludeSessionId = null): int
    {
        $query = static::query()->where('user_id', $userId);

        if ($excludeSessionId) {
            $query->where('id', '!=', $excludeSessionId);
        }

        $deleted = $query->delete();

        // Clear cache
        static::clearCache();

        return $deleted;
    }

    /**
     * Clean up expired sessions
     */
    public static function cleanupExpiredSessions(): int
    {
        $lifetime = config('session.lifetime', 120);
        $expiredBefore = now()->subMinutes($lifetime)->timestamp;

        $deleted = static::query()
            ->where('last_activity', '<', $expiredBefore)
            ->delete();

        static::clearCache();

        return $deleted;
    }

    /**
     * Get session statistics
     */
    public static function getStats(): array
    {
        $totalSessions = static::count();
        $activeSessions = static::query()
            ->where('last_activity', '>', now()->subMinutes(config('session.lifetime', 120))->timestamp)
            ->count();
        $expiredSessions = $totalSessions - $activeSessions;

        $sessionsByUser = static::query()
            ->selectRaw('user_id, COUNT(*) as count')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->get()
            ->pluck('count', 'user_id');

        return [
            'total_sessions' => $totalSessions,
            'active_sessions' => $activeSessions,
            'expired_sessions' => $expiredSessions,
            'unique_users' => $sessionsByUser->count(),
            'average_sessions_per_user' => $sessionsByUser->avg(),
            'max_sessions_per_user' => $sessionsByUser->max(),
        ];
    }

    /**
     * Clear cache
     */
    public static function clearCache(): void
    {
        $patterns = [
            'user_sessions_*',
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    /**
     * Scopes
     */
    public function scopeActive(Builder $query): Builder
    {
        $lifetime = config('session.lifetime', 120);
        return $query->where('last_activity', '>', now()->subMinutes($lifetime)->timestamp);
    }

    public function scopeExpired(Builder $query): Builder
    {
        $lifetime = config('session.lifetime', 120);
        return $query->where('last_activity', '<=', now()->subMinutes($lifetime)->timestamp);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByIp(Builder $query, string $ipAddress): Builder
    {
        return $query->where('ip_address', $ipAddress);
    }

    public function scopeRecent(Builder $query, int $minutes = 60): Builder
    {
        return $query->where('last_activity', '>', now()->subMinutes($minutes)->timestamp);
    }

    public function scopeOrderByActivity(Builder $query, string $direction = 'desc'): Builder
    {
        return $query->orderBy('last_activity', $direction);
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Accessors
     */
    public function getIsCurrentAttribute(): bool
    {
        return $this->id === session()->getId();
    }

    public function getDeviceAttribute(): string
    {
        return $this->getDeviceInfo()['device'];
    }

    public function getBrowserAttribute(): string
    {
        return $this->getDeviceInfo()['browser'];
    }

    public function getOsAttribute(): string
    {
        return $this->getDeviceInfo()['os'];
    }

    public function getLocationAttribute(): string
    {
        $location = $this->getLocationInfo();
        return $location['city'] . ', ' . $location['country'];
    }

    public function getLastActivityFormattedAttribute(): string
    {
        return $this->getAgeForHumans();
    }

    /**
     * Mutators
     */
    public function setPayloadAttribute($value): void
    {
        $this->attributes['payload'] = base64_encode($value);
    }

    public function getPayloadAttribute($value): string
    {
        return base64_decode($value);
    }
}
