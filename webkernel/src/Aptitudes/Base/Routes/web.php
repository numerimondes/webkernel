<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\JsonResponse;

Route::get('/modules-namespaces', function (): JsonResponse {
  $modules_namespaces = \Webkernel\Arcanes\QueryModules::make()
    ->select(['namespace', 'name', 'path', 'routesPath'])
    ->unique()
    ->get();

  return response()->json($modules_namespaces, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
});

Route::get('/filament-resources', function (): JsonResponse {
  $resources_filament = [];

  foreach (\Filament\Facades\Filament::getPanels() as $panel) {
    $resources = [];

    foreach ($panel->getResources() as $resourceClass) {
      $resources[] = [
        'name' => class_basename($resourceClass),
        'slug' => $resourceClass::getSlug(),
        'model' => $resourceClass::getModel(),
        'navigation_group' => $resourceClass::getNavigationGroup(),
        'navigation_label' => $resourceClass::getNavigationLabel(),
      ];
    }

    $resources_filament[$panel->getId()] = $resources;
  }

  return response()->json($resources_filament, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
});

Route::get('basix', function () {
  $basix = basix();
  return response()->json($basix, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
});

Route::get('/perf', function () {
  [$result, $time] = Benchmark::value(function () {
    // Votre logique ici
    return 'done';
  });

  return ['result' => $result, 'time_ms' => $time];
});
