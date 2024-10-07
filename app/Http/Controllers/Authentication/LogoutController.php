<?php

namespace App\Http\Controllers\Authentication;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutController extends Controller
{
  protected string $controllerName;
  protected string $methodName;

  public function __construct()
  {
    $routeAction = Route::currentRouteAction();
    list($controller, $method) = Str::parseCallback($routeAction, '__invoke');
    if ($controller) {
      $reflection = new \ReflectionClass($controller);
      $controllerName = $reflection->getShortName();
      $this->controllerName = $controllerName;
    }
    $this->methodName = $method;
  }

    public function __invoke()
    {
      try {
        // Invalidate the token
        JWTAuth::invalidate(JWTAuth::parseToken());

        // Return a success message
        return ResponseHelper::success(message: 'Successfully logged out');
      } catch (JWTException $e) {
        // If the token is invalid or can't be invalidated
        return response()->json([
          'error' => 'Failed to logout, please try again'
        ], 500);
      }

    }
}
