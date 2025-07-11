<?php

namespace App\Http\Controllers\AdminControllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\BarangayProfile;
use Illuminate\Support\Facades\Session;

class AdminProfileController
{
    public function profile()
    {
    $userId = Session::get('user_id');
    $currentUser = BarangayProfile::find($userId);

    if (!$currentUser) {
        return redirect()->route('landing');
    }

    return view('admin.profile', compact('currentUser'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => 'nullable|min:8|confirmed',
        ]);

        $userId = Session::get('user_id');
        $user = BarangayProfile::find($userId);

        if (!$user) {
            return redirect()->route('landing');
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        notify()->success('Profile updated successfully.');
        return redirect()->route('admin.profile');
            
    }
}
