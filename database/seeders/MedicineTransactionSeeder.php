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

        // Seed OUT transactions - create transactions for most requests (70-80% fulfillment rate)
        // This ensures accurate data where dispensed <= requested for most cases
        $processedRequests = [];
        
        foreach ($requests as $request) {
            // 75% chance to create a transaction for this request (fulfilled)
            if (rand(1, 100) <= 75) {
                // Dispensed quantity should be <= requested quantity
                // Sometimes dispense less than requested (partial fulfillment)
                $requestedQty = $request->quantity_requested ?? 0;
                $approvedQty = $request->quantity_approved ?? $requestedQty;
                
                // Skip if both requested and approved quantities are 0 or invalid
                if ($requestedQty <= 0 && $approvedQty <= 0) {
                    continue;
                }
                
                // Ensure we have a valid approved quantity (at least 1)
                if ($approvedQty <= 0) {
                    $approvedQty = max(1, $requestedQty);
                }
                
                // 80% chance to dispense full approved amount, 20% chance for partial
                if (rand(1, 100) <= 80) {
                    $dispensedQty = $approvedQty;
                } else {
                    // Partial fulfillment: dispense 50-90% of approved
                    $dispensedQty = max(1, (int)($approvedQty * (rand(50, 90) / 100)));
                }
                
                // Final safety check: ensure quantity is at least 1
                $dispensedQty = max(1, $dispensedQty);
                
                MedicineTransaction::create([
                    'medicine_id' => $request->medicine_id,
                    'resident_id' => $request->resident_id,
                    'medical_record_id' => $request->medical_record_id,
                    'transaction_type' => 'OUT',
                    'quantity' => $dispensedQty,
                    'transaction_date' => $request->request_date,
                    'prescribed_by' => $request->approved_by,
                    'notes' => 'Dispensed for resident request',
                ]);
                
                $processedRequests[] = $request->id;
            }
        }
        
        // Create some direct dispensations (transactions without requests) - 10-15% of residents
        $residents = \App\Models\Residents::all();
        $numDirectDispensations = (int)($residents->count() * 0.12); // 12% of residents
        
        for ($i = 0; $i < $numDirectDispensations; $i++) {
            $resident = $residents->random();
            $randomMedicine = $medicines->random();
            $randomPrescriber = $prescribers->random();
            $randomMedicalRecord = $medicalRecords->random();
            
            MedicineTransaction::create([
                'medicine_id' => $randomMedicine->id,
                'resident_id' => $resident->id,
                'medical_record_id' => $randomMedicalRecord->id,
                'transaction_type' => 'OUT',
                'quantity' => rand(1, 8),
                'transaction_date' => now()->subDays(rand(1, 30)),
                'prescribed_by' => $randomPrescriber,
                'notes' => 'Direct dispensation without prior request',
            ]);
        }
        
        $this->command->info('Created ' . count($processedRequests) . ' transactions from requests');
        $this->command->info('Created ' . $numDirectDispensations . ' direct dispensations');

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

