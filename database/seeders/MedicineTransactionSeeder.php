<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicineTransaction;
use App\Models\Medicine;
use App\Models\MedicalRecord;
use App\Models\BarangayProfile;
use App\Models\MedicineRequest;

class MedicineTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = Medicine::all();
        $medicalRecords = MedicalRecord::all();
        $prescribers = BarangayProfile::query()->whereIn('role', ['nurse'])->pluck('id');
        $requests = MedicineRequest::all();

        if ($medicines->isEmpty() || $medicalRecords->isEmpty() || $prescribers->isEmpty()) {
            return;
        }

        // Seed IN transactions (restocking)
        foreach ($medicines as $medicine) {
            MedicineTransaction::create([
                'medicine_id' => $medicine->id,
                'transaction_type' => 'IN',
                'quantity' => rand(50, 300),
                'transaction_date' => now()->subDays(rand(20, 60)),
                'prescribed_by' => null,
                'notes' => 'Restocking from supplier',
            ]);
        }

        // Seed OUT transactions based on medicine requests
        foreach ($requests as $request) {
            MedicineTransaction::create([
                'medicine_id' => $request->medicine_id,
                'resident_id' => $request->resident_id,
                'medical_record_id' => $request->medical_record_id,
                'transaction_type' => 'OUT',
                'quantity' => $request->quantity_approved ?? $request->quantity_requested,
                'transaction_date' => $request->request_date,
                'prescribed_by' => $request->approved_by,
                'notes' => 'Dispensed for request ID: ' . $request->id,
            ]);
        }

        // Seed EXPIRED adjustments for some medicines
        foreach ($medicines->take(2) as $expiredMedicine) {
            MedicineTransaction::create([
                'medicine_id' => $expiredMedicine->id,
                'transaction_type' => 'EXPIRED',
                'quantity' => rand(5, 20),
                'transaction_date' => now()->subDays(rand(1, 10)),
                'prescribed_by' => null,
                'notes' => 'Removed from inventory due to expiry',
            ]);
        }
    }
}

