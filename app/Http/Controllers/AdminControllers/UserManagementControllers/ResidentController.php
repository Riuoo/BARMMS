<?php

namespace App\Http\Controllers\AdminControllers\UserManagementControllers;

use App\Models\Residents;
use App\Models\AccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ResidentController
{
    public function residentProfile(Request $request)
    {
        // Statistics from full dataset (cache for 5 minutes to reduce DB load)
        $totalResidents = \Cache::remember('total_residents', 300, function() {
            return Residents::count();
        });
        $activeResidents = \Cache::remember('active_residents', 300, function() {
            return Residents::where('active', true)->count();
        });
        $recentResidents = \Cache::remember('recent_residents', 300, function() {
            return Residents::where('created_at', '>=', now()->startOfMonth())->count();
        });
        $withAddress = \Cache::remember('with_address_residents', 300, function() {
            return Residents::whereNotNull('address')->count();
        });

        // For display (filtered)
        $query = Residents::query();
        // Search by name, email, or contact number
        // Use CONCAT to search across separate name fields
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, '')) LIKE ?", ["%{$search}%"])
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
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
        // Filter by purok
        if ($request->filled('purok')) {
            $purok = $request->get('purok');
            $query->where('address', 'like', "%{$purok}%");
        }
        // Only select needed columns for the residents list and demographics modal
        $residents = $query->select([
            'id',
            'first_name',
            'middle_name',
            'last_name',
            'suffix',
            'email',
            'contact_number',
            'address',
            'birth_date',
            'active',
            'created_at',
            'age',
            'family_size',
            'education_level',
            'income_level',
            'employment_status',
        ])
        ->orderByDesc('active')
        ->orderByRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, ''))")
        ->paginate(10);
        return view('admin.residents.residents', compact('residents', 'totalResidents', 'activeResidents', 'recentResidents', 'withAddress'));
    }

    public function getDemographics(Residents $resident) // Use the correct model name, e.g., Residents
    {
        // Get all demographic fields with field-level access control
        $demographics = [
            'gender' => $resident->canViewField('gender') ? $resident->gender : null,
            'contact_number' => $resident->canViewField('contact_number') ? $resident->getMaskedContactNumber() : null,
            'birth_date' => $resident->canViewField('birth_date') && $resident->birth_date ? $resident->birth_date->format('Y-m-d') : null,
            'marital_status' => $resident->canViewField('marital_status') ? $resident->marital_status : null,
            'occupation' => $resident->canViewField('occupation') ? $resident->occupation : null,
            'age' => $resident->canViewField('age') ? $resident->age : null,
            'family_size' => $resident->canViewField('family_size') ? $resident->family_size : null,
            'education_level' => $resident->canViewField('education_level') ? $resident->education_level : null,
            'income_level' => $resident->canViewField('income_level') ? $resident->income_level : null,
            'employment_status' => $resident->canViewField('employment_status') ? $resident->employment_status : null,
            'is_pwd' => $resident->canViewField('is_pwd') ? ($resident->is_pwd ? 'Yes' : 'No') : null,
            'emergency_contact_name' => $resident->canViewField('emergency_contact_name') ? $resident->getMaskedEmergencyContactName() : null,
            'emergency_contact_number' => $resident->canViewField('emergency_contact_number') ? $resident->getMaskedEmergencyContactNumber() : null,
            'emergency_contact_relationship' => $resident->canViewField('emergency_contact_relationship') ? $resident->getMaskedEmergencyContactRelationship() : null
        ];

        // Remove null values (fields user cannot view)
        return response()->json(array_filter($demographics, function($value) {
            return $value !== null;
        }));
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

    public function checkDuplicateName(Request $request)
    {
        $firstName = trim((string) $request->get('first_name', ''));
        $middleName = trim((string) $request->get('middle_name', ''));
        $lastName = trim((string) $request->get('last_name', ''));
        $suffix = trim((string) $request->get('suffix', ''));

        if (empty($firstName) || empty($lastName)) {
            return response()->json(['duplicate' => false]);
        }

        // Try exact match first (case-insensitive, with all name parts)
        $duplicate = Residents::whereRaw(
            "LOWER(TRIM(first_name)) = ? AND LOWER(TRIM(last_name)) = ?",
            [strtolower($firstName), strtolower($lastName)]
        )->where(function($query) use ($middleName, $suffix) {
            // Handle middle name: both must have it or both must not have it
            if (!empty($middleName)) {
                // Request has middle name - resident must also have one
                $query->whereNotNull('middle_name')
                      ->where('middle_name', '!=', '')
                      ->whereRaw("LOWER(TRIM(middle_name)) = ?", [strtolower($middleName)]);
            } else {
                // Request has no middle name - resident must also not have one
                $query->where(function($q) {
                    $q->whereNull('middle_name')
                      ->orWhere('middle_name', '=', '');
                });
            }
        })->where(function($query) use ($suffix) {
            // Handle suffix similarly
            if (!empty($suffix)) {
                $query->whereRaw("LOWER(TRIM(COALESCE(suffix, ''))) = ?", [strtolower($suffix)]);
            } else {
                $query->where(function($q) {
                    $q->whereNull('suffix')
                      ->orWhere('suffix', '=', '');
                });
            }
        })->first();

        // If no exact match with middle name handling, try first+last only match
        // but flag it as a potential mismatch if middle names differ
        if (!$duplicate) {
            $firstLastMatch = Residents::whereRaw(
                "LOWER(TRIM(first_name)) = ? AND LOWER(TRIM(last_name)) = ?",
                [strtolower($firstName), strtolower($lastName)]
            )->where(function($query) use ($suffix) {
                if (!empty($suffix)) {
                    $query->whereRaw("LOWER(TRIM(COALESCE(suffix, ''))) = ?", [strtolower($suffix)]);
                } else {
                    $query->where(function($q) {
                        $q->whereNull('suffix')->orWhere('suffix', '=', '');
                    });
                }
            })->first();

            // If found but middle names differ, this is a mismatch that needs attention
            if ($firstLastMatch) {
                $residentMiddleName = trim($firstLastMatch->middle_name ?? '');
                $hasMiddleNameMismatch = (!empty($middleName) && empty($residentMiddleName)) ||
                                          (empty($middleName) && !empty($residentMiddleName));

                if ($hasMiddleNameMismatch) {
                    // Return as duplicate but with middle name mismatch warning
                    return response()->json([
                        'duplicate' => true,
                        'exact' => false,
                        'middle_name_mismatch' => true,
                        'existing_middle_name' => $residentMiddleName,
                        'resident_id' => $firstLastMatch->id,
                    ]);
                } else {
                    // Exact match on first+last+suffix, middle names both empty or both match
                    return response()->json([
                        'duplicate' => true,
                        'exact' => true,
                        'middle_name_mismatch' => false,
                        'resident_id' => $firstLastMatch->id,
                    ]);
                }
            }
        }

        if ($duplicate) {
            return response()->json([
                'duplicate' => true,
                'exact' => true,
                'middle_name_mismatch' => false,
                'resident_id' => $duplicate->id,
            ]);
        }

        return response()->json(['duplicate' => false]);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($value && !empty(trim($value))) {
                        $trimmed = trim($value);
                        // Check if it's a single letter
                        if (strlen($trimmed) === 1) {
                            $fail('Please enter your full middle name. Initials are not allowed.');
                        }
                        // Check if it's an initial with a period (e.g., "A." or "A. ")
                        if (preg_match('/^[A-Za-z]\.\s*$/', $trimmed)) {
                            $fail('Please enter your full middle name. Initials are not allowed.');
                        }
                        // Check if it's just an initial without period but only one character (already handled above)
                        // Additional check: if it's less than 2 characters after removing periods and spaces
                        $cleaned = preg_replace('/[.\s]+/', '', $trimmed);
                        if (strlen($cleaned) < 2) {
                            $fail('Please enter your full middle name. Initials are not allowed.');
                        }
                    }
                },
            ],
            'email' => [
                'nullable',
                'email',
                'unique:residents,email',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $exists = AccountRequest::where('email', $value)
                            ->whereIn('status', ['pending', 'approved', 'completed'])
                            ->exists();
                        if ($exists) {
                            $fail('This email has an account request that is pending, approved, or completed. Creating a resident account is not allowed for this email.');
                        }
                    }
                },
            ],
            'address' => 'required|string|max:500',
            'gender' => 'required|in:Male,Female',
            'contact_number' => 'nullable|string|max:255',
            'birth_date' => 'required|date|before:today',
            'marital_status' => 'required|in:Single,Married,Widowed,Divorced,Separated',
            'occupation' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'age' => 'required|integer|min:1|max:120',
            'family_size' => 'required|integer|min:1|max:20',
            'education_level' => 'required|string|in:No Education,Elementary,High School,Vocational,College,Post Graduate',
            'income_level' => 'required|string|in:Low,Lower Middle,Middle,Upper Middle,High',
            'employment_status' => 'required|string|in:Unemployed,Part-time,Self-employed,Full-time',
            'is_pwd' => 'required|boolean',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:255',
            'privacy_consent' => 'required|accepted',
        ]);

        // Check for duplicate name before creating
        $firstName = trim($request->first_name);
        $middleName = trim($request->middle_name ?? '');
        $lastName = trim($request->last_name);
        $suffix = trim($request->suffix ?? '');

        // Try exact match first (case-insensitive, with all name parts)
        $duplicate = Residents::whereRaw(
            "LOWER(TRIM(first_name)) = ? AND LOWER(TRIM(last_name)) = ?",
            [strtolower($firstName), strtolower($lastName)]
        )->where(function($query) use ($middleName, $suffix) {
            // Handle middle name: both must have it or both must not have it
            if (!empty($middleName)) {
                // Request has middle name - resident must also have one
                $query->whereNotNull('middle_name')
                      ->where('middle_name', '!=', '')
                      ->whereRaw("LOWER(TRIM(middle_name)) = ?", [strtolower($middleName)]);
            } else {
                // Request has no middle name - resident must also not have one
                $query->where(function($q) {
                    $q->whereNull('middle_name')
                      ->orWhere('middle_name', '=', '');
                });
            }
        })->where(function($query) use ($suffix) {
            // Handle suffix similarly
            if (!empty($suffix)) {
                $query->whereRaw("LOWER(TRIM(COALESCE(suffix, ''))) = ?", [strtolower($suffix)]);
            } else {
                $query->where(function($q) {
                    $q->whereNull('suffix')
                      ->orWhere('suffix', '=', '');
                });
            }
        })->first();

        // If no exact match with middle name handling, try first+last only match
        if (!$duplicate) {
            $firstLastMatch = Residents::whereRaw(
                "LOWER(TRIM(first_name)) = ? AND LOWER(TRIM(last_name)) = ?",
                [strtolower($firstName), strtolower($lastName)]
            )->where(function($query) use ($suffix) {
                if (!empty($suffix)) {
                    $query->whereRaw("LOWER(TRIM(COALESCE(suffix, ''))) = ?", [strtolower($suffix)]);
                } else {
                    $query->where(function($q) {
                        $q->whereNull('suffix')->orWhere('suffix', '=', '');
                    });
                }
            })->first();

            // If found but middle names differ, this is a mismatch that needs attention
            if ($firstLastMatch) {
                $residentMiddleName = trim($firstLastMatch->middle_name ?? '');
                $hasMiddleNameMismatch = (!empty($middleName) && empty($residentMiddleName)) ||
                                          (empty($middleName) && !empty($residentMiddleName));

                if ($hasMiddleNameMismatch) {
                    return back()->withInput()->withErrors([
                        'first_name' => 'A resident with the same first and last name already exists, but the middle name does not match. Please verify the name matches exactly (including middle name) before creating a duplicate record.'
                    ]);
                } else {
                    $duplicate = $firstLastMatch;
                }
            }
        }

        if ($duplicate) {
            return back()->withInput()->withErrors([
                'first_name' => 'A resident with this exact name already exists in the system. Please verify you are not creating a duplicate record.'
            ]);
        }

        try {
            Residents::create([
                'first_name' => $request->first_name,
                'middle_name' => !empty(trim($request->middle_name ?? '')) ? trim($request->middle_name) : null,
                'last_name' => $request->last_name,
                'suffix' => !empty(trim($request->suffix ?? '')) ? trim($request->suffix) : null,
                'email' => !empty($validatedData['email']) ? $validatedData['email'] : null,
                'role' => 'resident',
                'address' => $validatedData['address'],
                'gender' => $validatedData['gender'],
                'contact_number' => !empty($validatedData['contact_number']) ? $validatedData['contact_number'] : null,
                'birth_date' => $validatedData['birth_date'],
                'marital_status' => $validatedData['marital_status'],
                'occupation' => $validatedData['occupation'],
                'password' => Hash::make($validatedData['password']),
                'age' => $validatedData['age'],
                'family_size' => $validatedData['family_size'],
                'education_level' => $validatedData['education_level'],
                'income_level' => $validatedData['income_level'],
                'employment_status' => $validatedData['employment_status'],
                'is_pwd' => (bool)$validatedData['is_pwd'],
                'emergency_contact_name' => $validatedData['emergency_contact_name'],
                'emergency_contact_number' => $validatedData['emergency_contact_number'],
                'emergency_contact_relationship' => $validatedData['emergency_contact_relationship'],
                'active' => true,
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
                'address' => 'required|string|max:500',
                'gender' => 'required|in:Male,Female',
                'contact_number' => 'nullable|string|max:255',
                'birth_date' => 'required|date|before:today',
                'marital_status' => 'required|in:Single,Married,Widowed,Divorced,Separated',
                'occupation' => 'required|string|max:255',
                'password' => 'nullable|string|min:6|confirmed',
                'age' => 'required|integer|min:1|max:120',
                'family_size' => 'required|integer|min:1|max:20',
                'education_level' => 'required|string|in:No Education,Elementary,High School,Vocational,College,Post Graduate',
                'income_level' => 'required|string|in:Low,Lower Middle,Middle,Upper Middle,High',
                'employment_status' => 'required|string|in:Unemployed,Part-time,Self-employed,Full-time',
                'is_pwd' => 'required|boolean',
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_number' => 'nullable|string|max:255',
                'emergency_contact_relationship' => 'nullable|string|max:255',
                'privacy_consent' => 'required|accepted',
            ]);

            if (!empty($validatedData['password'])) {
                $resident->password = bcrypt($validatedData['password']);
            }

            $resident->address = $validatedData['address'];
            $resident->gender = $validatedData['gender'];
            $resident->contact_number = $validatedData['contact_number'];
            $resident->birth_date = $validatedData['birth_date'];
            $resident->marital_status = $validatedData['marital_status'];
            $resident->occupation = $validatedData['occupation'];
            $resident->age = $validatedData['age'];
            $resident->family_size = $validatedData['family_size'];
            $resident->education_level = $validatedData['education_level'];
            $resident->income_level = $validatedData['income_level'];
            $resident->employment_status = $validatedData['employment_status'];
            $resident->is_pwd = (bool)$validatedData['is_pwd'];
            $resident->emergency_contact_name = $validatedData['emergency_contact_name'];
            $resident->emergency_contact_number = $validatedData['emergency_contact_number'];
            $resident->emergency_contact_relationship = $validatedData['emergency_contact_relationship'];
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

    /**
     * Confirm and execute delete after 2FA verification
     */
    public function deleteConfirm($id)
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

        try {
            $residents = Residents::query()
                ->where('active', true)
                ->where(function ($q) use ($term) {
                    $q->whereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, '')) LIKE ?", ["%{$term}%"])
                      ->orWhere('email', 'like', "%{$term}%")
                      ->orWhere('address', 'like', "%{$term}%");
                })
                ->orderByRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, ''))")
                ->limit(10)
                ->get(['id', 'first_name', 'middle_name', 'last_name', 'suffix', 'email']);

            // Add computed name field for each resident
            $residents = $residents->map(function ($resident) {
                $parts = array_filter([
                    $resident->first_name,
                    $resident->middle_name,
                    $resident->last_name,
                    $resident->suffix
                ], function($part) {
                    return !empty(trim($part ?? ''));
                });
                $resident->name = implode(' ', $parts) ?: 'N/A';
                return $resident;
            });

            return response()->json($residents);
        } catch (\Exception $e) {
            \Log::error('Resident search error: ' . $e->getMessage());
            return response()->json(['error' => 'Search failed'], 500);
        }
    }

    /**
     * Get summary information for a resident (for auto-populating forms)
     */
    public function summary($id)
    {
        try {
            $resident = Residents::findOrFail($id);
            
            $summary = [
                'id'              => $resident->id,
                'name'            => $resident->canViewField('name') ? $resident->full_name : null,
                'age'             => $resident->canViewField('age') ? $resident->age : null,
                'address'         => $resident->canViewField('address') ? $resident->address : null,
                'civil_status'    => $resident->canViewField('marital_status') ? ($resident->marital_status ?? $resident->civil_status ?? null) : null,
                'contact_number'  => $resident->canViewField('contact_number') ? $resident->getMaskedContactNumber() : null,
                'birth_date'      => $resident->canViewField('birth_date') && $resident->birth_date ? $resident->birth_date->format('Y-m-d') : null,
                'gender'          => $resident->canViewField('gender') ? $resident->gender : null,
            ];

            // Remove null values
            return response()->json(array_filter($summary, function($value) {
                return $value !== null;
            }));
        } catch (\Exception $e) {
            Log::error('Error fetching resident summary: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch resident information'], 500);
        }
    }
}
