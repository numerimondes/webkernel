<?php

namespace Webkernel\Aptitudes\WebsiteBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Webkernel\Aptitudes\Enum\Traits\HasGlobalEnumTrait as HasGlobalEnum;

/**
 * @property int $id
 * @property string $name
 * @property string|null $site_title_key
 * @property string|null $slug
 * @property string|null $description
 * @property string $domain
 * @property string|null $canonical_url Canonical URL for SEO and reverse proxies
 * @property string $version
 * @property int|null $status_id
 * @property int|null $type_id
 * @property bool $is_multilingual
 * @property string|null $main_language
 * @property string|null $main_timezone
 * @property bool $no_accessibility Disable animations/cursors for accessibility
 * @property bool $preserve_url_parameters Preserve UTM and query params when navigating
 * @property string|null $favicon_path
 * @property string|null $logo_path
 * @property string|null $og_image_path
 * @property string|null $og_title_key
 * @property string|null $og_description_key
 * @property string|null $apple_touch_icon_path
 * @property string|null $password_protection
 * @property string|null $custom_starthead_tags
 * @property string|null $custom_endhead_tags
 * @property string|null $custom_startbody_tags
 * @property string|null $custom_endbody_tags
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Webkernel\Aptitudes\WebsiteBuilder\Models\WebsiteCache> $cacheEntries
 * @property-read int|null $cache_entries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Webkernel\Aptitudes\WebsiteBuilder\Models\CodeOverride> $codeOverrides
 * @property-read int|null $code_overrides_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Webkernel\Aptitudes\WebsiteBuilder\Models\WebsiteCollection> $collections
 * @property-read int|null $collections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Webkernel\Aptitudes\WebsiteBuilder\Models\WebsitePage> $pages
 * @property-read int|null $pages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Webkernel\Aptitudes\WebsiteBuilder\Models\Redirection> $redirections
 * @property-read int|null $redirections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Webkernel\Aptitudes\WebsiteBuilder\Models\WellKnownFile> $wellKnownFiles
 * @property-read int|null $well_known_files_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereAppleTouchIconPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereCanonicalUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereCustomEndbodyTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereCustomEndheadTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereCustomStartbodyTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereCustomStartheadTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereEnum(string $field, string $value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereEnumIn(string $field, array $values)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereFaviconPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereIsMultilingual($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereLogoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereMainLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereMainTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereNoAccessibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereOgDescriptionKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereOgImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereOgTitleKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject wherePasswordProtection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject wherePreserveUrlParameters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereSiteTitleKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteProject whereVersion($value)
 * @mixin \Eloquent
 */
class WebsiteProject extends Model
{
  use HasFactory, HasGlobalEnum;

  protected $table = 'apt_website_projects';
  protected $fillable = [
    'name',
    'site_title_key',
    'slug',
    'description',
    'domain',
    'canonical_url',
    'version',
    'status_id',
    'type_id',
    'is_multilingual',
    'main_language',
    'main_timezone',
    'no_accessibility',
    'preserve_url_parameters',
    'favicon_path',
    'logo_path',
    'og_image_path',
    'og_title_key',
    'og_description_key',
    'apple_touch_icon_path',
    'password_protection',
    'custom_starthead_tags',
    'custom_endhead_tags',
    'custom_startbody_tags',
    'custom_endbody_tags',
  ];

  protected $casts = [
    'is_multilingual' => 'boolean',
    'no_accessibility' => 'boolean',
    'preserve_url_parameters' => 'boolean',
  ];

  protected array $enumTypes = [
    'type_id' => 'website_project_type',
    'status_id' => 'website_project_status',
  ];

  public function pages(): HasMany
  {
    return $this->hasMany(WebsitePage::class, 'project_id');
  }

  public function redirections(): HasMany
  {
    return $this->hasMany(Redirection::class, 'project_id');
  }

  public function wellKnownFiles(): HasMany
  {
    return $this->hasMany(WellKnownFile::class, 'project_id');
  }

  public function collections(): HasMany
  {
    return $this->hasMany(WebsiteCollection::class, 'project_id');
  }

  public function codeOverrides(): HasMany
  {
    return $this->hasMany(CodeOverride::class, 'project_id');
  }

  public function cacheEntries(): HasMany
  {
    return $this->hasMany(WebsiteCache::class, 'project_id');
  }

  public function getHomepage(?string $language = null): ?WebsitePage
  {
    return $this->pages()
      ->where('is_homepage', true)
      ->where('language', $language ?? $this->main_language)
      ->where('status', 'active')
      ->first();
  }

  public function getLanguages(): array
  {
    if (!$this->is_multilingual) {
      return [$this->main_language];
    }

    return $this->pages()
      ->distinct()

      ->pluck('language')
      ->toArray();
  }
}

/**
 * @property int $id
 * @property string $identifier
 * @property string $name
 * @property string $category
 * @property string $blade_template
 * @property array<array-key, mixed>|null $default_config
 * @property array<array-key, mixed>|null $config_schema
 * @property string|null $preview_image
 * @property string|null $description
 * @property bool $is_active
 * @property string $version
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBlock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBlock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBlock query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBlock whereBladeTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBlock whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBlock whereConfigSchema($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBlock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBlock whereDefaultConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBlock whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBlock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBlock whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBlock whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBlock whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBlock wherePreviewImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBlock whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBlock whereVersion($value)
 * @mixin \Eloquent
 */
class WebsiteBlock extends Model
{
  use HasFactory;

  protected $table = 'apt_website_blocks';

  protected $fillable = [
    'identifier',
    'name',
    'category',
    'blade_template',
    'default_config',
    'config_schema',
    'preview_image',
    'description',
    'is_active',
    'version',
  ];

  protected $casts = [
    'default_config' => 'array',
    'config_schema' => 'array',
    'is_active' => 'boolean',
  ];

  public function getTemplatePath(): string
  {
    return "website-builder.blocks.{$this->identifier}";
  }

  public function isValid(): bool
  {
    return $this->is_active && !empty($this->identifier) && !empty($this->blade_template);
  }
}

/**
 * @property-read \Webkernel\Aptitudes\WebsiteBuilder\Models\WebsiteProject|null $project
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Redirection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Redirection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Redirection query()
 * @mixin \Eloquent
 */
class Redirection extends Model
{
  use HasFactory;

  protected $table = 'apt_redirections';

  protected $fillable = ['project_id', 'source_url', 'destination_url', 'status_code', 'status', 'type', 'priority'];

  protected $casts = [
    'priority' => 'integer',
  ];

  public function project(): BelongsTo
  {
    return $this->belongsTo(WebsiteProject::class, 'project_id');
  }

  public function isPermanent(): bool
  {
    return in_array($this->status_code, ['301', '308']);
  }
}

/**
 * @property-read \Webkernel\Aptitudes\WebsiteBuilder\Models\WebsiteProject|null $project
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WellKnownFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WellKnownFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WellKnownFile query()
 * @mixin \Eloquent
 */
class WellKnownFile extends Model
{
  use HasFactory;

  protected $table = 'apt_well_known_files';

  protected $fillable = ['project_id', 'name', 'status', 'path', 'content', 'mime_type'];

  public function project(): BelongsTo
  {
    return $this->belongsTo(WebsiteProject::class, 'project_id');
  }

  public function getFullPath(): string
  {
    return "/.well-known/{$this->path}";
  }
}

/**
 * @property int $id
 * @property int $project_id
 * @property string $name
 * @property string $slug
 * @property string $table_name
 * @property array<array-key, mixed> $fields_schema
 * @property string $status
 * @property bool $is_multilingual
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Webkernel\Aptitudes\WebsiteBuilder\Models\WebsiteProject $project
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCollection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCollection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCollection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCollection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCollection whereFieldsSchema($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCollection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCollection whereIsMultilingual($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCollection whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCollection whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCollection whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCollection whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCollection whereTableName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCollection whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WebsiteCollection extends Model
{
  use HasFactory;

  protected $table = 'apt_website_collections';

  protected $fillable = ['project_id', 'name', 'slug', 'table_name', 'fields_schema', 'status', 'is_multilingual'];

  protected $casts = [
    'fields_schema' => 'array',
    'is_multilingual' => 'boolean',
  ];

  public function project(): BelongsTo
  {
    return $this->belongsTo(WebsiteProject::class, 'project_id');
  }

  public function generateTableName(): string
  {
    return "apt_collection_{$this->project_id}_{$this->slug}";
  }

  public function getFieldNames(): array
  {
    $fields = $this->fields_schema ?? [];
    return array_keys($fields);
  }
}

/**
 * @property-read \Webkernel\Aptitudes\WebsiteBuilder\Models\WebsiteProject|null $project
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeOverride newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeOverride newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeOverride query()
 * @mixin \Eloquent
 */
class CodeOverride extends Model
{
  use HasFactory;

  protected $table = 'apt_code_overrides';

  protected $fillable = [
    'project_id',
    'name',
    'function_name',
    'php_code',
    'target_type',
    'target_identifier',
    'status',
  ];

  public function project(): BelongsTo
  {
    return $this->belongsTo(WebsiteProject::class, 'project_id');
  }

  public function isActive(): bool
  {
    return $this->status === 'active';
  }
}

/**
 * @property int $id
 * @property int $project_id
 * @property string $cache_key
 * @property string $cache_type
 * @property string $file_path
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property array<array-key, mixed>|null $dependencies
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Webkernel\Aptitudes\WebsiteBuilder\Models\WebsiteProject $project
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCache newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCache newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCache query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCache whereCacheKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCache whereCacheType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCache whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCache whereDependencies($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCache whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCache whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCache whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCache whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteCache whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WebsiteCache extends Model
{
  use HasFactory;

  protected $table = 'apt_website_cache';

  protected $fillable = ['project_id', 'cache_key', 'cache_type', 'file_path', 'expires_at', 'dependencies'];

  protected $casts = [
    'expires_at' => 'datetime',
    'dependencies' => 'array',
  ];

  public function project(): BelongsTo
  {
    return $this->belongsTo(WebsiteProject::class, 'project_id');
  }

  public function isExpired(): bool
  {
    return $this->expires_at && $this->expires_at->isPast();
  }
}
