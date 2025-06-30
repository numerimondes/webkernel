<?php

namespace Webkernel\Models;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PlatformSetting extends Model
{
    protected $table = 'platform_settings';

    protected $fillable = [
        'tenant_id',
        'key',
        'category',
        'value',
        'edited_by',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    protected $with = ['editor'];

    public function editor()
    {
        return $this->belongsTo(\App\Models\User::class, 'edited_by');
    }

    public function isArraySetting(): bool
    {
        return isset($this->value['value']) && is_array($this->value['value']);
    }

    protected function realValue(): Attribute
    {
        return Attribute::make(
            get: fn () => isset($this->value['value']) ? $this->value['value'] : null,
            set: fn ($newValue) => array_merge($this->value ?? [], ['value' => $newValue])
        );
    }

    public function getPlatformValidationRulesAttribute(): array
    {
        return isset($this->value['constraints']['validation_rules']) ? $this->value['constraints']['validation_rules'] : [];
    }

    public function getPlatformOptionsAttribute(): array
    {
        return isset($this->value['constraints']['options']) ? $this->value['constraints']['options'] : [];
    }

    public function getPlatformTypeAttribute(): ?string
    {
        return isset($this->value['type']) ? $this->value['type'] : null;
    }

    public function getPlatformIsEditableAttribute(): bool
    {
        return isset($this->value['is_editable']) ? $this->value['is_editable'] : false;
    }

    public function getPlatformIsPublicAttribute(): bool
    {
        return isset($this->value['is_public']) ? $this->value['is_public'] : false;
    }

    public function getPlatformLabelKeyAttribute(): ?string
    {
        return isset($this->value['label_key']) ? $this->value['label_key'] : null;
    }

    public function getPlatformDescriptionKeyAttribute(): ?string
    {
        return isset($this->value['description_key']) ? $this->value['description_key'] : null;
    }

    public function getPlatformCardGroupAttribute(): ?string
    {
        return isset($this->value['card_group']) ? $this->value['card_group'] : null;
    }

    public function getPlatformDisplayOrderAttribute(): int
    {
        return isset($this->value['display_order']) ? $this->value['display_order'] : 0;
    }

    public function getPlatformRequiredAttribute(): bool
    {
        return isset($this->value['required']) ? $this->value['required'] : false;
    }

    public function getPlatformRequiresCacheClearAttribute(): bool
    {
        return isset($this->value['requires_cache_clear']) ? $this->value['requires_cache_clear'] : false;
    }

    public function getPlatformIsFixedOrderAttribute(): bool
    {
        return isset($this->value['is_fixed_order']) ? $this->value['is_fixed_order'] : false;
    }

    public function getPlatformConstraintsAttribute(): array
    {
        return isset($this->value['constraints']) ? $this->value['constraints'] : [];
    }

    public static function getPlatformTypedValue(string $key, ?int $tenantId = null): mixed
    {
        $tenantId = $tenantId ?? 1;
        $cacheKey = "platform_settings_tenant_{$tenantId}_{$key}_typed";

        return Cache::remember($cacheKey, 3600, function () use ($key, $tenantId) {
            $setting = self::where('tenant_id', $tenantId)
                          ->where('key', $key)
                          ->first();

            if (!$setting || !isset($setting->value['value'])) {
                return null;
            }

            $value = $setting->value['value'];

            if ($setting->isArraySetting()) {
                return collect($value)->mapWithKeys(function ($item) {
                    return [($item['sub_key'] ?? null) => ($item['value'] ?? null)];
                })->filter()->all();
            }

            return $value;
        });
    }

    public static function setTypedValue(string $key, mixed $newValue, ?int $tenantId = null): bool
    {
        $tenantId = $tenantId ?? 1;
        $setting = self::where('tenant_id', $tenantId)->where('key', $key)->first();

        if (!$setting) {
            return false;
        }

        $setting->real_value = $newValue;
        $setting->edited_by = Auth::id();
        $setting->save();

        self::clearSettingCache($key, $tenantId);

        if ($setting->requires_cache_clear) {
            Cache::flush();
        }

        return true;
    }

    public static function getPlatformSetting(string $key, ?int $tenantId = null): ?self
    {
        $tenantId = $tenantId ?? 1;
        $cacheKey = "platform_settings_tenant_{$tenantId}_{$key}_full";

        return Cache::remember($cacheKey, 3600, function () use ($key, $tenantId) {
            return self::where('tenant_id', $tenantId)
                      ->where('key', $key)
                      ->first();
        });
    }

    public static function setSetting(string $key, array $settingData, ?int $tenantId = null): bool
    {
        $tenantId = $tenantId ?? 1;

        $setting = self::updateOrCreate(
            ['tenant_id' => $tenantId, 'key' => $key],
            [
                'category' => $settingData['category'] ?? 'general',
                'value' => $settingData,
                'edited_by' => Auth::id(),
            ]
        );

        self::clearSettingCache($key, $tenantId);

        if (isset($settingData['requires_cache_clear']) && $settingData['requires_cache_clear']) {
            Cache::flush();
        }

        return (bool) $setting;
    }

    public static function getPlatformPublicSettings(?int $tenantId = null): \Illuminate\Support\Collection
    {
        $tenantId = $tenantId ?? 1;
        $cacheKey = "platform_settings_tenant_{$tenantId}_public";

        return Cache::remember($cacheKey, 3600, function () use ($tenantId) {
            return self::where('tenant_id', $tenantId)
                      ->where('value->is_public', true)
                      ->orderBy('value->is_fixed_order', 'desc')
                      ->orderBy('value->display_order')
                      ->get();
        });
    }

    public static function getPlatformAllByCategory(string $category, ?int $tenantId = null): \Illuminate\Support\Collection
    {
        $tenantId = $tenantId ?? 1;
        $cacheKey = "platform_settings_tenant_{$tenantId}_cat_{$category}";

        return Cache::remember($cacheKey, 3600, function () use ($category, $tenantId) {
            return self::where('tenant_id', $tenantId)
                      ->where('category', $category)
                      ->orderBy('value->is_fixed_order', 'desc')
                      ->orderBy('value->display_order')
                      ->get();
        });
    }

    public static function updateDisplayOrder(string $key, int $newOrder, ?int $tenantId = null): bool
    {
        $tenantId = $tenantId ?? 1;
        $setting = self::where('tenant_id', $tenantId)->where('key', $key)->first();

        if (!$setting || $setting->is_fixed_order) {
            return false;
        }

        $valueData = $setting->value;
        $valueData['display_order'] = $newOrder;

        $setting->value = $valueData;
        $setting->edited_by = Auth::id();
        $setting->save();

        self::clearSettingCache($key, $tenantId);
        Cache::forget("platform_settings_tenant_{$tenantId}_cat_{$setting->category}");

        return true;
    }

    public static function bulkUpdate(array $settingsData, ?int $tenantId = null): bool
    {
        $tenantId = $tenantId ?? 1;

        try {
            $requiresCacheFlush = false;

            foreach ($settingsData as $key => $newValue) {
                $setting = self::getPlatformSetting($key, $tenantId);
                if ($setting && $setting->requires_cache_clear) {
                    $requiresCacheFlush = true;
                }
                self::setTypedValue($key, $newValue, $tenantId);
            }

            if ($requiresCacheFlush) {
                Cache::flush();
            }

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public static function getPlatformSettingsByCardGroup(string $cardGroup, ?int $tenantId = null): \Illuminate\Support\Collection
    {
        $tenantId = $tenantId ?? 1;
        $cacheKey = "platform_settings_tenant_{$tenantId}_group_{$cardGroup}";

        return Cache::remember($cacheKey, 3600, function () use ($cardGroup, $tenantId) {
            return self::where('tenant_id', $tenantId)
                      ->where('value->card_group', $cardGroup)
                      ->orderBy('value->is_fixed_order', 'desc')
                      ->orderBy('value->display_order')
                      ->get();
        });
    }

    public static function getPlatformEditableSettings(?int $tenantId = null): \Illuminate\Support\Collection
    {
        $tenantId = $tenantId ?? 1;
        $cacheKey = "platform_settings_tenant_{$tenantId}_editable";

        return Cache::remember($cacheKey, 3600, function () use ($tenantId) {
            return self::where('tenant_id', $tenantId)
                      ->where('value->is_editable', true)
                      ->orderBy('value->is_fixed_order', 'desc')
                      ->orderBy('value->display_order')
                      ->get();
        });
    }

    public static function getPlatformRequiredSettings(?int $tenantId = null): \Illuminate\Support\Collection
    {
        $tenantId = $tenantId ?? 1;
        $cacheKey = "platform_settings_tenant_{$tenantId}_required";

        return Cache::remember($cacheKey, 3600, function () use ($tenantId) {
            return self::where('tenant_id', $tenantId)
                      ->where('value->required', true)
                      ->orderBy('value->is_fixed_order', 'desc')
                      ->orderBy('value->display_order')
                      ->get();
        });
    }

    public static function getPlatformSystemSettings(?int $tenantId = null): \Illuminate\Support\Collection
    {
        $tenantId = $tenantId ?? 1;
        return self::getPlatformAllByCategory('system', $tenantId);
    }

    protected static function clearSettingCache(string $key, int $tenantId): void
    {
        $patterns = [
            "platform_settings_tenant_{$tenantId}_{$key}_typed",
            "platform_settings_tenant_{$tenantId}_{$key}_full",
            "platform_settings_tenant_{$tenantId}_{$key}_verified",
            "platform_settings_tenant_{$tenantId}_public",
            "platform_settings_tenant_{$tenantId}_editable",
            "platform_settings_tenant_{$tenantId}_required",
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    public function clearCache(): void
    {
        self::clearSettingCache($this->key, $this->tenant_id);

        if ($this->category) {
            Cache::forget("platform_settings_tenant_{$this->tenant_id}_cat_{$this->category}");
        }

        if ($this->card_group) {
            Cache::forget("platform_settings_tenant_{$this->tenant_id}_group_{$this->card_group}");
        }
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['real_value'] = $this->real_value;
        $array['type'] = $this->type;
        $array['is_editable'] = $this->is_editable;
        $array['is_public'] = $this->is_public;
        $array['required'] = $this->required;
        $array['label_key'] = $this->label_key;
        $array['description_key'] = $this->description_key;
        $array['card_group'] = $this->card_group;
        $array['display_order'] = $this->display_order;
        $array['options'] = $this->options;
        $array['validation_rules'] = $this->validation_rules;

        return $array;
    }

    public static function getAbsoluteUrl(string $key, ?int $tenantId = null, string $baseUrl = URL::asset()): ?string
    {
        $value = self::getPlatformTypedValue($key, $tenantId);

        if (empty($value)) {
            return null;
        }

        if (is_array($value)) {
            $url = $value['value'] ?? null;
        } else {
            $url = $value;
        }

        if (empty($url) || !is_string($url)) {
            return null;
        }

        if (filter_var($url, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $url)) {
            return $url;
        }

        if (str_starts_with($url, '/')) {
            return rtrim($baseUrl, '/') . $url;
        }

        return $url;
    }

    public static function getRawStoredData(string $key, ?int $tenantId = null): mixed
    {
        $value = self::getPlatformTypedValue($key, $tenantId);
        return is_array($value) ? ($value['value'] ?? null) : $value;
    }

    public static function getVerifiedStoredData(string $key, ?int $tenantId = null): ?string
    {
        $tenantId = $tenantId ?? 1;
        $cacheKey = "platform_settings_tenant_{$tenantId}_{$key}_verified";

        return Cache::remember($cacheKey, 3600, function () use ($key, $tenantId) {
            $value = self::getRawStoredData($key, $tenantId);

            if (empty($value) || !is_string($value)) {
                return null;
            }

            $url = self::getAbsoluteUrl($key, $tenantId);
            if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
                return null;
            }

            try {
                $response = Http::head($url);
                return $response->status() === 200 ? $url : null;
            } catch (\Exception $e) {
                return null;
            }
        });
    }
}
