<?php

namespace App\Http\Controllers\AdminControllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\BarangayProfile;
use App\Models\Residence;
use Illuminate\Support\Facades\Session;

class AdminProfileController
{
    public function profile()
    {
    $userId = Session::get('user_id');
    $currentUser = BarangayProfile::find($userId) ?? Residence::find($userId);

    if (!$currentUser) {
        return redirect()->route('landing');
    }

    return view('admin.profile', compact('currentUser'));
    }

    public function update(Request $request)
    {
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
    }
}
