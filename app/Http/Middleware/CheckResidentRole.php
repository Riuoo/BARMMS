<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckResidentRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the user's role from the session
        $userRole = Session::get('user_role');

        // Define the roles that are allowed to access admin sections
        $allowedRoles = ['resident'];

        // Check if the user is logged in and has an allowed role
        if (!Session::has('user_id') || !in_array($userRole, $allowedRoles)) {
            // If not authorized, redirect to the landing page with an error message
            return redirect()->route('landing')->with('error', 'You do not have permission to access this page.');
        }

        // If authorized, allow the request to proceed
        return $next($request);
    }
}
