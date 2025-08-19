<?php

use App\Helpers\ApiResponse;
use App\Models\Landlord\Tenant;
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

if (!function_exists('tenant')) {
    function tenant(): ?Tenant
    {
        return app()->bound('tenant') ?
            app('tenant') : null;
    }
}
