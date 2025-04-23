<?php

namespace Webkernel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InstallerSyncComposer extends Command
{
    protected $signature = 'webkernel:sync-composer';
    protected $description = 'Adds Webkernel and its local dependencies to the main composer.json';
    protected $hidden = true;
    public function handle()
    {
        $this->displayManualConfigurationWarning();

        $choices = ['global']; // List of available options (can be extended later)

        if (count($choices) === 1) {
            $choice = $choices[0];
        } else {
            $choice = $this->choice(
                'What type of synchronization would you like to perform?',
                $choices,
                0
            );
        }

        if ($choice === 'global') {
            $this->info('Global synchronization in progress...');
            $this->displayComposerHeader();
            $this->syncGlobalComposer();
        }
    }

    public function rootConfiguration(): array
    {
        return [
            'packages' => [
            ],
            'paths' => [
                './packages/webkernel'
            ],
            'autoload' => [
                'psr-4' => [
                    'Webkernel\\' => 'packages/webkernel/src/',
                    "Webkernel\\Database\\Factories\\" => "packages/webkernel/src/database/factories/",
                    "Webkernel\\Database\\Seeders\\" => "packages/webkernel/src/database/seeders/",
                ],
                'files' => [
                    'packages/webkernel/src/Helpers/helpers.php',
                ]
            ]
        ];
    }

    public function syncGlobalComposer(bool $calledOutsideCli = false): int
    {
        $log = fn($message) => $calledOutsideCli ? Log::info($message) : $this->info($message);
        $error = fn($message) => $calledOutsideCli ? Log::error($message) : $this->error($message);
        $line = fn($message) => $calledOutsideCli ? Log::debug($message) : $this->line($message);

        $log('🔄 Loading the main composer.json...');
        $mainComposerPath = base_path('composer.json');

        if (!file_exists($mainComposerPath)) {
            $error('/!\ composer.json not found at the root of the project.');
            return 1;
        }

        $json = json_decode(file_get_contents($mainComposerPath), true);
        $config = $this->rootConfiguration();

        foreach ($config['packages'] as $package => $version) {
            $json['require'][$package] = $version;
        }

        foreach ($config['paths'] as $path) {
            if (!collect($json['repositories'] ?? [])->contains(fn($repo) => $repo['type'] === 'path' && $repo['url'] === $path)) {
                $json['repositories'][] = ['type' => 'path', 'url' => $path];
            }
        }

        // Ensure autoload and psr-4 keys exist
        $json['autoload'] = $json['autoload'] ?? [];
        $json['autoload']['psr-4'] = $json['autoload']['psr-4'] ?? [];

        // Merge psr-4
        foreach ($config['autoload']['psr-4'] as $namespace => $dir) {
            $json['autoload']['psr-4'][$namespace] = $dir;
        }

        // Merge files autoload
        $json['autoload']['files'] = array_values(array_unique(array_merge(
            $json['autoload']['files'] ?? [],
            $config['autoload']['files']
        )));

        // Backup and write
        copy($mainComposerPath, base_path('composer.json.backup'));
        file_put_contents($mainComposerPath, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $log('[✓] composer.json updated.');
        $log('[✓] Running composer dump-autoload...');
        exec('composer dump-autoload', $output);
        $line(implode("\n", $output));

        $log('[✓] Running composer update...');
        exec('composer update', $updateOutput);
        $line(implode("\n", $updateOutput));

        return 0;
    }

    public function displayManualConfigurationWarning()
    {
       // $this->info(PHP_EOL);
       // $this->line("\033[31mWARNING: BEWARE OF THIS MANUAL CONFIGURATION\033[0m");
       // $this->info(PHP_EOL);
       // $this->line("\033[31mThis action is based on the file\033[0m");
       // $this->line("\033[31mpackages/webkernel/src/Commands/LoadToMainComposerCommand.php,\033[0m");
       // $this->line("\033[31mwhich is manually configured. This action will add these\033[0m");
       // $this->line("\033[31mpackages if they are not already present in the composer at\033[0m");
       // $this->line("\033[31mthe root of " . base_path() . "...\033[0m");
       // $this->info(PHP_EOL);

        $config = $this->rootConfiguration();
        $this->info('The following packages will be added:');
        $this->info(PHP_EOL);
        foreach ($config['packages'] as $package => $version) {
            $this->info("- $package => $version");
        }
    }

    public function displayComposerHeader()
    {
        $this->info(PHP_EOL);
        $this->info(" __      __      ___.    ____  __.                         .__   ");
        $this->info("/  \\    /  \\ ____\\_ |__ |    |/ _|___________  ____   ____ |  |  ");
        $this->info("\\   \\/\\/   // __ \\| __ \\|      <_/ __ \\_  __ \\/    \\_/ __ \\|  |  ");
        $this->info(" \\        /\\  ___/| \\_\\ \\    |  \\  ___/|  | \\/   |  \\  ___/|  |__");
        $this->info("  \\__/\\  /  \\___  >___  /____|__ \\___  >__|  |___|  /\\___  >____/");
        $this->info("       \\/       \\/    \\/        \\/   \\/           \\/     \\/      ");
        $this->info(PHP_EOL);
        $this->info("By \033]8;;http://www.numerimondes.com\033\\Numerimondes\033]8;;\033\\ for Laravel and FilamentPHP");
        $this->info(PHP_EOL);
    }
}
