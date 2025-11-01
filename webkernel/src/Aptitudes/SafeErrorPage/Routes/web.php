<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Webkernel\Aptitudes\SafeErrorPage\Pages\ErrorPage;
use Webkernel\Aptitudes\SafeErrorPage\Support\ActionRegistry;
use Illuminate\Http\Request;

// Clean URL structure: /application-error/{token}
// Token must be lowercase alphanumeric (hex)
Route::get('/application-error/{token}', ErrorPage::class)->name('error.page')->where('token', '[a-z0-9]{12}');

// POST route for executing actions (NO LIVEWIRE, PURE FORM POST)
Route::post('/application-error/execute-action', function (Request $request) {
  $actionName = $request->input('action_name');
  $returnUrl = $request->input('return_url', '/');

  if (empty($actionName)) {
    return redirect($returnUrl)->with('error', 'No action specified');
  }

  if (!ActionRegistry::has($actionName)) {
    return redirect($returnUrl)->with('error', "Action '{$actionName}' not found");
  }

  try {
    $result = ActionRegistry::execute($actionName);

    return redirect($returnUrl)->with('success', $result ?? 'Action completed successfully');
  } catch (\Throwable $e) {
    return redirect($returnUrl)->with('error', 'Action failed: ' . $e->getMessage());
  }
})->name('error.execute-action');
