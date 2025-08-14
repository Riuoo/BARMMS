<?php

namespace App\Http\Controllers\AdminControllers\UserManagementControllers;

use App\Models\Residents;
use App\Models\AccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResidentController
{
    public function residentProfile(Request $request)
    {
        // Statistics from full dataset
        $totalResidents = Residents::count();
        $activeResidents = Residents::where('active', true)->count();
        $recentResidents = Residents::where('created_at', '>=', now()->startOfMonth())->count();
        $withAddress = Residents::whereNotNull('address')->count();

        // For display (filtered)
        $query = Residents::query();
        // Search by name, email, or address
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }
        // Filter by status
        if ($request->filled('status')) {
            if ($request->get('status') === 'active') {
                $query->where('active', true);
            } elseif ($request->get('status') === 'inactive') {
                $query->where('active', false);
            }
        }
        // Filter by recently added (last 30 days)
        if ($request->filled('recent') && $request->get('recent') === 'recent') {
            $query->where('created_at', '>=', now()->subDays(30));
        }
        $residents = $query->orderByDesc('active')->orderBy('name')->paginate(10);
        return view('admin.residents.residents', compact('residents', 'totalResidents', 'activeResidents', 'recentResidents', 'withAddress'));
    }

    public function getDemographics(Residents $resident) // Use the correct model name, e.g., Residents
    {
        // Return all available demographic fields from the residents table
        return response()->json([
            'age' => $resident->age,
            'family_size' => $resident->family_size,
            'education_level' => $resident->education_level,
            'income_level' => $resident->income_level,
            'employment_status' => $resident->employment_status,
            'health_status' => $resident->health_status
        ]);
    }

    public function create()
    {
        return view('admin.residents.create_resident_profile');
    }
    
    public function checkEmailRequest(Request $request)
    {
        $email = trim((string) $request->get('email'));
        if ($email === '') {
            return response()->json(['blocked' => false]);
        }

        $existing = AccountRequest::where('email', $email)
            ->whereIn('status', ['pending', 'approved', 'completed'])
            ->first();

        if ($existing) {
            return response()->json([
                'blocked' => true,
                'status' => $existing->status,
            ]);
        }

        return response()->json(['blocked' => false]);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:residents,email',
                function ($attribute, $value, $fail) {
                    $exists = AccountRequest::where('email', $value)
                        ->whereIn('status', ['pending', 'approved', 'completed'])
                        ->exists();
                    if ($exists) {
                        $fail('This email has an account request that is pending, approved, or completed. Creating a resident account is not allowed for this email.');
                    }
                },
            ],
            'address' => 'required|string|max:500',
            'password' => 'required|string|min:6|confirmed',
            'age' => 'required|integer|min:1|max:120',
            'family_size' => 'required|integer|min:1|max:20',
            'education_level' => 'required|string|in:No Education,Elementary,High School,Vocational,College,Post Graduate',
            'income_level' => 'required|string|in:Low,Lower Middle,Middle,Upper Middle,High',
            'employment_status' => 'required|string|in:Unemployed,Part-time,Self-employed,Full-time',
            'health_status' => 'required|string|in:Critical,Poor,Fair,Good,Excellent',
        ]);
        try {
            Residents::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'role' => 'resident',
                'address' => $validatedData['address'],
                'password' => Hash::make($validatedData['password']),
                'age' => $validatedData['age'],
                'family_size' => $validatedData['family_size'],
                'education_level' => $validatedData['education_level'],
                'income_level' => $validatedData['income_level'],
                'employment_status' => $validatedData['employment_status'],
                'health_status' => $validatedData['health_status'],
            ]);
            notify()->success('New resident added successfully.');
            return redirect()->route('admin.residents');
            
        } catch (\Exception $e) {
            notify()->error('Error adding resident: ' . $e->getMessage());
            return back()->withInput();
            
        }
    }

    public function edit($id)
    {
        $resident = Residents::findOrFail($id);
        return view('admin.residents.edit_resident_profile', compact('resident'));
    }

    public function update(Request $request, $id)
    {
        try {
            $resident = Residents::findOrFail($id);

            $validatedData = $request->validate([
                'password' => 'nullable|string|min:6|confirmed',
                'age' => 'required|integer|min:1|max:120',
                'family_size' => 'required|integer|min:1|max:20',
                'education_level' => 'required|string|in:No Education,Elementary,High School,Vocational,College,Post Graduate',
                'income_level' => 'required|string|in:Low,Lower Middle,Middle,Upper Middle,High',
                'employment_status' => 'required|string|in:Unemployed,Part-time,Self-employed,Full-time',
                'health_status' => 'required|string|in:Critical,Poor,Fair,Good,Excellent',
            ]);

            if (!empty($validatedData['password'])) {
                $resident->password = bcrypt($validatedData['password']);
            }

            $resident->age = $validatedData['age'];
            $resident->family_size = $validatedData['family_size'];
            $resident->education_level = $validatedData['education_level'];
            $resident->income_level = $validatedData['income_level'];
            $resident->employment_status = $validatedData['employment_status'];
            $resident->health_status = $validatedData['health_status'];
            $resident->save();

            notify()->success('Resident updated successfully.');
            return redirect()->route('admin.residents');
            
        } catch (\Exception $e) {
            notify()->error('Error updating Resident: ' . $e->getMessage());
            return back()->withInput();
            
        }
    }

    public function deactivate($id)
    {
        try {
            $resident = Residents::findOrFail($id);
            $resident->active = false;
            $resident->save();
            notify()->success('Resident deactivated successfully.');
            return redirect()->route('admin.residents');
            
        } catch (\Exception $e) {
            notify()->error('Error deactivating Resident: ' . $e->getMessage());
            return redirect()->route('admin.residents');
            
        }
    }

    public function activate($id)
    {
        try {
            $resident = Residents::findOrFail($id);
            $resident->active = true;
            $resident->save();
            notify()->success('Resident activated successfully.');
            return redirect()->route('admin.residents');
            
        } catch (\Exception $e) {
            notify()->error('Error activating Resident: ' . $e->getMessage());
            return redirect()->route('admin.residents');
            
        }
    }

    public function delete($id)
    {
        try {
            $resident = Residents::findOrFail($id);
            $residentEmail = $resident->email;
            
            // Delete the resident
            $resident->delete();
            
            // Also delete any account requests with the same email
            AccountRequest::where('email', $residentEmail)->delete();
            
            notify()->success('Resident deleted successfully.');
            return redirect()->route('admin.residents');
            
        } catch (\Exception $e) {
            notify()->error('Error deleting Resident: ' . $e->getMessage());
            return redirect()->route('admin.residents');
            
        }
    }
    
    public function search(Request $request)
    {
        $term = $request->get('term');
        
        if (!$term || strlen($term) < 2) {
            return response()->json([]);
        }

        $residents = Residents::query()
            ->where('active', true)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('address', 'like', "%{$term}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($residents);
    }
}
