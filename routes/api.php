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

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::post('/update', [UserController::class, 'update'])->name('update');
        Route::get('/films', [UserController::class, 'getFilmsByHeroName'])->name('get.films.by.hero.name');
        Route::get('/planets', [UserController::class, 'getPlanetsByHeroName'])->name('get.planets.by.hero.name');
        Route::get('/{resources}/{id}', [UserController::class, 'getResources'])->name('get.resources');
    });
});
