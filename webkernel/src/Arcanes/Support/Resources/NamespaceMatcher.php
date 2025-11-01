<?php declare(strict_types=1);

namespace Webkernel\Arcanes\Support\Resources;

/**
 * Namespace matcher for resource to module matching
 * Uses multiple strategies to find the best match
 */
class NamespaceMatcher
{
  /**
   * Match a resource class to a module namespace
   *
   * @param string $resourceClass Full resource class name
   * @param string $resourceNamespace Resource namespace
   * @param string $moduleNamespace Module namespace to match against
   * @return bool
   */
  public function matches(string $resourceClass, string $resourceNamespace, string $moduleNamespace): bool
  {
    $strategies = [
      $this->exactPrefixMatch($resourceClass, $moduleNamespace),
      $this->containsClassMatch($resourceClass, $moduleNamespace),
      $this->containsNamespaceMatch($resourceNamespace, $moduleNamespace),
      $this->namespacePrefixMatch($resourceNamespace, $moduleNamespace),
    ];

    return in_array(true, $strategies, true);
  }

  /**
   * Get detailed match results for debugging
   *
   * @param string $resourceClass
   * @param string $resourceNamespace
   * @param string $moduleNamespace
   * @return array<string, bool>
   */
  public function getMatchDetails(string $resourceClass, string $resourceNamespace, string $moduleNamespace): array
  {
    return [
      'exact_prefix' => $this->exactPrefixMatch($resourceClass, $moduleNamespace),
      'contains_class' => $this->containsClassMatch($resourceClass, $moduleNamespace),
      'contains_namespace' => $this->containsNamespaceMatch($resourceNamespace, $moduleNamespace),
      'namespace_prefix' => $this->namespacePrefixMatch($resourceNamespace, $moduleNamespace),
    ];
  }

  /**
   * Exact prefix match strategy
   *
   * @param string $resourceClass
   * @param string $moduleNamespace
   * @return bool
   */
  private function exactPrefixMatch(string $resourceClass, string $moduleNamespace): bool
  {
    return str_starts_with($resourceClass, $moduleNamespace);
  }

  /**
   * Contains class match strategy
   *
   * @param string $resourceClass
   * @param string $moduleNamespace
   * @return bool
   */
  private function containsClassMatch(string $resourceClass, string $moduleNamespace): bool
  {
    return str_contains($resourceClass, $moduleNamespace);
  }

  /**
   * Contains namespace match strategy
   *
   * @param string $resourceNamespace
   * @param string $moduleNamespace
   * @return bool
   */
  private function containsNamespaceMatch(string $resourceNamespace, string $moduleNamespace): bool
  {
    return str_contains($resourceNamespace, $moduleNamespace);
  }

  /**
   * Namespace prefix match strategy
   *
   * @param string $resourceNamespace
   * @param string $moduleNamespace
   * @return bool
   */
  private function namespacePrefixMatch(string $resourceNamespace, string $moduleNamespace): bool
  {
    return str_starts_with($resourceNamespace, $moduleNamespace);
  }
}
