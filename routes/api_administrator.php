<?php

use App\Http\Controllers\ApiAdministrator\Auth\AuthenticateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Administrator Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api_administrator" middleware group. Make something great!
|
*/

Route::middleware('api_administrator')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::controller(AuthenticateController::class)->group(function () {
            Route::post('/login', 'login');
        });
    });
});

Route::middleware('auth:api_administrator')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::controller(AuthenticateController::class)->group(function () {
            Route::post('/logout', 'logout');
        });
    });
});
