<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Security\CommandGuard;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Output\ConsoleOutput;

class CommandGuardServiceProvider extends ServiceProvider
{
  public function register(): void
  {
    $this->app->singleton(AuditLogger::class, function () {
      return new AuditLogger();
    });

    $this->app->singleton(CommandBlocker::class, function ($app) {
      return new CommandBlocker($app->make(AuditLogger::class));
    });

    $this->app->singleton(CommandConfirmation::class, function ($app) {
      return new CommandConfirmation($app->make(AuditLogger::class));
    });

    $this->app->singleton(ConsoleHeader::class, function () {
      return new ConsoleHeader();
    });
  }

  public function boot(): void
  {
    if (!app()->runningInConsole()) {
      return;
    }

    $this->guardCommand();
  }

  private function displayHeader(): void
  {
    app(ConsoleHeader::class)->display();
  }

  private function guardCommand(): void
  {
    $commandName = $_SERVER['argv'][1] ?? null;

    if (!$commandName) {
      return;
    }

    $config = basix();
    $restrictedEnvs = $config['restricted_environments'] ?? [];

    if (!$this->isRestrictedEnvironment($restrictedEnvs)) {
      return;
    }

    $blocker = app(CommandBlocker::class);
    $confirmation = app(CommandConfirmation::class);

    $isBlocked = $blocker->isBlocked($commandName);
    $needsApproval =
      $confirmation->requiresConfirmation($commandName) ||
      $confirmation->requiresPassword($commandName) ||
      $confirmation->requiresBackup($commandName);

    if ($isBlocked || $needsApproval) {
      $this->displayHeader();
    }

    if ($isBlocked) {
      $blocker->block($commandName);
    }

    if ($needsApproval) {
      if (!$confirmation->confirm($commandName)) {
        exit(1);
      }
    }
  }

  private function isRestrictedEnvironment(array $envs): bool
  {
    if ($envs === ['*']) {
      return true;
    }

    return in_array(app()->environment(), $envs, true);
  }
}

class AuditLogger
{
  private string $logPath;

  public function __construct()
  {
    $this->logPath = storage_path('basix/basix.log');
  }

  public function log(string $commandName, string $status, string $reason = ''): void
  {
    $logDirectory = dirname($this->logPath);

    if (!is_dir($logDirectory)) {
      mkdir($logDirectory, 0755, true);
    }

    $entry = sprintf(
      "[%s] Command: %s | Status: %s | Environment: %s | Reason: %s\n",
      now()->format('Y-m-d H:i:s'),
      $commandName,
      $status,
      app()->environment(),
      $reason,
    );

    $result = file_put_contents($this->logPath, $entry, FILE_APPEND | LOCK_EX);

    if ($result === false) {
      error_log('Failed to write to audit log: ' . $this->logPath);
    }
  }
}

class ConsoleHeader
{
  private ConsoleOutput $output;

  public function __construct()
  {
    $this->output = new ConsoleOutput();
  }

  public function display(): void
  {
    $env = strtoupper(app()->environment());
    $datetime = now()->format('Y-m-d H:i:s');

    $this->output->writeln([
      '',
      '<fg=cyan>====================================================</>',
      '<fg=green>BASIX - Basic Command Restrictions eXperience</>',
      '<fg=cyan>====================================================</>',
      '<fg=white>Environment: <fg=yellow>' . $env . '</>',
      '<fg=white>DateTime: <fg=yellow>' . $datetime . '</>',
      '<fg=cyan>====================================================</>',
      '',
    ]);
  }
}

class CommandConfirmation
{
  private ConsoleOutput $output;
  private AuditLogger $logger;
  private array $config;

  public function __construct(AuditLogger $logger)
  {
    $this->config = basix();
    $this->logger = $logger;
    $this->output = new ConsoleOutput();
  }

  public function requiresConfirmation(string $commandName): bool
  {
    $allowListEntry = $this->getAllowListEntry($commandName);

    return is_array($allowListEntry) && isset($allowListEntry['confirm']) && $allowListEntry['confirm'] === true;
  }

  public function requiresPassword(string $commandName): bool
  {
    $allowListEntry = $this->getAllowListEntry($commandName);

    return is_array($allowListEntry) && isset($allowListEntry['password']) && $allowListEntry['password'] === true;
  }

  public function requiresBackup(string $commandName): bool
  {
    $allowListEntry = $this->getAllowListEntry($commandName);

    return is_array($allowListEntry) && isset($allowListEntry['backup']) && $allowListEntry['backup'] === true;
  }

  public function getReason(string $commandName): string
  {
    $allowListEntry = $this->getAllowListEntry($commandName);

    return is_array($allowListEntry) && isset($allowListEntry['reason'])
      ? $allowListEntry['reason']
      : 'Command requires approval';
  }

  public function confirm(string $commandName): bool
  {
    $reason = $this->getReason($commandName);

    $requiresPassword = $this->requiresPassword($commandName);
    $requiresConfirmation = $this->requiresConfirmation($commandName);
    $requiresBackup = $this->requiresBackup($commandName);

    if ($requiresPassword && !$this->validatePassword($commandName, $reason)) {
      return false;
    }

    if ($requiresConfirmation && !$this->askConfirmation($commandName, $reason)) {
      return false;
    }

    if ($requiresBackup && !$this->performBackup($commandName)) {
      return false;
    }

    $this->logger->log($commandName, 'CONFIRMED', $reason);

    return true;
  }

  private function validatePassword(string $commandName, string $reason): bool
  {
    $this->output->writeln('<fg=yellow>WARNING: This command is risky</>');
    $this->output->writeln('<fg=white>Reason: <fg=red>' . $reason . '</>');
    $this->output->write('<fg=cyan>Enter password: </>');

    $input = trim((string) fgets(STDIN));

    if ($input !== $this->config['password']) {
      $this->logger->log($commandName, 'PASSWORD_FAILED', $reason);
      $this->output->writeln('<error>Incorrect password</error>');

      return false;
    }

    return true;
  }

  private function askConfirmation(string $commandName, string $reason): bool
  {
    $this->output->writeln('<fg=yellow>WARNING: This command is risky</>');
    $this->output->writeln('<fg=white>Reason: <fg=red>' . $reason . '</>');
    $this->output->write('<fg=cyan>Type "YES" to continue or press Enter to cancel [no]: </>');

    $input = strtoupper(trim((string) fgets(STDIN)));

    if ($input === '') {
      $input = 'NO';
    }

    if ($input !== 'YES') {
      $this->logger->log($commandName, 'CONFIRMATION_DENIED', $reason);
      $this->output->writeln('<comment>Command cancelled</comment>');

      return false;
    }

    return true;
  }

  private function performBackup(string $commandName): bool
  {
    $this->output->writeln('<fg=cyan>Creating database backup before command execution...</</>');

    $backupPath = storage_path('basix/backups');

    if (!is_dir($backupPath)) {
      mkdir($backupPath, 0755, true);
    }

    $timestamp = now()->format('Y-m-d_H-i-s');
    $backupFile = $backupPath . '/' . $commandName . '_' . $timestamp . '.sql';

    $connection = config('database.default');
    $config = config('database.connections.' . $connection);

    if (!$config || !isset($config['driver']) || $config['driver'] !== 'mysql') {
      $this->logger->log($commandName, 'BACKUP_SKIPPED', 'Database driver not supported');
      $this->output->writeln('<fg=yellow>Backup skipped: only MySQL is supported</>');

      return true;
    }

    $command = $this->buildMysqldumpCommand($config, $backupFile);

    if ($command === null) {
      $this->logger->log($commandName, 'BACKUP_FAILED', 'Invalid database configuration');
      $this->output->writeln('<error>Backup failed: database configuration error</error>');

      return false;
    }

    $exitCode = 0;
    passthru($command, $exitCode);

    if ($exitCode !== 0) {
      $this->logger->log($commandName, 'BACKUP_FAILED', 'mysqldump execution failed');
      $this->output->writeln('<error>Backup failed: mysqldump error</error>');

      return false;
    }

    if (!file_exists($backupFile) || filesize($backupFile) === 0) {
      $this->logger->log($commandName, 'BACKUP_FAILED', 'Backup file is empty');
      $this->output->writeln('<error>Backup failed: backup file is empty</error>');

      return false;
    }

    $backupSize = $this->formatBytes(filesize($backupFile));
    $this->logger->log($commandName, 'BACKUP_CREATED', 'Backup file: ' . $backupFile . ' (' . $backupSize . ')');
    $this->output->writeln('<fg=green>Backup created successfully: ' . $backupFile . ' (' . $backupSize . ')</>');

    return true;
  }

  private function buildMysqldumpCommand(array $config, string $backupFile): ?string
  {
    $host = $config['host'] ?? 'localhost';
    $port = $config['port'] ?? 3306;
    $database = $config['database'] ?? null;
    $username = $config['username'] ?? null;
    $password = $config['password'] ?? null;

    if ($database === null || $username === null) {
      return null;
    }

    $command = 'mysqldump';
    $command .= ' --host=' . escapeshellarg($host);
    $command .= ' --port=' . (int) $port;
    $command .= ' --user=' . escapeshellarg($username);

    if ($password !== null && $password !== '') {
      $command .= ' --password=' . escapeshellarg($password);
    }

    $command .= ' ' . escapeshellarg($database);
    $command .= ' > ' . escapeshellarg($backupFile) . ' 2>&1';

    return $command;
  }

  private function formatBytes(int $bytes): string
  {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= 1 << 10 * $pow;

    return round($bytes, 2) . ' ' . $units[$pow];
  }

  private function getAllowListEntry(string $commandName): mixed
  {
    $allowList = $this->config['allow_list'] ?? [];

    return $allowList[$commandName] ?? null;
  }
}

class CommandBlocker
{
  private array $config;
  private AuditLogger $logger;
  private ConsoleOutput $output;

  public function __construct(AuditLogger $logger)
  {
    $this->config = basix();
    $this->logger = $logger;
    $this->output = new ConsoleOutput();
  }

  public function isBlocked(string $commandName): bool
  {
    $blockList = $this->config['block_list'] ?? [];

    return isset($blockList[$commandName]) && !$this->isAllowed($commandName);
  }

  public function block(string $commandName): void
  {
    $blockList = $this->config['block_list'] ?? [];
    $reason = $blockList[$commandName] ?? 'Command blocked for security reasons';

    $this->logger->log($commandName, 'BLOCKED', $reason);

    $this->output->writeln([
      '',
      '<error>COMMAND BLOCKED: ' . strtoupper($commandName) . '</error>',
      '<comment>Reason: ' . $reason . '</comment>',
      '<fg=red>This operation is not permitted in this environment.</>',
      '',
    ]);

    exit(1);
  }

  private function isAllowed(string $commandName): bool
  {
    $allowList = $this->config['allow_list'] ?? [];

    return isset($allowList[$commandName]);
  }
}
