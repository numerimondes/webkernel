<?php
// packages/webkernel/src/Console/Commands/CleanupAssetsCache.php

namespace Webkernel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CleanupAssetsCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webkernel:cleanup-assets-cache {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired assets cache entries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will clean up all expired assets cache entries. Continue?')) {
                $this->info('Operation cancelled.');
                return;
            }
        }

        $this->info('Starting assets cache cleanup...');

        $cleaned = $this->cleanupExpiredTokens();
        $this->cleanupOrphanedEntries();

        $this->info("Cleanup completed. Removed {$cleaned} expired entries.");
    }

    /**
     * Clean up expired asset tokens
     */
    protected function cleanupExpiredTokens(): int
    {
        $cleaned = 0;
        $store = Cache::getStore();

        if (method_exists($store, 'getAllKeys')) {
            $keys = $store->getAllKeys();
        } else {
            // Pour les stores qui ne supportent pas getAllKeys
            $this->warn('Cache store does not support getAllKeys. Manual cleanup may be needed.');
            return 0;
        }

        foreach ($keys as $key) {
            if (str_starts_with($key, 'asset_token:') || str_starts_with($key, 'asset_url:')) {
                $data = Cache::get($key);

                if ($data && isset($data['expires_at']) && now()->gt($data['expires_at'])) {
                    Cache::forget($key);
                    $cleaned++;

                    // Nettoyer les entrées liées
                    if (str_starts_with($key, 'asset_token:') && isset($data['path'])) {
                        $urlCacheKey = 'asset_url:' . md5($data['path']);
                        Cache::forget($urlCacheKey);
                    }
                }
            }
        }

        return $cleaned;
    }

    /**
     * Clean up orphaned cache entries
     */
    protected function cleanupOrphanedEntries(): void
    {
        $store = Cache::getStore();

        if (!method_exists($store, 'getAllKeys')) {
            return;
        }

        $keys = $store->getAllKeys();
        $tokenKeys = [];
        $urlKeys = [];

        // Séparer les clés par type
        foreach ($keys as $key) {
            if (str_starts_with($key, 'asset_token:')) {
                $tokenKeys[] = $key;
            } elseif (str_starts_with($key, 'asset_url:')) {
                $urlKeys[] = $key;
            }
        }

        // Nettoyer les URLs orphelines
        foreach ($urlKeys as $urlKey) {
            $urlData = Cache::get($urlKey);
            if ($urlData && isset($urlData['token'])) {
                $tokenKey = 'asset_token:' . $urlData['token'];
                if (!in_array($tokenKey, $tokenKeys)) {
                    Cache::forget($urlKey);
                    $this->line("Removed orphaned URL cache: {$urlKey}");
                }
            }
        }
    }
}
