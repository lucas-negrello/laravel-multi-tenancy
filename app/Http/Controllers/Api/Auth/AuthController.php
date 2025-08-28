<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Landlord\User;
use App\Services\Utils\Auth\AuthService;
use App\Services\Utils\Auth\MeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, AuthService $authService): JsonResponse
    {
        $result = $authService->register($request->validated(), true);

        return successResponse('Successfully registered',
            ['user' => $result['user'], 'token' => $result['token']],
            [],
            ResponseAlias::HTTP_CREATED,
        );
    }

    public function login(LoginRequest $request, AuthService $authService): JsonResponse
    {
        $result = $authService->login($request->validated(), false, true);

        return successResponse('Successfully logged in',
            ['user' => $result['user'], 'token' => $result['token']],
            [],
            ResponseAlias::HTTP_OK,
        );

    }

    public function logout(Request $request, AuthService $authService): JsonResponse
    {
        $authService->logout($request, true);

        return successResponse('Successfully logged out', [], [], ResponseAlias::HTTP_OK);
    }

    public function me(Request $request, MeService $meService): JsonResponse
    {
        $userData = $meService->getMeInfo($request);

        return successResponse('User data retrieved successfully',
            $userData,
            [],
            ResponseAlias::HTTP_OK,
        );
    }
}
