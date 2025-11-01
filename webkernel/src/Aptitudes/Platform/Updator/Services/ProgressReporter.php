<?php

namespace Webkernel\Aptitudes\Platform\Updator\Services;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class ProgressReporter
{
  public function __construct(private Application $app) {}

  public function report(string $message, int $percent = 0): void
  {
    $formatted = $percent > 0 ? sprintf('Receiving objects: %d%% (%s)', $percent, $message) : $message;
    Log::info($formatted);

    if ($this->app->runningInConsole()) {
      echo $formatted . PHP_EOL;
    } else {
      Event::dispatch(new ProgressEvent($formatted, $percent));
    }
  }
}

class ProgressEvent
{
  public function __construct(public string $message, public int $percent) {}
}
