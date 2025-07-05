<?php

declare(strict_types=1);

namespace Webkernel\Console\Platform;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ComposerActionCommand extends Command
{
    protected $signature = 'webkernel:composer-action
        {--pre-install-cmd}
        {--post-install-cmd}
        {--pre-update-cmd}
        {--post-update-cmd}
        {--pre-autoload-dump}
        {--post-autoload-dump}
        {--pre-status-cmd}
        {--post-status-cmd}
        {--pre-package-install}
        {--post-package-install}
        {--pre-package-update}
        {--post-package-update}
        {--pre-package-uninstall}
        {--post-package-uninstall}
        {--post-root-package-install}
        {--post-create-project-cmd}';

    protected $description = 'Dispatch specific Artisan commands based on composer lifecycle events.';

    public function handle(): int
    {
        Artisan::call('webkernel:show-ascii-header');
        $this->line(Artisan::output()); // 👈 Affiche la sortie du header

        if (! $this->checkWholePlatformPreRequisites()) {
            return self::FAILURE;
        }

        $hookMap = $this->getHookMapComposerActionCommand();
        $executed = false;

        foreach ($hookMap as $option => $artisanCommands) {
            if (! $this->option($option)) {
                continue;
            }

            if (empty($artisanCommands)) {
                $this->warn("[$option] is not implemented. Skipped.");
                $this->logAction($option, 'SKIPPED (not implemented)');
                continue;
            }

            foreach ($artisanCommands as $artisanCommand) {
                $this->info("Executing [$option] → $artisanCommand");
                $this->logAction($option, "START $artisanCommand");

                try {
                    Artisan::call($artisanCommand);
                    $this->line(Artisan::output());
                    $this->logAction($option, "SUCCESS $artisanCommand");
                    $executed = true;
                } catch (\Throwable $e) {
                    $this->error("Error executing [$artisanCommand]: {$e->getMessage()}");
                    $this->logAction($option, "FAILURE: {$e->getMessage()}");
                    return self::FAILURE;
                }
            }
        }

        if (! $executed) {
            $this->handleComposerActionCommandWithNoOptions();
        }

        $this->afterProcessing();
        return self::SUCCESS;
    }

    protected function checkWholePlatformPreRequisites(): bool
    {
        if (! file_exists(base_path('composer.json'))) {
            $this->error('composer.json not found. Aborting.');
            $this->logAction('prerequisite', 'composer.json missing');
            return false;
        }

        $this->logAction('prerequisite', 'OK');
        return true;
    }

    protected function handleComposerActionCommandWithNoOptions(): void
    {
        $this->warn('No options provided or nothing executed.');
        $this->logAction('execution', 'No options provided');
    }

    protected function afterProcessing(): void
    {
        $this->info('Composer action(s) completed.');
        $this->logAction('execution', 'Completed');
    }

    protected function logAction(string $context, string $message): void
    {
        Log::channel('single')->info("[WebkernelComposerAction:$context] $message");
    }

    protected function getHookMapComposerActionCommand(): array
    {
        return [
            'pre-install-cmd' => [],
            'post-install-cmd' => [],
            'pre-update-cmd' => [],
            'post-update-cmd' => [],
            'pre-autoload-dump' => [],
            'post-autoload-dump' => [
                'webkernel:prepare-platform-composer'
            ],
            'pre-status-cmd' => [],
            'post-status-cmd' => [],
            'pre-package-install' => [],
            'post-package-install' => [],
            'pre-package-update' => [],
            'post-package-update' => [],
            'pre-package-uninstall' => [],
            'post-package-uninstall' => [],
            'post-root-package-install' => [],
            'post-create-project-cmd' => [],
        ];
    }
}
