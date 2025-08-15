<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicineRequest;
use App\Models\Medicine;
use App\Models\MedicalRecord;
use App\Models\BarangayProfile;

class MedicineRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = Medicine::all();
        $medicalRecords = MedicalRecord::all();
        $approvers = BarangayProfile::query()->whereIn('role', ['nurse'])->pluck('id');

        if ($medicines->isEmpty() || $medicalRecords->isEmpty() || $approvers->isEmpty()) {
            return;
        }

        foreach ($medicalRecords as $record) {
            $medicine = $medicines->random();
            $requested = rand(1, 10);
            MedicineRequest::create([
                'medicine_id' => $medicine->id,
                'resident_id' => $record->resident_id,
                'medical_record_id' => $record->id,
                'request_date' => $record->consultation_datetime->toDateString(),
                'quantity_requested' => $requested,
                'quantity_approved' => max(0, $requested - rand(0, 2)),
                'approved_by' => $approvers->random(),
                'notes' => 'Requested during consultation for home use',
            ]);
        }
    }
}

