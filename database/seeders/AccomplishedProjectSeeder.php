<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AccomplishedProject;

class AccomplishedProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'title' => 'Barangay Health Center Modernization Project',
                'description' => 'Complete renovation and modernization of the barangay health center to provide better healthcare services to residents. The project included new medical equipment, improved waiting areas, better facilities for healthcare workers, and a dedicated maternal and child care room.',
                'category' => 'Health',
                'location' => 'Barangay Hall Compound, Lower Malinao',
                'budget' => 2800000.00,
                'start_date' => '2023-03-15',
                'completion_date' => '2023-08-20',
                'status' => 'completed',
                'beneficiaries' => 'All residents of Lower Malinao (approximately 5,200 people)',
                'impact' => 'Improved healthcare access, reduced travel time to medical facilities, better emergency response capabilities, increased patient satisfaction from 65% to 92%',
                'funding_source' => 'Department of Health (DOH) - Health Facilities Enhancement Program',
                'implementing_agency' => 'Barangay Council of Lower Malinao',
                'is_featured' => true,
            ]
        ];

        foreach ($projects as $project) {
            AccomplishedProject::create($project);
        }
    }
} 