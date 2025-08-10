<?php

namespace App\Http\Controllers\AdminControllers\HealthManagementControllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use App\Models\Residents;
use Illuminate\Support\Facades\Session;

class MedicalRecordController
{
    public function index(Request $request)
    {
        $query = MedicalRecord::with(['resident', 'attendingHealthWorker']);

        // SEARCH - Patient name, email, complaint, or diagnosis
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('resident', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('chief_complaint', 'like', "%{$search}%")
              ->orWhere('diagnosis', 'like', "%{$search}%");
        }

        // Calculate Statistics
        $stats = [
            'total' => MedicalRecord::count(),
            'last_month' => MedicalRecord::where('consultation_datetime', '>=', now()->subDays(30))->count()
        ];
        $medicalRecords = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.medical-records.index', compact('medicalRecords', 'stats'));
    }


    public function create()
    {
        $residents = Residents::where('active', true)->get();
        return view('admin.medical-records.create', compact('residents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'consultation_datetime' => 'required|date|before_or_equal:now',
            'consultation_type' => 'required|string|max:100',
            'consultation_type_other' => 'nullable|string|max:100',
            'chief_complaint' => 'nullable|string|max:1000',
            'symptoms' => 'nullable|string|max:1000',
            'diagnosis' => 'nullable|string|max:1000',
            'prescribed_medications' => 'nullable|string|max:1000',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'blood_pressure_systolic' => 'nullable|integer|min:50|max:300',
            'blood_pressure_diastolic' => 'nullable|integer|min:30|max:200',
            'pulse_rate' => 'nullable|integer|min:40|max:200',
            'weight_kg' => 'nullable|numeric|min:1|max:500',
            'height_cm' => 'nullable|numeric|min:50|max:300',
            'notes' => 'nullable|string|max:2000',
            'follow_up_date' => 'nullable|date|after:consultation_datetime',
        ]);

        // Handle "Other" consultation type
        if ($validated['consultation_type'] === 'Other') {
            if (empty($request->input('consultation_type_other'))) {
                notify()->error('Please specify the consultation type when selecting "Other".');
                return back()->withInput();
            }
            $validated['consultation_type'] = $request->input('consultation_type_other');
        }

        // Remove the consultation_type_other field as it's not needed in the database
        unset($validated['consultation_type_other']);

        $user = Residents::find($validated['resident_id']);
        if (!$user || !$user->active) {
            notify()->error('This user account is inactive and cannot make transactions.');
            return back()->withInput();
        }
        try {
            // Require a session-authenticated admin and set attending health worker foreign key
            $workerId = Session::get('user_id');
            if (!$workerId) {
                notify()->error('You must be logged in to create medical consultation records.');
                return back()->withInput();
            }
            $validated['attending_health_worker_id'] = $workerId;
            
            MedicalRecord::create($validated);
            notify()->success('Medical consultation record created successfully.');
            return redirect()->route('admin.medical-records.index');
        } catch (\Exception $e) {
            notify()->error('Error creating medical consultation record: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function show($id)
    {
        $medicalRecord = MedicalRecord::with([
            'resident', 
            'attendingHealthWorker', 
            'medicineRequests.medicine',
            'medicineRequests.approvedByUser'
        ])->findOrFail($id);
        return view('admin.medical-records.show', compact('medicalRecord'));
    }

    public function destroy($id)
    {
        try {
            $medicalRecord = MedicalRecord::findOrFail($id);
            $medicalRecord->delete();
            
            notify()->success('Medical consultation record deleted successfully.');
            return redirect()->route('admin.medical-records.index');
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

        $query = MedicalRecord::with('resident')
            ->whereBetween('consultation_datetime', [$startDate, $endDate]);

        if ($consultationType) {
            $query->where('consultation_type', $consultationType);
        }

        $medicalRecords = $query->orderBy('consultation_datetime', 'desc')->get();

        $summary = [
            'total_consultations' => $medicalRecords->count(),
            'by_type' => $medicalRecords->groupBy('consultation_type')->map->count(),
            'by_month' => $medicalRecords->groupBy(function($record) {
                return $record->consultation_datetime->format('Y-m');
            })->map->count(),
        ];

        return view('admin.medical-records.report', compact('medicalRecords', 'summary', 'startDate', 'endDate', 'consultationType'));
    }
} 