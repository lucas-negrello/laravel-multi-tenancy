<?php

use App\Helpers\ApiResponse;
use Illuminate\Http\JsonResponse;

if (!function_exists('errorResponse')) {
    function errorResponse($message, $code = 400, $errors = null): JsonResponse
    {
        return ApiResponse::errorResponse($message, $code, $errors);
    }
}

if (!function_exists('successResponse')) {
    function successResponse($message, $data = null, $meta = null, $code = 200): JsonResponse
    {
        return ApiResponse::successResponse($message, $data, $meta, $code);
    }
}
