<?php

namespace App\Http\Controllers\AdminControllers\UserManagementControllers;

use App\Models\BarangayProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BarangayProfileController
{
    public function barangayProfile(Request $request)
    {
        if (session('user_role') !== 'barangay') {
            // Abort the request with a 403 Unauthorized error
            abort(403, 'Unauthorized');
        }
        // Statistics from full dataset
        $totalOfficials = BarangayProfile::count();
        $captainCount = BarangayProfile::where('role', 'captain')->count();
        $councilorCount = BarangayProfile::where('role', 'councilor')->count();
        $otherCount = BarangayProfile::whereNotIn('role', ['captain', 'councilor'])->count();

        // For display (filtered)
        $query = BarangayProfile::query();
        // Search by name, email, or role
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }
        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }
        // Filter by status
        if ($request->filled('status')) {
            if ($request->get('status') === 'active') {
                $query->where('active', true);
            } elseif ($request->get('status') === 'inactive') {
                $query->where('active', false);
            }
        }
        $barangayProfiles = $query->orderByDesc('active')->orderBy('name')->get();
        return view('admin.barangay-profiles.barangay-profiles', compact('barangayProfiles', 'totalOfficials', 'captainCount', 'councilorCount', 'otherCount'));
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

            // Only validate password since other fields are readonly
            $validatedData = $request->validate([
                'password' => 'nullable|string|min:6|confirmed',
            ]);

            // Only update password if provided
            if (!empty($validatedData['password'])) {
                $barangayProfile->password = Hash::make($validatedData['password']);
                $barangayProfile->save();
                notify()->success('Password updated successfully.');
            } else {
                notify()->info('No changes were made to the profile.');
            }

            return redirect()->route('admin.barangay-profiles');
            
        } catch (\Exception $e) {
            notify()->error('Error updating Barangay profile: ' . $e->getMessage());
            return back()->withInput();
            
        }
    }

    public function deactivate($id)
    {
        try {
            $barangayProfile = BarangayProfile::findOrFail($id);
            $barangayProfile->active = false;
            $barangayProfile->save();
            notify()->success('Barangay profile deactivated successfully.');
            return redirect()->route('admin.barangay-profiles');
            
        } catch (\Exception $e) {
            notify()->error('Error deactivating Barangay profile: ' . $e->getMessage());
            return redirect()->route('admin.barangay-profiles');
            
        }
    }

    public function activate($id)
    {
        try {
            $barangayProfile = BarangayProfile::findOrFail($id);
            $barangayProfile->active = true;
            $barangayProfile->save();
            notify()->success('Barangay profile activated successfully.');
            return redirect()->route('admin.barangay-profiles');
            
        } catch (\Exception $e) {
            notify()->error('Error activating Barangay profile: ' . $e->getMessage());
            return redirect()->route('admin.barangay-profiles');
            
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