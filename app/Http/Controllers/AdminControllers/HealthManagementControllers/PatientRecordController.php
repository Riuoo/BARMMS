<?php

namespace App\Http\Controllers\AdminControllers\HealthManagementControllers;

use App\Models\PatientRecord;
use App\Models\Residents;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PatientRecordController
{
    public function index(Request $request)
    {
        $query = PatientRecord::with('resident');
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('resident', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('patient_number', 'like', "%{$search}%");
        }
        
        // Apply risk level filter
        if ($request->filled('risk_level')) {
            $query->where('risk_level', $request->get('risk_level'));
        }
        
        // Apply blood type filter
        if ($request->filled('blood_type')) {
            $query->where('blood_type', $request->get('blood_type'));
        }
        
        $patientRecords = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Calculate statistics
        $totalRecords = PatientRecord::count();
        $highRiskCount = PatientRecord::where('risk_level', 'high')->count();
        $withBloodTypeCount = PatientRecord::whereNotNull('blood_type')->count();
        $recentRecordsCount = PatientRecord::where('created_at', '>=', now()->subDays(30))->count();
        
        return view('admin.patient-records.index', compact(
            'patientRecords',
            'totalRecords',
            'highRiskCount',
            'withBloodTypeCount',
            'recentRecordsCount'
        ));
    }

    public function create()
    {
        $residents = Residents::where('active', true)->orderBy('name')->get();
        return view('admin.patient-records.create', compact('residents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id|unique:patient_records,resident_id',
            // 'patient_number' => 'required|string|max:50|unique:patient_records,patient_number', // removed, will be auto-generated
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string|max:1000',
            'medical_history' => 'nullable|string|max:2000',
            'family_medical_history' => 'nullable|string|max:2000',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'blood_pressure_status' => 'nullable|string|in:Normal,Elevated,Stage 1 Hypertension,Stage 2 Hypertension',
            'height_cm' => 'nullable|numeric|min:50|max:300',
            'weight_kg' => 'nullable|numeric|min:1|max:500',
            'current_medications' => 'nullable|string|max:1000',
            'lifestyle_factors' => 'nullable|string|max:1000',
            'risk_level' => 'nullable|string|in:low,medium,high',
            'notes' => 'nullable|string|max:2000',
        ]);
        $user = \App\Models\Residents::find($validated['resident_id']);
        if (!$user || !$user->active) {
            notify()->error('This user account is inactive and cannot make transactions.');
            return back()->withInput();
        }
        try {
            // Auto-generate patient_number: P-YYYY-XXXX
            $year = date('Y');
            $lastRecord = PatientRecord::whereYear('created_at', $year)
                ->orderBy('id', 'desc')
                ->first();
            if ($lastRecord && preg_match('/P-' . $year . '-(\\d{4})/', $lastRecord->patient_number, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            } else {
                $nextNumber = 1;
            }
            $patient_number = 'P-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            // Ensure uniqueness
            while (PatientRecord::where('patient_number', $patient_number)->exists()) {
                $nextNumber++;
                $patient_number = 'P-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }

            $patientRecord = PatientRecord::create([
                'resident_id' => $validated['resident_id'],
                'patient_number' => $patient_number,
                'blood_type' => $validated['blood_type'],
                'allergies' => $validated['allergies'],
                'medical_history' => $validated['medical_history'],
                'family_medical_history' => $validated['family_medical_history'],
                'emergency_contact_name' => $validated['emergency_contact_name'],
                'emergency_contact_number' => $validated['emergency_contact_number'],
                'emergency_contact_relationship' => $validated['emergency_contact_relationship'],
                'blood_pressure_status' => $validated['blood_pressure_status'],
                'height_cm' => $validated['height_cm'],
                'weight_kg' => $validated['weight_kg'],
                'current_medications' => $validated['current_medications'],
                'lifestyle_factors' => $validated['lifestyle_factors'],
                'risk_level' => $validated['risk_level'],
                'notes' => $validated['notes'],
            ]);

            // Calculate BMI if height and weight are provided
            if ($patientRecord->height_cm && $patientRecord->weight_kg) {
                $patientRecord->calculateBMI();
            }

            notify()->success('Patient record created successfully.');
            return redirect()->route('admin.patient-records.index');
        } catch (\Exception $e) {
            notify()->error('Error creating patient record: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function show($id)
    {
        $patientRecord = PatientRecord::with(['resident', 'medicalLogbooks', 'vaccinationRecords'])->findOrFail($id);
        return view('admin.patient-records.show', compact('patientRecord'));
    }

    public function edit($id)
    {
        $patientRecord = PatientRecord::with('resident')->findOrFail($id);
        $residents = Residents::orderBy('name')->get();
        return view('admin.patient-records.edit', compact('patientRecord', 'residents'));
    }

    public function update(Request $request, $id)
    {
        $patientRecord = PatientRecord::findOrFail($id);
        $user = $patientRecord->resident;
        if (!$user || !$user->active) {
            notify()->error('This user account is inactive and cannot make transactions.');
            return back()->withInput();
        }
        
        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'patient_number' => 'required|string|max:50|unique:patient_records,patient_number,' . $id,
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string|max:1000',
            'medical_history' => 'nullable|string|max:2000',
            'family_medical_history' => 'nullable|string|max:2000',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'blood_pressure_status' => 'nullable|string|in:Normal,Elevated,Stage 1 Hypertension,Stage 2 Hypertension',
            'height_cm' => 'nullable|numeric|min:50|max:300',
            'weight_kg' => 'nullable|numeric|min:1|max:500',
            'current_medications' => 'nullable|string|max:1000',
            'lifestyle_factors' => 'nullable|string|max:1000',
            'risk_level' => 'nullable|string|in:low,medium,high',
            'notes' => 'nullable|string|max:2000',
        ]);

        try {
            $patientRecord->update($validated);

            // Recalculate BMI if height or weight changed
            if ($patientRecord->height_cm && $patientRecord->weight_kg) {
                $patientRecord->calculateBMI();
            }

            notify()->success('Patient record updated successfully.');
            return redirect()->route('admin.patient-records.index');
        } catch (\Exception $e) {
            notify()->error('Error updating patient record: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $patientRecord = PatientRecord::findOrFail($id);
            $patientRecord->delete();
            
            notify()->success('Patient record deleted successfully.');
            return redirect()->route('admin.patient-records.index');
        } catch (\Exception $e) {
            notify()->error('Error deleting patient record: ' . $e->getMessage());
            return back();
        }
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $patientRecords = PatientRecord::with('resident')
            ->whereHas('resident', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->orWhere('patient_number', 'like', "%{$query}%")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculate statistics
        $totalRecords = PatientRecord::count();
        $highRiskCount = PatientRecord::where('risk_level', 'high')->count();
        $withBloodTypeCount = PatientRecord::whereNotNull('blood_type')->count();
        $recentRecordsCount = PatientRecord::where('created_at', '>=', now()->subDays(30))->count();

        return view('admin.patient-records.index', compact(
            'patientRecords', 
            'query',
            'totalRecords',
            'highRiskCount',
            'withBloodTypeCount',
            'recentRecordsCount'
        ));
    }
} 