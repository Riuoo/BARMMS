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

        // Clear existing medicine transactions
        MedicineTransaction::truncate();
        $this->command->info('Cleared existing medicine transactions');

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

        // Seed OUT transactions - one per resident only
        $residents = \App\Models\Residents::all();
        foreach ($residents as $resident) {
            // Get a random medicine request for this resident (if any exists)
            $residentRequest = $requests->where('resident_id', $resident->id)->first();
            
            if ($residentRequest) {
                // Use the existing request data
                MedicineTransaction::create([
                    'medicine_id' => $residentRequest->medicine_id,
                    'resident_id' => $resident->id,
                    'medical_record_id' => $residentRequest->medical_record_id,
                    'transaction_type' => 'OUT',
                    'quantity' => $residentRequest->quantity_approved ?? $residentRequest->quantity_requested,
                    'transaction_date' => $residentRequest->request_date,
                    'prescribed_by' => $residentRequest->approved_by,
                    'notes' => 'Dispensed for resident: ' . $resident->name,
                ]);
            } else {
                // If no request exists, create a transaction with random medicine
                $randomMedicine = $medicines->random();
                $randomPrescriber = $prescribers->random();
                $randomMedicalRecord = $medicalRecords->random();
                
                MedicineTransaction::create([
                    'medicine_id' => $randomMedicine->id,
                    'resident_id' => $resident->id,
                    'medical_record_id' => $randomMedicalRecord->id,
                    'transaction_type' => 'OUT',
                    'quantity' => rand(1, 10),
                    'transaction_date' => now()->subDays(rand(1, 30)),
                    'prescribed_by' => $randomPrescriber,
                    'notes' => 'Dispensed for resident: ' . $resident->name,
                ]);
            }
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

