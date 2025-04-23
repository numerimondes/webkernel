<?php

namespace Webkernel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallerUpdateComposers extends Command
{
    protected $signature = 'webkernel:install-update-composer';
    protected $description = 'Webkernel Installer Composer Updater';
    protected $hidden = true;
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->modifyComposerJson();
    }

    protected function modifyComposerJson(): void
    {
        $this->displayLogo();

        $composerPath = base_path('composer.json');

        if (!File::exists($composerPath)) {
            $this->error('❌ composer.json not found. Please run this command from your Laravel project root.');
            exit(1);
        }

        $composer = json_decode(File::get($composerPath), true);

        if (!isset($composer['require']['laravel/framework'])) {
            $this->error('❌ Laravel framework not found in dependencies.');
            exit(1);
        }

        $this->info('✅ Laravel detected.');
        $this->info('🔍 Checking installed Composer packages...');

        exec('composer show -N', $installedPackages);
        $installedPackages = array_flip($installedPackages);

        $this->info('[✓] Found composer.json at: ' . $composerPath);

        $namespace = 'Webkernel\\';
        $path = 'packages/webkernel/src/';

        // Add PSR-4 autoload entry if not present
        if (!isset($composer['autoload']['psr-4'][$namespace]) || $composer['autoload']['psr-4'][$namespace] !== $path) {
            $composer['autoload']['psr-4'][$namespace] = $path;
            $this->info('[✓] Added PSR-4 autoload entry for WebKernel');
        } else {
            $this->info('[✓] PSR-4 autoload entry for WebKernel already present');
        }

        // Add local repository for WebKernel if not present
        $repo = ['type' => 'path', 'url' => 'packages/webkernel'];
        $foundRepo = false;

        foreach ($composer['repositories'] ?? [] as $r) {
            if ($r['type'] === 'path' && $r['url'] === $repo['url']) {
                $foundRepo = true;
                break;
            }
        }

        if (!$foundRepo) {
            $composer['repositories'][] = $repo;
            $this->info('[✓] Added local repository for WebKernel');
        } else {
            $this->info('[✓] Local repository for WebKernel already present');
        }

        // Set minimum stability to dev if not set
        if (!isset($composer['minimum-stability'])) {
            $composer['minimum-stability'] = 'dev';
            $this->info('[✓] Added minimum-stability: dev');
        } else {
            $this->info('[✓] minimum-stability already set: ' . $composer['minimum-stability']);
        }

        // Set prefer-stable to true if not set
        if (!isset($composer['prefer-stable']) || $composer['prefer-stable'] !== true) {
            $composer['prefer-stable'] = true;
            $this->info('[✓] Added prefer-stable: true');
        } else {
            $this->info('[✓] prefer-stable already set');
        }

        // Save changes to composer.json
        File::put($composerPath, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->info('[✓] Saved changes to composer.json');
    }

    protected function displayLogo(): void
    {
        $this->info('====================================');
        $this->info('  Webkernel Installer Update');
        $this->info('====================================');
    }
}
