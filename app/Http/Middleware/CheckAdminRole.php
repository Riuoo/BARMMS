<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
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

        // Use passed roles if any; otherwise, use defaults
        if (empty($roles)) {
            $roles = ['secretary', 'captain', 'nurse', 'treasurer', 'councilor'];
        }

        if (!in_array($userRole, $roles)) {
            // Log unauthorized access attempt
            Log::warning('Unauthorized access attempt', [
                'user_role' => $userRole,
                'user_id' => Session::get('user_id'),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'allowed_roles' => $roles
            ]);

            // Show specific error message based on user role and requested access
            $this->showAccessDeniedMessage($userRole, $roles, $request);
            
            // Redirect to appropriate dashboard
            return $this->redirectToAppropriateDashboard($userRole);
        }

        return $next($request);
    }

    /**
     * Show appropriate access denied message based on user role
     */
    private function showAccessDeniedMessage(string $userRole, array $allowedRoles, Request $request): void
    {
        $currentUrl = $request->path();
        
        // Check if trying to access health features
        if (str_contains($currentUrl, 'health') || str_contains($currentUrl, 'vaccination') || 
            str_contains($currentUrl, 'medical') || str_contains($currentUrl, 'medicine')) {
            
            if ($userRole === 'treasurer') {
                notify()->error('Access denied. Treasurers can only access project and financial features.');
            } elseif ($userRole === 'secretary') {
                notify()->error('Access denied. Secretaries cannot access health management features.');
            } elseif ($userRole === 'captain') {
                notify()->error('Access denied. Barangay Captains cannot access health management features.');
            } elseif ($userRole === 'councilor') {
                notify()->error('Access denied. Councilors cannot access health management features.');
            } else {
                notify()->error('Access denied. You do not have permission to access health management features.');
            }
            return;
        }
        
        // Check if trying to access reports & requests
        if (str_contains($currentUrl, 'blotter') || str_contains($currentUrl, 'community-concerns') || 
            str_contains($currentUrl, 'document-requests') || str_contains($currentUrl, 'new-account-requests') ||
            str_contains($currentUrl, 'templates')) {
            
            if ($userRole === 'treasurer') {
                notify()->error('Access denied. Treasurers can only access project and financial features.');
            } elseif ($userRole === 'nurse') {
                notify()->error('Access denied. Nurses can only access health management features.');
            } else {
                notify()->error('Access denied. You do not have permission to access reports and requests.');
            }
            return;
        }
        
        // Check if trying to access projects
        if (str_contains($currentUrl, 'accomplished-projects')) {
            if ($userRole === 'secretary') {
                notify()->error('Access denied. Secretaries cannot access project management features.');
            } elseif ($userRole === 'captain') {
                notify()->error('Access denied. Barangay Captains cannot access project management features.');
            } elseif ($userRole === 'councilor') {
                notify()->error('Access denied. Councilors cannot access project management features.');
            } elseif ($userRole === 'nurse') {
                notify()->error('Access denied. Nurses can only access health management features.');
            } else {
                notify()->error('Access denied. You do not have permission to access project management features.');
            }
            return;
        }
        
        // Check if trying to access user management (create/edit operations)
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
        
        // Generic access denied message
        notify()->error('Access denied. You do not have permission to access this section.');
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
            case 'secretary':
            case 'captain':
            case 'councilor':
            default:
                return redirect()->route('admin.dashboard');
        }
    }

}
