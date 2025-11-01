<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

if (!function_exists('error_response')) {
  /**
   * Create and display an error page immediately.
   *
   * @return object Fluent error builder
   */
  function error_response(): object
  {
    return new class {
      private int $code = 500;

      private string $message = 'An error has occurred.';

      private ?string $source = null;

      private ?string $details = null;

      private ?string $trace = null;

      private ?string $identifier = null;

      private ?string $errorCode = null;

      private ?string $documentationUrl = null;

      private ?string $originalUrl = null;

      private ?string $previousUrl = null;

      private array $actions = [];

      private bool $showBackButton = true;

      private bool $showReloadButton = true;

      private bool $showHomeButton = true;

      public function code(int $code): self
      {
        $this->code = $code;
        return $this;
      }

      public function message(string $message): self
      {
        $this->message = $message;
        return $this;
      }

      public function source(string $source): self
      {
        $this->source = $source;
        return $this;
      }

      public function details(string $details): self
      {
        $this->details = $details;
        return $this;
      }

      public function trace(string $trace): self
      {
        $this->trace = $trace;
        return $this;
      }

      public function identifier(?string $customIdentifier = null): self
      {
        $this->identifier = $customIdentifier ?? strtoupper(Str::ulid()->toString());
        return $this;
      }

      public function errorCode(string $code): self
      {
        $this->errorCode = $code;
        return $this;
      }

      public function documentation(string $url): self
      {
        $this->documentationUrl = $url;
        return $this;
      }

      public function originalUrl(?string $url): self
      {
        $this->originalUrl = $url;
        return $this;
      }

      public function previousUrl(?string $url): self
      {
        $this->previousUrl = $url;
        return $this;
      }

      public function showBackButton(bool $show = true): self
      {
        $this->showBackButton = $show;
        return $this;
      }

      public function showReloadButton(bool $show = true): self
      {
        $this->showReloadButton = $show;
        return $this;
      }

      public function showHomeButton(bool $show = true): self
      {
        $this->showHomeButton = $show;
        return $this;
      }

      public function action(
        string $type,
        string $color,
        string $label,
        ?string $href = null,
        ?string $description = null,
        ?string $tooltip = null,
      ): self {
        if (!in_array($type, ['button', 'link'], true)) {
          throw new \InvalidArgumentException("Action type must be 'button' or 'link', got: {$type}");
        }

        if ($type === 'link' && empty($href)) {
          throw new \InvalidArgumentException('Link actions require an href');
        }

        if ($href !== null && str_contains($href, '?')) {
          throw new \InvalidArgumentException('URLs must not contain query parameters (?). Use clean URLs only.');
        }

        $this->actions[] = [
          'type' => $type,
          'color' => $color,
          'label' => $label,
          'description' => $description,
          'tooltip' => $tooltip,
          'href' => $href,
        ];

        return $this;
      }

      public function redirect(): never
      {
        if ($this->identifier === null) {
          $this->identifier = strtoupper(Str::ulid()->toString());
        }

        if ($this->originalUrl === null) {
          $this->originalUrl = request()->fullUrl();
        }

        if ($this->previousUrl === null) {
          $this->previousUrl = request()->header('referer') ?? $this->originalUrl;
        }

        $token = bin2hex(random_bytes(6));

        $errorData = [
          'identifier' => $this->identifier,
          'message' => $this->message,
          'code' => $this->code,
          'source' => $this->source,
          'details' => $this->details,
          'trace' => $this->trace,
          'error_code' => $this->errorCode,
          'documentation_url' => $this->documentationUrl,
          'original_url' => $this->originalUrl,
          'previous_url' => $this->previousUrl,
          'actions' => $this->actions,
          'show_back_button' => $this->showBackButton,
          'show_reload_button' => $this->showReloadButton,
          'show_home_button' => $this->showHomeButton,
        ];

        Cache::put('error_' . $token, $errorData, now()->addMinutes(10));

        $this->logError($errorData, $token);

        redirect()
          ->to('/application-error/' . $token)
          ->send();

        exit();
      }

      public function throw(): never
      {
        $this->redirect();
      }

      private function logError(array $errorData, string $token): void
      {
        try {
          $logEntry = sprintf(
            "[%s] ID: %s | Token: %s | Code: %d | %s\n",
            now()->toIso8601String(),
            $errorData['identifier'],
            $token,
            $errorData['code'],
            $errorData['message'],
          );

          if ($errorData['source']) {
            $logEntry .= "Source: {$errorData['source']}\n";
          }

          if (isset($errorData['error_code']) && $errorData['error_code']) {
            $logEntry .= "Error Code: {$errorData['error_code']}\n";
          }

          if ($errorData['original_url']) {
            $logEntry .= "URL Attempted: {$errorData['original_url']}\n";
          }

          if ($errorData['previous_url']) {
            $logEntry .= "URL Previous: {$errorData['previous_url']}\n";
          }

          if ($errorData['trace']) {
            $logEntry .= "Trace: {$errorData['trace']}\n";
          }

          if ($errorData['details']) {
            $logEntry .= "Details: {$errorData['details']}\n";
          }

          $logEntry .= str_repeat('-', 80) . "\n";

          \Illuminate\Support\Facades\Storage::disk('local')->append('logs/app-errors.log', $logEntry);
        } catch (\Throwable $e) {
          // Silent fail on logging errors
        }
      }
    };
  }
}

if (!function_exists('exception_response')) {
  /**
   * Create error response from an exception.
   *
   * @param \Throwable $exception The exception to convert
   * @return object Fluent error builder
   */
  function exception_response(\Throwable $exception): object
  {
    return error_response()
      ->code(500)
      ->message($exception->getMessage())
      ->source(get_class($exception))
      ->details($exception->getFile() . ':' . $exception->getLine())
      ->trace($exception->getTraceAsString());
  }
}
