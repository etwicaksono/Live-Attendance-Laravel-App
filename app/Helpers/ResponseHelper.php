<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function success($message = "Success", $data = null, $meta = null, $httpCode = 200): JsonResponse
    {
        return response()->json([
            'success' => 1,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ], $httpCode);
    }

    public static function error($message = "Error Happened", $data = null, $meta = null, $httpCode = 422): JsonResponse
    {
        return response()->json([
            'success' => 0,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ], $httpCode);
    }

}
