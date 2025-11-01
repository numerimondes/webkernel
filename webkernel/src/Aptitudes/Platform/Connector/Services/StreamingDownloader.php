<?php

namespace Webkernel\Aptitudes\Platform\Connector\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class StreamingDownloader
{
  private const CHUNK_SIZE = 8192; // 8 Kio
  private const WINDOW_SECONDS = 5; // Fenêtre pour débit

  /**
   * Download and stream ZIP, emitting progress events.
   */
  public function downloadWithProgress(Response $response, string $outputPath): void
  {
    if ($response->failed()) {
      throw new \RuntimeException('Download response failed.');
    }

    $totalSize = $response->header('Content-Length', 0);
    $bytesDownloaded = 0;
    $startTime = microtime(true);
    $handle = fopen($outputPath, 'wb');

    $response->body()->each(function ($chunk) use (&$bytesDownloaded, $totalSize, &$startTime, $handle) {
      fwrite($handle, $chunk);
      $bytesDownloaded += strlen($chunk);

      $elapsed = microtime(true) - $startTime;
      if ($elapsed >= self::WINDOW_SECONDS) {
        $speed = $bytesDownloaded / $elapsed / 1024; // Kio/s
        $progress = $totalSize > 0 ? round(($bytesDownloaded / $totalSize) * 100) : 0;

        Event::dispatch(new DownloadProgressEvent($progress, $speed, $bytesDownloaded, $totalSize));
        Log::info("Download progress: {$progress}% at {$speed} Kio/s");

        $startTime = microtime(true);
        $bytesDownloaded = 0;
      }
    });

    fclose($handle);
    Event::dispatch(new DownloadCompletedEvent($outputPath));
  }

  /**
   * Emit Git-like progress message via console or event.
   */
  public static function reportProgress(int $percent, float $speed, int $bytes, int $total): void
  {
    $bar = str_repeat('=', $percent / 5) . str_repeat(' ', 20 - $percent / 5);
    $message = "Receiving objects: {$percent}% ({$bytes}/{$total}) ... {$speed} Kio/s\r";
    echo $message; // For CLI; use event for UI
  }
}

// Événements (définir dans un dossier Events si needed)
class DownloadProgressEvent
{
  public function __construct(public int $percent, public float $speed, public int $bytes, public int $total) {}
}

class DownloadCompletedEvent
{
  public function __construct(public string $path) {}
}
