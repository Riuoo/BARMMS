<?php

use Illuminate\Support\Facades\Route;

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

Route::post('/login', function () {
    // Handle login logic here
    return redirect('/dashboard'); // or wherever you want to redirect after login
});

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
