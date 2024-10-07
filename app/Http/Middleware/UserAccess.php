<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next, $userRole = "user")
    {
        if (auth()->check() && Helper::hasRole(auth()->user(),$userRole)) {
            return $next($request);
        } else {
            return ResponseHelper::error(message: 'Unauthorized, you don\'t have access to this resource', httpCode: 401);
        }
    }
}
