<?php

declare(strict_types=1);

if (!function_exists('basix')) {
  /**
   * Retrieve or set values from the BASIX configuration.
   *
   * If an array is passed, it sets multiple values.
   * If null is passed, it returns the full configuration array.
   *
   * @param array<string, mixed>|string|null $key
   * @param mixed $default
   * @return mixed
   */
  function basix($key = null, $default = null)
  {
    static $config;

    if ($config === null) {
      $path = base_path('BASIX');
      $config = file_exists($path) ? include $path : [];
    }

    if ($key === null) {
      return $config;
    }

    if (is_array($key)) {
      $config = array_merge($config, $key);
      return true;
    }

    return $config[$key] ?? $default;
  }
}
