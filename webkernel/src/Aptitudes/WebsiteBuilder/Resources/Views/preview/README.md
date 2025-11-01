# Website Builder Preview System

This preview system provides dynamic routes that can handle any type of website preview with multiple access methods.

## Available Routes

### Preview Routes (Direct HTML Output)

1. **Project Preview (Homepage)**
   ```
   /preview/project/{project-slug}
   ```
   - Shows the homepage or first page of a project
   - Example: `/preview/project/my-business-site`

2. **Page Preview**
   ```
   /preview/project/{project-slug}/page/{page-slug}
   ```
   - Shows a specific page
   - Example: `/preview/project/my-business-site/page/about`

3. **Preview by ID**
   ```
   /preview/project-id/{projectId}
   /preview/project-id/{projectId}/page-id/{pageId}
   ```
   - Direct access using database IDs
   - Example: `/preview/project-id/1/page-id/5`

4. **Preview by Domain**
   ```
   /preview/domain/{domain}
   /preview/domain/{domain}/{path}
   ```
   - Production-like preview using domain and path
   - Example: `/preview/domain/example.com/about`

### Previewer Routes (Livewire Interface)

1. **Previewer Interface**
   ```
   /previewer/project/{project-slug}
   /previewer/project/{project-slug}/page/{page-slug}
   ```
   - Full preview interface with controls
   - Example: `/previewer/project/my-business-site/page/about`

## Usage in Code

### From PageEditor Component

```php
// Get preview URL for current page
$previewUrl = $this->getPagePreviewUrl();

// Get all preview URLs
$allUrls = $this->getAllPreviewUrls();

// Open preview in new window
$this->openPreview();

// Navigate to previewer interface
$this->goToPreviewer();
```

### From Any Controller

```php
use Webkernel\Aptitudes\WebsiteBuilder\Http\Controllers\PreviewController;

// Get preview URL for a project
$url = PreviewController::getProjectPreviewUrl($project);

// Get preview URL for a page
$url = PreviewController::getPagePreviewUrl($page);

// Get preview URL by IDs
$url = PreviewController::getPreviewUrlById($projectId, $pageId);

// Get preview URL by domain
$url = PreviewController::getPreviewUrlByDomain('example.com', '/about');
```

## Features

- **Dynamic Route Handling**: Supports any website type through the WebsiteBuilderService
- **Multiple Access Methods**: Slug-based, ID-based, and domain-based previews
- **Error Handling**: Graceful error pages with preview mode indicators
- **Livewire Integration**: Seamless integration with the existing PageEditor
- **Responsive Interface**: Mobile-friendly previewer interface
- **Copy to Clipboard**: Easy URL sharing functionality
- **Fullscreen Support**: Fullscreen preview mode
- **Real-time Updates**: Live preview updates as you edit

## Integration

The preview system is automatically integrated into the WebsiteBuilder interface with:

- Preview buttons in the top bar
- Livewire event handling
- JavaScript integration for popup windows
- Responsive design for all screen sizes

## Error Handling

The system includes comprehensive error handling:

- 404 errors for missing projects/pages
- 500 errors with detailed error pages
- Preview mode indicators
- Graceful fallbacks

## Security

- All routes are protected by Laravel's routing system
- Preview mode headers for identification
- Safe error handling without exposing sensitive information
