<?php
declare(strict_types=1);

namespace Webkernel\Arcanes\Support\Base;

/**
 * Contract for runtime loaders used by the service provider
 *
 * Implementations encapsulate specific loading tasks executed during
 * application bootstrapping. Each loader handles one aspect of module
 * initialization such as routes, helpers, policies, or providers.
 */
interface ArcanesLoader
{
  /**
   * Execute the loader task
   *
   * @return void
   */
  public function load(): void;
}
