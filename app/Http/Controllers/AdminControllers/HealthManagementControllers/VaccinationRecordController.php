<?php

namespace App\Http\Controllers\AdminControllers\HealthManagementControllers;

use App\Models\VaccinationRecord;
use App\Models\VaccinationSchedule;
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
                $q->where('name', 'like', "%{$search}%");
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
                    $subQ->where('name', 'like', "%{$search}%");
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

    public function createChild()
    {
        $residents = Residents::where('active', true)->get();
        $ageGroup = 'child';
        $schedules = VaccinationSchedule::active()->byAgeGroup('Child')->get();
        $childProfiles = ChildProfile::where('is_active', true)->orderBy('first_name')->get();
        return view('admin.vaccination-records.create-child', compact('residents', 'ageGroup', 'schedules', 'childProfiles'));
    }

    public function createInfant()
    {
        $residents = Residents::where('active', true)->get();
        $ageGroup = 'infant';
        $schedules = VaccinationSchedule::active()->byAgeGroup('Infant')->get();
        $childProfiles = ChildProfile::where('is_active', true)->orderBy('first_name')->get();
        return view('admin.vaccination-records.create-infant', compact('residents', 'ageGroup', 'schedules', 'childProfiles'));
    }

    public function createToddler()
    {
        $residents = Residents::where('active', true)->get();
        $ageGroup = 'toddler';
        $schedules = VaccinationSchedule::active()->byAgeGroup('Toddler')->get();
        $childProfiles = ChildProfile::where('is_active', true)->orderBy('first_name')->get();
        return view('admin.vaccination-records.create-toddler', compact('residents', 'ageGroup', 'schedules', 'childProfiles'));
    }

    public function createAdolescent()
    {
        $residents = Residents::where('active', true)->get();
        $ageGroup = 'adolescent';
        $schedules = VaccinationSchedule::active()->byAgeGroup('Adolescent')->get();
        $childProfiles = ChildProfile::where('is_active', true)->orderBy('first_name')->get();
        return view('admin.vaccination-records.create-adolescent', compact('residents', 'ageGroup', 'schedules', 'childProfiles'));
    }

    public function createAdult()
    {
        $residents = Residents::where('active', true)->get();
        $ageGroup = 'adult';
        $schedules = VaccinationSchedule::active()->byAgeGroup('Adult')->get();
        return view('admin.vaccination-records.create-adult', compact('residents', 'ageGroup', 'schedules'));
    }

    public function createElderly()
    {
        $residents = Residents::where('active', true)->get();
        $ageGroup = 'elderly';
        $schedules = VaccinationSchedule::active()->byAgeGroup('Elderly')->get();
        return view('admin.vaccination-records.create-elderly', compact('residents', 'ageGroup', 'schedules'));
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
            'batch_number' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:255',
            'dose_number' => 'required|integer|min:1',
            'total_doses_required' => 'nullable|integer|min:1',
            'next_dose_date' => 'nullable|date|after:vaccination_date',
            'administered_by' => 'nullable|string|max:255',
            'age_group' => 'nullable|string|in:Infant,Toddler,Child,Adolescent,Adult,Elderly',
            'age_at_vaccination' => 'nullable|integer|min:0',
            'is_booster' => 'boolean',
            'is_annual' => 'boolean',
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
                    $q->where('name', 'like', "%{$search}%")
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

        // Defaults
        if (empty($validated['total_doses_required'])) {
            $validated['total_doses_required'] = 1;
        }

        // Set administered_by from session if not provided
        if (empty($validated['administered_by'])) {
            // Prefer the session user id for auditing
            $sessionUserId = Session::get('user_id');
            if ($sessionUserId) {
                $validated['administered_by'] = (string) $sessionUserId;
            } else {
                $validated['administered_by'] = session('user_name') ?: 'Nurse';
            }
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
        $vaccinationRecord = VaccinationRecord::with('resident')->findOrFail($id);
        return view('admin.vaccination-records.show', compact('vaccinationRecord'));
    }

    public function edit($id)
    {
        $vaccinationRecord = VaccinationRecord::with('resident')->findOrFail($id);
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
            'batch_number' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:255',
            'dose_number' => 'required|integer|min:1',
            'next_dose_date' => 'nullable|date|after:vaccination_date',
            'administered_by' => 'nullable|string|max:255',
            'side_effects' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
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
        $query = VaccinationRecord::with(['resident', 'childProfile'])
            ->whereNotNull('next_dose_date')
            ->where('next_dose_date', '<=', now()->addDays(30));

        // Apply status filter
        if ($request->filled('status')) {
            switch($request->get('status')) {
                case 'overdue':
                    $query->where('next_dose_date', '<', now());
                    break;
                case 'due_this_week':
                    $query->where('next_dose_date', '>=', now())
                          ->where('next_dose_date', '<=', now()->addDays(7));
                    break;
                case 'due_soon':
                    $query->where('next_dose_date', '>', now()->addDays(7))
                          ->where('next_dose_date', '<=', now()->addDays(30));
                    break;
            }
        }

        $dueVaccinations = $query->orderBy('next_dose_date', 'asc')
            ->paginate(15)
            ->withQueryString();

        // Get additional statistics for the due vaccinations
        $stats = [
            'overdue' => $dueVaccinations->where('next_dose_date', '<', now())->count(),
            'due_this_week' => $dueVaccinations->where('next_dose_date', '>=', now())->where('next_dose_date', '<=', now()->addDays(7))->count(),
            'due_soon' => $dueVaccinations->where('next_dose_date', '>', now()->addDays(7))->where('next_dose_date', '<=', now()->addDays(30))->count(),
            'total_due' => $dueVaccinations->total(),
        ];

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
        $ageGroup = $request->get('age_group');
        $ageInMonths = $request->get('age_months');
        $ageInYears = $request->get('age_years');

        $schedules = VaccinationSchedule::active()
            ->byAgeGroup($ageGroup)
            ->get()
            ->filter(function($schedule) use ($ageInMonths, $ageInYears) {
                return $schedule->isAgeAppropriate($ageInMonths, $ageInYears);
            });

        return response()->json($schedules);
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