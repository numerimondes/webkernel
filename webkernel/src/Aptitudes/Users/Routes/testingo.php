<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// Test route demonstrating the new action() syntax
Route::get('/testingo-simple', function () {
  return error_response()
    ->code(500)
    ->message('This is a test error with multiple action types')
    ->source('testingo-simple')
    ->details('Testing button and link actions')
    ->context('Test environment')
    ->identifier()
    ->action(
      type: 'button',
      color: 'primary',
      action: 'cache-clear',
      label: 'Clear Cache',
      description: 'Clear application cache',
      tooltip: 'This will clear all cached data',
    )
    ->action(
      type: 'button',
      color: 'warning',
      action: 'optimize-clear',
      label: 'Clear All',
      description: 'Clear all optimization caches',
    )
    ->action(
      type: 'link',
      color: 'info',
      action: null,
      label: 'View Documentation',
      href: 'https://docs.webkernel.dev/testing',
    )
    ->action(
      type: 'link',
      color: 'danger',
      action: null,
      label: 'Report Error',
      description: 'Send error report to support',
      href: 'https://support.webkernel.dev/report/error-{identifier}',
    )
    ->redirect();
})->name('testingo.simple');

// Test route with conditional actions
Route::get('/testingo-conditional', function () {
  return error_response()
    ->code(403)
    ->message('Access denied - conditional actions demo')
    ->source('testingo-conditional')
    ->identifier()
    ->action(
      type: 'link',
      color: 'primary',
      action: null,
      label: 'Public Support',
      href: 'https://support.webkernel.dev/public',
    )
    ->action(
      type: 'button',
      color: 'danger',
      action: 'emergency-cache-reset',
      label: 'Emergency Reset',
      description: 'Only visible in local/staging',
    )
    ->showIf(app()->environment('local', 'staging'))
    ->action(
      type: 'link',
      color: 'warning',
      action: null,
      label: 'Admin Panel',
      href: '/admin/errors/error-{identifier}',
    )
    ->showIf(auth()->check() && auth()->user()->isAdmin())
    ->redirect();
})->name('testingo.conditional');

// Test exception handling
Route::get('/testingo-exception', function () {
  try {
    throw new \RuntimeException('Simulated runtime exception for testing');
  } catch (\Throwable $e) {
    return exception_response($e)
      ->code(500)
      ->message('An exception occurred during testing')
      ->identifier()
      ->action(type: 'button', color: 'warning', action: 'cache-clear', label: 'Clear Cache')
      ->action(
        type: 'link',
        color: 'info',
        action: null,
        label: 'Exception Documentation',
        href: 'https://docs.webkernel.dev/exceptions',
      )
      ->redirect();
  }
})->name('testingo.exception');

// Test clean URL verification
Route::get('/testingo-url-validation', function () {
  try {
    // This should throw an exception due to query parameter in href
    return error_response()
      ->code(400)
      ->message('Testing URL validation')
      ->action(
        type: 'link',
        color: 'info',
        action: null,
        label: 'Invalid Link',
        href: 'https://example.com/page?query=param', // This will throw
      )
      ->redirect();
  } catch (\InvalidArgumentException $e) {
    return response()->json([
      'success' => true,
      'message' => 'URL validation working correctly',
      'error' => $e->getMessage(),
    ]);
  }
})->name('testingo.url-validation');

// Test minimal error (backwards compatibility)
Route::get('/testingo-minimal', function () {
  return error_response()->code(404)->message('Page not found')->redirect();
})->name('testingo.minimal');

// Test complete feature set
Route::get('/testingo-complete', function () {
  return error_response()
    ->code(503)
    ->message('Service temporarily unavailable')
    ->source('testingo-complete')
    ->details('Database connection failed after 3 retry attempts')
    ->context('User: ' . (auth()->user()?->email ?? 'guest') . ' | IP: ' . request()->ip())
    ->identifier('CUSTOM-ERROR-ID-' . time())
    ->action(
      type: 'button',
      color: 'primary',
      action: 'cache-clear',
      label: 'Clear Cache',
      description: 'Clear application cache',
      tooltip: 'Click to clear all cached data',
    )
    ->action(
      type: 'button',
      color: 'warning',
      action: 'optimize-clear',
      label: 'Clear All Caches',
      description: 'Clear all optimization caches',
    )
    ->action(type: 'button', color: 'danger', action: 'emergency-cache-reset', label: 'Emergency Reset')
    ->showIf(app()->environment('local'))
    ->action(type: 'link', color: 'info', action: null, label: 'Status Page', href: 'https://status.webkernel.dev')
    ->action(
      type: 'link',
      color: 'danger',
      action: null,
      label: 'Report Outage',
      description: 'Report this issue to the operations team',
      href: 'https://support.webkernel.dev/outage/error-{identifier}',
    )
    ->documentation('https://docs.webkernel.dev/troubleshooting/service-unavailable')
    ->redirect();
})->name('testingo.complete');
