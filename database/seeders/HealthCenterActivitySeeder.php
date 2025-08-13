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
                'activity_name' => 'Mass COVID-19 Vaccination Drive',
                'activity_type' => 'Vaccination Drive',
                'activity_date' => now()->subDays(15)->toDateString(),
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'location' => 'Barangay Health Center, Lower Malinao',
                'description' => 'Free COVID-19 vaccination for all eligible residents aged 18 and above. First and second dose administration.',
                'objectives' => 'Achieve 90% vaccination coverage among eligible population to achieve herd immunity.',
                'target_participants' => 500,
                'actual_participants' => 487,
                'organizer' => 'Barangay Health Office - Lower Malinao',
                'materials_needed' => 'COVID-19 vaccines, syringes, PPE kits, registration forms, medical supplies',
                'budget' => 75000,
                'outcomes' => '487 residents successfully vaccinated. No adverse reactions reported.',
                'challenges' => 'Vaccine hesitancy among elderly residents, limited parking space',
                'recommendations' => 'Conduct house-to-house information campaign, coordinate with LGU for additional parking',
                'status' => 'Completed',
                'is_featured' => true,
            ],
            [
                'activity_name' => 'Prenatal Care and Family Planning Seminar',
                'activity_type' => 'Maternal Care',
                'activity_date' => now()->subDays(8)->toDateString(),
                'start_time' => '09:00:00',
                'end_time' => '12:00:00',
                'location' => 'Barangay Multi-Purpose Hall, Lower Malinao',
                'description' => 'Comprehensive prenatal care education and family planning consultation for expecting mothers and couples.',
                'objectives' => 'Improve maternal health outcomes and promote family planning awareness.',
                'target_participants' => 80,
                'actual_participants' => 73,
                'organizer' => 'Barangay Health Office - Lower Malinao',
                'materials_needed' => 'Projector, educational materials, family planning supplies, medical equipment',
                'budget' => 25000,
                'outcomes' => '73 participants educated on prenatal care, 45 accepted family planning methods',
                'challenges' => 'Some participants arrived late, limited seating capacity',
                'recommendations' => 'Implement registration system, secure larger venue for future events',
                'status' => 'Completed',
                'is_featured' => true,
            ],
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
                'is_featured' => false,
            ],
            [
                'activity_name' => 'Senior Citizens Health Check-up',
                'activity_type' => 'Elderly Care',
                'activity_date' => now()->addDays(3)->toDateString(),
                'start_time' => '08:00:00',
                'end_time' => '12:00:00',
                'location' => 'Barangay Health Center, Lower Malinao',
                'description' => 'Free comprehensive health check-up for senior citizens including blood pressure, blood sugar, and general health assessment.',
                'objectives' => 'Monitor health status of senior citizens and provide early intervention for health issues.',
                'target_participants' => 120,
                'actual_participants' => null,
                'organizer' => 'Barangay Health Office - Lower Malinao',
                'materials_needed' => 'BP apparatus, glucometer, weighing scale, medical records, referral forms',
                'budget' => 20000,
                'outcomes' => null,
                'challenges' => null,
                'recommendations' => null,
                'status' => 'Planned',
                'is_featured' => true,
            ],
            [
                'activity_name' => 'Nutrition Month Celebration',
                'activity_type' => 'Nutrition Program',
                'activity_date' => now()->addDays(10)->toDateString(),
                'start_time' => '09:00:00',
                'end_time' => '15:00:00',
                'location' => 'Barangay Plaza, Lower Malinao',
                'description' => 'Annual nutrition month celebration with cooking demonstrations, nutrition education, and healthy food tasting.',
                'objectives' => 'Promote healthy eating habits and proper nutrition among residents of all ages.',
                'target_participants' => 200,
                'actual_participants' => null,
                'organizer' => 'Barangay Nutrition Council - Lower Malinao',
                'materials_needed' => 'Cooking equipment, ingredients, educational materials, sound system, tables and chairs',
                'budget' => 35000,
                'outcomes' => null,
                'challenges' => null,
                'recommendations' => null,
                'status' => 'Planned',
                'is_featured' => false,
            ],
            [
                'activity_name' => 'Mental Health Awareness Seminar',
                'activity_type' => 'Mental Health',
                'activity_date' => now()->addDays(7)->toDateString(),
                'start_time' => '13:00:00',
                'end_time' => '16:00:00',
                'location' => 'Barangay Multi-Purpose Hall, Lower Malinao',
                'description' => 'Mental health awareness seminar focusing on stress management, depression recognition, and seeking help.',
                'objectives' => 'Increase mental health awareness and reduce stigma around mental health issues.',
                'target_participants' => 100,
                'actual_participants' => null,
                'organizer' => 'Barangay Health Office - Lower Malinao',
                'materials_needed' => 'Projector, educational materials, stress relief items, referral information',
                'budget' => 18000,
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


