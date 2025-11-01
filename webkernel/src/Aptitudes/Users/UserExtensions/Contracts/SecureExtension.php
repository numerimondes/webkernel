<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Users\UserExtensions\Contracts;

/**
 * Interface for secure user extensions that require special validation
 * Only extensions implementing this interface can access sensitive user data
 */
interface SecureExtension
{
    /**
     * Get the security level of this extension
     * Higher numbers = more secure, only RBAC should have level 100
     *
     * @return int Security level (0-100)
     */
    public static function getSecurityLevel(): int;

    /**
     * Get the required permissions to use this extension
     *
     * @return array<string> Array of required permission names
     */
    public static function getRequiredPermissions(): array;

    /**
     * Validate if the current context can access this extension
     *
     * @return bool True if access is allowed
     */
    public static function validateAccess(): bool;
}
