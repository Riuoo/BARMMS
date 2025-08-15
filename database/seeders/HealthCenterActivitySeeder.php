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
                'activity_name' => 'Dengue Prevention and Control Campaign',
                'activity_type' => 'Health Education',
                'activity_date' => now()->subDays(5)->toDateString(),
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'location' => 'Purok 1-8, Lower Malinao',
                'description' => 'House-to-house campaign to eliminate mosquito breeding sites and educate residents on dengue prevention.',
                'objectives' => 'Reduce mosquito breeding sites by 80% and increase community awareness on dengue prevention.',
                'target_participants' => 300,
                'actual_participants' => 285,
                'organizer' => 'Barangay Health Office - Lower Malinao',
                'materials_needed' => 'Larvicides, educational materials, inspection forms, cleaning supplies',
                'budget' => 15000,
                'outcomes' => '285 households visited, 45 breeding sites eliminated, increased community awareness',
                'challenges' => 'Some residents not at home during visit, resistance to larvicide application',
                'recommendations' => 'Coordinate visit schedules with residents, conduct follow-up visits',
                'status' => 'Completed',
                'is_featured' => true,
            ]
        ];

        foreach ($activities as $data) {
            HealthCenterActivity::create($data);
        }
    }
}


