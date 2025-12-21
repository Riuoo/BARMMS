<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicineRequest;
use App\Models\Medicine;
use App\Models\MedicalRecord;
use App\Models\BarangayProfile;
use App\Models\Residents;
use Carbon\Carbon;

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
        $residents = Residents::all();

        if ($medicines->isEmpty() || $medicalRecords->isEmpty() || $approvers->isEmpty() || $residents->isEmpty()) {
            return;
        }

        // Clear existing medicine requests
        MedicineRequest::truncate();
        $this->command->info('Cleared existing medicine requests');

        // Define purok-specific medicine preferences for more realistic data
        $purokMedicinePreferences = [
            'Purok 1' => ['Biogesic', 'Amoxicillin', 'Cetirizine', 'ORS', 'Ascorbic Acid'],
            'Purok 2' => ['Losartan', 'Metformin', 'Biogesic', 'Cetirizine', 'Ascorbic Acid'],
            'Purok 3' => ['Amoxicillin', 'Biogesic', 'ORS', 'Cetirizine', 'Losartan'],
            'Purok 4' => ['Metformin', 'Losartan', 'Biogesic', 'Amoxicillin', 'Cetirizine'],
            'Purok 5' => ['Biogesic', 'Cetirizine', 'ORS', 'Ascorbic Acid', 'Amoxicillin'],
            'Purok 6' => ['Losartan', 'Metformin', 'Biogesic', 'Cetirizine', 'Ascorbic Acid'],
            'Purok 7' => ['Amoxicillin', 'Biogesic', 'ORS', 'Losartan', 'Metformin'],
        ];

        // Create medicine requests with purok-based patterns
        foreach ($residents as $resident) {
            // Extract purok from address
            $purok = $this->extractPurok($resident->address);
            
            // Get preferred medicines for this purok
            $preferredMedicines = $purokMedicinePreferences[$purok] ?? ['Biogesic', 'Amoxicillin', 'Cetirizine'];
            
            // Create 2-5 medicine requests per resident
            $numRequests = rand(2, 5);
            
            for ($i = 0; $i < $numRequests; $i++) {
                // 70% chance to use preferred medicine, 30% chance for random
                if (rand(1, 100) <= 70 && !empty($preferredMedicines)) {
                    $medicineName = $preferredMedicines[array_rand($preferredMedicines)];
                    $medicine = $medicines->where('name', $medicineName)->first();
                } else {
                    $medicine = $medicines->random();
                }
                
                if (!$medicine) continue;
                
                // Create request within last 30 days
                $requestDate = Carbon::now()->subDays(rand(0, 30));
                
                $requested = rand(1, 10);
                // Approved should be at least 1 if requested is valid, but can be less than requested
                // This simulates partial approval scenarios
                $approved = max(1, $requested - rand(0, min(2, $requested - 1)));
                
                MedicineRequest::create([
                    'medicine_id' => $medicine->id,
                    'resident_id' => $resident->id,
                    'medical_record_id' => $medicalRecords->random()->id,
                    'request_date' => $requestDate->toDateString(),
                    'quantity_requested' => $requested,
                    'quantity_approved' => $approved,
                    'approved_by' => $approvers->random(),
                    'notes' => 'Requested during consultation for home use',
                ]);
            }
        }

        // Create additional recent requests to ensure good data for the report
        $this->createRecentRequests($medicines, $residents, $approvers, $medicalRecords);
        
        // Show final summary
        $finalCount = MedicineRequest::count();
        $this->command->info("Medicine Request Seeder completed successfully!");
        $this->command->info("Total medicine requests created: {$finalCount}");
    }

    /**
     * Extract purok from address string
     */
    private function extractPurok(string $address): string
    {
        if (preg_match('/Purok\s*(\d+)/i', $address, $matches)) {
            return 'Purok ' . $matches[1];
        }
        return 'Other';
    }

    /**
     * Create additional recent requests to ensure good data for the report
     */
    private function createRecentRequests($medicines, $residents, $approvers, $medicalRecords)
    {
        // Create some high-frequency requests for specific medicines in specific puroks
        $highFrequencyPatterns = [
            ['purok' => 'Purok 1', 'medicine' => 'Biogesic', 'count' => 25],
            ['purok' => 'Purok 2', 'medicine' => 'Losartan', 'count' => 20],
            ['purok' => 'Purok 3', 'medicine' => 'Amoxicillin', 'count' => 22],
            ['purok' => 'Purok 4', 'medicine' => 'Metformin', 'count' => 18],
            ['purok' => 'Purok 5', 'medicine' => 'Cetirizine', 'count' => 15],
            ['purok' => 'Purok 6', 'medicine' => 'Losartan', 'count' => 16],
            ['purok' => 'Purok 7', 'medicine' => 'ORS', 'count' => 12],
        ];

        $this->command->info('Creating high-frequency medicine requests by purok...');

        foreach ($highFrequencyPatterns as $pattern) {
            $medicine = $medicines->where('name', $pattern['medicine'])->first();
            if (!$medicine) continue;

            $purokResidents = $residents->filter(function ($resident) use ($pattern) {
                return str_contains($resident->address, $pattern['purok']);
            });

            if ($purokResidents->isEmpty()) continue;

            $this->command->info("Creating {$pattern['count']} requests for {$pattern['medicine']} in {$pattern['purok']}");

            for ($i = 0; $i < $pattern['count']; $i++) {
                $resident = $purokResidents->random();
                $requestDate = Carbon::now()->subDays(rand(0, 30));
                
                $requested = rand(1, 8);
                // Approved can be same or slightly less than requested, but at least 1
                $approved = rand(1, $requested);
                
                MedicineRequest::create([
                    'medicine_id' => $medicine->id,
                    'resident_id' => $resident->id,
                    'medical_record_id' => $medicalRecords->random()->id,
                    'request_date' => $requestDate->toDateString(),
                    'quantity_requested' => $requested,
                    'quantity_approved' => $approved,
                    'approved_by' => $approvers->random(),
                    'notes' => 'High frequency request for ' . $pattern['purok'],
                ]);
            }
        }
    }

    /**
     * Run the seeder independently for testing
     */
    public static function runIndependently()
    {
        $seeder = new self();
        $seeder->run();
    }
}

