<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\SafeErrorPage\Pages;

use Filament\Pages\Page;
use Webkernel\Aptitudes\SafeErrorPage\Services\ErrorService;

class ErrorPage extends Page
{
  protected static ?string $navigationLabel = '_';

  protected string $view = 'safe-error-page::error-page';

  protected static string $layout = 'filament-panels::components.layout.simple';

  protected static bool $shouldRegisterNavigation = false;

  public string $message = 'An error has occurred.';

  public int $code = 500;

  public ?string $source = null;

  public ?string $details = null;

  public ?string $trace = null;

  public ?string $identifier = null;

  public ?string $errorCode = null;

  public ?string $documentationUrl = null;

  public ?string $originalUrl = null;

  public ?string $previousUrl = null;

  public array $actions = [];

  public bool $showDetails = false;

  public bool $showBackButton = true;

  public bool $showReloadButton = true;

  public bool $showHomeButton = true;

  public function getTitle(): string
  {
    return '';
  }

  public function getHeading(): string
  {
    return '';
  }

  public function mount(?string $token = null): void
  {
    if ($token === null) {
      $this->message = __('No error token provided.');
      $this->code = 400;
      $this->source = 'error-page';
      $this->details = 'URL: ' . request()->fullUrl();
      return;
    }

    $token = strtolower($token);
    $errorService = app(ErrorService::class);
    $errorData = $errorService->retrieveError($token);

    if ($errorData === null) {
      $this->message = __('Error information not found. It may have expired.');
      $this->code = 404;
      $this->source = 'error-page';
      $this->details = "Token: {$token} | Cache driver: " . config('cache.default');
      return;
    }

    $this->identifier = $errorData['identifier'] ?? null;
    $this->message = $errorData['message'] ?? __('Unknown error');
    $this->code = $errorData['code'] ?? 500;
    $this->source = $errorData['source'] ?? null;
    $this->details = $errorData['details'] ?? null;
    $this->trace = $errorData['trace'] ?? null;
    $this->errorCode = $errorData['error_code'] ?? null;
    $this->documentationUrl = $errorData['documentation_url'] ?? null;
    $this->originalUrl = $errorData['original_url'] ?? null;
    $this->previousUrl = $errorData['previous_url'] ?? null;
    $this->actions = $errorData['actions'] ?? [];
    $this->showDetails = config('app.debug', false) && !empty($this->details);
    $this->showBackButton = $errorData['show_back_button'] ?? true;
    $this->showReloadButton = $errorData['show_reload_button'] ?? true;
    $this->showHomeButton = $errorData['show_home_button'] ?? true;

    // DO NOT clear cache here - let it expire naturally (10 minutes)
    // This allows users to reload/refresh without losing error data
    // Cache will auto-expire via TTL
  }
}
