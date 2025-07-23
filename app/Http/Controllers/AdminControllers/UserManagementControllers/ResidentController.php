<?php

namespace App\Http\Controllers\AdminControllers\UserManagementControllers;

use App\Models\Residents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResidentController
{
    public function residentProfile()
    {
        if (session('user_role') !== 'barangay') {
            // Abort the request with a 403 Unauthorized error
            abort(403, 'Unauthorized');
        }
        
        $residents = Residents::orderByDesc('active')->orderBy('name')->get();
        return view('admin.residents.residents', compact('residents'));
    }

    public function create()
    {
        return view('admin.residents.create_resident_profile');
    }
    

        public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:residents,email',
            'address' => 'required|string|max:500',
            'password' => 'required|string|min:6|confirmed',
            'age' => 'nullable|integer|min:1|max:120',
            'family_size' => 'nullable|integer|min:1|max:20',
            'education_level' => 'nullable|string|in:No Education,Elementary,High School,Vocational,College,Post Graduate',
            'income_level' => 'nullable|string|in:Low,Lower Middle,Middle,Upper Middle,High',
            'employment_status' => 'nullable|string|in:Unemployed,Part-time,Self-employed,Full-time',
            'health_status' => 'nullable|string|in:Critical,Poor,Fair,Good,Excellent',
        ]);
        try {
            Residents::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'role' => 'resident',
                'address' => $validatedData['address'],
                'password' => Hash::make($validatedData['password']),
                'age' => $validatedData['age'] ?? null,
                'family_size' => $validatedData['family_size'] ?? null,
                'education_level' => $validatedData['education_level'] ?? null,
                'income_level' => $validatedData['income_level'] ?? null,
                'employment_status' => $validatedData['employment_status'] ?? null,
                'health_status' => $validatedData['health_status'] ?? null,
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
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:residents,email,' . $id,
                'role' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'password' => 'nullable|string|min:6|confirmed',
                'age' => 'nullable|integer|min:1|max:120',
                'family_size' => 'nullable|integer|min:1|max:20',
                'education_level' => 'nullable|string|in:No Education,Elementary,High School,Vocational,College,Post Graduate',
                'income_level' => 'nullable|string|in:Low,Lower Middle,Middle,Upper Middle,High',
                'employment_status' => 'nullable|string|in:Unemployed,Part-time,Self-employed,Full-time',
                'health_status' => 'nullable|string|in:Critical,Poor,Fair,Good,Excellent',
            ]);

            if (!empty($validatedData['password'])) {
                $resident->password = bcrypt($validatedData['password']);
            }

            $resident->name = $validatedData['name'];
            $resident->email = $validatedData['email'];
            $resident->role = $validatedData['role'];
            $resident->address = $validatedData['address'];
            $resident->age = $validatedData['age'] ?? null;
            $resident->family_size = $validatedData['family_size'] ?? null;
            $resident->education_level = $validatedData['education_level'] ?? null;
            $resident->income_level = $validatedData['income_level'] ?? null;
            $resident->employment_status = $validatedData['employment_status'] ?? null;
            $resident->health_status = $validatedData['health_status'] ?? null;
            $resident->save();

            notify()->success('Resident updated successfully.');
            return redirect()->route('admin.residents');
            
        } catch (\Exception $e) {
            notify()->error('Error updating Resident: ' . $e->getMessage());
            return back()->withInput();
            
        }
    }

    public function delete($id)
    {
        try {
            $resident = Residents::findOrFail($id);
            $resident->delete();
            notify()->success('Resident deleted successfully.');
            return redirect()->route('admin.residents');
            
        } catch (\Exception $e) {
            notify()->error('Error deleting Resident: ' . $e->getMessage());
            return redirect()->route('admin.residents');
            
        }
    }

    public function toggleActive($id)
    {
        $resident = Residents::findOrFail($id);
        $resident->active = !$resident->active;
        $resident->save();
        notify()->success('Resident status updated successfully.');
        return redirect()->back();
    }
}
