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
            $status = $this->getRandomStatus();
            $createdAt = now()->subDays(rand(1, 30));
            $assignedAt = in_array($status, ['under_review', 'in_progress', 'resolved', 'closed'])
                ? $createdAt->copy()->addHours(rand(4, 24))
                : null;
            $resolvedAt = in_array($status, ['resolved', 'closed'])
                ? $assignedAt?->copy()->addDays(rand(1, 7))
                : null;
            $adminRemarks = $resolvedAt
                ? 'Concern resolved after site inspection and coordination with utilities.'
                : null;

            CommunityConcern::create([
                'resident_id' => $resident->id,
                'title' => 'Sample Community Concern for ' . $resident->first_name,
                'description' => 'This is a sample community concern description for demonstration purposes.',
                'status' => $status,
                'location' => 'Barangay Lower Malinao',
                'is_read' => rand(0, 1),
                'assigned_at' => $assignedAt,
                'resolved_at' => $resolvedAt,
                'admin_remarks' => $adminRemarks,
                'created_at' => $createdAt,
                'updated_at' => $resolvedAt ?? $assignedAt ?? $createdAt,
            ]);
        }
    }

    private function getRandomStatus(): string
    {
        $statuses = ['pending', 'in_progress', 'under_review', 'resolved', 'closed'];
        return $statuses[array_rand($statuses)];
    }
}
