<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicalRecord;
use App\Models\Residents;
use App\Models\BarangayProfile;

class MedicalRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $residentIds = Residents::query()->inRandomOrder()->limit(8)->pluck('id');
        $healthWorkers = BarangayProfile::query()->whereIn('role', ['nurse', 'bhw'])->pluck('id');

        if ($residentIds->isEmpty() || $healthWorkers->isEmpty()) {
            return;
        }

        $records = [
            [
                'consultation_type' => 'General Check-up',
                'chief_complaint' => 'Intermittent headaches and fatigue',
                'symptoms' => 'Headache, mild dizziness, fatigue',
                'diagnosis' => 'Tension headache, rule out anemia',
                'prescribed_medications' => 'Paracetamol 500mg q6-8h PRN; Advise hydration and rest',
                'temperature' => 37.2,
                'blood_pressure_systolic' => 120,
                'blood_pressure_diastolic' => 80,
                'pulse_rate' => 78,
                'weight_kg' => 64.5,
                'height_cm' => 165.0,
                'notes' => 'Advised lifestyle modification. Return if symptoms persist.',
                'follow_up_date' => now()->addDays(14)->toDateString(),
            ],
            [
                'consultation_type' => 'Hypertension Follow-up',
                'chief_complaint' => 'Elevated BP readings at home',
                'symptoms' => 'Occasional palpitations, mild headache',
                'diagnosis' => 'Hypertension Stage 1',
                'prescribed_medications' => 'Losartan 50mg OD; Low-salt diet; Daily BP monitoring',
                'temperature' => 36.9,
                'blood_pressure_systolic' => 138,
                'blood_pressure_diastolic' => 88,
                'pulse_rate' => 82,
                'weight_kg' => 72.3,
                'height_cm' => 170.2,
                'notes' => 'Counseled on compliance; schedule lab tests (FBS, lipid profile).',
                'follow_up_date' => now()->addDays(30)->toDateString(),
            ],
            [
                'consultation_type' => 'Diabetes Management',
                'chief_complaint' => 'Increased thirst and urination',
                'symptoms' => 'Polydipsia, polyuria, fatigue',
                'diagnosis' => 'Type 2 Diabetes Mellitus',
                'prescribed_medications' => 'Metformin 500mg BID; Diet and exercise counseling',
                'temperature' => 36.8,
                'blood_pressure_systolic' => 126,
                'blood_pressure_diastolic' => 84,
                'pulse_rate' => 80,
                'weight_kg' => 78.0,
                'height_cm' => 168.0,
                'notes' => 'Refer to barangay nutritionist for meal plan.',
                'follow_up_date' => now()->addDays(30)->toDateString(),
            ],
            [
                'consultation_type' => 'Pediatric Consultation',
                'chief_complaint' => 'Fever and cough for 3 days',
                'symptoms' => 'Fever 38.2C, productive cough, runny nose',
                'diagnosis' => 'Acute upper respiratory infection',
                'prescribed_medications' => 'Paracetamol syrup; Cetirizine 2.5mg QHS PRN',
                'temperature' => 38.2,
                'blood_pressure_systolic' => 100,
                'blood_pressure_diastolic' => 65,
                'pulse_rate' => 96,
                'weight_kg' => 24.1,
                'height_cm' => 125.0,
                'notes' => 'Advise hydration and rest. Return if fever persists > 3 days.',
                'follow_up_date' => now()->addDays(7)->toDateString(),
            ],
            [
                'consultation_type' => 'Prenatal Check-up',
                'chief_complaint' => 'Routine prenatal visit at 24 weeks',
                'symptoms' => 'No alarming symptoms reported',
                'diagnosis' => 'Normal pregnancy, 24 weeks gestation',
                'prescribed_medications' => 'Prenatal vitamins; Iron supplements as needed',
                'temperature' => 36.7,
                'blood_pressure_systolic' => 118,
                'blood_pressure_diastolic' => 76,
                'pulse_rate' => 88,
                'weight_kg' => 62.8,
                'height_cm' => 160.0,
                'notes' => 'Fundal height appropriate. Fetal heart tones present.',
                'follow_up_date' => now()->addDays(28)->toDateString(),
            ],
            [
                'consultation_type' => 'Senior Wellness Check',
                'chief_complaint' => 'Annual senior wellness check',
                'symptoms' => 'Occasional joint pains',
                'diagnosis' => 'Osteoarthritis, mild',
                'prescribed_medications' => 'Paracetamol PRN; Joint exercises and warm compress',
                'temperature' => 36.6,
                'blood_pressure_systolic' => 130,
                'blood_pressure_diastolic' => 82,
                'pulse_rate' => 76,
                'weight_kg' => 58.4,
                'height_cm' => 158.0,
                'notes' => 'Encouraged regular low-impact exercise.',
                'follow_up_date' => now()->addDays(90)->toDateString(),
            ],
        ];

        $healthWorkerId = $healthWorkers->random();

        foreach ($residentIds as $index => $residentId) {
            $data = $records[$index % count($records)];
            MedicalRecord::create(array_merge($data, [
                'resident_id' => $residentId,
                'attending_health_worker_id' => $healthWorkerId,
                'consultation_datetime' => now()->subDays(rand(1, 40))->setTime(rand(8, 15), [0, 15, 30, 45][rand(0, 3)]),
            ]));
        }
    }
}

