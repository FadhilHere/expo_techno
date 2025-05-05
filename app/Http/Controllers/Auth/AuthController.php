<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{
    /**
     * Display the login view.
     */
    public function showLogin()
    {
        return Inertia::render('auth/Login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate
        if (
            Auth::attempt([
                'username' => $request->username,
                'password' => $request->password
            ])
        ) {
            $request->session()->regenerate();

            return response()->json([
                'success' => true,

                'redirect' => route('dashboard')

            ]);
        }

        // Authentication failed
        throw ValidationException::withMessages([
            'username' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();


        return redirect()->route('login');

    }

}
