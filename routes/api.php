<?php

use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Authentication\LogoutController;
use App\Http\Controllers\Authentication\RefreshTokenController;
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


/* Authentication */
Route::post('auth/login', LoginController::class);
Route::middleware('jwt.auth')->group(function (){
  Route::post('auth/refresh-token', RefreshTokenController::class);
  Route::post('auth/logout', LogoutController::class);
});

