<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\BarangayProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BarangayProfileController
{
    public function barangayProfile()
    {
        $barangayProfiles = BarangayProfile::all();
        return view('admin.barangay-profiles', compact('barangayProfiles'));
    }

    public function edit($id)
    {    
        $barangayProfile = BarangayProfile::findOrFail($id);
        return view('admin.edit_barangay_profile', compact('barangayProfile'));
    }

    public function update(Request $request, $id)
    {
        try {
            $barangayProfile = BarangayProfile::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:barangay_profiles,email,' . $id,
                'role' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'password' => 'nullable|string|min:6|confirmed',
            ]);

            if (!empty($validatedData['password'])) {
                $barangayProfile->password = bcrypt($validatedData['password']);
            }

            $barangayProfile->name = $validatedData['name'];
            $barangayProfile->email = $validatedData['email'];
            $barangayProfile->role = $validatedData['role'];
            $barangayProfile->address = $validatedData['address'];
            $barangayProfile->save();

            return redirect()->route('admin.barangay-profiles')->with('success', 'Barangay profile updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error updating Barangay profile: ' . $e->getMessage()])->withInput();
        }
    }

    public function delete($id)
    {
        try {
            $barangayProfile = BarangayProfile::findOrFail($id);
            $barangayProfile->delete();
            return redirect()->route('admin.barangay-profiles')->with('success', 'Barangay profile deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.barangay-profiles')->with('error', 'Error deleting Barangay profile: ' . $e->getMessage());
        }
    }
}
