<?php

use Illuminate\Support\Facades\Route;
use Webkernel\Core\Helpers\RouteHelper;

Route::get('/', function () {
    return view('welcome');
});

// Exemple d'utilisation des routes avec vérification d'accès aux modules
// RouteHelper::moduleGroup('system', function () {
//     Route::get('/admin', function () {
//         return 'Accès autorisé au module System';
//     });
// });

// RouteHelper::moduleGroup('mar', function () {
//     Route::get('/clients', function () {
//         return 'Accès autorisé au module MAR';
//     });
// });
