<?php

namespace Webkernel\Helpers;

/**
 *  Loop through all the PHP files in the Helpers directory
 *  Exclude this file (WebkernelHelpers.php) to avoid an infinite loop
 */
class WebkernelHelpers
{
    /**
     * Dynamically load all PHP helpers from the helpers directory, excluding this file.
     *
     * @return void
     */
    public static function loadHelpers(): void
    {
        // Charger tous les fichiers PHP présents dans le répertoire actuel
        foreach (glob(__DIR__ . '/*.php') as $file) {
            // Exclure ce fichier (WebkernelHelpers.php) pour éviter une boucle infinie
            if (basename($file) !== 'WebkernelHelpers.php') {
                require_once $file; // Inclure chaque fichier helper
            }
        }

        $helperPath = __DIR__.'/../../Helpers/helpers.php';
    }

}
