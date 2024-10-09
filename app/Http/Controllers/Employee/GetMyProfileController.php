<?php

namespace App\Http\Controllers\Employee;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use JWTAuth;
use ReflectionClass;

class GetMyProfileController extends Controller
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
      // Find the employee by ID
      $user = JWTAuth::parseToken()->authenticate();
      $employee = Employee::with('user')->where('user_id', $user->id)->first();
      if (!$employee) {
        return ResponseHelper::error(
          message: 'Employee not found',
          httpCode: 404
        );
      }

      return ResponseHelper::success(data: $employee);
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
