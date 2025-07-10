<?php
namespace Webkernel\Applications;

class ApplicationContext
{
    private static ?string $currentApplication = null;
    private static ?int $currentTenantId = null;

    public static function setCurrentApplication(string $application): void
    {
        self::$currentApplication = $application;
    }
    public static function getCurrentApplication(): ?string
    {
        // Peut être déterminé par session, requête, ou param explicite
        return self::$currentApplication;
    }
    public static function setCurrentTenantId(int $tenantId): void
    {
        self::$currentTenantId = $tenantId;
    }
    public static function getCurrentTenantId(): ?int
    {
        return self::$currentTenantId;
    }
} 