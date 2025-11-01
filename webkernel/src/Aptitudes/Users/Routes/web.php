<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Webkernel\Aptitudes\Users\Models\UserExtensions\UserPreference;

Route::post('/theme/change', function (Request $request) {
  $theme = $request->input('theme');

  if (!auth()->check()) {
    return redirect()->back()->with('error', 'Not authenticated');
  }

  $user = auth()->user();
  $preferences = UserPreference::getOrCreateForUser($user);
  $preferences->theme_name = $theme;
  $preferences->save();

  return redirect()->back()->with('success', 'Theme changed');
})
  ->middleware('auth')
  ->name('theme.change');
