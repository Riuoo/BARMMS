<?php

namespace App\Http\Controllers\AdminControllers\HealthManagementControllers;

use App\Models\VaccinationRecord;
use Illuminate\Http\Request;
use App\Models\Residents;

class VaccinationRecordController
{
    public function index(Request $request)
    {
        $query = VaccinationRecord::with('resident');

        // SEARCH - Patient name or vaccine details
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('resident', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
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

            $stats = [
            'total' => VaccinationRecord::count(),
            'due_soon' => VaccinationRecord::whereNotNull('next_dose_date')
                        ->whereBetween('next_dose_date', [now(), now()->addDays(30)])
                        ->count(),
            'overdue' => VaccinationRecord::whereNotNull('next_dose_date')
                        ->where('next_dose_date', '<', now())
                        ->count(),
            'completed' => VaccinationRecord::whereNull('next_dose_date')->count(),
            'last_month' => VaccinationRecord::where('vaccination_date', '>=', now()->subDays(30))->count()
        ];

        $vaccinationRecords = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.vaccination-records.index', compact('vaccinationRecords', 'stats'));
    
    }

    public function search(Request $request)
    {
        $vaccinationRecords = VaccinationRecord::with('resident')
            ->when($request->filled('search'), function($q) use ($request) {
                $search = $request->get('search');
                $q->whereHas('resident', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
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
            ->paginate(15);

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

    public function store(Request $request)
    {
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
        $user = Residents::find($validated['resident_id']);
        if (!$user || !$user->active) {
            notify()->error('This user account is inactive and cannot make transactions.');
            return back()->withInput();
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

    public function dueVaccinations()
    {
        $dueVaccinations = VaccinationRecord::with('resident')
            ->whereNotNull('next_dose_date')
            ->where('next_dose_date', '<=', now()->addDays(30))
            ->orderBy('next_dose_date', 'asc')
            ->get();

        return view('admin.vaccination-records.due', compact('dueVaccinations'));
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
} 