<?php

namespace App\Http\Controllers\AdminControllers\HealthManagementControllers;

use App\Models\Residents;
use App\Models\MedicalRecord;
use App\Models\MedicineRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PatientHealthProfileController
{
    public function search(Request $request)
    {
        $term = trim((string) ($request->get('q') ?? $request->get('term') ?? ''));
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $results = Residents::query()
            ->where('active', true)
            ->where(function ($q) use ($term) {
                $q->whereRaw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(middle_name,''), ' ', COALESCE(last_name,''), ' ', COALESCE(suffix,'')) LIKE ?", ["%{$term}%"])
                  ->orWhere('first_name', 'like', "%{$term}%")
                  ->orWhere('middle_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('address', 'like', "%{$term}%");
            })
            ->orderByRaw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(middle_name,''), ' ', COALESCE(last_name,''), ' ', COALESCE(suffix,''))")
            ->limit(10)
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'suffix', 'purok', 'birth_date', 'email'])
            ->map(function ($resident) {
                $parts = array_filter([
                    $resident->first_name,
                    $resident->middle_name,
                    $resident->last_name,
                    $resident->suffix
                ], function ($part) {
                    return !empty(trim($part ?? ''));
                });
                $fullName = implode(' ', $parts) ?: 'N/A';
                $age = null;
                if (!empty($resident->birth_date)) {
                    try {
                        $age = Carbon::parse($resident->birth_date)->age;
                    } catch (\Exception $e) {
                        $age = null;
                    }
                }
                return [
                    'id' => $resident->id,
                    'name' => $fullName,
                    'purok' => $resident->purok,
                    'age' => $age,
                    'email' => $resident->email,
                ];
            })
            ->values();

        return response()->json($results);
    }

    public function show(int $residentId)
    {
        $resident = Residents::findOrFail($residentId);
        $patientName = trim(collect([
            $resident->first_name ?? '',
            $resident->middle_name ?? '',
            $resident->last_name ?? '',
            $resident->suffix ?? '',
        ])->filter()->implode(' '));

        // Core collections for the profile
        $medicalRecords = MedicalRecord::with(['attendingHealthWorker'])
            ->where('resident_id', $residentId)
            ->orderByDesc('consultation_datetime')
            ->limit(15)
            ->get();


        $medicineRequests = MedicineRequest::with(['medicine', 'approvedByUser'])
            ->where('resident_id', $residentId)
            ->orderByDesc('request_date')
            ->limit(15)
            ->get();

        // Build a simple unified timeline
        $timeline = collect()
            ->merge($medicalRecords->map(function ($record) {
                return [
                    'type' => 'consultation',
                    'date' => $record->consultation_datetime,
                    'title' => $record->consultation_type ?? 'Consultation',
                    'details' => $record->complaint ?? 'Consultation recorded',
                    'link' => route('admin.medical-records.show', $record->id),
                ];
            }))
            ->merge($medicineRequests->map(function ($request) {
                $qty = $request->quantity_requested ?? $request->quantity_approved ?? $request->quantity ?? 'N/A';
                return [
                    'type' => 'medicine_request',
                    'date' => $request->request_date ?? $request->created_at,
                    'title' => optional($request->medicine)->name ?? 'Medicine Request',
                    'details' => 'Quantity: ' . $qty,
                    'link' => null,
                ];
            }))
            ->filter(fn ($item) => !empty($item['date']))
            ->sortByDesc('date')
            ->values();

        $stats = [
            'total_consultations' => $medicalRecords->count(),
            'total_requests' => $medicineRequests->count(),
        ];

        return view('admin.health.patient-profile', compact(
            'resident',
            'patientName',
            'medicalRecords',
            'medicineRequests',
            'timeline',
            'stats'
        ));
    }
}

