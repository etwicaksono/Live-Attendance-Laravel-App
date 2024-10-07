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

class UpdateUserController extends Controller
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
  public function __invoke($id, Request $request)
  {
    try {
      // Find the user by ID
      $user = User::find($id);

      if (!$user) {
        return ResponseHelper::error(message: 'User not found', httpCode: 404);
      }

      // Validate request data
      $validator = Validator::make($request->all(), [
        'name' => 'required',
        'username' => 'required|unique:users,username,' . $user->id,
        'password' => 'nullable|min:8|confirmed',
        'role' => 'required|in:admin,user',
        'is_active' => 'required|in:0,1',
      ]);

      if ($validator->fails()) {
        return ResponseHelper::error(message: 'Validation Error', data: ['validation' => $validator->errors()], httpCode: 422);
      }

      // Update user data
      $user->name = $request->name;
      $user->username = $request->username;
      if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
      }
      $user->role = $request->role;
      $user->is_active = $request->is_active;
      $user->save();

      // Return response JSON user is updated
      return ResponseHelper::success(data: $user, httpCode: 200);

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
