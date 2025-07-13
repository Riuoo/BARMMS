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
                'title' => 'Barangay Health Center Renovation',
                'description' => 'Complete renovation and modernization of the barangay health center to provide better healthcare services to residents. The project included new medical equipment, improved waiting areas, and better facilities for healthcare workers.',
                'category' => 'Health',
                'location' => 'Barangay Hall Compound',
                'budget' => 2500000.00,
                'start_date' => '2023-03-15',
                'completion_date' => '2023-08-20',
                'status' => 'completed',
                'beneficiaries' => 'All residents of Lower Malinao (approximately 5,000 people)',
                'impact' => 'Improved healthcare access, reduced travel time to medical facilities, better emergency response capabilities',
                'funding_source' => 'Department of Health',
                'implementing_agency' => 'Barangay Council',
                'is_featured' => true,
            ],
            [
                'title' => 'Solar Street Lighting Project',
                'description' => 'Installation of solar-powered street lights along major roads and pathways to improve safety and visibility during nighttime. This eco-friendly project promotes sustainable energy use.',
                'category' => 'Infrastructure',
                'location' => 'Main roads and pathways',
                'budget' => 1800000.00,
                'start_date' => '2023-01-10',
                'completion_date' => '2023-04-25',
                'status' => 'completed',
                'beneficiaries' => 'All residents, especially those who travel at night',
                'impact' => 'Enhanced public safety, reduced crime incidents, improved community mobility at night',
                'funding_source' => 'Department of Energy',
                'implementing_agency' => 'Barangay Council',
                'is_featured' => true,
            ],
            [
                'title' => 'Computer Literacy Training Program',
                'description' => 'Comprehensive computer literacy training for residents, especially senior citizens and unemployed youth. The program covered basic computer operations, internet usage, and digital skills.',
                'category' => 'Education',
                'location' => 'Barangay Multi-Purpose Hall',
                'budget' => 500000.00,
                'start_date' => '2023-06-01',
                'completion_date' => '2023-09-30',
                'status' => 'completed',
                'beneficiaries' => '150 residents (senior citizens and unemployed youth)',
                'impact' => 'Improved digital literacy, better employment opportunities, enhanced communication skills',
                'funding_source' => 'Department of Information and Communications Technology',
                'implementing_agency' => 'Barangay Council',
                'is_featured' => false,
            ],
            [
                'title' => 'Organic Farming Training and Support',
                'description' => 'Training program for farmers on organic farming techniques and sustainable agriculture practices. Included distribution of organic seeds and farming tools.',
                'category' => 'Agriculture',
                'location' => 'Agricultural areas of Lower Malinao',
                'budget' => 800000.00,
                'start_date' => '2023-02-01',
                'completion_date' => '2023-05-15',
                'status' => 'completed',
                'beneficiaries' => '50 local farmers and their families',
                'impact' => 'Improved agricultural productivity, reduced chemical use, better food security',
                'funding_source' => 'Department of Agriculture',
                'implementing_agency' => 'Barangay Council',
                'is_featured' => false,
            ],
            [
                'title' => 'Waste Management and Segregation Program',
                'description' => 'Implementation of comprehensive waste management system including proper waste segregation, recycling initiatives, and community awareness campaigns.',
                'category' => 'Environment',
                'location' => 'Entire barangay',
                'budget' => 600000.00,
                'start_date' => '2023-07-01',
                'completion_date' => '2023-10-31',
                'status' => 'completed',
                'beneficiaries' => 'All residents and the environment',
                'impact' => 'Cleaner environment, reduced pollution, improved public health',
                'funding_source' => 'Department of Environment and Natural Resources',
                'implementing_agency' => 'Barangay Council',
                'is_featured' => true,
            ],
            [
                'title' => 'Senior Citizens Activity Center',
                'description' => 'Construction of a dedicated activity center for senior citizens with facilities for recreation, exercise, and social activities. Includes medical monitoring equipment.',
                'category' => 'Social Services',
                'location' => 'Barangay Hall Compound',
                'budget' => 1200000.00,
                'start_date' => '2023-04-01',
                'completion_date' => '2023-07-15',
                'status' => 'completed',
                'beneficiaries' => 'Senior citizens of Lower Malinao (approximately 300 people)',
                'impact' => 'Improved quality of life for seniors, better social engagement, enhanced healthcare monitoring',
                'funding_source' => 'Department of Social Welfare and Development',
                'implementing_agency' => 'Barangay Council',
                'is_featured' => false,
            ],
            [
                'title' => 'Youth Skills Development Program',
                'description' => 'Training program for out-of-school youth in various skills including carpentry, electrical work, plumbing, and other technical skills for better employment opportunities.',
                'category' => 'Livelihood',
                'location' => 'Barangay Multi-Purpose Hall',
                'budget' => 750000.00,
                'start_date' => '2023-05-01',
                'completion_date' => '2023-08-31',
                'status' => 'completed',
                'beneficiaries' => '75 out-of-school youth',
                'impact' => 'Reduced unemployment, improved economic opportunities, enhanced community skills',
                'funding_source' => 'Technical Education and Skills Development Authority',
                'implementing_agency' => 'Barangay Council',
                'is_featured' => false,
            ],
            [
                'title' => 'Emergency Response Equipment Upgrade',
                'description' => 'Upgrade of emergency response equipment including new fire extinguishers, first aid kits, and emergency communication systems for better disaster preparedness.',
                'category' => 'Infrastructure',
                'location' => 'Barangay Hall and strategic locations',
                'budget' => 400000.00,
                'start_date' => '2023-09-01',
                'completion_date' => '2023-11-30',
                'status' => 'completed',
                'beneficiaries' => 'All residents in emergency situations',
                'impact' => 'Improved disaster response, enhanced public safety, better emergency preparedness',
                'funding_source' => 'Department of the Interior and Local Government',
                'implementing_agency' => 'Barangay Council',
                'is_featured' => false,
            ],
        ];

        foreach ($projects as $project) {
            AccomplishedProject::create($project);
        }
    }
} 