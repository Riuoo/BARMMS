<?php

namespace App\Http\Controllers\AdminControllers\UserManagementControllers;

use App\Models\BarangayProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BarangayProfileController
{
    public function barangayProfile(Request $request)
    {
        // Statistics from full dataset
        $totalOfficials = BarangayProfile::count();
        $captainCount = BarangayProfile::where('role', 'captain')->count();
        $councilorCount = BarangayProfile::where('role', 'councilor')->count();
        $otherCount = BarangayProfile::whereNotIn('role', ['captain', 'councilor'])->count();

        // For display (filtered)
        $query = BarangayProfile::query();
        // Search by name, email, contact_number, or role
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
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
        $barangayProfiles = $query->orderByDesc('active')->orderBy('name')->paginate(10);
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'email' => 'required|email|unique:barangay_profiles,email',
            'role' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'contact_number' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            BarangayProfile::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'role' => $validatedData['role'],
                'address' => $validatedData['address'],
                'contact_number' => $validatedData['contact_number'],
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
        
        // Parse name into components for display
        $nameParts = $this->parseName($barangayProfile->name);
        
        return view('admin.barangay-profiles.edit_barangay_profile', compact('barangayProfile', 'nameParts'));
    }

    /**
     * Parse full name into first, middle, last, and suffix
     */
    private function parseName($fullName)
    {
        if (empty($fullName)) {
            return [
                'first_name' => '',
                'middle_name' => '',
                'last_name' => '',
                'suffix' => ''
            ];
        }
        
        $parts = array_filter(explode(' ', trim($fullName)), function($part) {
            return !empty(trim($part));
        });
        $parts = array_values($parts); // Re-index array
        
        $suffixes = ['Jr.', 'Sr.', 'II', 'III', 'IV', 'V', 'Jr', 'Sr'];
        
        $result = [
            'first_name' => '',
            'middle_name' => '',
            'last_name' => '',
            'suffix' => ''
        ];
        
        if (empty($parts)) {
            return $result;
        }
        
        // Check if last part is a suffix
        $lastPart = end($parts);
        $hasSuffix = in_array($lastPart, $suffixes, true);
        
        if ($hasSuffix && count($parts) > 1) {
            $result['suffix'] = array_pop($parts);
        }
        
        if (count($parts) >= 1) {
            $result['first_name'] = $parts[0];
        }
        
        if (count($parts) >= 2) {
            $result['last_name'] = end($parts);
        }
        
        if (count($parts) >= 3) {
            // Middle name(s) are everything between first and last
            $middleParts = array_slice($parts, 1, -1);
            $result['middle_name'] = implode(' ', $middleParts);
        }
        
        return $result;
    }

    public function update(Request $request, $id)
    {
        try {
            $barangayProfile = BarangayProfile::findOrFail($id);

            // Validate password and contact_number
            $validatedData = $request->validate([
                'password' => 'nullable|string|min:6|confirmed',
                'contact_number' => 'required|string|max:20',
            ]);

            // Update contact number
            $barangayProfile->contact_number = $validatedData['contact_number'];

            // Only update password if provided
            if (!empty($validatedData['password'])) {
                $barangayProfile->password = Hash::make($validatedData['password']);
                notify()->success('Profile updated successfully.');
            } else {
                notify()->success('Contact number updated successfully.');
            }
            
            $barangayProfile->save();

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