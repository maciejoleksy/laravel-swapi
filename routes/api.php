<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;
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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::post('/update', [UserController::class, 'update']);
        Route::get('/films', [UserController::class, 'getFilmsByHeroName']);
        Route::get('/planets', [UserController::class, 'getPlanetsByHeroName']);
    });

    Route::get('/people/{id}', [UserController::class, 'getResourcePeople']);
    Route::get('/films/{id}', [UserController::class, 'getResourceFilms']);
    Route::get('/planets/{id}', [UserController::class, 'getResourcePlanets']);
    Route::get('/starships/{id}', [UserController::class, 'getResourceStarships']);
    Route::get('/vehicles/{id}', [UserController::class, 'getResourceVehicles']);
    Route::get('/species/{id}', [UserController::class, 'getResourceSpecies']);
});
