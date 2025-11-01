<?php

declare(strict_types=1);

namespace Platform\Numerimondes\MasterConnector\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = ['name', 'slug', 'namespace', 'description', 'metadata', 'status'];

  protected $casts = [
    'metadata' => 'array',
    'status' => 'string',
  ];

  /**
   * Relationships
   */
  public function licenses(): HasMany
  {
    return $this->hasMany(License::class);
  }

  public function modules(): HasMany
  {
    return $this->hasMany(Module::class);
  }

  /**
   * Scopes
   */
  public function scopeActive($query)
  {
    return $query->where('status', 'active');
  }

  public function scopeBySlug($query, string $slug)
  {
    return $query->where('slug', $slug);
  }

  /**
   * Check if organization can create custom modules
   */
  public function canCreateCustomModules(): bool
  {
    return config('master-connector.organizations.enabled') && $this->status === 'active';
  }

  /**
   * Get the custom namespace for this organization
   */
  public function getCustomNamespace(): ?string
  {
    return $this->namespace;
  }

  /**
   * Generate a unique namespace for a new organization
   */
  public static function generateNamespace(string $name): string
  {
    $slug = \Illuminate\Support\Str::slug($name);
    $namespace = 'Org' . ucfirst(\Illuminate\Support\Str::camel($slug));

    // Ensure uniqueness
    $counter = 1;
    $originalNamespace = $namespace;
    while (self::where('namespace', $namespace)->exists()) {
      $namespace = $originalNamespace . $counter;
      $counter++;
    }

    return $namespace;
  }
}
