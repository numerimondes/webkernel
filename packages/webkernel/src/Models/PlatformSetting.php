<?php
namespace Webkernel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

class PlatformSetting extends Model
{
    protected $table = 'core_platform_settings';
    protected $fillable = [
        'settings_reference',
        'value',
        'type',
        'category',
        'name_lang_key',
        'description_lang_key',
        'validation_rules',
        'metadata',
        'is_public',
        'requires_cache_clear',
        'is_fixed_order',
        'display_order',
        'card_group',
        'tenant_id',
    ];
    protected $casts = [
        'validation_rules' => 'array',
        'metadata' => 'array',
        'is_public' => 'boolean',
        'requires_cache_clear' => 'boolean',
        'is_fixed_order' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function ($setting) {
            Cache::forget("platform_setting_{$setting->settings_reference}_{$setting->tenant_id}");
            Cache::forget("platform_settings_tenant_{$setting->tenant_id}");
            Cache::forget("public_platform_settings_tenant_{$setting->tenant_id}");
            Event::dispatch('setting.updated', [
                'reference' => $setting->settings_reference,
                'value' => $setting->getTypedValue(),
                'tenant_id' => $setting->tenant_id,
                'is_public' => $setting->is_public,
            ]);
            if ($setting->requires_cache_clear) {
                Cache::flush();
            }
        });
    }

    public function getTypedValue(): mixed
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($this->value) ? (float) $this->value : 0,
            'json' => json_decode($this->value, true) ?? [],
            'select' => $this->value,
            default => $this->value,
        };
    }

    public function setTypedValue(mixed $value): void
    {
        $this->value = match ($this->type) {
            'boolean' => $value ? 'true' : 'false',
            'json' => is_array($value) ? json_encode($value) : $value,
            default => (string) $value,
        };
    }

    public static function getSetting(string $reference, mixed $default = null, ?int $tenantId = null): mixed
    {
        $tenantId = $tenantId ?? auth()->user()?->tenant_id ?? 1;
        return Cache::remember(
            "platform_setting_{$reference}_{$tenantId}",
            3600,
            function () use ($reference, $default, $tenantId) {
                $setting = static::where('settings_reference', $reference)
                    ->where('tenant_id', $tenantId)
                    ->first();
                return $setting ? $setting->getTypedValue() : $default;
            }
        );
    }

    public static function setSetting(string $reference, mixed $value, ?int $tenantId = null): ?self
    {
        $tenantId = $tenantId ?? auth()->user()?->tenant_id ?? 1;
        $setting = static::where('settings_reference', $reference)
            ->where('tenant_id', $tenantId)
            ->first();
        if ($setting) {
            $setting->setTypedValue($value);
            $setting->save();
        }
        return $setting;
    }

    public static function getPublicSettings(?int $tenantId = null): array
    {
        $tenantId = $tenantId ?? 1;
        return Cache::remember(
            "public_platform_settings_tenant_{$tenantId}",
            3600,
            function () use ($tenantId) {
                return static::where('tenant_id', $tenantId)
                    ->where('is_public', true)
                    ->get()
                    ->keyBy('settings_reference')
                    ->map(fn ($setting) => $setting->getTypedValue())
                    ->toArray();
            }
        );
    }

    public static function getAllByCategory(?int $tenantId = null)
    {
        $tenantId = $tenantId ?? auth()->user()?->tenant_id ?? 1;
        return Cache::remember(
            "platform_settings_tenant_{$tenantId}",
            3600,
            function () use ($tenantId) {
                return static::where('tenant_id', $tenantId)
                    ->orderBy('is_fixed_order', 'desc')
                    ->orderBy('display_order')
                    ->orderBy('settings_reference')
                    ->get()
                    ->groupBy('card_group');
            }
        );
    }

    public static function updateDisplayOrder(array $order, ?int $tenantId = null): void
    {
        $tenantId = $tenantId ?? auth()->user()?->tenant_id ?? 1;
        foreach ($order as $index => $reference) {
            $setting = static::where('settings_reference', $reference)
                ->where('tenant_id', $tenantId)
                ->where('is_fixed_order', false)
                ->first();
            if ($setting) {
                $setting->display_order = $index + 100; // Start from 100 to avoid conflicts with fixed settings
                $setting->save();
            }
        }
        Cache::forget("platform_settings_tenant_{$tenantId}");
    }
}
