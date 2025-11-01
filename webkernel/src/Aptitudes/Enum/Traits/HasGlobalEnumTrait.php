<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Enum\Traits;

use Webkernel\Aptitudes\Enum\Models\GlobalEnum;

/**
 * Minimalist trait for models using global enums
 *
 * Usage:
 * 1. Add trait to model: use HasGlobalEnum;
 * 2. Define enum mapping: protected array $enumTypes = ['field' => 'enum_type'];
 * 3. Use: $model->enum('field', 'icon') or GlobalEnum::get('field', $value, 'icon');
 */
trait HasGlobalEnumTrait
{
    /**
     * Get enum data for a field
     *
     * @param string $field Field name (e.g., 'status_id')
     * @param string|null $requesting Specific field to return (icon, label, css_class, etc.)
     * @return mixed
     */
    public function enum(string $field, ?string $requesting = null): mixed
    {
        $value = $this->{$field};
        if (!$value) {
            return null;
        }

        // If it's a foreign key (ID), get the enum directly
        if (is_numeric($value)) {
            $enum = GlobalEnum::find($value);
            if (!$enum) {
                return null;
            }
            return $requesting ? $enum->{$requesting} : $enum;
        }

        // If it's a key string, use the original method
        return GlobalEnum::get(
            type: $field,
            id: $value,
            requesting: $requesting,
            modelClass: static::class
        );
    }

    /**
     * Magic method to automatically create enum accessors
     * Usage: $model->status_enum, $model->type_enum
     */
    public function __get($key)
    {
        // Check if it's an enum accessor (ends with _enum)
        if (str_ends_with($key, '_enum')) {
            $field = str_replace('_enum', '_id', $key);
            if (in_array($field, array_keys($this->getEnumTypes()))) {
                return $this->{$field} ? GlobalEnum::find($this->{$field}) : null;
            }
        }

        return parent::__get($key);
    }

    /**
     * Get enum type mapping
     */
    public function getEnumTypes(): array
    {
        return $this->enumTypes ?? [];
    }

    /**
     * Get options for a field (for dropdowns) with IDs as keys
     */
    public function getEnumOptionsWithIds(string $field, bool $hierarchical = false): array
    {
        $enumTypes = $this->getEnumTypes();
        $type = $enumTypes[$field] ?? $field;

        return $hierarchical
            ? GlobalEnum::hierarchicalOptions($type)
            : GlobalEnum::optionsWithIds($type);
    }

    /**
     * Check if enum value is valid
     */
    public function isValidEnum(string $field, string $value): bool
    {
        $enumTypes = $this->getEnumTypes();
        $type = $enumTypes[$field] ?? $field;

        return GlobalEnum::get($type, $value) !== null;
    }

    /**
     * Get enum label for a field
     * Usage: $model->getEnumLabel('status_id')
     */
    public function getEnumLabel(string $field): ?string
    {
        return $this->enum($field, 'default_label');
    }

    /**
     * Get enum icon for a field
     * Usage: $model->getEnumIcon('status_id')
     */
    public function getEnumIcon(string $field): ?string
    {
        return $this->enum($field, 'icon');
    }

    /**
     * Get enum CSS class for a field
     * Usage: $model->getEnumCssClass('status_id')
     */
    public function getEnumCssClass(string $field): ?string
    {
        return $this->enum($field, 'css_class');
    }

    /**
     * Check if enum field has specific value
     * Usage: $model->isEnum('status_id', 'active')
     */
    public function isEnum(string $field, string $value): bool
    {
        $enum = $this->enum($field);
        return $enum && $enum->key === $value;
    }

    /**
     * Set enum value with validation
     */
    public function setEnum(string $field, string $value, bool $validate = true): self
    {
        if ($validate && !$this->isValidEnum($field, $value)) {
            throw new \InvalidArgumentException("Invalid enum value '{$value}' for field '{$field}'");
        }

        $this->{$field} = $value;
        return $this;
    }

    /**
     * Check if enum field matches value
     */
    public function enumIs(string $field, string $value): bool
    {
        return $this->{$field} === $value;
    }

    /**
     * Check if enum field is in values
     */
    public function enumIn(string $field, array $values): bool
    {
        return in_array($this->{$field}, $values);
    }

    /**
     * Scope: filter by enum value
     */
    public function scopeWhereEnum($query, string $field, string $value)
    {
        return $query->where($field, $value);
    }

    /**
     * Scope: filter by enum values
     */
    public function scopeWhereEnumIn($query, string $field, array $values)
    {
        return $query->whereIn($field, $values);
    }

    /**
     * Get validation rules for enum fields
     */
    public function getEnumValidationRules(): array
    {
        $rules = [];
        $enumTypes = $this->getEnumTypes();

        foreach ($enumTypes as $field => $type) {
            $validKeys = array_keys(GlobalEnum::options($type));
            $rules[$field] = ['nullable', 'string', 'in:' . implode(',', $validKeys)];
        }

        return $rules;
    }

    /**
     * Boot the trait
     */
    public static function bootHasGlobalEnum(): void
    {
        static::saving(function ($model) {
            $enumTypes = $model->getEnumTypes();

            foreach ($enumTypes as $field => $type) {
                $value = $model->{$field};

                if ($value && !$model->isValidEnum($field, $value)) {
                    throw new \InvalidArgumentException(
                        "Invalid enum value '{$value}' for field '{$field}' of type '{$type}'"
                    );
                }
            }
        });
    }
}
