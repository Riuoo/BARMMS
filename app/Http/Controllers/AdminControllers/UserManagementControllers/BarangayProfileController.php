<?php

namespace App\Http\Controllers\AdminControllers\UserManagementControllers;

use App\Models\BarangayProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BarangayProfileController
{
    public function barangayProfile()
    {
        if (session('user_role') !== 'barangay') {
            // Abort the request with a 403 Unauthorized error
            abort(403, 'Unauthorized');
        }
        
        $barangayProfiles = BarangayProfile::all();
        return view('admin.barangay-profiles.barangay-profiles', compact('barangayProfiles'));
    }

    public function create()
    {
        return view('admin.barangay-profiles.create_barangay_profile');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:barangay_profiles,email',
            'role' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            BarangayProfile::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'role' => $validatedData['role'],
                'address' => $validatedData['address'],
                'password' => Hash::make($validatedData['password']),
            ]);

            notify()->success('New Barangay profile added successfully.');
            return redirect()->route('admin.barangay-profiles');
            
        } catch (\Exception $e) {
            notify()->error('Error adding Barangay profile: ' . $e->getMessage());
            return back()->withInput();
            
        }
    }

    public function edit($id)
    {    
        $barangayProfile = BarangayProfile::findOrFail($id);
        return view('admin.barangay-profiles.edit_barangay_profile', compact('barangayProfile'));
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
                $barangayProfile->password = Hash::make($validatedData['password']);
            }

            $barangayProfile->name = $validatedData['name'];
            $barangayProfile->email = $validatedData['email'];
            $barangayProfile->role = $validatedData['role'];
            $barangayProfile->address = $validatedData['address'];
            $barangayProfile->save();

            notify()->success('Barangay profile updated successfully.');
            return redirect()->route('admin.barangay-profiles');
            
        } catch (\Exception $e) {
            notify()->error('Error updating Barangay profile: ' . $e->getMessage());
            return back()->withInput();
            
        }
    }

    public function delete($id)
    {
        try {
            $barangayProfile = BarangayProfile::findOrFail($id);
            $barangayProfile->delete();
            notify()->success('Barangay profile deleted successfully.');
            return redirect()->route('admin.barangay-profiles');
            
        } catch (\Exception $e) {
            notify()->error('Error deleting Barangay profile: ' . $e->getMessage());
            return redirect()->route('admin.barangay-profiles');
            
        }
    }
}