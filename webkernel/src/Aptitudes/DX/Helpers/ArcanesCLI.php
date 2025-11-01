<?php
declare(strict_types=1);
namespace Webkernel\Aptitudes\DX\Helpers;

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Symfony\Component\Console\Application as ConsoleApp;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Console\Command;
use ReflectionClass;

class ArcanesCLI
{
    protected Application $app;
    protected ConsoleApp $console;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->console = new ConsoleApp('WebKernel Aptitudes', $app->version());
    }

    public function handleCommand(InputInterface $input): int
    {
        $this->register();

        $name = $input->getFirstArgument();
        if ($name === null) {
            return $this->help();
        }

        // If command is Aptitudes-only, run via Symfony
        if ($this->isAptitudesCommand($name)) {
            return $this->console->run($input);
        }

        // Otherwise, delegate to Laravel's Artisan kernel
        return $this->app[ConsoleKernel::class]->handle($input, new ConsoleOutput());
    }

    protected function isAptitudesCommand(string $name): bool
    {
        $cmd = $this->console->has($name) ? $this->console->get($name) : null;
        return $cmd !== null && !$cmd->isHidden();
    }

    protected function register(): void
    {
        $base = $this->app->basePath('webkernel/src');
        if (!is_dir($base)) return;

        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($it as $f) {
            if ($f->getExtension() === 'php') {
                $this->load($f->getPathname());
            }
        }
    }

    protected function load(string $file): void
    {
        $class = $this->extract($file);
        if (!$class || !$this->isValid($class)) return;

        try {
            $cmd = new $class();
            if ($cmd instanceof Command) {
                $cmd->setLaravel($this->app);
                $cmd->setHidden(false);
                $this->console->add($cmd);
            }
        } catch (\Throwable) {

        }
    }

    protected function extract(string $file): ?string
    {
        $c = file_get_contents($file);
        return preg_match('/class\s+(\w+)\s+extends\s+Command/', $c, $m)
            && preg_match('/namespace\s+([^;]+);/', $c, $n)
            ? "$n[1]\\$m[1]"
            : null;
    }

    protected function isValid(string $class): bool
    {
        try {
            $r = new ReflectionClass($class);
            if (!$r->isSubclassOf(Command::class) || $r->isAbstract() || $r->isInterface()) return false;
            $p = $r->getProperty('aptitudes_command');
            $p->setAccessible(true);
            return $p->getValue(new $class()) === true;
        } catch (\ReflectionException) {
            return false;
        }
    }

    protected function help(): int
    {
        $out = new ConsoleOutput();
        $out->writeln("\nWebKernel Aptitudes CLI");
        $out->writeln("Modular command interface for all registered Aptitudes.");
        $out->writeln("<comment>Framework-driven. Developer-focused. Extensible by design.<comment>\n");
        $out->writeln("Usage:\n  <comment>command</comment> [options] [arguments]\n");

        $groups = [];
        foreach ($this->console->all() as $cmd) {
            if ($cmd->isHidden()) continue;
            $prefix = str_contains($cmd->getName(), ':') ? explode(':', $cmd->getName())[0] : '_core';
            $groups[$prefix][] = [$cmd->getName(), $cmd->getDescription()];
        }

        $out->writeln("Available commands");
        foreach ($groups as $prefix => $cmds) {
            if ($prefix !== '_core') $out->writeln("\n" . ucfirst($prefix) . " commands");
            foreach ($cmds as [$name, $desc]) {
                $out->writeln('  <comment>' . str_pad($name, 20) . '</comment> ' . $desc);
            }
        }

        $out->writeln('');
        return 0;
    }

    public function __call(string $method, array $args)
    {
        return $this->app->$method($args);
    }
}
