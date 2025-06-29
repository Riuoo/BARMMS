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
    }
}