<?php

use Illuminate\Support\Facades\Route;
use Novius\LaravelNovaOrderNestedsetField\Http\Controllers\PositionController;

Route::post('{resource}', PositionController::class);
