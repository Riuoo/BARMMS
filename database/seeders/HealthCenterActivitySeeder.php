<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HealthCenterActivity;
use Illuminate\Support\Str;

class HealthCenterActivitySeeder extends Seeder
{
    public function run(): void
    {
        $activities = [
            [
                'activity_name' => 'Community Vaccination Drive',
                'activity_type' => 'Vaccination Drive',
                'activity_date' => now()->subDays(10)->toDateString(),
                'start_time' => '08:00:00',
                'end_time' => '12:00:00',
                'location' => 'Barangay Health Center',
                'description' => 'A vaccination drive to immunize residents against common diseases.',
                'objectives' => 'Increase vaccination coverage to 95% of target population.',
                'target_participants' => 200,
                'actual_participants' => 185,
                'organizer' => 'Barangay Health Office',
                'materials_needed' => 'Syringes, vaccines, PPE, registration forms',
                'budget' => 25000,
                'outcomes' => '185 residents vaccinated successfully.',
                'challenges' => 'Vaccine hesitancy in some households.',
                'recommendations' => 'Conduct more information campaigns.',
                'status' => 'Completed',
                'is_featured' => true,
            ],
            [
                'activity_name' => 'Health Education Seminar',
                'activity_type' => 'Health Education',
                'activity_date' => now()->subDays(3)->toDateString(),
                'start_time' => '13:00:00',
                'end_time' => '16:00:00',
                'location' => 'Barangay Hall',
                'description' => 'Seminar on proper nutrition and hygiene practices.',
                'objectives' => 'Educate residents on healthy lifestyle and sanitation.',
                'target_participants' => 100,
                'actual_participants' => 92,
                'organizer' => 'Barangay Nutrition Council',
                'materials_needed' => 'Projector, pamphlets, speaker system',
                'budget' => 8000,
                'outcomes' => 'Increased awareness on nutrition and hygiene.',
                'challenges' => 'Limited seating capacity.',
                'recommendations' => 'Use larger venue next time.',
                'status' => 'Completed',
                'is_featured' => true,
            ],
            [
                'activity_name' => 'Maternal Care Check-up',
                'activity_type' => 'Maternal Care',
                'activity_date' => now()->addDays(7)->toDateString(),
                'start_time' => '09:00:00',
                'end_time' => '11:30:00',
                'location' => 'Health Center Room 2',
                'description' => 'Prenatal check-up and counseling for expecting mothers.',
                'objectives' => 'Ensure safe pregnancy and early detection of risks.',
                'target_participants' => 40,
                'actual_participants' => null,
                'organizer' => 'Midwives Association',
                'materials_needed' => 'BP apparatus, fetal doppler, records',
                'budget' => 5000,
                'outcomes' => null,
                'challenges' => null,
                'recommendations' => null,
                'status' => 'Planned',
                'is_featured' => false,
            ],
        ];

        foreach ($activities as $data) {
            HealthCenterActivity::create($data);
        }
    }
}


