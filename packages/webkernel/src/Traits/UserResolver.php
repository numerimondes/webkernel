<?php

namespace Webkernel\Traits;



/**
 * UserResolver - Uses only the active package's UserTrait
 * Active package: webkernel
 * Generated automatically based on webkernel-user.php configuration
 */
trait UserResolver
{


    /**
     * Get the active package name from configuration
     */
    protected static function getActivePackage(): string
    {
        return config('webkernel.user_extensions.active_package', 'webkernel');
    }
}
