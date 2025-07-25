<?php

namespace App\Http\Controllers\AdminControllers\HealthManagementControllers;

use App\Models\MedicalLogbook;
use Illuminate\Http\Request;
use App\Models\Residents;

class MedicalLogbookController
{
    public function index(Request $request)
    {
        $query = MedicalLogbook::with('resident');

        // SEARCH - Patient name, email, complaint, or diagnosis
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('resident', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('chief_complaint', 'like', "%{$search}%")
              ->orWhere('diagnosis', 'like', "%{$search}%");
        }

        // STATUS FILTER
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Calculate Statistics
        $stats = [
            'total' => MedicalLogbook::count(),
            'completed' => MedicalLogbook::where('status', 'Completed')->count(),
            'pending' => MedicalLogbook::where('status', 'Pending')->count(),
            'referred' => MedicalLogbook::where('status', 'Referred')->count(),
            'last_month' => MedicalLogbook::where('consultation_date', '>=', now()->subDays(30))->count()
        ];
        $medicalLogbooks = $query->paginate(15);
        return view('admin.medical-logbooks.index', compact('medicalLogbooks', 'stats'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $status = $request->input('status');

        $medicalLogbooks = MedicalLogbook::with('resident')
            ->when($query, function($q) use ($query) {
                $q->whereHas('resident', function($subQ) use ($query) {
                    $subQ->where('name', 'like', "%{$query}%");
                })
                ->orWhere('chief_complaint', 'like', "%{$query}%")
                ->orWhere('diagnosis', 'like', "%{$query}%");
            })
            ->when($status, function($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderBy('consultation_date', 'desc')
            ->paginate(15);

        return view('admin.medical-logbooks.index', [
            'medicalLogbooks' => $medicalLogbooks,
            'search' => $query,
            'status' => $status
        ]);
    }

    public function create()
    {
        $residents = Residents::where('active', true)->get();
        return view('admin.medical-logbooks.create', compact('residents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'consultation_date' => 'required|date|before_or_equal:today',
            'consultation_time' => 'required|date_format:H:i',
            'consultation_type' => 'required|string|max:100',
            'chief_complaint' => 'required|string|max:1000',
            'symptoms' => 'required|string|max:1000',
            'diagnosis' => 'nullable|string|max:1000',
            'treatment_plan' => 'required|string|max:1000',
            'prescribed_medications' => 'nullable|string|max:1000',
            'lab_tests_ordered' => 'nullable|string|max:1000',
            'lab_results' => 'nullable|string|max:1000',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'blood_pressure_systolic' => 'nullable|integer|min:50|max:300',
            'blood_pressure_diastolic' => 'nullable|integer|min:30|max:200',
            'pulse_rate' => 'nullable|integer|min:40|max:200',
            'weight_kg' => 'nullable|numeric|min:1|max:500',
            'height_cm' => 'nullable|numeric|min:50|max:300',
            'physical_examination' => 'required|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'attending_health_worker' => 'required|string|max:255',
            'follow_up_date' => 'nullable|date|after:consultation_date',
            'status' => 'required|string|in:Completed,Pending,Referred,Cancelled',
        ]);
        $user = Residents::find($validated['resident_id']);
        if (!$user || !$user->active) {
            notify()->error('This user account is inactive and cannot make transactions.');
            return back()->withInput();
        }
        try {
            MedicalLogbook::create($validated);
            notify()->success('Medical consultation record created successfully.');
            return redirect()->route('admin.medical-logbooks.index');
        } catch (\Exception $e) {
            notify()->error('Error creating medical consultation record: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function show($id)
    {
        $medicalLogbook = MedicalLogbook::with('resident')->findOrFail($id);
        return view('admin.medical-logbooks.show', compact('medicalLogbook'));
    }

    public function edit($id)
    {
        $medicalLogbook = MedicalLogbook::with('resident')->findOrFail($id);
        $residents = Residents::all();
        return view('admin.medical-logbooks.edit', compact('medicalLogbook', 'residents'));
    }

    public function update(Request $request, $id)
    {
        $medicalLogbook = MedicalLogbook::findOrFail($id);
        $user = $medicalLogbook->resident;
        if (!$user || !$user->active) {
            notify()->error('This user account is inactive and cannot make transactions.');
            return back()->withInput();
        }
        
        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'consultation_date' => 'required|date|before_or_equal:today',
            'consultation_time' => 'required|date_format:H:i',
            'consultation_type' => 'required|string|max:100',
            'chief_complaint' => 'required|string|max:1000',
            'symptoms' => 'required|string|max:1000',
            'diagnosis' => 'nullable|string|max:1000',
            'treatment_plan' => 'required|string|max:1000',
            'prescribed_medications' => 'nullable|string|max:1000',
            'lab_tests_ordered' => 'nullable|string|max:1000',
            'lab_results' => 'nullable|string|max:1000',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'blood_pressure_systolic' => 'nullable|integer|min:50|max:300',
            'blood_pressure_diastolic' => 'nullable|integer|min:30|max:200',
            'pulse_rate' => 'nullable|integer|min:40|max:200',
            'weight_kg' => 'nullable|numeric|min:1|max:500',
            'height_cm' => 'nullable|numeric|min:50|max:300',
            'physical_examination' => 'required|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'attending_health_worker' => 'required|string|max:255',
            'follow_up_date' => 'nullable|date|after:consultation_date',
            'status' => 'required|string|in:Completed,Pending,Referred,Cancelled',
        ]);

        try {
            $medicalLogbook->update($validated);
            notify()->success('Medical consultation record updated successfully.');
            return redirect()->route('admin.medical-logbooks.index');
        } catch (\Exception $e) {
            notify()->error('Error updating medical consultation record: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $medicalLogbook = MedicalLogbook::findOrFail($id);
            $medicalLogbook->delete();
            
            notify()->success('Medical consultation record deleted successfully.');
            return redirect()->route('admin.medical-logbooks.index');
        } catch (\Exception $e) {
            notify()->error('Error deleting medical consultation record: ' . $e->getMessage());
            return back();
        }
    }

    public function generateReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        $consultationType = $request->get('consultation_type');

        $query = MedicalLogbook::with('resident')
            ->whereBetween('consultation_date', [$startDate, $endDate]);

        if ($consultationType) {
            $query->where('consultation_type', $consultationType);
        }

        $medicalLogbooks = $query->orderBy('consultation_date', 'desc')->get();

        $summary = [
            'total_consultations' => $medicalLogbooks->count(),
            'by_type' => $medicalLogbooks->groupBy('consultation_type')->map->count(),
            'by_status' => $medicalLogbooks->groupBy('status')->map->count(),
            'by_month' => $medicalLogbooks->groupBy(function($record) {
                return $record->consultation_date->format('Y-m');
            })->map->count(),
        ];

        return view('admin.medical-logbooks.report', compact('medicalLogbooks', 'summary', 'startDate', 'endDate', 'consultationType'));
    }
} 