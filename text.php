<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Vérifie si l'application est en mode maintenance
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Charge l'autoloader Composer
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel et récupère l'application
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Inclure le helper de webkernel (attention au chemin relatif)
require_once __DIR__ . '/../packages/webkernel/src/Helpers/helpers_platformHttp.php';

// Ne pas inclure le fichier public/index.php qui est le front controller Laravel — cela risque de créer un conflit et une récursion

// Utiliser dd uniquement pour debug, ici tu veux afficher 'platformAbsoluteUrl'
dd('platformAbsoluteUrl');
