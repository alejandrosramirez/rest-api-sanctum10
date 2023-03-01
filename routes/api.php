<?php

use App\Http\Controllers\Api\Auth\AuthenticateController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::controller(AuthenticateController::class)->group(function () {
            Route::post('/login', 'login');
        });
    });
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::controller(AuthenticateController::class)->group(function () {
            Route::post('/logout', 'logout');
        });
    });

    Route::patch('/users/{user}/disable', [UserController::class, 'disable']);
    Route::apiResource('users', UserController::class);
});
