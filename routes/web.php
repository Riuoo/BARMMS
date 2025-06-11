<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\Auth\LoginController;
use App\Models\User;
use App\Models\BlotterRequest;
use App\Models\DocumentRequest;
use Illuminate\Support\Facades\Log;

// Landing page route
Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/admin/contact', function () {
    return view('admin_contact');
})->name('admin.contact')->middleware('guest');

use App\Models\AccountRequest;

Route::post('/admin/contact', function (Request $request) {
    try {
        // Validate the form data
        $request->validate([
            'email' => 'required|email',
        ]);

       // Store the email address in the account_requests table
        $accountRequest = new AccountRequest();
        $accountRequest->email = $request->email;
        $accountRequest->status = 'pending';
        $accountRequest->save();

        return response()->json(['success' => 'Request sent successfully! We will contact you soon.']);
    } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
        return response()->json(['error' => 'An account request with this email address already exists.']);
    } catch (\Exception $e) {
        Log::error('Error submitting request: ' . $e->getMessage());
        return response()->json(['error' => 'Error submitting request: ' . $e->getMessage()]);
    }
})->name('admin.contact')->middleware('guest');

Route::get('/login', function () {
    return view('landing');
})->name('login')->middleware('guest');

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    // Validate input
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Attempt to find user by email
    $user = User::where('email', $credentials['email'])->first();

    if (!$user || !Hash::check($credentials['password'], $user->password)) {
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    // Set user session
    Session::put('user_id', $user->id);
    Session::put('user_role', $user->role); // assuming 'role' column exists in users table

    // Redirect based on role
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('residents');
    }
})->name('login.post')->middleware('guest');

// Forgot Password Routes
Route::get('/forgot-password', function () {
    return view('fpass');
})->name('password.request')->middleware('guest');

Route::post('/forgot-password', function () {
    // TODO: Implement sending password reset email
    return back()->with('status', 'Password reset link sent! (Not implemented)');
})->middleware('guest');

use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AccountRequestController;

Route::prefix('admin')->group(function () {
    Route::resource('account-requests', AccountRequestController::class)->only(['index']);
    Route::put('account-requests/{account_request}/approve', [AccountRequestController::class, 'approve'])->name('admin.account-requests.approve');
});

Route::get('/register/{token}', [RegistrationController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegistrationController::class, 'register'])->name('register');

use App\Http\Controllers\AdminAccountRequestController;
use App\Http\Controllers\AdminDashboardController;

// Admin routes with session checks
Route::get('/admin/dashboard', function () {
    if (Session::get('user_role') !== 'admin') {
        return redirect()->route('landing');
    }
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/users', function () {
    if (Session::get('user_role') !== 'admin') {
        return redirect()->route('landing');
    }
    $users = User::all();
    return view('admin.users', compact('users'));
})->name('admin.users');

Route::get('/admin/users/{id}/edit', function ($id) {
    if (Session::get('user_role') !== 'admin') {
        return redirect()->route('landing');
    }
    $user = User::findOrFail($id);
    return view('admin.edit_user', compact('user'));
})->name('admin.users.edit');


Route::delete('/admin/users/{id}', [AdminDashboardController::class, 'deleteUser'])->name('admin.users.delete');


Route::get('/admin/blotter-reports', function () {
    if (Session::get('user_role') !== 'admin') {
        return redirect()->route('landing');
    }
    $blotterRequests = BlotterRequest::all();
    return view('admin.blotter-reports', compact('blotterRequests'));
})->name('admin.blotter-reports');

Route::get('/admin/document-requests', function () {
    if (Session::get('user_role') !== 'admin') {
        return redirect()->route('landing');
    }
    $documentRequests = DocumentRequest::all();
    return view('admin.document-requests', compact('documentRequests'));
})->name('admin.document-requests');

Route::get('/admin/new-account-requests', [App\Http\Controllers\AdminDashboardController::class, 'accountRequests'])->name('admin.new-account-requests');

Route::put('/admin/new-account-requests/{id}/approve', [App\Http\Controllers\AdminDashboardController::class, 'approveAccountRequest'])->name('admin.account-approve');
Route::get('/register/form/{token}', [RegistrationController::class, 'showRegistrationForm'])->name('register.form');

Route::get('/admin/test-email', [App\Http\Controllers\AdminDashboardController::class, 'testEmail'])->name('admin.test-email');


Route::get('/admin/accomplished-projects', function () {
    if (Session::get('user_role') !== 'admin') {
        return redirect()->route('landing');
    }
    return view('admin.accomplished-projects');
})->name('admin.accomplished-projects');

Route::put('/admin/blotter-reports/{id}/approve', [AdminDashboardController::class, 'approveBlotterRequest'])->name('admin.blotter-approve');

Route::put('/admin/users/{id}', [AdminDashboardController::class, 'updateUser'])->name('admin.users.update');


Route::put('/document-requests/{id}/approve', [AdminDashboardController::class, 'approveDocumentRequest'])->name('admin.document-approve');

Route::get('/admin/users/live-search', [AdminDashboardController::class, 'liveSearchUsers'])->name('admin.users.live-search');


Route::get('/blotter-reports/live-search', [AdminDashboardController::class, 'liveSearchBlotterReports'])->name('admin.blotter-reports.live-search');

Route::get('/document-requests/live-search', [AdminDashboardController::class, 'liveSearchDocumentRequests'])->name('admin.document-requests.live-search');

Route::get('/admin/new-account-requests/live-search', [AdminDashboardController::class, 'liveSearchAccountRequests'])->name('admin.new-account-requests.live-search');

Route::put('/admin/profile/update', function () {
    if (Session::get('user_role') !== 'admin') {
        return redirect()->route('landing');
    }
    // Update profile logic removed for now
    return redirect()->route('admin.profile');
})->name('admin.profile.update');

Route::get('/admin/profile', function () {
    if (Session::get('user_role') !== 'admin') {
        return redirect()->route('landing');
    }
    return view('admin.profile');
})->name('admin.profile');

Route::get('/captain/dashboard', function () {
    return view('captain.dashboard');
})->name('captain.dashboard');

Route::get('/secretary/dashboard', function () {
    return view('secretary.dashboard');
})->name('secretary.dashboard');

use App\Http\Controllers\TestEmailController;

Route::get('/residents', function () {
    if (Session::get('user_role') !== 'user') {
        return redirect()->route('landing');
    }
    return view('residents.dashboard');
})->name('residents');

Route::post('/logout', function () {
    // Clear user session
    Session::flush();
    return redirect()->route('landing');
})->name('logout');

Route::get('/test-email-form', function () {
    return view('test-email-form');
})->name('test.email.form');

Route::post('/send-test-email', [TestEmailController::class, 'sendTestEmail'])->name('send.test.email');
