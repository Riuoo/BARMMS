<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminSecretaryAccess
{
    /**
     * Handle an incoming request.
     *
     * This middleware restricts access to only secretary role.
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

        // Only secretary can access
        if ($userRole !== 'secretary') {
            // Log unauthorized access attempt
            Log::warning('Unauthorized access attempt', [
                'user_role' => $userRole,
                'user_id' => Session::get('user_id'),
                'url' => $request->fullUrl(),
                'ip' => $request->ip()
            ]);

            // Show specific error message based on user role
            $this->showAccessDeniedMessage($userRole, $request);
            
            // Redirect to appropriate dashboard
            return $this->redirectToAppropriateDashboard($userRole);
        }

        return $next($request);
    }

    /**
     * Show appropriate access denied message based on user role
     */
    private function showAccessDeniedMessage(string $userRole, Request $request): void
    {
        $currentUrl = $request->path();
        
        // Check if trying to access user management operations
        if (str_contains($currentUrl, 'create') || str_contains($currentUrl, 'edit') || 
            str_contains($currentUrl, 'store') || str_contains($currentUrl, 'update') ||
            str_contains($currentUrl, 'delete') || str_contains($currentUrl, 'destroy')) {
            
            if ($userRole === 'captain') {
                notify()->error('Access denied. Barangay Captains can only view information, not modify records.');
            } elseif ($userRole === 'councilor') {
                notify()->error('Access denied. Councilors can only view information, not modify records.');
            } elseif ($userRole === 'treasurer') {
                notify()->error('Access denied. Treasurers can only access project and financial features.');
            } elseif ($userRole === 'nurse') {
                notify()->error('Access denied. Nurses can only access health management features.');
            } else {
                notify()->error('Access denied. You do not have permission to modify records.');
            }
            return;
        }
        
        // Check if trying to access reports & requests operations
        if (str_contains($currentUrl, 'blotter') || str_contains($currentUrl, 'community-concerns') || 
            str_contains($currentUrl, 'document-requests') || str_contains($currentUrl, 'new-account-requests') ||
            str_contains($currentUrl, 'templates')) {
            
            if ($userRole === 'captain') {
                notify()->error('Access denied. Barangay Captains can only view reports, not modify them.');
            } elseif ($userRole === 'councilor') {
                notify()->error('Access denied. Councilors can only view reports, not modify them.');
            } elseif ($userRole === 'treasurer') {
                notify()->error('Access denied. Treasurers can only access project and financial features.');
            } elseif ($userRole === 'nurse') {
                notify()->error('Access denied. Nurses can only access health management features.');
            } else {
                notify()->error('Access denied. You do not have permission to modify reports and requests.');
            }
            return;
        }
        
        // Generic access denied message
        notify()->error('Access denied. Only secretaries can access this section.');
    }

    /**
     * Redirect to appropriate dashboard based on user role
     */
    private function redirectToAppropriateDashboard(string $userRole): Response
    {
        switch ($userRole) {
            case 'nurse':
                return redirect()->route('admin.health-reports');
            case 'treasurer':
                return redirect()->route('admin.dashboard');
            case 'captain':
            case 'councilor':
            default:
                return redirect()->route('admin.dashboard');
        }
    }
}
