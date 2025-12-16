<?php

namespace App\Http\Controllers\AdminControllers\HealthManagementControllers;

use App\Models\VaccinationRecord;
use App\Models\ChildProfile;
use Illuminate\Http\Request;
use App\Models\Residents;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class VaccinationRecordController
{
    public function index(Request $request)
    {
        $query = VaccinationRecord::with(['resident', 'childProfile']);

        // SEARCH - Patient name or vaccine details
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('resident', function($q) use ($search) {
                $q->whereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, '')) LIKE ?", ["%{$search}%"]);
            })->orWhereHas('childProfile', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            })->orWhere('vaccine_name', 'like', "%{$search}%")
              ->orWhere('vaccine_type', 'like', "%{$search}%");
        }

        // DOSE STATUS FILTER
        if ($request->filled('dose_status')) {
            switch($request->get('dose_status')) {
                case 'overdue':
                    $query->whereNotNull('next_dose_date')
                          ->where('next_dose_date', '<', now());
                    break;
                case 'due_soon':
                    $query->whereNotNull('next_dose_date')
                          ->whereBetween('next_dose_date', [now(), now()->addDays(30)]);
                    break;
                case 'up_to_date':
                    $query->whereNull('next_dose_date');
                    break;
            }
        }

        // AGE GROUP FILTER
        if ($request->filled('age_group')) {
            $query->where('age_group', $request->get('age_group'));
        }

        $stats = [
            'total' => VaccinationRecord::count(),
            'due_soon' => VaccinationRecord::whereNotNull('next_dose_date')
                        ->whereBetween('next_dose_date', [now(), now()->addDays(30)])
                        ->count(),
            'overdue' => VaccinationRecord::whereNotNull('next_dose_date')
                        ->where('next_dose_date', '<', now())
                        ->count(),
            'completed' => VaccinationRecord::whereNull('next_dose_date')->count(),
            'last_month' => VaccinationRecord::where('vaccination_date', '>=', now()->subDays(30))->count(),
            'children' => VaccinationRecord::whereNotNull('child_profile_id')->count(),
            'residents' => VaccinationRecord::whereNotNull('resident_id')->count(),
        ];

        $vaccinationRecords = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.vaccination-records.index', compact('vaccinationRecords', 'stats'));
    }

    public function search(Request $request)
    {
        $vaccinationRecords = VaccinationRecord::with(['resident', 'childProfile'])
            ->when($request->filled('search'), function($q) use ($request) {
                $search = $request->get('search');
                $q->whereHas('resident', function($subQ) use ($search) {
                    $subQ->whereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, '')) LIKE ?", ["%{$search}%"]);
                })->orWhereHas('childProfile', function($subQ) use ($search) {
                    $subQ->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%");
                })
                ->orWhere('vaccine_name', 'like', "%{$search}%")
                ->orWhere('vaccine_type', 'like', "%{$search}%");
            })
            ->when($request->filled('dose_status'), function($q) use ($request) {
                switch($request->get('dose_status')) {
                    case 'overdue':
                        $q->whereNotNull('next_dose_date')
                          ->where('next_dose_date', '<', now());
                        break;
                    case 'due_soon':
                        $q->whereNotNull('next_dose_date')
                          ->whereBetween('next_dose_date', [now(), now()->addDays(30)]);
                        break;
                    case 'up_to_date':
                        $q->whereNull('next_dose_date');
                        break;
                }
            })
            ->orderBy('vaccination_date', 'desc')
            ->paginate(10);

        return view('admin.vaccination-records.index', [
            'vaccinationRecords' => $vaccinationRecords,
            'search' => $request->get('search'),
            'dose_status' => $request->get('dose_status')
        ]);
    }

    public function create()
    {
        $residents = Residents::where('active', true)->get();
        return view('admin.vaccination-records.create', compact('residents'));
    }

    public function createChild(Request $request)
    {
        $residents = Residents::where('active', true)->get();
        $ageGroup = 'child';
        $childProfiles = ChildProfile::where('is_active', true)->orderBy('first_name')->get();
        $prefillChild = null;
        if ($request->filled('child_id')) {
            $prefillChild = ChildProfile::where('is_active', true)->find($request->get('child_id'));
        }
        return view('admin.vaccination-records.create-child', compact('residents', 'ageGroup', 'childProfiles', 'prefillChild'));
    }

    public function createInfant()
    {
        $residents = Residents::where('active', true)->get();
        $ageGroup = 'infant';
        $childProfiles = ChildProfile::where('is_active', true)->orderBy('first_name')->get();
        return view('admin.vaccination-records.create-infant', compact('residents', 'ageGroup', 'childProfiles'));
    }

    public function createToddler()
    {
        $residents = Residents::where('active', true)->get();
        $ageGroup = 'toddler';
        $childProfiles = ChildProfile::where('is_active', true)->orderBy('first_name')->get();
        return view('admin.vaccination-records.create-toddler', compact('residents', 'ageGroup', 'childProfiles'));
    }

    public function createAdolescent()
    {
        $residents = Residents::where('active', true)->get();
        $ageGroup = 'adolescent';
        $childProfiles = ChildProfile::where('is_active', true)->orderBy('first_name')->get();
        return view('admin.vaccination-records.create-adolescent', compact('residents', 'ageGroup', 'childProfiles'));
    }

    public function createAdult()
    {
        $residents = Residents::where('active', true)->get();
        $ageGroup = 'adult';
        return view('admin.vaccination-records.create-adult', compact('residents', 'ageGroup'));
    }

    public function createElderly()
    {
        $residents = Residents::where('active', true)->get();
        $ageGroup = 'elderly';
        return view('admin.vaccination-records.create-elderly', compact('residents', 'ageGroup'));
    }

    public function store(Request $request)
    {
        Log::info('storeChildProfile invoked');
        $validated = $request->validate([
            'resident_id' => 'nullable|exists:residents,id',
            'child_profile_id' => 'nullable|exists:child_profiles,id',
            'vaccine_name' => 'required|string|max:255',
            'vaccine_type' => 'required|string|in:COVID-19,Influenza,Pneumonia,Tetanus,Hepatitis B,MMR,Varicella,HPV,DTaP,Pneumococcal,Rotavirus,Hib,Other',
            'vaccination_date' => 'required|date|before_or_equal:today',
            'dose_number' => 'required|integer|min:1',
            'next_dose_date' => 'nullable|date|after:vaccination_date',
            // now ID based
            'administered_by' => 'nullable|integer|exists:barangay_profiles,id',
            'privacy_consent' => 'required|accepted',
        ]);

        // Fallback: if IDs are missing, try to resolve from search text
        if (empty($validated['child_profile_id']) && $request->filled('child_search')) {
            $search = trim($request->get('child_search'));
            $matches = ChildProfile::where('is_active', true)
                ->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"]);
                })->limit(2)->get();
            if ($matches->count() === 1) {
                $validated['child_profile_id'] = $matches->first()->id;
            }
        }

        if (empty($validated['resident_id']) && $request->filled('resident_search')) {
            $search = trim($request->get('resident_search'));
            $matches = Residents::where('active', true)
                ->where(function($q) use ($search) {
                    $q->whereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, '')) LIKE ?", ["%{$search}%"])
                      ->orWhere('email', 'like', "%{$search}%");
                })->limit(2)->get();
            if ($matches->count() === 1) {
                $validated['resident_id'] = $matches->first()->id;
            }
        }

        // Ensure either resident_id or child_profile_id is set
        if (empty($validated['resident_id']) && empty($validated['child_profile_id'])) {
            notify()->error('Either resident or child profile must be selected.');
            return back()->withInput();
        }

        if (!empty($validated['resident_id'])) {
            $user = Residents::find($validated['resident_id']);
            if (!$user) {
                notify()->error('This resident record no longer exists.');
                return back()->withInput();
            }
            if ($user->active === false) {
                notify()->error('This user account is inactive and cannot make transactions.');
                return back()->withInput();
            }
        }

        // Defaults removed with simplified schema

        // Set administered_by from session as FK if not provided
        $sessionUserId = Session::get('user_id');
        if (empty($validated['administered_by']) && !empty($sessionUserId)) {
            $validated['administered_by'] = (int) $sessionUserId;
        }

        try {
            VaccinationRecord::create($validated);
            notify()->success('Vaccination record created successfully.');
            return redirect()->route('admin.vaccination-records.index');
        } catch (\Exception $e) {
            notify()->error('Error creating vaccination record: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function show($id)
    {
        $vaccinationRecord = VaccinationRecord::with(['resident', 'childProfile', 'administeredByProfile'])->findOrFail($id);
        return view('admin.vaccination-records.show', compact('vaccinationRecord'));
    }

    public function edit($id)
    {
        $vaccinationRecord = VaccinationRecord::with(['resident'])->findOrFail($id);
        $residents = Residents::all();
        return view('admin.vaccination-records.edit', compact('vaccinationRecord', 'residents'));
    }

    public function update(Request $request, $id)
    {
        $vaccinationRecord = VaccinationRecord::findOrFail($id);
        
        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'vaccine_name' => 'required|string|max:255',
            'vaccine_type' => 'required|string|in:COVID-19,Influenza,Pneumonia,Tetanus,Hepatitis B,MMR,Varicella,HPV,Other',
            'vaccination_date' => 'required|date|before_or_equal:today',
            'dose_number' => 'required|integer|min:1',
            'next_dose_date' => 'nullable|date|after:vaccination_date',
            'administered_by' => 'nullable|integer|exists:barangay_profiles,id',
            
        ]);

        try {
            $vaccinationRecord->update($validated);
            notify()->success('Vaccination record updated successfully.');
            return redirect()->route('admin.vaccination-records.index');
        } catch (\Exception $e) {
            notify()->error('Error updating vaccination record: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $vaccinationRecord = VaccinationRecord::findOrFail($id);
            $vaccinationRecord->delete();
            
            notify()->success('Vaccination record deleted successfully.');
            return redirect()->route('admin.vaccination-records.index');
        } catch (\Exception $e) {
            notify()->error('Error deleting vaccination record: ' . $e->getMessage());
            return back();
        }
    }

    public function dueVaccinations(Request $request)
    {
        $baseQuery = VaccinationRecord::with(['resident', 'childProfile'])
            ->whereNotNull('next_dose_date')
            ->where('next_dose_date', '<=', now()->addDays(30));

        if ($request->filled('search')) {
            $search = $request->input('search');
            $baseQuery->where(function ($q) use ($search) {
                $q->where('vaccine_name', 'like', '%' . $search . '%')
                    ->orWhere('vaccine_type', 'like', '%' . $search . '%')
                    ->orWhereHas('resident', function ($residentQuery) use ($search) {
                        $residentQuery->whereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, '')) LIKE ?", ['%' . $search . '%']);
                    })
                    ->orWhereHas('childProfile', function ($childQuery) use ($search) {
                        $childQuery->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%');
                    });
            });
        }

        $now = now();
        $oneWeekFromNow = $now->copy()->addDays(7);
        $oneMonthFromNow = $now->copy()->addDays(30);

        $statsBaseQuery = clone $baseQuery;
        $stats = [
            'total_due' => (clone $statsBaseQuery)->count(),
            'overdue' => (clone $statsBaseQuery)->where('next_dose_date', '<', $now)->count(),
            'due_this_week' => (clone $statsBaseQuery)
                ->whereBetween('next_dose_date', [$now, $oneWeekFromNow])
                ->count(),
            'due_soon' => (clone $statsBaseQuery)
                ->where('next_dose_date', '>', $oneWeekFromNow)
                ->where('next_dose_date', '<=', $oneMonthFromNow)
                ->count(),
        ];

        $query = clone $baseQuery;

        if ($request->filled('status')) {
            switch ($request->get('status')) {
                case 'overdue':
                    $query->where('next_dose_date', '<', $now);
                    break;
                case 'due_this_week':
                    $query->whereBetween('next_dose_date', [$now, $oneWeekFromNow]);
                    break;
                case 'due_soon':
                    $query->where('next_dose_date', '>', $oneWeekFromNow)
                        ->where('next_dose_date', '<=', $oneMonthFromNow);
                    break;
            }
        }

        $dueVaccinations = $query->orderBy('next_dose_date', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.vaccination-records.due', compact('dueVaccinations', 'stats'));
    }

    public function generateReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        $vaccineType = $request->get('vaccine_type');

        $query = VaccinationRecord::with('resident')
            ->whereBetween('vaccination_date', [$startDate, $endDate]);

        if ($vaccineType) {
            $query->where('vaccine_type', $vaccineType);
        }

        $vaccinationRecords = $query->orderBy('vaccination_date', 'desc')->get();

        $summary = [
            'total_vaccinations' => $vaccinationRecords->count(),
            'by_vaccine_type' => $vaccinationRecords->groupBy('vaccine_type')->map->count(),
            'by_month' => $vaccinationRecords->groupBy(function($record) {
                return $record->vaccination_date->format('Y-m');
            })->map->count(),
        ];

        return view('admin.vaccination-records.report', compact('vaccinationRecords', 'summary', 'startDate', 'endDate', 'vaccineType'));
    }

    public function getRecommendedVaccines(Request $request)
    {
        return response()->json([]);
    }

    public function getChildProfiles(Request $request)
    {
        $query = ChildProfile::where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('mother_name', 'like', "%{$search}%");
            });
        }

        // If AJAX or JSON requested, return compact JSON for search dropdowns
        if ($request->ajax() || $request->wantsJson()) {
            $children = $query->select('id', 'first_name', 'last_name')
                              ->orderBy('first_name')
                              ->limit(10)
                              ->get()
                              ->map(function($c){
                                  return [
                                      'id' => $c->id,
                                      'first_name' => $c->first_name,
                                      'last_name' => $c->last_name,
                                  ];
                              });
            return response()->json(['data' => $children]);
        }

        $children = $query->orderBy('first_name')->paginate(15);
        return view('admin.vaccination-records.child-profiles', compact('children'));
    }

    public function createChildProfile()
    {
        return view('admin.vaccination-records.create-child-profile');
    }

    public function storeChildProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|string|in:Male,Female,Other',
            'birth_place' => 'nullable|string|max:255',
            'birth_certificate_number' => 'nullable|string|max:255',
            'mother_name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_relationship' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'purok' => 'required|string|max:255',
            'medical_conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
            'special_notes' => 'nullable|string',
            'privacy_consent' => 'required|accepted',
        ]);

        // Use session-based user ID consistent with the rest of the app
        $validated['registered_by'] = Session::get('user_id');
        Log::debug('storeChildProfile user_id from session: '.($validated['registered_by'] ?? 'null'));
        if (empty($validated['registered_by'])) {
            notify()->error('Your session has expired. Please log in again.');
            return back()->withInput();
        }

        try {
            Log::debug('storeChildProfile payload', $validated);
            $created = ChildProfile::create($validated);
            Log::info('storeChildProfile created id: '.($created->id ?? 'null'));
            if (!$created) {
                notify()->error('Child profile could not be created.');
                return back()->withInput();
            }
            notify()->success('Child profile created successfully.');
            return redirect()->route('admin.vaccination-records.child-profiles');
        } catch (\Throwable $e) {
            // Make sure we see the error during debugging
            if (config('app.debug')) {
                throw $e;
            }
            notify()->error('Error creating child profile.');
            return back()->withInput();
        }
    }
} 