<?php

use Illuminate\Support\Facades\Route;
use WebkernelTestpackage\Http\Controllers\Controller;

Route::prefix('testpackage')
    ->name('webkernel_testpackage.')
    ->middleware(['web'])
    ->group(function () {
        Route::get('/', [Controller::class, 'index'])->name('index');
        Route::get('/about', [Controller::class, 'about'])->name('about');
    });
