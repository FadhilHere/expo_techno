<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is logged in
        if (!Auth::check()) {
            // If using Inertia, redirect to login page
            if ($request->wantsJson() || $request->header('X-Inertia')) {
                return redirect()->route('login');
            }

            // For regular requests
            return redirect('/login');
        }

        // If roles are specified, check if user has one of the required roles
        if (!empty($roles)) {
            $allowedRoles = [];

            // Process comma-separated roles
            foreach ($roles as $role) {
                $allowedRoles = array_merge($allowedRoles, explode(',', $role));
            }

            if (!in_array(Auth::user()->role, $allowedRoles)) {
                return redirect()->route('home')->with('error', 'Unauthorized access');
            }
        }

        return $next($request);
    }
}
