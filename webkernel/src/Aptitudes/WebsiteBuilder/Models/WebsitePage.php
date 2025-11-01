<?php

namespace Webkernel\Aptitudes\WebsiteBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * @property int $id
 * @property int $project_id
 * @property string $name
 * @property string $slug
 * @property string $path
 * @property string $template
 * @property string $status
 * @property string $type
 * @property array<array-key, mixed>|null $seo_config
 * @property array<array-key, mixed>|null $page_config
 * @property array<array-key, mixed>|null $blocks_config
 * @property int $sort_order
 * @property bool $is_homepage
 * @property string $language
 * @property int|null $parent_page_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, WebsitePage> $children
 * @property-read int|null $children_count
 * @property-read WebsitePage|null $parent
 * @property-read \Webkernel\Aptitudes\WebsiteBuilder\Models\WebsiteProject $project
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage whereBlocksConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage whereIsHomepage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage wherePageConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage whereParentPageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage whereSeoConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WebsitePage extends Model
{
    use HasFactory;

    protected $table = 'apt_website_pages';

    protected $fillable = [
        'project_id',
        'name',
        'slug',
        'path',
        'template',
        'status',
        'type',
        'seo_config',
        'page_config',
        'blocks_config',
        'sort_order',
        'is_homepage',
        'language',
        'parent_page_id',
    ];

    protected $casts = [
        'seo_config' => 'array',
        'page_config' => 'array',
        'blocks_config' => 'array',
        'is_homepage' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(WebsiteProject::class, 'project_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(WebsitePage::class, 'parent_page_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(WebsitePage::class, 'parent_page_id');
    }

    public function getFullUrl(): string
    {
        $baseUrl = $this->project->canonical_url ?: "https://{$this->project->domain}";
        return rtrim($baseUrl, '/') . $this->path;
    }

    public function getSeoTitle(): string
    {
        $seoConfig = $this->seo_config ?? [];
        return $seoConfig['title'] ?? $this->name;
    }

    public function getSeoDescription(): string
    {
        $seoConfig = $this->seo_config ?? [];
        return $seoConfig['description'] ?? '';
    }

    public function getBlocksCount(): int
    {
        return count($this->blocks_config ?? []);
    }
}
