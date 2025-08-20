<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentRequest;
use App\Models\Residents;
use App\Models\DocumentTemplate;
use Illuminate\Support\Arr;

class DocumentRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['pending', 'approved', 'completed'];
        $purposes = [
            'For employment application',
            'For school enrollment',
            'For financial assistance',
            'For travel requirements',
            'For SSS/GSIS benefits',
            'For PhilHealth registration',
            'For business permit',
            'For legal purposes',
            'For medical assistance',
            'For housing loan',
            'For scholarship application',
            'For government ID application',
            'For insurance claim',
            'For marriage requirements',
            'For police clearance',
            'For passport application',
            'For driver’s license',
            'For barangay records',
            'For community project',
            'For personal record keeping'
        ];
        $residents = Residents::all();
        $templates = DocumentTemplate::all();
        if ($residents->isEmpty() || $templates->isEmpty()) return;

        for ($i = 0; $i < 20; $i++) {
            $resident = $residents->random();
            $template = $templates->random();
            $status = Arr::random($statuses);
            $purpose = $purposes[$i % count($purposes)];
            $createdAt = now()->subDays(rand(1, 90))->setTime(rand(8, 18), Arr::random([0, 15, 30, 45]));
            $approvedAt = $status !== 'pending' ? $createdAt->copy()->addDays(rand(1, 3)) : null;
            $completedAt = $status === 'completed' ? $approvedAt?->copy()->addDays(rand(1, 5)) : null;

            DocumentRequest::create([
                'resident_id' => $resident->id,
                'document_template_id' => $template->id,
                'document_type' => $template->document_type,
                'description' => $purpose,
                'status' => $status,
                'is_read' => (bool)rand(0, 1),
                'resident_is_read' => (bool)rand(0, 1),
                'created_at' => $createdAt,
                'updated_at' => $completedAt ?? $approvedAt ?? $createdAt,
            ]);
        }
    }
}
