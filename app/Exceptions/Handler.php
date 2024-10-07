<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

      $this->renderable(function (NotFoundHttpException $e, $request) {
        return ResponseHelper::error(message: 'Route Not Found', httpCode: 404);
      });

      $this->renderable(function (UnauthorizedHttpException $e, $request) {
        return ResponseHelper::error(message: 'Unauthorized', httpCode: 401);
      });

      $this->renderable(function (MethodNotAllowedException $e, $request) {
        return ResponseHelper::error(message: $e->getMessage(), httpCode: 405);
      });

      $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
        return ResponseHelper::error(message: $e->getMessage(), httpCode: 405);
      });
    }
}
