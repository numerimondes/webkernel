<?php

declare(strict_types=1);

namespace Webkernel\Arcanes\Support\RemoteComponents;

use Filament\Panel;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Webkernel\Arcanes\Runtime\WebkernelManager;

class RCService
{
  private static array $components = [];
  private static bool $loaded = false;
  private ConsoleOutput $output;

  public function __construct()
  {
    $this->output = new ConsoleOutput();
  }

  public function register(string $method, string $class, string|array $panels): void
  {
    if (!class_exists($class)) {
      $this->renderError(
        type: 'WARNING',
        title: 'Class does not exist',
        details: [
          'Class' => $class,
          'Method' => $method,
          'Panels' => is_array($panels) ? implode(', ', $panels) : $panels,
        ],
        color: 'yellow',
      );
      return;
    }

    $method = strtolower($method);
    $panels = $this->normalizePanels($panels);

    if (!isset(self::$components[$method])) {
      self::$components[$method] = [];
    }

    self::$components[$method][] = [
      'class' => $class,
      'panels' => $panels,
    ];
  }

  public function inject(Panel $panel): Panel
  {
    $panel_id = $panel->getId();

    if (!$panel_id) {
      return $panel;
    }

    $total_injected = 0;

    foreach (self::$components as $method => $items) {
      $classes = [];

      foreach ($items as $item) {
        if ($item['panels'] === '*' || in_array($panel_id, $item['panels'])) {
          $classes[] = $item['class'];
        }
      }

      if (!empty($classes)) {
        if (!method_exists($panel, $method)) {
          $this->renderError(
            type: 'ERROR',
            title: 'Method does not exist on Panel',
            details: [
              'Method' => $method,
              'Panel' => $panel_id,
              'Classes' => implode(', ', $classes),
            ],
            color: 'red',
          );
          continue;
        }

        $unique_classes = array_unique($classes);
        $panel->$method($unique_classes);
        $total_injected += count($unique_classes);
      }
    }

    return $panel;
  }

  public function loadFromModules(array $modules = []): void
  {
    if (self::$loaded) {
      return;
    }

    $manager = app(WebkernelManager::class);
    $all_modules = $manager->getModules();

    foreach ($all_modules as $module_id => $module_data) {
      if (!isset($module_data['class']) || !class_exists($module_data['class'])) {
        continue;
      }

      try {
        $module = app($module_data['class']);

        if (!method_exists($module, 'getModuleConfig')) {
          continue;
        }

        $config = $module->getModuleConfig();

        if (empty($config->providedComponents)) {
          continue;
        }

        foreach ($config->providedComponents as $method => $items) {
          if (!is_array($items) || empty($items)) {
            continue;
          }

          foreach ($items as $item) {
            if (!is_array($item) || count($item) < 2) {
              $this->renderError(
                type: 'WARNING',
                title: 'Invalid component format',
                details: [
                  'Module' => $module_id,
                  'Method' => $method,
                  'Item' => json_encode($item),
                ],
                color: 'yellow',
              );
              continue;
            }

            $class = $item[0];
            $panels = $item[1];

            if (!class_exists($class)) {
              $panels_str = is_array($panels) ? implode(', ', $panels) : $panels;

              $details = [
                'Class' => $class,
                'Module' => $module_id,
                'Method' => $method,
                'Panels' => $panels_str,
              ];

              $file = $this->guessClassFile($class);
              if ($file !== null) {
                $details['File'] = $file;
              }

              $this->renderError(type: 'WARNING', title: 'Missing class', details: $details, color: 'orange');
              continue;
            }

            $this->register($method, $class, $panels);
          }
        }
      } catch (\Exception $e) {
        $this->renderError(
          type: 'ERROR',
          title: 'Module loading failed',
          details: [
            'Module' => $module_id,
            'Error' => $e->getMessage(),
            'File' => $e->getFile() . ':' . $e->getLine(),
          ],
          color: 'red',
        );
      }
    }

    self::$loaded = true;

    $this->renderSuccess('RemoteComponents loaded', $this->getStatistics());
  }

  private function renderError(string $type, string $title, array $details, string $color): void
  {
    $timestamp = date('Y-m-d\TH:i:s');

    $textColor = match ($color) {
      'red' => 'red',
      'orange' => 'yellow',
      'yellow' => 'yellow',
      default => 'yellow',
    };

    $bgColor = match ($color) {
      'red' => 'white;bg=red',
      'orange' => 'white;bg=yellow',
      'yellow' => 'black;bg=yellow',
      default => 'white;bg=yellow',
    };

    $this->output->writeln('');
    $this->output->writeln(
      sprintf(
        '  <fg=%s;options=bold>%s</>   <fg=%s;options=bold>[%s] RemoteComponents: %s</>',
        $bgColor,
        $type,
        $textColor,
        $timestamp,
        $title,
      ),
    );

    $detailsArray = array_values($details);
    $keysArray = array_keys($details);
    $lastIndex = count($keysArray) - 1;

    foreach ($keysArray as $index => $key) {
      $value = $detailsArray[$index];
      $symbol = $index === $lastIndex ? '└──' : '├──';

      $this->output->writeln(
        sprintf('  <fg=%s>%s %s: <fg=white>%s</></>', $textColor, $symbol, $key, OutputFormatter::escape($value)),
      );
    }
  }

  private function renderSuccess(string $message, array $stats): void
  {
    $statsStr = json_encode($stats);
    error_log("RemoteComponents: {$message}. Statistics: {$statsStr}");
  }

  private function guessClassFile(string $class): ?string
  {
    if (!class_exists($class)) {
      try {
        $reflection = new \ReflectionClass($class);
        $file = $reflection->getFileName();
        return $file ? str_replace(base_path() . '/', '', $file) : null;
      } catch (\ReflectionException $e) {
        return null;
      }
    }

    return null;
  }

  private function normalizePanels(string|array $panels): string|array
  {
    if ($panels === '*') {
      return '*';
    }

    return is_string($panels) ? [$panels] : $panels;
  }

  public static function clear(): void
  {
    self::$components = [];
    self::$loaded = false;
  }

  public function getStatistics(): array
  {
    $stats = [];

    foreach (self::$components as $method => $items) {
      $stats[$method] = count($items);
    }

    return $stats;
  }
}
