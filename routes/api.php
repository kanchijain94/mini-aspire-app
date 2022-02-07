<?php

use App\Http\Controllers\API\v1\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1', 'middleware' => ['jsonify']], function () {
    Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::group(['prefix' => 'users'], function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login'])->name('login');

        Route::group(['middleware' => 'auth:api'], function(){
            Route::post('logout', [AuthController::class, 'logout']);
        });
    });
});
