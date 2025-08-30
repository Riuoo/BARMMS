<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminSecretaryAccess
{
    /**
     * Handle an incoming request.
     *
     * This middleware restricts access to only admin and secretary roles.
     * Other roles (captain, councilor, treasurer, nurse) will be denied access.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userRole = Session::get('user_role');

        if (!Session::has('user_id')) {
            // If session expired, redirect to the landing page
            notify()->error('Session expired. Please log in again.');
            return redirect()->route('landing');
        }

        // Only admin and secretary can access
        if (!in_array($userRole, ['admin', 'secretary'])) {
            // Log unauthorized access attempt
            \Log::warning('Unauthorized access attempt', [
                'user_role' => $userRole,
                'user_id' => Session::get('user_id'),
                'url' => $request->fullUrl(),
                'ip' => $request->ip()
            ]);

            // Redirect to dashboard with error message
            notify()->error('Access denied. Only administrators and secretaries can access this section.');
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
