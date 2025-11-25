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
            'For employment application For employment application For employment application For employment applicationFor employment applicationFor employment applicationFor employment applicationFor employment application',
            'For school enrollmentFor employment applicationFor employment applicationFor employment applicationFor employment application',
            'For financial assistanceFor employment applicationFor employment applicationFor employment application',
            'For travel requirementsFor employment applicationFor employment applicationFor employment application',
            'For SSS/GSIS benefitsFor employment applicationFor employment applicationFor employment application',
            'For PhilHealth registrationFor employment applicationFor employment applicationFor employment application',
            'For business permitFor employment applicationFor employment applicationFor employment application',
            'For legal purposesFor employment applicationFor employment application',
            'For medical assistanceFor employment applicationFor employment application',
            'For housing loanFor employment applicationFor employment application',
            'For scholarship applicationFor employment applicationFor employment applicationFor employment application',
            'For government ID applicationFor employment applicationFor employment applicationFor employment application',
            'For insurance claimFor employment applicationFor employment applicationFor employment application',
            'For marriage requirementsFor employment applicationFor employment applicationFor employment application',
            'For police clearanceFor employment applicationFor employment applicationFor employment application',
            'For passport applicationFor employment applicationFor employment applicationFor employment application',
            'For driver’s licenseFor employment applicationFor employment applicationFor employment application',
            'For barangay recordsFor employment applicationFor employment applicationFor employment application',
            'For community projectFor employment applicationFor employment applicationFor employment application',
            'For personal record keepingFor employment applicationFor employment applicationFor employment application'
        ];
        $residents = Residents::all();
        $templates = DocumentTemplate::all();
        if ($residents->isEmpty() || $templates->isEmpty()) return;

        foreach ($residents as $resident) {
            $template = $templates->random();
            $status = Arr::random($statuses);
            $purpose = Arr::random($purposes);
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
