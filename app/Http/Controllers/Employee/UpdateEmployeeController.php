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
use ReflectionClass;

class UpdateEmployeeController extends Controller
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
  public function __invoke($id,Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id|unique:employees,user_id,' . $id, // Allow current user_id
        'name' => 'required',
        'job_role' => 'required',
      ]);
      if ($validator->fails()) {
        return ResponseHelper::error(message: 'Validation Error', data: ['validation' => $validator->errors()], httpCode: 422);
      }

      // Find the employee by ID
      $employee = Employee::find($id);
      if (!$employee) {
        return ResponseHelper::error(
          message: 'Employee not found',
          httpCode: 404
        );
      }

      // Update employee details
      $employee->user_id = $request->user_id;
      $employee->name = $request->name;
      $employee->job_role = $request->job_role;

      // Save updated employee
      if ($employee->save()) {
        return ResponseHelper::success(data: $employee, httpCode: 200);
      }

      // Return JSON process update failed
      return ResponseHelper::error(
        message: 'Failed to update employee',
        httpCode: 500
      );
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
