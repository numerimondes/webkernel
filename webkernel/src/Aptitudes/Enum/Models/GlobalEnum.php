<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Enum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $type
 * @property string $key
 * @property string $label_key
 * @property string $default_label
 * @property string|null $description_key
 * @property string|null $icon
 * @property string|null $css_class
 * @property int $sort_order
 * @property bool $is_active
 * @property int|null $parent_id
 * @property array<array-key, mixed>|null $metadata
 * @property array<array-key, mixed>|null $contexts
 * @property string|null $model_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, GlobalEnum> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, GlobalEnum> $descendants
 * @property-read int|null $descendants_count
 * @property-read GlobalEnum|null $parent
 * @method static Builder<static>|GlobalEnum active()
 * @method static Builder<static>|GlobalEnum forContext(string $context)
 * @method static Builder<static>|GlobalEnum newModelQuery()
 * @method static Builder<static>|GlobalEnum newQuery()
 * @method static Builder<static>|GlobalEnum ofType(string $type)
 * @method static Builder<static>|GlobalEnum query()
 * @method static Builder<static>|GlobalEnum root()
 * @method static Builder<static>|GlobalEnum whereContexts($value)
 * @method static Builder<static>|GlobalEnum whereCreatedAt($value)
 * @method static Builder<static>|GlobalEnum whereCssClass($value)
 * @method static Builder<static>|GlobalEnum whereDefaultLabel($value)
 * @method static Builder<static>|GlobalEnum whereDescriptionKey($value)
 * @method static Builder<static>|GlobalEnum whereIcon($value)
 * @method static Builder<static>|GlobalEnum whereId($value)
 * @method static Builder<static>|GlobalEnum whereIsActive($value)
 * @method static Builder<static>|GlobalEnum whereKey($value)
 * @method static Builder<static>|GlobalEnum whereLabelKey($value)
 * @method static Builder<static>|GlobalEnum whereMetadata($value)
 * @method static Builder<static>|GlobalEnum whereModelType($value)
 * @method static Builder<static>|GlobalEnum whereParentId($value)
 * @method static Builder<static>|GlobalEnum whereSortOrder($value)
 * @method static Builder<static>|GlobalEnum whereType($value)
 * @method static Builder<static>|GlobalEnum whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GlobalEnum extends Model
{
    protected $table = 'apt_global_enums';

    protected $fillable = [
        'type',
        'key',
        'label_key',
        'default_label',
        'description_key',
        'icon',
        'css_class',
        'sort_order',
        'is_active',
        'parent_id',
        'metadata',
        'contexts',
        'model_type'
    ];

    protected $casts = [
        'metadata' => 'array',
        'contexts' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Main method: Get enum data with automatic type resolution
     * Usage: GlobalEnum::get(type: 'company_type_id', id: $id, requesting: 'icon')
     */
    public static function get(string $type, ?string $id = null, ?string $requesting = null, ?string $modelClass = null): mixed
    {
        // Resolve the actual enum type if it's a field name
        $actualType = static::resolveEnumType($type, $modelClass);

        $cacheKey = "global_enum_{$actualType}" . ($id ? "_{$id}" : '');

        $result = Cache::remember($cacheKey, 3600, function() use ($actualType, $id) {
            $query = static::query()
                ->where('type', $actualType)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('default_label');

            if ($id !== null) {
                return $query->where('key', $id)->first();
            } else {
                return $query->get();
            }
        });

        // If requesting specific field, return only that
        if ($requesting && $result && !$result instanceof \Illuminate\Support\Collection) {
            return match($requesting) {
                'label' => $result->getLabel(),
                'description' => $result->getDescription(),
                default => $result->{$requesting} ?? null
            };
        }

        return $result;
    }

    /**
     * Resolve enum type from field name using calling model context
     */
    protected static function resolveEnumType(string $fieldOrType, ?string $modelClass = null): string
    {
        // If no model class provided, try to get it from debug backtrace
        if (!$modelClass) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
            foreach ($trace as $frame) {
                if (isset($frame['class']) &&
                    str_ends_with($frame['class'], 'Models\\') === false &&
                    method_exists($frame['class'], 'getEnumTypes')) {
                    $modelClass = $frame['class'];
                    break;
                }
            }
        }

        // If model class found, try to resolve enum type
        if ($modelClass && class_exists($modelClass)) {
            $model = new $modelClass();
            if (method_exists($model, 'getEnumTypes')) {
                $enumTypes = $model->getEnumTypes();
                if (isset($enumTypes[$fieldOrType])) {
                    return $enumTypes[$fieldOrType];
                }
            }
        }

        // Return as-is if no resolution possible
        return $fieldOrType;
    }

    /**
     * Get enum options for select dropdowns with IDs as keys
     */
    public static function optionsWithIds(string $type): array
    {
        $enums = static::get($type);

        if (!$enums || $enums->isEmpty()) {
            return [];
        }

        return $enums->pluck('default_label', 'id')->toArray();
    }

    /**
     * Get hierarchical options with parent grouping
     */
    public static function hierarchicalOptions(string $type): array
    {
        $enums = static::query()
            ->where('type', $type)
            ->where('is_active', true)
            ->with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $options = [];
        foreach ($enums as $enum) {
            $options[$enum->key] = $enum->default_label;

            foreach ($enum->children as $child) {
                $options[$child->key] = '-- ' . $child->default_label;
            }
        }

        return $options;
    }

    /**
     * Get localized label
     */
    public function getLabel(): string
    {
        if (function_exists('lang') && $this->label_key && lang()->has($this->label_key)) {
            return lang($this->label_key);
        }

        return $this->default_label;
    }

    /**
     * Get localized description
     */
    public function getDescription(): ?string
    {
        if (!$this->description_key) {
            return null;
        }

        if (function_exists('lang') && lang()->has($this->description_key)) {
            return lang($this->description_key);
        }

        return null;
    }

    /**
     * Clear cache - Octane/Swoole compatible
     */
    public static function clearCache(): void
    {
        Cache::forget('global_enum_*');
    }

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
     * Scopes
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForContext(Builder $query, string $context): Builder
    {
        return $query->whereJsonContains('contexts', $context);
    }

    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Relations
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('default_label');
    }

    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Metadata helpers
     */
    public function hasMeta(string $key): bool
    {
        return isset($this->metadata[$key]);
    }

    public function getMeta(string $key, mixed $default = null): mixed
    {
        return $this->metadata[$key] ?? $default;
    }

    public function setMeta(string $key, mixed $value): self
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Context validation
     */
    public function isValidForContext(string $context): bool
    {
        return empty($this->contexts) || in_array($context, $this->contexts);
    }

    /**
     * Hierarchy helpers
     */
    public function getHierarchyPath(string $separator = ' > '): string
    {
        $path = [$this->default_label];

        $parent = $this->parent;
        while ($parent) {
            array_unshift($path, $parent->default_label);
            $parent = $parent->parent;
        }

        return implode($separator, $path);
    }

    public function isAncestorOf(GlobalEnum $enum): bool
    {
        $parent = $enum->parent;

        while ($parent) {
            if ($parent->id === $this->id) {
                return true;
            }
            $parent = $parent->parent;
        }

        return false;
    }

    public function isDescendantOf(GlobalEnum $enum): bool
    {
        return $enum->isAncestorOf($this);
    }

    /**
     * Statistics
     */
    public static function getStats(): array
    {
        return [
            'total_enums' => static::count(),
            'active_enums' => static::where('is_active', true)->count(),
            'enum_types' => static::distinct('type')->count('type'),
            'hierarchical_enums' => static::whereNotNull('parent_id')->count(),
        ];
    }

    public static function getTypes(): array
    {
        return static::distinct('type')
            ->orderBy('type')
            ->pluck('type')
            ->toArray();
    }
}
