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


        // Get credentials to check
        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
            'is_active' => true // Only allow active users to login
        ];

        // Attempt to authenticate with active status check
        if (Auth::attempt($credentials)) {
          
            $request->session()->regenerate();

            // Get the authenticated user
            $user = Auth::user();

            // Determine redirect based on user role
            $redirectRoute = match($user->role) {
                'mahasiswa' => 'mahasiswa.dashboard',
                'admin', 'super_admin' => 'dashboard',
                default => 'login' // Fallback if role is not recognized
            };

            return response()->json([
                'success' => true,

                'user' => [
                    'username' => $user->username,
                    'role' => $user->role
                ],
                'redirect' => route($redirectRoute)
            ]);
        }

        // Check if user exists but is inactive
        $user = User::where('username', $request->username)->first();
        if ($user && Hash::check($request->password, $user->password) && !$user->is_active) {
            throw ValidationException::withMessages([
                'username' => ['Your account has been deactivated. Please contact administrator.'],
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
