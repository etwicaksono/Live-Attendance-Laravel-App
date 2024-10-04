<?php

use App\Http\Controllers\Authentication\LoginController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/* Authentication */
Route::post('auth/login', LoginController::class);
//Route::post('auth/refresh', RefreshTokenController::class);
Route::middleware('jwt.auth')->group(function () {
//    Route::post('auth/logout', LogoutController::class);
});



//Route::post('auth/registration', RegistrationController::class);
