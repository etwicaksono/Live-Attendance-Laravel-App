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

class CheckInController extends Controller
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
        'latitude' => 'required|numeric|min:-90|max:90',
        'longitude' => 'required|numeric|min:-180|max:180',
        'photo' => 'required|mimes:jpeg,png,jpg',
      ]);
      if ($validator->fails()) {
        return ResponseHelper::error(message: 'Validation Error', data: ['validation' => $validator->errors()], httpCode: 422);
      }

      $user = JWTAuth::parseToken()->authenticate();
      if (!$user) {
        return ResponseHelper::error(message: 'Unauthorized, failed to get user data from token', httpCode: 401);
      }


      $isCheckIn = Presence::where('user_id',$user->id)
        ->where('check_in','!=',null)
        ->whereDate('created_at', Carbon::today())
        ->exists();
      if ($isCheckIn) {
        return ResponseHelper::error(message: 'Already check in today', httpCode: 409);
      }

      $fileUrl = null;
      if ($request->hasFile('photo')) {
        try {
          $file = $request->file('photo');
          $filePath = 'check-in/' . Str::slug(pathinfo($user->username, PATHINFO_FILENAME)) . '-' . time() . '.' . $file->getClientOriginalExtension();

          // Upload to MinIO and check if upload succeeded
          $uploaded = Storage::disk('minio')->put($filePath, file_get_contents($file));

          if (!$uploaded) {
            Log::error('Failed to upload file to MinIO at ' . $filePath);
            return ResponseHelper::error(message: 'File upload failed', httpCode: 500);
          }

          // Get the file URL after upload
          $relativePath = Storage::disk('minio')->url($filePath);
          $fileUrl = parse_url($relativePath, PHP_URL_PATH);
        } catch (Exception $e) {
          Log::error('Error on ' . $this->controllerName . ':' . $this->methodName . ': ' . $e->getMessage());
          return ResponseHelper::error(message: 'File upload failed: ' . $e->getMessage(), httpCode: 500);
        }
      }

      Presence::create([
        'user_id' => $user->id,
        'check_in' => Carbon::now(),
        'check_in_latitude' => $request->latitude,
        'check_in_longitude' => $request->longitude,
        'photo_check_in' => $fileUrl
      ]);

      //return JSON process insert failed
      return ResponseHelper::success(message: 'Success check in', data: null, httpCode: 201);
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
