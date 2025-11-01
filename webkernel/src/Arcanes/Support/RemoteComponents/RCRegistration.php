<?php

declare(strict_types=1);

namespace Webkernel\Arcanes\Support\RemoteComponents;

trait RCRegistration
{
  public static function registerComponent(string $class, string|array $panels): void
  {
    $service = app(RCService::class);
    $method = self::detectMethod($class);
    $service->register($method, $class, $panels);
  }

  public static function registerComponents(array $components): void
  {
    $service = app(RCService::class);

    foreach ($components as $method => $items) {
      foreach ($items as $item) {
        if (is_string($item)) {
          $service->register($method, $item, '*');
        } elseif (is_array($item) && count($item) >= 2) {
          $service->register($method, $item[0], $item[1]);
        }
      }
    }
  }

  private static function detectMethod(string $class): string
  {
    $name = class_basename($class);

    if (str_contains($name, 'Resource')) {
      return 'resources';
    }

    if (str_contains($name, 'Page')) {
      return 'pages';
    }

    if (str_contains($name, 'Widget')) {
      return 'widgets';
    }

    if (str_contains($name, 'Middleware')) {
      return 'middleware';
    }

    return 'resources';
  }
}
