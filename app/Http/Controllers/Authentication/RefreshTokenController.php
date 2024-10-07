<?php

namespace App\Http\Controllers\Authentication;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RefreshTokenController extends Controller
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
        // Attempt to refresh the token
        $newToken = JWTAuth::parseToken()->refresh();

        // Return the new token in the response
        return ResponseHelper::success(
          data: [
            'token' => $newToken,
          ],
          meta: [
            'created_at' => now()->format('Y-m-d H:i:s'),
            'expired_at' => now()->addMinutes(config('jwt.ttl'))->format('Y-m-d H:i:s'),
            'refreshable_till' => now()->addMinutes(config('jwt.refresh_ttl'))->format('Y-m-d H:i:s')
          ]
        );
      } catch (JWTException $e) {
        // Handle token refresh failure
        return ResponseHelper::error(
          message: 'Could not refresh token',
          meta: [
            'error' => $e->getMessage()
          ],
          httpCode: 401
        );
      }

    }
}
