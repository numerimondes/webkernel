<?php
use Illuminate\Support\Facades\Route;
use Webkernel\Arcanes\QueryModules;
use Illuminate\Support\Facades\Cache;
use Filament\Facades\Filament;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

Route::get('/', fn() => view('enjoy-the-world::software.my-index'))->name('base.welcome');
// Route::get('/{page}.html', function ($page) { $viewPath = "enjoy-the-world::html.$page"; if (View::exists($viewPath)) { return view($viewPath); } abort(404); });
