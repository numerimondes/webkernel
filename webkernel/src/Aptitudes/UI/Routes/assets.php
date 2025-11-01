<?php

use Illuminate\Support\Facades\Route;

Route::get('app-blade-ui', function () {
    return view('ui::app');
});
