<?php

return [
  /*
    |--------------------------------------------------------------------------
    | Numerimondes Connector Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the client connector to the master server.
    |
    */

  // Master server URL
  'master_url' => env('NUMERIMONDES_MASTER_URL', 'https://numerimondes.com'),

  // Connection settings
  'connection' => [
    'timeout' => 30, // Base timeout in seconds
    'timeout_per_mb' => 10, // Additional seconds per MB
    'connect_timeout' => 10,
    'retry_attempts' => 3,
    'retry_delay' => 100, // Milliseconds
  ],

  // SSL/TLS settings
  'ssl' => [
    'verify' => env('NUMERIMONDES_SSL_VERIFY', true),
    'verify_peer' => true,
    'verify_peer_name' => true,
  ],

  // Download settings
  'download' => [
    'chunk_size' => 8192, // 8 KiB
    'temp_directory' => storage_path('app/temp/numerimondes'),
    'progress_window' => 5, // seconds for speed calculation
  ],

  // Sync settings
  'sync' => [
    'auto_sync' => env('NUMERIMONDES_AUTO_SYNC', false),
    'sync_frequency' => 'daily', // daily, weekly, monthly
    'stale_warning_days' => 7,
    'max_offline_days' => 30,
  ],

  // Cache settings
  'cache' => [
    'enabled' => true,
    'ttl' => 3600, // 1 hour
    'driver' => env('CACHE_DRIVER', 'file'),
    'prefix' => 'numerimondes.client',
  ],

  // Logging
  'logging' => [
    'channel' => env('LOG_CHANNEL', 'stack'),
    'level' => env('NUMERIMONDES_LOG_LEVEL', 'info'),
  ],
];
