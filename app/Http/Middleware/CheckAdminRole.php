<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes:
     * Route::middleware(['check.admin.role:secretary,captain'])->get(...);
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $userRole = Session::get('user_role');

        if (!Session::has('user_id')) {
            // If session expired, redirect to the landing page
            notify()->error('Session expired. Please log in again.');
            return redirect()->route('landing');
        }

        if ($userRole === 'admin') {
            // Admin can access everything
            return $next($request);
        }

        // Use passed roles if any; otherwise, use defaults
        if (empty($roles)) {
            $roles = ['secretary', 'captain', 'nurse', 'treasurer', 'councilor'];
        }

        if (!in_array($userRole, $roles)) {
            // If not authorized or session expired, redirect to the landing page
            notify()->error('Session expired. Please log in again.');
            return redirect()->route('landing');
        }

        return $next($request);
    }

}
