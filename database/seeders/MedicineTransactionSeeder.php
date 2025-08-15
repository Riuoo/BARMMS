<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicineTransaction;
use App\Models\Medicine;
use App\Models\MedicalRecord;
use App\Models\BarangayProfile;

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

        // Seed OUT transactions linked to medical records (dispensing)
        foreach ($medicalRecords as $record) {
            $medicine = $medicines->random();
            MedicineTransaction::create([
                'medicine_id' => $medicine->id,
                'resident_id' => $record->resident_id,
                'medical_record_id' => $record->id,
                'transaction_type' => 'OUT',
                'quantity' => rand(1, 10),
                'transaction_date' => $record->consultation_datetime,
                'prescribed_by' => $prescribers->random(),
                'notes' => 'Dispensed as prescribed during consultation',
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

