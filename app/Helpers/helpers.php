<?php

use App\Helpers\ApiResponse;
use Illuminate\Http\JsonResponse;

if (!function_exists('apiError')) {
    function apiError($message, $code = 400, $errors = null): JsonResponse
    {
        return ApiResponse::apiError($message, $code, $errors);
    }
}

if (!function_exists('apiSuccess')) {
    function apiSuccess($message, $data = null, $meta = null, $code = 200): JsonResponse
    {
        return ApiResponse::apiSuccess($message, $data, $meta, $code);
    }
}
