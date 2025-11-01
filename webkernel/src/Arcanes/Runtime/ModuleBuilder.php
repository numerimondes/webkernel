<?php
declare(strict_types=1);

namespace Webkernel\Arcanes\Runtime;

/**
 * Fluent builder for module configuration
 */
class ModuleBuilder
{
  private string $id = '';
  private string $name = '';
  private string $version = '1.0.0';
  private string $description = '';
  private string $viewNamespace = '';
  private array $dependencies = [];
  private array $aliases = [];
  private array $providers = [];
  private array $providedComponents = [];

  /**
   * Set module ID
   *
   * @param string $id Module identifier
   * @return self
   */
  public function id(string $id): self
  {
    $this->id = $id;
    return $this;
  }

  /**
   * Set module name
   *
   * @param string $name Module name
   * @return self
   */
  public function name(string $name): self
  {
    $this->name = $name;
    return $this;
  }

  /**
   * Set module version
   *
   * @param string $version Module version
   * @return self
   */
  public function version(string $version): self
  {
    $this->version = $version;
    return $this;
  }

  /**
   * Set module description
   *
   * @param string $description Module description
   * @return self
   */
  public function description(string $description): self
  {
    $this->description = $description;
    return $this;
  }

  /**
   * Set view namespace
   *
   * @param string $namespace View namespace
   * @return self
   */
  public function viewNamespace(string $namespace): self
  {
    $this->viewNamespace = $namespace;
    return $this;
  }

  /**
   * Set module dependencies
   *
   * @param array<string> $dependencies List of dependency IDs
   * @return self
   */
  public function dependencies(array $dependencies): self
  {
    $this->dependencies = $dependencies;
    return $this;
  }

  /**
   * Set module aliases
   *
   * @param array<string, string> $aliases Alias mappings
   * @return self
   */
  public function aliases(array $aliases): self
  {
    $this->aliases = $aliases;
    return $this;
  }

  /**
   * Set service providers
   *
   * @param array<string> $providers Provider class names
   * @return self
   */
  public function providers(array $providers): self
  {
    $this->providers = $providers;
    return $this;
  }

  /**
   * Set provided components
   *
   * @param array<string, mixed> $components Component configuration
   * @return self
   */
  public function moduleProvides(array $components): self
  {
    $this->providedComponents = $components;
    return $this;
  }

  /**
   * Build module configuration
   *
   * @return ModuleConfig
   */
  public function build(): ModuleConfig
  {
    return new ModuleConfig(
      $this->id,
      $this->name,
      $this->version,
      $this->description,
      $this->viewNamespace,
      $this->dependencies,
      $this->aliases,
      $this->providers,
      $this->providedComponents,
    );
  }
}
