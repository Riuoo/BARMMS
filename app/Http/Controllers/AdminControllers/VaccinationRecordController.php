<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\VaccinationRecord;
use App\Models\Residents;
use Illuminate\Http\Request;

class VaccinationRecordController
{
    public function index()
    {
        $vaccinationRecords = VaccinationRecord::with('resident')
            ->orderBy('vaccination_date', 'desc')
            ->paginate(15);
        return view('admin.vaccination-records.index', compact('vaccinationRecords'));
    }

    public function create()
    {
        $residents = Residents::all();
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

    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $vaccinationRecords = VaccinationRecord::with('resident')
            ->whereHas('resident', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orWhere('vaccine_name', 'like', "%{$query}%")
            ->orWhere('vaccine_type', 'like', "%{$query}%")
            ->orderBy('vaccination_date', 'desc')
            ->paginate(15);

        return view('admin.vaccination-records.index', compact('vaccinationRecords', 'query'));
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