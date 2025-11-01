<?php

namespace Webkernel\Aptitudes\WebsiteBuilder\Filament\Pages;

use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Webkernel\Aptitudes\WebsiteBuilder\Filament\System\Resources\WebsiteProjects\WebsiteProjectResource;
use Webkernel\Aptitudes\WebsiteBuilder\Models\WebsitePage;
use Webkernel\Aptitudes\WebsiteBuilder\WebsiteBuilderService;

class WebsiteBuilder extends Page
{
    use InteractsWithRecord;
    protected static string $resource = WebsiteProjectResource::class;
    protected static ?string $title = null;
    protected string $view          = 'website-builder::builder.V1.layouts.root';
    protected static string $layout = 'website-builder::builder.V1.layouts.blank';
    protected static ?string $slug  = 'website-builder';
    protected static ?string $navigationLabel = 'Website Builder';

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return false;
    }
    public ?WebsitePage $currentPage = null;
    public array $projectPages = [];
    public array $availableBlocks = [];

    /**
     *Page settings properties for the modal
     */
    public string $pageName = '';
    public string $pageSlug = '';
    public string $pagePath = '';
    public string $pageTemplate = 'default';
    public string $metaTitle = '';
    public string $metaDescription = '';
    public bool $isHomepage = false;
    protected WebsiteBuilderService $service;
    public function boot(WebsiteBuilderService $service): void
    {
        $this->service = $service;
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        // Load project pages and current page
        $this->loadProjectData();
    }

    private function loadProjectData(): void
    {
        if (! $this->record) {
            return;
        }

        // Get project pages
        $this->projectPages = $this->service->getProjectPages($this->record->id);

        // Get available blocks
        $this->availableBlocks = $this->service->getAvailableBlocks();

        // Try to get current page from URL parameter
        $pageSlug = request()->route('page');
        if ($pageSlug) {
            $this->currentPage = $this->service->getPage($pageSlug, $this->record->id);
        }

        // Load current page settings into form properties
        $this->loadCurrentPageSettings();
    }

    private function loadCurrentPageSettings(): void
    {
        if (! $this->currentPage) {
            // Set default values for new page
            $this->pageName = '';
            $this->pageSlug = '';
            $this->pagePath = '/';
            $this->pageTemplate = 'default';
            $this->metaTitle = '';
            $this->metaDescription = '';
            $this->isHomepage = false;

            return;
        }

        // Load from current page
        $this->pageName = $this->currentPage->name ?? '';
        $this->pageSlug = $this->currentPage->slug ?? '';
        $this->pagePath = $this->currentPage->path ?? '/';
        $this->pageTemplate = $this->currentPage->template ?? 'default';

        $seoConfig = $this->currentPage->seo_config ?? [];
        $this->metaTitle = $seoConfig['title'] ?? '';
        $this->metaDescription = $seoConfig['description'] ?? '';

        $this->isHomepage = $this->currentPage->is_homepage ?? false;
    }

    public function updatePageSettings(): void
    {
        if (! $this->currentPage) {
            // Create new page
            $this->currentPage = $this->service->createPage([
                'project_id' => $this->record->id,
                'name' => $this->pageName,
                'slug' => $this->pageSlug,
                'path' => $this->pagePath,
                'template' => $this->pageTemplate,
                'seo_config' => [
                    'title' => $this->metaTitle,
                    'description' => $this->metaDescription,
                ],
                'is_homepage' => $this->isHomepage,
            ]);
        } else {
            // Update existing page
            $this->currentPage->update([
                'name' => $this->pageName,
                'slug' => $this->pageSlug,
                'path' => $this->pagePath,
                'template' => $this->pageTemplate,
                'seo_config' => [
                    'title' => $this->metaTitle,
                    'description' => $this->metaDescription,
                ],
                'is_homepage' => $this->isHomepage,
            ]);
        }

        // Reload project data to reflect changes
        $this->loadProjectData();

        // Dispatch event to close modal
        $this->dispatch('close-page-settings');
    }

    public function getTitle(): string
    {
        return isset($this->record) && $this->record ? $this->record->name . ' - Website Builder' : 'Website Builder';
    }

    public function getHeading(): string
    {
        return isset($this->record) && $this->record ? $this->record->name : 'Website Builder';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view($this->view, [
            'project' => isset($this->record) ? $this->record : null,
            'currentPage' => $this->currentPage,
            'availableBlocks' => $this->availableBlocks,
            'projectPages' => $this->projectPages,
        ]);
    }
}
