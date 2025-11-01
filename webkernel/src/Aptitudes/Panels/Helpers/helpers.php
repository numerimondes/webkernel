<?php
declare(strict_types=1);

/**
 * Purpose: Utility functions for handling multiple panel configuration sources
 *
 * These functions provide a unified interface for loading panel configurations
 * from different sources while maintaining the same structure as traditional
 * PanelProvider classes. All sources return Panel instances ready for registration.
 *
 * Supported sources:
 * - database: Load from apt_panels table
 * - array: Load from PHP configuration arrays
 * - api: Load from external REST endpoints
 *
 * Usage examples:
 * - panel_source('database')
 * - panel_source('array', $panelConfigs)
 * - panel_source('api', 'https://api.example.com/panels')
 */

/**
 * Load panel configurations from specified source
 */
if (!function_exists('panel_source')) {
  function panel_source(string $source, mixed $data = null): \Illuminate\Support\Collection
  {
    return match ($source) {
      'database' => panel_source_database(),
      'array' => panel_source_array($data ?? []),
      'api' => panel_source_api($data),
      default => throw new InvalidArgumentException("Unsupported panel source: {$source}"),
    };
  }
}

/**
 * Load panels from database source
 */
if (!function_exists('panel_source_database')) {
  function panel_source_database(): \Illuminate\Support\Collection
  {
    try {
      $model = new \Webkernel\Aptitudes\Panels\Models\Panels();

      if (!\Illuminate\Support\Facades\Schema::hasTable($model->getTable())) {
        return collect();
      }

      return \Webkernel\Aptitudes\Panels\Models\Panels::active()
        ->bySource('database')
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();
    } catch (\Throwable $e) {
      return collect();
    }
  }
}

/**
 * Load panels from array configuration
 */
if (!function_exists('panel_source_array')) {
  function panel_source_array(array $configs): \Illuminate\Support\Collection
  {
    try {
      $panels = collect();

      foreach ($configs as $config) {
        if (!isset($config['id'], $config['path'])) {
          continue;
        }

        $panelData = array_merge(
          [
            'panel_source' => 'array',
            'is_active' => true,
            'is_default' => false,
            'sort_order' => 0,
            'methods' => [],
            'version' => '4.0',
            'description' => "Array-sourced panel: {$config['id']}",
          ],
          $config,
        );

        $panel = new \Webkernel\Aptitudes\Panels\Models\Panels();
        $panel->fill($panelData);
        $panel->exists = true;

        $panels->push($panel);
      }

      return $panels->sortBy('sort_order');
    } catch (\Throwable $e) {
      return collect();
    }
  }
}

/**
 * Load panels from external API
 */
if (!function_exists('panel_source_api')) {
  function panel_source_api(?string $endpoint = null): \Illuminate\Support\Collection
  {
    try {
      if (!$endpoint) {
        $endpoint = config('panels.api.endpoint');
      }

      if (!$endpoint) {
        return collect();
      }

      $response = \Illuminate\Support\Facades\Http::timeout(30)->retry(2, 1000)->get($endpoint);

      if (!$response->successful()) {
        return collect();
      }

      $apiData = $response->json();

      if (!isset($apiData['panels']) || !is_array($apiData['panels'])) {
        return collect();
      }

      $panels = collect();
      foreach ($apiData['panels'] as $config) {
        if (!isset($config['id'], $config['path'])) {
          continue;
        }

        $panelData = array_merge(
          [
            'panel_source' => 'api',
            'is_active' => true,
            'is_default' => false,
            'sort_order' => 0,
            'methods' => [],
            'version' => '4.0',
            'description' => "API-sourced panel: {$config['id']}",
            'metadata' => [
              'api_endpoint' => $endpoint,
              'loaded_at' => now()->toISOString(),
            ],
          ],
          $config,
        );

        $panel = new \Webkernel\Aptitudes\Panels\Models\Panels();
        $panel->fill($panelData);
        $panel->exists = true;

        $panels->push($panel);
      }

      return $panels->sortBy('sort_order');
    } catch (\Throwable $e) {
      return collect();
    }
  }
}

/**
 * Load panels from multiple sources and merge them
 */
if (!function_exists('panel_source_multi')) {
  function panel_source_multi(array $sources): \Illuminate\Support\Collection
  {
    $allPanels = collect();

    foreach ($sources as $sourceType => $sourceData) {
      if (!in_array($sourceType, ['database', 'array', 'api'])) {
        continue;
      }

      try {
        $sourcePanels = panel_source($sourceType, $sourceData);
        $allPanels = $allPanels->merge($sourcePanels);
      } catch (\Throwable $e) {
      }
    }

    return $allPanels->sortBy([['sort_order', 'asc'], ['id', 'asc']]);
  }
}

/**
 * Create a panel configuration array suitable for array source
 */
if (!function_exists('make_panel_config')) {
  function make_panel_config(
    string $id,
    string $path,
    array $methods = [],
    bool $isDefault = false,
    bool $isActive = true,
    int $sortOrder = 0,
    ?string $description = null,
  ): array {
    return [
      'id' => $id,
      'path' => $path,
      'methods' => array_merge(
        [
          'login' => true,
          'colors' => [['primary' => 'blue']],
        ],
        $methods,
      ),
      'is_default' => $isDefault,
      'is_active' => $isActive,
      'sort_order' => $sortOrder,
      'description' => $description ?? "Generated panel: {$id}",
    ];
  }
}
