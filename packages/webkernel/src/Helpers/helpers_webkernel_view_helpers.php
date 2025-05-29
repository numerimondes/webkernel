<?php

use Illuminate\Support\Facades\Auth;
use Webkernel\Models\Language;
use Webkernel\Models\LanguageTranslation;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Webkernel\Helpers\ResourceLayoutHelper;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;



/*
|--------------------------------------------------------------------------
| VIEW AND RENDERING HELPERS - CONCERNING VIEW RENDERING AND INCLUDES - view_helpers.php
|--------------------------------------------------------------------------
| Enhanced view rendering helpers for Webkernel modules with fallback
| support, custom view resolution, and hook-based rendering system.
|--------------------------------------------------------------------------
| Credits: El Moumen Yassine - www.numerimondes.com
|
*/

if (!function_exists('webkernel_include')) {
     /**
     * Inclut une vue du module Webkernel.
     *
     * @return string
     */
    function webkernel_include(string $path, ?string $alias = null): string
    {
        $view = 'webkernel::' . $path;

        if (!view()->exists($view)) {
            // Commentaire dynamique pour IDE ou débogage
            return "<!-- Webkernel view not found: {$view} at " . now() . " -->"; // Ajout de l'heure actuelle pour un suivi précis.
        }

        return view($view, ['__alias' => $alias])->render();
    }
}

if (!function_exists('customizable_render_hook_view')) {
    function customizable_render_hook_view(string $view, array $data = []): \Illuminate\Contracts\View\View
    {
        // Convert name to Laravel path
        // Ex: webkernel::components.webkernel.ui.atoms.search-hide => components.webkernel.ui.atoms.search-hide
        $customView = str_replace('webkernel::', '', $view);

        if (view()->exists($customView)) {
            return view($customView, $data);
        }

        return view($view, $data); // fallback to the original package view
    }
}
