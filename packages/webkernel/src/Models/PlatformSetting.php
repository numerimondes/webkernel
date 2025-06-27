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
        'tenant_id',
    ];

    protected $casts = [
        'validation_rules' => 'array',
        'metadata' => 'array',
        'is_public' => 'boolean',
        'requires_cache_clear' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function ($setting) {
            // Clear cache for this setting
            Cache::forget("platform_setting_{$setting->settings_reference}_{$setting->tenant_id}");
            Cache::forget("platform_settings_tenant_{$setting->tenant_id}");
            Cache::forget("public_platform_settings_tenant_{$setting->tenant_id}");

            // Broadcast setting change for real-time updates
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

    public function getTypedValue()
    {
        switch ($this->type) {
            case 'boolean':
                return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return is_numeric($this->value) ? (float) $this->value : 0;
            case 'json':
                return json_decode($this->value, true) ?? [];
            default:
                return $this->value;
        }
    }

    public function setTypedValue($value)
    {
        switch ($this->type) {
            case 'boolean':
                $this->value = $value ? 'true' : 'false';
                break;
            case 'json':
                $this->value = is_array($value) ? json_encode($value) : $value;
                break;
            default:
                $this->value = (string) $value;
        }
    }

    public static function get(string $reference, $default = null, ?int $tenantId = null)
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

    public static function set(string $reference, $value, ?int $tenantId = null)
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

    public static function getPublicSettings(?int $tenantId = null)
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
                    ->map(fn($setting) => $setting->getTypedValue())
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
                    ->orderBy('category')
                    ->orderBy('settings_reference')
                    ->get()
                    ->groupBy('category');
            }
        );
    }
}
