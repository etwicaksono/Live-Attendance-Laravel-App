<?php

namespace App\Http\Controllers\Authentication;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    protected $controllerName;
    protected $methodName;

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

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username'     => 'required',
                'password'  => 'required'
            ]);
            if ($validator->fails()) {
                return ResponseHelper::error(message: 'Validation Error', data: ['validation' => $validator->errors()], httpCode: 422);
            }

            //get credentials from request
            $credentials = $request->only('username', 'password');

            //if auth failed
            if (!auth()->attempt($credentials)) {
                return ResponseHelper::error(message: 'Wrong username or password', httpCode: 401);
            }

            //if auth success
            return ResponseHelper::success(
                data: [
                    'token' => JWTAuth::fromUser(auth()->user()),
                    'role' => auth()->user()->role
                ],
                meta: [
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'expired_at' => now()->addMinutes(config('jwt.ttl'))->format('Y-m-d H:i:s'),
                    'refreshable_till' => now()->addMinutes(config('jwt.refresh_ttl'))->format('Y-m-d H:i:s')
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error on ' . $this->controllerName . ':' . $this->methodName . ': ' . $e->getMessage());
            return ResponseHelper::error(
                message: $e->getMessage(),
                httpCode: 500,
                meta: [
                    'controller' => $this->controllerName,
                    'method' => $this->methodName
                ]
            );
        }
    }
}
