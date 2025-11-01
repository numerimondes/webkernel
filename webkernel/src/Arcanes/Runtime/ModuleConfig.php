<?php
declare(strict_types=1);

namespace Webkernel\Arcanes\Runtime;

/**
 * Immutable module configuration
 */
class ModuleConfig
{
  public function __construct(
    public readonly string $id,
    public readonly string $name = '',
    public readonly string $version = '1.0.0',
    public readonly string $description = '',
    public readonly string $viewNamespace = '',
    public readonly array $dependencies = [],
    public readonly array $aliases = [],
    public readonly array $providers = [],
    public readonly array $providedComponents = [],
  ) {}
}
