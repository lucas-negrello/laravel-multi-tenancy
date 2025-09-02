<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Utils\Auth\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('pages.auth.login');
    }

    public function login(LoginRequest $request, AuthService $authService): RedirectResponse
    {
        $remember = $request->boolean('remember');

        try {
            $authService->login($request->validated(), $remember, false);
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Invalid credentials.');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request, AuthService $authService): RedirectResponse
    {
        $authService->logout($request, false);

        return redirect()->route('login');
    }

    public function showRegisterForm(): View
    {
        return view('pages.auth.register');
    }

    public function register(RegisterRequest $request, AuthService $authService): RedirectResponse
    {
        $result = $authService->register($request->validated(), false);

        Auth::login($result['user']);
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }
}
