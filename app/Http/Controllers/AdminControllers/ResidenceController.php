<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\Residence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ResidenceController
{
    public function residenceProfile()
    {
        $residences = Residence::all();
        return view('admin.residences', compact('residences'));
    }

    public function edit($id)
    {
        $residence = Residence::findOrFail($id);
        return view('admin.edit_residence_profile', compact('residence'));
    }

    public function update(Request $request, $id)
    {
        try {
            $residence = Residence::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:residences,email,' . $id,
                'role' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'password' => 'nullable|string|min:6|confirmed',
            ]);

            if (!empty($validatedData['password'])) {
                $residence->password = bcrypt($validatedData['password']);
            }

            $residence->name = $validatedData['name'];
            $residence->email = $validatedData['email'];
            $residence->role = $validatedData['role'];
            $residence->address = $validatedData['address'];
            $residence->save();

            return redirect()->route('admin.residences')->with('success', 'Residence updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error updating Residence: ' . $e->getMessage()])->withInput();
        }
    }

    public function delete($id)
    {
        try {
            $residence = Residence::findOrFail($id);
            $residence->delete();
            return redirect()->route('admin.residences')->with('success', 'Residence deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.residences')->with('error', 'Error deleting Residence: ' . $e->getMessage());
        }
    }
}
