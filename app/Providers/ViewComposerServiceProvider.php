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
            // Only fetch if a user is logged in and has an admin role
            // This prevents errors if the layout is used on public pages or for non-admin users
            if (session()->has('user_role') && in_array(session('user_role'), ['barangay_staff', 'barangay'])) {
                $pendingBlotterReports = BlotterRequest::where('status', 'pending')->count();
                $pendingDocumentRequests = DocumentRequest::where('status', 'pending')->count();
                $pendingAccountRequests = AccountRequest::where('status', 'pending')->count();
                
                $totalPendingNotifications = $pendingBlotterReports + $pendingDocumentRequests + $pendingAccountRequests;
                
                $view->with(compact(
                    'pendingBlotterReports',
                    'pendingDocumentRequests',
                    'pendingAccountRequests',
                    'totalPendingNotifications'
                ));
            } else {
                // Provide default values if not logged in or not admin
                // This prevents "Undefined variable" errors on public pages or for non-admin users
                $view->with([
                    'pendingBlotterReports' => 0,
                    'pendingDocumentRequests' => 0,
                    'pendingAccountRequests' => 0,
                    'totalPendingNotifications' => 0,
                ]);
            }
        });
    }
}