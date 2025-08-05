<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function apiSuccess($message, $data = null, $meta = null, $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'code' => $code,
        ];

        if ($data) {
            $response['data'] = $data;
        }

        if ($meta) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    public static function apiError($message, $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'code' => $code,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

}
