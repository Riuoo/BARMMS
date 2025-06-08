<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    // Other admin routes...

    Route::put('/users/{id}', [\App\Http\Controllers\AdminDashboardController::class, 'updateUser'])->name('users.update');
});


Route::get('/admin/profile', function () {
    return view('admin.profile');
})->name('admin.profile');

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/forgot-password', function () {
    return view('fpass');
});

Route::post('/forgot-password', function () {
    // Handle password reset email sending logic here
    return back()->with('status', 'Password reset link sent to your email!');
});

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\LoginController;

Route::post('/login', function () {
    // Handle login logic here
    return redirect('/admin/dashboard'); // redirect to admin dashboard route
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

Route::get('/admin/users', [AdminDashboardController::class, 'users'])->name('admin.users');

Route::get('/admin/users/{id}/edit', [AdminDashboardController::class, 'editUser'])->name('admin.users.edit');

Route::delete('/admin/users/{id}', [AdminDashboardController::class, 'deleteUser'])->name('admin.users.delete');

Route::get('/admin/blotter-reports', [AdminDashboardController::class, 'blotterReports'])->name('admin.blotter-reports');

Route::get('/admin/document-requests', [AdminDashboardController::class, 'documentRequests'])->name('admin.document-requests');

Route::get('/admin/accomplished-projects', [AdminDashboardController::class, 'accomplishedProjects'])->name('admin.accomplished-projects');

Route::get('/admin-contact', function () {
    return view('admin_contact');
})->name('admin.contact');

Route::post('/admin-contact', function (\Illuminate\Http\Request $request) {
    // Basic validation
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    // Here you would typically send an email or store the message in the database
    // For now, just redirect back with a success message

    return back()->with('success', 'Your message has been sent to the administrator. Thank you!');
})->name('admin.contact.submit');
