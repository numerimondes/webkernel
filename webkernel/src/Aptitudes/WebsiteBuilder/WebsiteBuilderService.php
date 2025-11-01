<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\WebsiteBuilder;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Webkernel\Aptitudes\WebsiteBuilder\Models\WebsiteProject;
use Webkernel\Aptitudes\WebsiteBuilder\Models\WebsitePage;

class WebsiteBuilderService
{
    private const CACHE_TTL = 3600;
    private const CACHE_PREFIX = 'wb_';

    // ========================================================================
    // PROJECT CRUD OPERATIONS
    // ========================================================================

    /**
     * Create a new website project
     */
    public function createProject(array $data): WebsiteProject
    {
        $defaults = [
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'status' => 'active',
            'type' => 'business',
            'main_language' => 'en',
            'version' => '1.0.0'
        ];

        $project = WebsiteProject::create(array_merge($defaults, $data));
        $this->clearProjectCache($project->id);

        return $project;
    }

    /**
     * Get project by ID or slug
     */
    public function getProject(string|int $identifier): ?WebsiteProject
    {
        $cacheKey = self::CACHE_PREFIX . "project_" . $identifier;

        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($identifier) {
            if (is_numeric($identifier)) {
                return WebsiteProject::find($identifier);
            }
            return WebsiteProject::where('slug', $identifier)->first();
        });
    }

    /**
     * Update project
     */
    public function updateProject(int $projectId, array $data): bool
    {
        $project = WebsiteProject::find($projectId);
        if (!$project) return false;

        $updated = $project->update($data);
        $this->clearProjectCache($projectId);

        return $updated;
    }

    /**
     * Delete project and all related data
     */
    public function deleteProject(int $projectId): bool
    {
        $project = WebsiteProject::find($projectId);
        if (!$project) return false;

        // Delete all pages
        $project->pages()->delete();

        // Delete project
        $deleted = $project->delete();
        $this->clearProjectCache($projectId);

        return $deleted;
    }

    /**
     * List all projects with pagination
     */
    public function listProjects(int $page = 1, int $perPage = 20): array
    {
        $projects = WebsiteProject::orderBy('created_at', 'desc')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return [
            'data' => $projects,
            'total' => WebsiteProject::count(),
            'page' => $page,
            'per_page' => $perPage
        ];
    }

    // ========================================================================
    // PAGE CRUD OPERATIONS
    // ========================================================================

    /**
     * Create a new page
     */
    public function createPage(array $data): WebsitePage
    {
        $defaults = [
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'path' => $data['path'] ?? '/' . ($data['slug'] ?? Str::slug($data['name'])),
            'status' => 'active',
            'template' => 'default',
            'type' => 'page',
            'language' => 'en',
            'is_homepage' => false,
            'sort_order' => $this->getNextPageSortOrder($data['project_id']),
            'seo_config' => [],
            'page_config' => [],
            'blocks_config' => []
        ];

        $page = WebsitePage::create(array_merge($defaults, $data));
        $this->clearPageCache($page->id);

        return $page;
    }

    /**
     * Get page by ID or slug
     */
    public function getPage(string|int $identifier, int $projectId = null): ?WebsitePage
    {
        $cacheKey = self::CACHE_PREFIX . "page_" . $identifier . "_" . ($projectId ?? 'any');

        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($identifier, $projectId) {
            $query = WebsitePage::query();

            if ($projectId) {
                $query->where('project_id', $projectId);
            }

            if (is_numeric($identifier)) {
                return $query->find($identifier);
            }

            return $query->where('slug', $identifier)->first();
        });
    }

    /**
     * Update page
     */
    public function updatePage(int $pageId, array $data): bool
    {
        $page = WebsitePage::find($pageId);
        if (!$page) return false;

        $updated = $page->update($data);
        $this->clearPageCache($pageId);
        $this->clearRenderCache($page->project->domain, $page->path);

        return $updated;
    }

    /**
     * Delete page
     */
    public function deletePage(int $pageId): bool
    {
        $page = WebsitePage::find($pageId);
        if (!$page) return false;

        $deleted = $page->delete();
        $this->clearPageCache($pageId);

        return $deleted;
    }

    /**
     * Get pages for a project
     */
    public function getProjectPages(int $projectId): array
    {
        $cacheKey = self::CACHE_PREFIX . "project_pages_" . $projectId;

        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($projectId) {
            return WebsitePage::where('project_id', $projectId)
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->get()
                ->toArray();
        });
    }

    // ========================================================================
    // BLOCK OPERATIONS
    // ========================================================================

    /**
     * Get available blocks from filesystem
     */
    public function getAvailableBlocks(): array
    {
        $cacheKey = self::CACHE_PREFIX . "available_blocks";

        return Cache::remember($cacheKey, self::CACHE_TTL, function() {
            $blocks = [];
            $blocksPath = resource_path('views/website-builder/blocks');

            if (!File::exists($blocksPath)) {
                return $blocks;
            }

            $files = File::files($blocksPath);

            foreach ($files as $file) {
                if ($file->getExtension() === 'php' && str_ends_with($file->getFilename(), '.blade.php')) {
                    $blockId = str_replace('.blade.php', '', $file->getFilename());
                    $config = $this->parseBlockConfig($blockId);

                    if ($config) {
                        $blocks[$blockId] = $config;
                    }
                }
            }

            return $blocks;
        });
    }

    /**
     * Add block to page
     */
    public function addBlockToPage(int $pageId, string $blockType, array $config = [], int $position = null): bool
    {
        $page = WebsitePage::find($pageId);
        if (!$page) return false;

        $blocksConfig = $page->blocks_config ?? [];
        $blockId = 'block_' . uniqid();

        $newBlock = [
            'block_type' => $blockType,
            'config' => $config,
            'sort_order' => $position ?? (count($blocksConfig) + 1),
            'is_active' => true
        ];

        $blocksConfig[$blockId] = $newBlock;

        return $this->updatePageBlocks($pageId, $blocksConfig);
    }

    /**
     * Remove block from page
     */
    public function removeBlockFromPage(int $pageId, string $blockId): bool
    {
        $page = WebsitePage::find($pageId);
        if (!$page) return false;

        $blocksConfig = $page->blocks_config ?? [];
        unset($blocksConfig[$blockId]);

        return $this->updatePageBlocks($pageId, $blocksConfig);
    }

    /**
     * Update block configuration
     */
    public function updateBlockConfig(int $pageId, string $blockId, array $config): bool
    {
        $page = WebsitePage::find($pageId);
        if (!$page) return false;

        $blocksConfig = $page->blocks_config ?? [];

        if (!isset($blocksConfig[$blockId])) return false;

        $blocksConfig[$blockId]['config'] = $config;

        return $this->updatePageBlocks($pageId, $blocksConfig);
    }

    /**
     * Update all blocks configuration for a page
     */
    public function updatePageBlocks(int $pageId, array $blocksConfig): bool
    {
        $updated = WebsitePage::where('id', $pageId)->update(['blocks_config' => $blocksConfig]);

        if ($updated) {
            $this->clearPageCache($pageId);

            // Clear render cache
            $page = WebsitePage::find($pageId);
            if ($page) {
                $this->clearRenderCache($page->project->domain, $page->path);
            }
        }

        return (bool) $updated;
    }

    // ========================================================================
    // RENDERING OPERATIONS
    // ========================================================================

    /**
     * Render complete page (ultra-fast with caching)
     */
    public function renderPage(string $domain, string $path, string $language = 'en'): string
    {
        $cacheKey = $this->getRenderCacheKey($domain, $path, $language);

        // Try filesystem cache first (< 1ms)
        $cached = $this->loadRenderCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // Build and cache
        $html = $this->buildPageHtml($domain, $path, $language);
        $this->saveRenderCache($cacheKey, $html);

        return $html;
    }

    /**
     * Build page HTML from database
     */
    private function buildPageHtml(string $domain, string $path, string $language): string
    {
        $project = $this->getProjectByDomain($domain);
        if (!$project) return $this->render404();

        $page = $this->getPageByPath($project->id, $path, $language);
        if (!$page) return $this->render404();

        $blocksHtml = '';
        $blocksConfig = $page->blocks_config ?? [];

        foreach ($blocksConfig as $blockId => $blockData) {
            if (!($blockData['is_active'] ?? true)) continue;

            $blockHtml = $this->renderSingleBlock(
                $blockData['block_type'],
                $blockData['config'] ?? [],
                $project,
                $page
            );

            $blocksHtml .= $blockHtml;
        }

        return $this->wrapInTemplate($project, $page, $blocksHtml);
    }

    /**
     * Render single block
     */
    public function renderSingleBlock(string $blockType, array $config, WebsiteProject $project, WebsitePage $page): string
    {
        $viewPath = "website-builder.blocks.{$blockType}";

        if (!view()->exists($viewPath)) {
            return "<!-- Block '{$blockType}' not found -->";
        }

        return view($viewPath, [
            'config' => $config,
            'project' => $project,
            'page' => $page,
            'blockId' => uniqid($blockType . '_')
        ])->render();
    }

    /**
     * Wrap content in page template
     */
    private function wrapInTemplate(WebsiteProject $project, WebsitePage $page, string $content): string
    {
        $templatePath = "website-builder.templates.{$page->template}";

        if (!view()->exists($templatePath)) {
            $templatePath = "website-builder.templates.default";
        }

        return view($templatePath, [
            'project' => $project,
            'page' => $page,
            'content' => $content,
            'seoConfig' => $page->seo_config ?? []
        ])->render();
    }

    // ========================================================================
    // CACHE MANAGEMENT
    // ========================================================================

    private function clearProjectCache(int $projectId): void
    {
        Cache::forget(self::CACHE_PREFIX . "project_{$projectId}");
        Cache::forget(self::CACHE_PREFIX . "project_pages_{$projectId}");
    }

    private function clearPageCache(int $pageId): void
    {
        Cache::forget(self::CACHE_PREFIX . "page_{$pageId}");
    }

    private function getRenderCacheKey(string $domain, string $path, string $language): string
    {
        return md5($domain . '|' . $path . '|' . $language);
    }

    private function loadRenderCache(string $key): ?string
    {
        $filePath = storage_path("framework/cache/wb_render_{$key}.php");

        if (File::exists($filePath)) {
            $data = include $filePath;
            if (($data['expires'] ?? 0) > time()) {
                return $data['content'];
            }
            File::delete($filePath);
        }

        return null;
    }

    private function saveRenderCache(string $key, string $content): void
    {
        $data = [
            'content' => $content,
            'expires' => time() + self::CACHE_TTL
        ];

        $filePath = storage_path("framework/cache/wb_render_{$key}.php");
        File::put($filePath, "<?php\nreturn " . var_export($data, true) . ";\n");
    }

    private function clearRenderCache(string $domain, string $path): void
    {
        $key = $this->getRenderCacheKey($domain, $path, 'en');
        $filePath = storage_path("framework/cache/wb_render_{$key}.php");

        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }

    // ========================================================================
    // HELPER METHODS
    // ========================================================================

    private function getProjectByDomain(string $domain): ?WebsiteProject
    {
        return Cache::remember(self::CACHE_PREFIX . "domain_{$domain}", self::CACHE_TTL, function() use ($domain) {
            return WebsiteProject::where('domain', $domain)->where('status', 'active')->first();
        });
    }

    private function getPageByPath(int $projectId, string $path, string $language): ?WebsitePage
    {
        return WebsitePage::where('project_id', $projectId)
            ->where('path', $path)
            ->where('language', $language)
            ->where('status', 'active')
            ->first();
    }

    private function getNextPageSortOrder(int $projectId): int
    {
        return WebsitePage::where('project_id', $projectId)->max('sort_order') + 1;
    }

    private function parseBlockConfig(string $blockId): ?array
    {
        $filePath = resource_path("views/website-builder/blocks/{$blockId}.blade.php");

        if (!File::exists($filePath)) return null;

        $content = File::get($filePath);

        if (preg_match('/{{--\s*@config\s*(.*?)\s*@endconfig\s*--}}/s', $content, $matches)) {
            return json_decode(trim($matches[1]), true);
        }

        return ['name' => ucfirst(str_replace(['-', '_'], ' ', $blockId))];
    }

    private function render404(): string
    {
        return '<html><head><title>404</title></head><body><h1>Page Not Found</h1></body></html>';
    }
}
