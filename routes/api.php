<?php

use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Authentication\LogoutController;
use App\Http\Controllers\Authentication\RefreshTokenController;
use App\Http\Controllers\Employee\CreateEmployeeController;
use App\Http\Controllers\Employee\DeleteEmployeeController;
use App\Http\Controllers\Employee\UpdateEmployeeController;
use App\Http\Controllers\User\CreateUserController;
use App\Http\Controllers\User\DeleteUserController;
use App\Http\Controllers\User\GetUserDetailController;
use App\Http\Controllers\User\UpdateUserController;
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

const USER_BY_ID = 'user/{id}';
const EMPLOYEE_BY_ID = 'employee/{id}';

/* Authentication */
Route::post('auth/login', LoginController::class);
Route::middleware('jwt.auth')->group(function (){
  Route::post('auth/refresh-token', RefreshTokenController::class);
  Route::post('auth/logout', LogoutController::class);
});

/* User */
Route::middleware('jwt.auth')->group(function (){
  Route::get(USER_BY_ID, GetUserDetailController::class);
  Route::middleware('user-access:admin')->group(function () {
    Route::post('user', CreateUserController::class);
    Route::put(USER_BY_ID, UpdateUserController::class);
    Route::delete(USER_BY_ID, DeleteUserController::class);
  });
});

/* Employee */
Route::middleware('jwt.auth')->group(function (){
  Route::middleware('user-access:admin')->group(function () {
    Route::post('employee', CreateEmployeeController::class);
    Route::put(EMPLOYEE_BY_ID, UpdateEmployeeController::class);
    Route::delete(EMPLOYEE_BY_ID, DeleteEmployeeController::class);
  });
});

