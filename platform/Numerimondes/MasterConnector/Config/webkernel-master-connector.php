<?php

return [
  /*
    |--------------------------------------------------------------------------
    | Master Connector Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Numerimondes master server module.
    |
    */

  // Storage disk for module ZIP files
  'storage_disk' => env('NUMERIMONDES_STORAGE_DISK', 'local'),

  // Storage path for modules
  'modules_path' => env('NUMERIMONDES_MODULES_PATH', 'numerimondes/modules'),

  // Master server secret for internal operations
  'master_secret' => env('NUMERIMONDES_MASTER_SECRET', '1k9a0j6k2k3g1k3k4k3k4k3k4k3k4'),

  // Token configuration
  'token' => [
    'length' => 32, // bytes (256 bits)
    'hash_algo' => 'sha256',
  ],

  // Rate limiting (requests per hour)
  'rate_limits' => [
    'auth' => 60, // Validation requests per IP
    'download' => 10, // Download requests per token
    'list' => 300, // List requests per token
  ],

  // Download configuration
  'download' => [
    'chunk_size' => 8192, // 8 KiB
    'timeout_base' => 30, // seconds
    'timeout_per_mb' => 10, // additional seconds per MB
  ],

  // Cache configuration
  'cache' => [
    'ttl' => 3600, // 1 hour in seconds
    'prefix' => 'numerimondes.master',
  ],

  // Audit logging
  'audit' => [
    'enabled' => env('NUMERIMONDES_AUDIT_ENABLED', true),
    'log_downloads' => true,
    'log_validations' => true,
  ],

  // Organization features (PROPLUS)
  'organizations' => [
    'enabled' => env('NUMERIMONDES_ORGANIZATIONS_ENABLED', false),
    'custom_namespaces' => true,
  ],

  // Validation
  'validation' => [
    'strict_domain' => true, // Enforce exact domain matching
    'allow_wildcards' => false, // MVP: no wildcards
  ],
];
