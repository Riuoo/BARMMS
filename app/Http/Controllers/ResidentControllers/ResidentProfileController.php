<?php

namespace App\Http\Controllers\ResidentControllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Residents;
use Illuminate\Support\Facades\Session;

class ResidentProfileController
{
    public function profile()
    {
    $userId = Session::get('user_id');
    $resident = Residents::find($userId);

    if (!$resident) {
        return redirect()->route('landing');
    }

    return view('resident.profile', compact('resident'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'password' => 'nullable|min:8|confirmed',
        ]);

        $userId = Session::get('user_id');
        $user = Residents::find($userId);

        if (!$user) {
            return redirect()->route('landing');
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('resident.profile')->with('success', 'Profile updated successfully.');
    }
}
