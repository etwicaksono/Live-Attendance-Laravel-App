<?php

namespace App\Http\Controllers\Presence;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Presence;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use JWTAuth;
use ReflectionClass;

class CheckStatusController extends Controller
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
      $user = JWTAuth::parseToken()->authenticate();
      if (!$user) {
        return ResponseHelper::error(message: 'Unauthorized, failed to get user data from token', httpCode: 401);
      }


      $presenceToday = Presence::where('user_id', $user->id)
        ->whereDate('created_at', Carbon::today())
        ->first();
      if (!$presenceToday || $presenceToday->check_in == null) { // Not check in today
        return ResponseHelper::success('Not check in today', data: ['status' => 'NEED_CHECK_IN'], httpCode: 200);
      }

      if ($presenceToday->check_in != null && $presenceToday->check_out == null) {
        return ResponseHelper::success('Checked in but not check out today', data: ['status' => 'NEED_CHECK_OUT'], httpCode: 200);
      }

      if ($presenceToday->check_in != null && $presenceToday->check_out != null) {
        return ResponseHelper::success('Checked in but not check out today', data: ['status' => 'CHECKED_OUT'], httpCode: 200);
      }

      //return JSON error
      return ResponseHelper::error(message: 'Unhandled condition', data: null, httpCode: 500);
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
