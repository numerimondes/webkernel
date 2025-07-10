<?php
namespace Webkernel\Applications;

class ApplicationRegistry
{
    private array $applications = [];

    public function register(string $id, ApplicationDefinition $definition): void
    {
        $this->applications[$id] = $definition;
    }

    public function getApplication(string $id): ?ApplicationDefinition
    {
        return $this->applications[$id] ?? null;
    }

    public function getActiveApplicationsForTenant(int $tenantId): array
    {
        // À implémenter : requête sur tenant_applications
        return [];
    }
}

class ApplicationDefinition
{
    public function __construct(
        public string $id,
        public string $name,
        public string $version,
        public array $dependencies = [],
        public array $settingsSchema = [],
        public array $permissions = [],
        public ?string $licenseType = null
    ) {}
} 