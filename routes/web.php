<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

use App\Models\Residence;
use App\Models\BarangayProfile;
use App\Models\BlotterRequest;
use App\Models\DocumentRequest;
use Illuminate\Support\Facades\Log;
use App\Models\AccountRequest;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AccountRequestController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\BlotterReportController;
use App\Http\Controllers\DocumentRequestController;
use Illuminate\Support\Str;

// Landing page route
Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');

Route::get('/admin/contact', function () {
    return view('admin_contact');
})->name('admin.contact')->middleware('guest');

// Account Request Route
Route::post('/admin/contact', function (Request $request) {
    try {
        $request->validate([
            'email' => 'required|email',
        ]);

        $token = Str::random(32);

        $accountRequest = new AccountRequest();
        $accountRequest->email = $request->email;
        $accountRequest->status = 'pending';
        $accountRequest->token = $token;
        $accountRequest->save();

        return response()->json(['success' => 'Request sent successfully! We will contact you soon.']);
    } catch (\Illuminate\Database\QueryException $e) {
        if(strpos($e->getMessage(), 'Unique') !== false) {
            return response()->json(['error' => 'An account request with this email address already exists.']);
        }
        return response()->json(['error' => 'Error submitting request: ' . $e->getMessage()]);
    } catch (\Exception $e) {
        Log::error('Error submitting request: ' . $e->getMessage());
        return response()->json(['error' => 'Error submitting request: ' . $e->getMessage()]);
    }
})->name('admin.contact')->middleware('guest');

// Authentication route with distinct user tables
Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $barangayProfile = BarangayProfile::where('email', $credentials['email'])->first();
    $residence = Residence::where('email', $credentials['email'])->first();

    $authenticatedUser = null;
    $role = null;

    if ($barangayProfile && Hash::check($credentials['password'], $barangayProfile->password)) {
        $authenticatedUser = $barangayProfile;
        if (in_array($barangayProfile->role, ['admin', 'captain', 'secretary', 'treasurer', 'councilor'])) {
            $role = 'barangay_staff';
        } else {
            $role = 'barangay';
        }
    } elseif ($residence && Hash::check($credentials['password'], $residence->password)) {
        $authenticatedUser = $residence;
        $role = 'residence';
    }

    if (!$authenticatedUser) {
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    Session::put('user_id', $authenticatedUser->id);
    Session::put('user_role', $role);

    if ($role === 'barangay_staff') {
        return redirect()->route('admin.dashboard');
    } elseif ($role === 'residence') {
        return redirect()->route('residents');
    } else {
        return redirect()->route('landing');
    }
})->name('login.post')->middleware('guest');

// Forgot Password Routes
Route::get('/forgot-password', function () {
    return view('fpass');
})->name('password.request')->middleware('guest');

Route::post('/forgot-password', function () {
    return back()->with('status', 'Password reset link sent! (Not implemented)');
})->middleware('guest');

// Account Request Routes

Route::prefix('admin')->group(function () {
    Route::resource('account-requests', AccountRequestController::class)->only(['index']);
    Route::put('account-requests/{accountRequest}/approve', [AccountRequestController::class, 'approve'])->name('admin.account-requests.approve');
});

// Registration routes
Route::get('/register/{token}', [RegistrationController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegistrationController::class, 'register'])->name('register');

// Admin dashboard
Route::get('/admin/dashboard', function () {
    $userRole = Session::get('user_role');
    if ($userRole !== 'barangay_staff' && $userRole !== 'barangay') {
        return redirect()->route('landing');
    }
    return view('admin.dashboard');
})->name('admin.dashboard');

// Barangay Profiles routes
Route::get('/admin/barangay-profiles', function () {
    $userRole = Session::get('user_role');
    if ($userRole !== 'barangay_staff' && $userRole !== 'barangay') {
        return redirect()->route('landing');
    }
    $barangayProfiles = BarangayProfile::all();
    return view('admin.barangay-profiles', compact('barangayProfiles'));
})->name('admin.barangay-profiles');

Route::get('/admin/barangay-profiles/{id}/edit', function ($id) {
    $userRole = Session::get('user_role');
    if ($userRole !== 'barangay_staff' && $userRole !== 'barangay') {
        return redirect()->route('landing');
    }
    $barangayProfile = BarangayProfile::findOrFail($id);
    return view('admin.edit_barangay_profile', compact('barangayProfile'));
})->name('admin.barangay-profiles.edit');

Route::put('/admin/barangay-profiles/{id}', [AdminDashboardController::class, 'updateBarangayProfile'])->name('admin.barangay-profiles.update');

Route::delete('/admin/barangay-profiles/{id}', [AdminDashboardController::class, 'deleteBarangayProfile'])->name('admin.barangay-profiles.delete');

// Residences routes
Route::get('/admin/residences', function () {
    $userRole = Session::get('user_role');
    if ($userRole !== 'barangay_staff' && $userRole !== 'barangay') {
        return redirect()->route('landing');
    }
    $residences = Residence::all();
    return view('admin.residences', compact('residences'));
})->name('admin.residences');

Route::get('/admin/residences/{id}/edit', function ($id) {
    $userRole = Session::get('user_role');
    if ($userRole !== 'barangay_staff' && $userRole !== 'barangay') {
        return redirect()->route('landing');
    }
    $residence = Residence::findOrFail($id);
    return view('admin.edit_residence_profile', compact('residence'));
})->name('admin.residences.edit');

Route::put('/admin/residences/{id}', [AdminDashboardController::class, 'updateResidence'])->name('admin.residences.update');

Route::delete('/admin/residences/{id}', [AdminDashboardController::class, 'deleteResidence'])->name('admin.residences.delete');

// Account Requests listing and approval
Route::get('/admin/new-account-requests', [AdminDashboardController::class, 'accountRequests'])->name('admin.new-account-requests');
Route::put('/admin/new-account-requests/{id}/approve', [AdminDashboardController::class, 'approveAccountRequest'])->name('admin.account-approve');

// Profile routes for viewing and updating profile
Route::get('/admin/profile', function () {
    $userRole = Session::get('user_role');
    if (!in_array($userRole, ['barangay_staff', 'barangay'])) {
        return redirect()->route('landing');
    }

    $userId = Session::get('user_id');
    $currentUser = BarangayProfile::find($userId) ?? Residence::find($userId);

    if (!$currentUser) {
        return redirect()->route('landing');
    }

    return view('admin.profile', compact('currentUser'));
})->name('admin.profile');

Route::put('/admin/profile/update', function (Request $request) {
    $userRole = Session::get('user_role');
    if (!in_array($userRole, ['barangay_staff', 'barangay'])) {
        return redirect()->route('landing');
    }

    $request->validate([
        'email' => 'required|email',
        'password' => 'nullable|min:6|confirmed',
    ]);

    $userId = Session::get('user_id');
    $user = BarangayProfile::find($userId) ?? Residence::find($userId);

    if (!$user) {
        return redirect()->route('landing');
    }

    $user->email = $request->email;

    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
})->name('admin.profile.update');

// Residents dashboard route for residence users
Route::get('/residents', function () {
    if (Session::get('user_role') !== 'residence') {
        return redirect()->route('landing');
    }
    return view('residents.dashboard');
})->name('residents');



Route::get('/admin/blotter-reports', function () {
    $userRole = Session::get('user_role');
    if (!in_array($userRole, ['barangay_staff', 'barangay'])) {
        return redirect()->route('landing');
    }
    $blotterRequests = BlotterRequest::with('user')->get();
    return view('admin.blotter-reports', compact('blotterRequests'));
})->name('admin.blotter-reports');
Route::put('/admin/blotter-reports/{id}/approve', [BlotterReportController::class, 'approve'])
    ->name('admin.blotter-approve');

// Document Requests route
Route::get('/admin/document-requests', function () {
    $userRole = Session::get('user_role');
    if (!in_array($userRole, ['barangay_staff', 'barangay'])) {
        return redirect()->route('landing');
    }
    $documentRequests = DocumentRequest::with('user')->get();
    return view('admin.document-requests', compact('documentRequests'));
})->name('admin.document-requests');
Route::put('/admin/document-requests/{id}/approve', [DocumentRequestController::class, 'approve'])
    ->name('admin.document-approve');

// Accomplished Projects Route
Route::get('/admin/accomplished-projects', function () {
    $userRole = Session::get('user_role');
    if (!in_array($userRole, ['barangay_staff', 'barangay'])) {
        return redirect()->route('landing');
    }
    return view('admin.accomplished-projects');
})->name('admin.accomplished-projects');

// Health Status Route
Route::get('/admin/health-status', function () {
    $userRole = Session::get('user_role');
    if (!in_array($userRole, ['barangay_staff', 'barangay'])) {
        return redirect()->route('landing');
    }
    return view('admin.health-status');
})->name('admin.health-status');

// Health Reports Route
Route::get('/admin/health-reports', function () {
    $userRole = Session::get('user_role');
    if (!in_array($userRole, ['barangay_staff', 'barangay'])) {
        return redirect()->route('landing');
    }
    return view('admin.health-reports');
})->name('admin.health-reports');

// Logout route
Route::post('/logout', function () {
    Session::flush();
    return redirect()->route('landing');
})->name('logout');
