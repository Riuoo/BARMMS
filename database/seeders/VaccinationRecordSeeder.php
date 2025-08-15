<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VaccinationRecord;
use App\Models\Residents;
use App\Models\ChildProfile;
use App\Models\BarangayProfile;

class VaccinationRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $administeredById = BarangayProfile::query()
            ->whereIn('role', ['nurse'])
            ->inRandomOrder()
            ->value('id');

        if (!$administeredById) {
            return;
        }

        $residentIds = Residents::query()->inRandomOrder()->limit(1)->pluck('id');
        $childIds = ChildProfile::query()->inRandomOrder()->limit(1)->pluck('id');

        $adultVaccines = [
            ['vaccine_name' => 'Pfizer-BioNTech', 'vaccine_type' => 'COVID-19'],
            ['vaccine_name' => 'Moderna', 'vaccine_type' => 'COVID-19'],
            ['vaccine_name' => 'Influvac Tetra', 'vaccine_type' => 'Influenza'],
            ['vaccine_name' => 'Pneumovax 23', 'vaccine_type' => 'Pneumonia'],
            ['vaccine_name' => 'Tetanus-Diphtheria', 'vaccine_type' => 'Tetanus'],
            ['vaccine_name' => 'Engerix-B', 'vaccine_type' => 'Hepatitis B'],
        ];

        $childVaccines = [
            ['vaccine_name' => 'Pentavalent (DTaP-HepB-Hib)', 'vaccine_type' => 'DTaP'],
            ['vaccine_name' => 'MMR II', 'vaccine_type' => 'MMR'],
            ['vaccine_name' => 'Varivax', 'vaccine_type' => 'Varicella'],
            ['vaccine_name' => 'Rotarix', 'vaccine_type' => 'Rotavirus'],
            ['vaccine_name' => 'ActHIB', 'vaccine_type' => 'Hib'],
            ['vaccine_name' => 'Pneumococcal Conjugate', 'vaccine_type' => 'Pneumococcal'],
        ];

        foreach ($residentIds as $index => $residentId) {
            $v = $adultVaccines[$index % count($adultVaccines)];
            VaccinationRecord::create([
                'resident_id' => $residentId,
                'child_profile_id' => null,
                'vaccine_name' => $v['vaccine_name'],
                'vaccine_type' => $v['vaccine_type'],
                'vaccination_date' => now()->subDays(rand(5, 90))->toDateString(),
                'dose_number' => rand(1, 3),
                'next_dose_date' => rand(0, 1) ? now()->addDays(rand(30, 120))->toDateString() : null,
                'administered_by' => $administeredById,
            ]);
        }

        foreach ($childIds as $index => $childId) {
            $v = $childVaccines[$index % count($childVaccines)];
            VaccinationRecord::create([
                'resident_id' => null,
                'child_profile_id' => $childId,
                'vaccine_name' => $v['vaccine_name'],
                'vaccine_type' => $v['vaccine_type'],
                'vaccination_date' => now()->subDays(rand(5, 60))->toDateString(),
                'dose_number' => rand(1, 3),
                'next_dose_date' => rand(0, 1) ? now()->addDays(rand(30, 90))->toDateString() : null,
                'administered_by' => $administeredById,
            ]);
        }
    }
}


