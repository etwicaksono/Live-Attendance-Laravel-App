<?php

namespace App\Http\Controllers\User;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use ReflectionClass;

class CreateUserController extends Controller
{
  protected string $controllerName;
  protected string $methodName;

  public function __construct()
  {
    $routeAction = Route::currentRouteAction();
    list($controller, $method) = Str::parseCallback($routeAction, '__invoke');
    if ($controller) {
      $reflection = new ReflectionClass($controller);
      $controllerName = $reflection->getShortName();
      $this->controllerName = $controllerName;
    }
    $this->methodName = $method;
  }

  /**
   * Handle the incoming request.
   */
  public function __invoke(Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        'username' => 'required|unique:users',
        'password' => 'required|min:8|confirmed',
        'role' => 'required|in:admin,employee',
        'is_active' => 'required|in:0,1',
      ]);
      if ($validator->fails()) {
        return ResponseHelper::error(message: 'Validation Error', data: ['validation' => $validator->errors()], httpCode: 422);
      }

      //create user
      $user = User::create([
        'username' => $request->username,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'is_active' => $request->is_active
      ]);

      //return response JSON user is created
      if ($user) {
        return ResponseHelper::success(data: $user, httpCode: 201);
      }

      //return JSON process insert failed
      return ResponseHelper::error(message: 'Failed to create user', httpCode: 409);
    } catch (Exception $e) {
      Log::error('Error on ' . $this->controllerName . ':' . $this->methodName . ': ' . $e->getMessage());
      $meta = [
        'controller' => $this->controllerName,
        'method' => $this->methodName
      ];
      return ResponseHelper::error(
        message: $e->getMessage(),
        httpCode: 500,
        meta: $meta,
      );
    }
  }
}
