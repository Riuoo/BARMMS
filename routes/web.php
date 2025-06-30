<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

use App\Helpers\AuthHelper;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\LoginControllers\ContactAdminController;
use App\Http\Controllers\AdminControllers\RegistrationController;
use App\Http\Controllers\AdminControllers\AccountRequestController;
use App\Http\Controllers\AdminControllers\AdminProfileController;
use App\Http\Controllers\AdminControllers\AdminDashboardController;
use App\Http\Controllers\AdminControllers\BarangayProfileController;
use App\Http\Controllers\AdminControllers\ResidenceController;
use App\Http\Controllers\AdminControllers\BlotterReportController;
use App\Http\Controllers\AdminControllers\DocumentRequestController;
use App\Http\Controllers\AdminControllers\AccomplishProjectController;
use App\Http\Controllers\AdminControllers\AdminNotificationController;
use App\Http\Controllers\AdminControllers\HealthReportController;
use App\Http\Controllers\AdminControllers\HealthStatusController;

// Landing page route
Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');

// Route for guest users to request an account
Route::get('/admin/contact', [ContactAdminController::class, 'contactAdmin'])->name('admin.contact');
Route::post('/admin/contact', [ContactAdminController::class, 'store'])->name('admin.contact.store');

// Forgot Password Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
// Reset Password Routes
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Authentication route with distinct user tables
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);
    // Attempt to log in using the helper
    if ($user = AuthHelper::attemptLogin($credentials, $request->remember)) {
        $request->session()->regenerate();

        // Set user ID and role in the session
        session(['user_id' => $user->id]);
        $role = $user instanceof App\Models\BarangayProfile ? 'barangay' : 'residence';
        session(['user_role' => $role]);

        return redirect()->intended(
            $role === 'barangay' ? route('admin.dashboard') : route('residents')
        );
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->name('login.post');

// Registration routes (accessible via token, not directly admin)
Route::get('/register/{token}', [RegistrationController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegistrationController::class, 'register'])->name('register');

// Residents dashboard route for residence users (might need its own middleware if not admin)
Route::get('/residents', function () {
    // This check remains here as it's specific to 'residence' role, not 'admin.role'
    if (Session::get('user_role') !== 'residence') {
        return redirect()->route('landing');
    }
    return view('residents.dashboard');
})->name('residents');

// --- ADMIN ROUTES GROUP (Protected by 'admin.role' middleware) ---
Route::middleware([\App\Http\Middleware\CheckAdminRole::class])->prefix('admin')->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Barangay Profiles routes
    Route::get('/barangay-profiles', [BarangayProfileController::class, 'barangayProfile'])->name('admin.barangay-profiles');
    Route::get('/barangay-profiles/{id}/edit', [BarangayProfileController::class, 'edit'])->name('admin.barangay-profiles.edit');
    Route::put('/barangay-profiles/{id}', [BarangayProfileController::class, 'update'])->name('admin.barangay-profiles.update');
    Route::delete('/barangay-profiles/{id}', [BarangayProfileController::class, 'delete'])->name('admin.barangay-profiles.delete');

    // Residences routes
    Route::get('/residences', [ResidenceController::class, 'residenceProfile'])->name('admin.residences');
    Route::get('/residences/{id}/edit', [ResidenceController::class, 'edit'])->name('admin.residences.edit');
    Route::put('/residences/{id}', [ResidenceController::class, 'update'])->name('admin.residences.update');
    Route::delete('/residences/{id}', [ResidenceController::class, 'delete'])->name('admin.residences.delete');

    // Account Requests listing and approval
    Route::get('/new-account-requests', [AccountRequestController::class, 'accountRequest'])->name('admin.new-account-requests');
    Route::put('/new-account-requests/{id}/approve', [AccountRequestController::class, 'approveAccountRequest'])->name('admin.account-requests.approve');

    // Profile routes for viewing and updating profile
    Route::get('/profile', [AdminProfileController::class, 'profile'])->name('admin.profile');
    Route::put('/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');

    // Blotter Reports route
    Route::get('/blotter-reports', [BlotterReportController::class, 'blotterReport'])->name('admin.blotter-reports');
    Route::put('/blotter-reports/{id}/approve', [BlotterReportController::class, 'approve'])->name('admin.blotter-approve');

    // Document Requests route
    Route::get('/document-requests', [DocumentRequestController::class, 'documentRequest'])->name('admin.document-requests');
    Route::put('/document-requests/{id}/approve', [DocumentRequestController::class, 'approve'])->name('admin.document-approve');

    // Accomplished Projects Route
    Route::get('/accomplished-projects', [AccomplishProjectController::class, 'accomplishProject'])->name('admin.accomplished-projects');

    // Health Status Route
    Route::get('/health-status', [HealthStatusController::class, 'healthStatus'])->name('admin.health-status');

    // Health Reports Route
    Route::get('/health-reports', [HealthReportController::class, 'healthReport'])->name('admin.health-reports');

    // Route to mark all notifications as read
    Route::post('/notifications/mark-all-as-read', [AdminNotificationController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-as-read');
    Route::get('/notifications/count', [AdminNotificationController::class, 'getNotificationCounts'])->name('admin.notifications.count');
    Route::get('/notifications', [AdminNotificationController::class, 'showNotifications'])->name('admin.notifications');

});

// Logout route
Route::post('/logout', function () {
    Session::flush();
    return redirect()->route('landing');
})->name('logout');