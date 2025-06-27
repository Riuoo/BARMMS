<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

class LoginController
{
    public function showLoginForm()
    {
        return view('landing');
    }

    public function login(Request $request)
    {
        // Login validation and session management removed as requested
        return redirect()->route('landing');
    }

    public function logout(Request $request)
    {
        // Logout session invalidation removed as requested
        return redirect('/');
    }
}
