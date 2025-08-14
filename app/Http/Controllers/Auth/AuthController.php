<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Landlord\Role;
use App\Models\Landlord\User;
use App\Services\Utils\Auth\MeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $userData = $request->validated();

        $user = User::create([
            ...$userData,
            'email_verified_at' => Carbon::now(),
            ]);

        $user->assignRole(Role::ROOT_USER);

        $token = $user->createToken('auth_token')->plainTextToken;

        return successResponse('Successfully registered',
            ['user' => $user, 'token' => $token],
            [],
            ResponseAlias::HTTP_CREATED,
        );
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return successResponse('Successfully logged in',
            ['user' => $user, 'token' => $token],
            [],
            ResponseAlias::HTTP_OK,
        );

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return successResponse('Successfully logged out', [], [], ResponseAlias::HTTP_OK);
    }

    public function me(Request $request, MeService $meService)
    {
        $userData = $meService->getMeInfo($request);

        return successResponse('User data retrieved successfully',
            $userData,
            [],
            ResponseAlias::HTTP_OK,
        );
    }
}
