<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\AccountRequest;
use App\Models\BlotterRequest;
use App\Models\DocumentRequest;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }
    
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Compose data for the admin layout
        View::composer('admin.layout', function ($view) {
            // Get the correct user data for admins
            $currentAdminUser = null;
            
            if (session()->has('user_role') && session('user_role') === 'barangay') {
                $userId = session('user_id');
                if ($userId) {
                    $currentAdminUser = \App\Models\BarangayProfile::find($userId);
                }
            }
            
            $view->with('currentAdminUser', $currentAdminUser);
            
            // Only fetch if a user is logged in and has an admin role
            if (session()->has('user_role') && in_array(session('user_role'), ['barangay_staff', 'barangay'])) {
                // Fetch only unread notifications for the header count
                $pendingBlotterReports = BlotterRequest::where('status', 'pending')->where('is_read', false)->count();
                $pendingDocumentRequests = DocumentRequest::where('status', 'pending')->where('is_read', false)->count();
                $pendingAccountRequests = AccountRequest::where('status', 'pending')->where('is_read', false)->count();
                
                $totalPendingNotifications = $pendingBlotterReports + $pendingDocumentRequests + $pendingAccountRequests;
                
                $view->with(compact(
                    'pendingBlotterReports',
                    'pendingDocumentRequests',
                    'pendingAccountRequests',
                    'totalPendingNotifications'
                ));
            } else {
                // Provide default values if not logged in or not admin
                $view->with([
                    'pendingBlotterReports' => 0,
                    'pendingDocumentRequests' => 0,
                    'pendingAccountRequests' => 0,
                    'totalPendingNotifications' => 0,
                ]);
            }
        });

        // Compose data for the resident layout
        View::composer('resident.layout', function ($view) {
            // Get the correct user data for residents
            $currentUser = null;
            
            if (session()->has('user_role') && session('user_role') === 'resident') {
                $userId = session('user_id');
                $currentUser = \App\Models\Residents::find($userId);
            }
            
            $view->with('currentUser', $currentUser);
        });
    }
}