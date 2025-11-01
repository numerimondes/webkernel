<?php

if (!function_exists('formatThemeName')) {
    /**
     * Formate un nom de thème en remplaçant les underscores par des espaces
     * et en capitalisant la première lettre de chaque mot.
     *
     * @param string $themeName
     * @return string
     */
    function formatThemeName(string $themeName): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $themeName));
    }
}
