<?php

use Illuminate\Support\Facades\Route;

Route::post('{resource}', \Novius\LaravelNovaOrderNestedsetField\Http\Controllers\PositionController::class);
