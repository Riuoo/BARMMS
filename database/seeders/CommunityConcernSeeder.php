<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CommunityConcern;
use App\Models\Residents;

class CommunityConcernSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $residents = Residents::all();
        
        if ($residents->isEmpty()) {
            return;
        }

        foreach ($residents as $resident) {
            CommunityConcern::create([
                'resident_id' => $resident->id,
                'title' => 'Sample Community Concern for ' . $resident->first_name,
                'description' => 'This is a sample community concern description for demonstration purposes.',
                'category' => $this->getRandomCategory(),
                'status' => $this->getRandomStatus(),
                'location' => 'Barangay Lower Malinao',
                'is_read' => rand(0, 1),
                'assigned_at' => rand(0, 1) ? now()->subDays(rand(1, 20)) : null,
                'resolved_at' => rand(0, 1) ? now()->subDays(rand(1, 10)) : null,
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }

    private function getRandomCategory(): string
    {
        $categories = [
            'Water Supply',
            'Electricity',
            'Roads & Infrastructure',
            'Garbage Collection',
            'Street Lighting',
            'Drainage & Sewage',
            'Noise Pollution',
            'Air Pollution',
            'Public Safety',
            'Health & Sanitation',
            'Transportation',
            'Other'
        ];
        
        return $categories[array_rand($categories)];
    }



    private function getRandomStatus(): string
    {
        $statuses = ['pending', 'in_progress', 'under_review', 'resolved', 'closed'];
        return $statuses[array_rand($statuses)];
    }
}
