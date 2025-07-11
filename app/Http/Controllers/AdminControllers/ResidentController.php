<?php

namespace App\Http\Controllers\AdminControllers;

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
        
        $residents = Residents::all();
        return view('admin.residents', compact('residents'));
    }

    public function create()
    {
        return view('admin.create_resident_profile');
    }
    

        public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:residents,email',
            'address' => 'required|string|max:500',
            'password' => 'required|string|min:6|confirmed',
        ]);
        try {
            Residents::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'role' => 'resident',
                'address' => $validatedData['address'],
                'password' => Hash::make($validatedData['password']),
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
        return view('admin.edit_resident_profile', compact('resident'));
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
            ]);

            if (!empty($validatedData['password'])) {
                $resident->password = bcrypt($validatedData['password']);
            }

            $resident->name = $validatedData['name'];
            $resident->email = $validatedData['email'];
            $resident->role = $validatedData['role'];
            $resident->address = $validatedData['address'];
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
}
