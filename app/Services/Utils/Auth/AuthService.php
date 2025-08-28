<?php

namespace App\Services\Utils\Auth;

use App\Models\Landlord\Role;
use App\Models\Landlord\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data, bool $issueToken = false): array
    {
        $user = User::create([
            ...$data,
            'email_verified_at' => Carbon::now(),
        ]);

        $user->assignRole(Role::ROOT_USER);

        $token = $issueToken
            ? $user->createToken('auth_token')->plainTextToken
            : null;

        return ['user' => $user, 'token' => $token];
    }

    public function login(array $credentials, bool $remember = false, bool $issueToken = false): array
    {
        $ok = Auth::attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            $remember
        );

        if (!$ok) throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);

        $user = User::where('email', $credentials['email'])->firstOrFail();
        $token = $issueToken
            ? $user->createToken('auth_token')->plainTextToken
            : null;

        return ['user' => $user, 'token' => $token];
    }

    public function logout(Request $request, bool $api = false): void
    {
        if ($api) {
            $request->user()->currentAccessToken()->delete();
            return;
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
