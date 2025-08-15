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
        $residentIds = Residents::query()->inRandomOrder()->limit(1)->pluck('id');
        $healthWorkers = BarangayProfile::query()->whereIn('role', ['nurse'])->pluck('id');

        if ($residentIds->isEmpty() || $healthWorkers->isEmpty()) {
            return;
        }

        $records = [
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
            ]
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

