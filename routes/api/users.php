<?php

use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::resource('users', UserController::class);
});
