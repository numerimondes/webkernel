<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Base\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BaseServiceProvider extends ServiceProvider
{
  /**
   * Taille maximale du fichier de log en bytes (100MB)
   */
  private const MAX_LOG_SIZE = 100 * 1024 * 1024; // 100MB en bytes

  public function register()
  {
    //
  }

  public function boot()
  {
    $this->manageLogFileSize();
    $this->optimizeLogging();
  }

  /**
   * Vérifie et gère la taille du fichier laravel.log
   * Si le fichier dépasse 100MB, il est supprimé
   */
  private function manageLogFileSize(): void
  {
    $logPath = storage_path('logs/laravel.log');

    if (File::exists($logPath)) {
      $fileSize = File::size($logPath);

      if ($fileSize > self::MAX_LOG_SIZE) {
        try {
          File::delete($logPath);

          // Log l'action dans un nouveau fichier
          Log::info('Fichier laravel.log supprimé automatiquement', [
            'taille_precedente' => $this->formatBytes($fileSize),
            'seuil_maximum' => $this->formatBytes(self::MAX_LOG_SIZE),
            'timestamp' => now()->toDateTimeString(),
          ]);
        } catch (\Exception $e) {
          // En cas d'erreur, on log dans un fichier séparé pour éviter les boucles
          error_log('Erreur lors de la suppression du fichier laravel.log: ' . $e->getMessage());
        }
      }
    }
  }

  /**
   * Optimise la configuration des logs pour réduire la verbosité
   */
  private function optimizeLogging(): void
  {
    // Configurer le niveau de log en fonction de l'environnement
    if (app()->environment('production')) {
      // En production, ne logger que les erreurs et warnings critiques
      config(['logging.level' => 'error']);
    } elseif (app()->environment('staging', 'testing')) {
      // En staging/testing, réduire la verbosité
      config(['logging.level' => 'warning']);
    }

    // Configurer la rotation automatique des logs
    config([
      'logging.channels.single.max_files' => 5, // Garder max 5 fichiers de logs
      'logging.channels.daily.days' => 7, // Logs quotidiens gardés 7 jours
    ]);

    // Désactiver certains logs verbeux si ce n'est pas en mode debug
    if (!config('app.debug')) {
      // Réduire les logs de ArcanesServicesProvider
      $this->reduceArcanesLogging();
    }
  }

  /**
   * Réduit la verbosité des logs de ArcanesServicesProvider
   */
  private function reduceArcanesLogging(): void
  {
    // Créer un canal de log personnalisé pour les messages moins importants
    config([
      'logging.channels.arcanes_verbose' => [
        'driver' => 'single',
        'path' => storage_path('logs/arcanes-verbose.log'),
        'level' => 'debug',
        'days' => 3, // Garder seulement 3 jours
      ],
    ]);

    // En production, rediriger les logs verbeux vers un fichier séparé
    if (app()->environment('production')) {
      // Les logs de panels et de modules sont redirigés vers un canal moins critique
      Log::info('Configuration des logs optimisée pour réduire la verbosité', [
        'environment' => app()->environment(),
        'log_level' => config('logging.level', 'debug'),
        'max_files' => config('logging.channels.single.max_files', 'non défini'),
        'daily_retention' => config('logging.channels.daily.days', 'non défini'),
      ]);
    }
  }

  /**
   * Formate les bytes en unités lisibles
   */
  private function formatBytes(int $size): string
  {
    $units = ['B', 'KB', 'MB', 'GB'];
    $unitIndex = 0;

    while ($size >= 1024 && $unitIndex < count($units) - 1) {
      $size /= 1024;
      $unitIndex++;
    }

    return round($size, 2) . ' ' . $units[$unitIndex];
  }
}
