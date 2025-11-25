<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlotterRequest;
use App\Models\Residents;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class BlotterRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $types = [
            'Physical Injury', 'Theft', 'Vandalism', 'Family Dispute', 'Verbal Abuse',
            'Trespassing', 'Property Damage', 'Public Disturbance', 'Threat', 'Others'
        ];
        $statuses = ['pending', 'approved', 'completed'];
        $descriptions = [
            'Reported altercation between neighbors over property boundaries.',
            'Complaint about loud music disturbing the peace at night.',
            'Incident involving theft of a bicycle from a resident’s yard.',
            'Family dispute escalated to physical confrontation.',
            'Verbal threats made during a barangay meeting.',
            'Vandalism of barangay property reported by a resident.',
            'Trespassing incident in a private garden.',
            'Complaint about stray animals causing property damage.',
            'Public disturbance during a fiesta celebration.',
            'Threatening messages received via text.',
            'Physical altercation at the basketball court.',
            'Dispute over unpaid debts between neighbors.',
            'Noise complaint from a karaoke party.',
            'Damage to parked vehicle outside a residence.',
            'Complaint about illegal parking blocking driveway.',
            'Argument over water supply distribution.',
            'Petty theft at the local sari-sari store.',
            'Complaint about garbage dumping in vacant lot.',
            'Dispute over land inheritance.',
            'Alleged harassment at the barangay plaza.'
        ];
        $recipients = [
            'Barangay Captain', 'Barangay Secretary', 'Barangay Councilor', 'Barangay Tanod',
            'Barangay Treasurer', 'Barangay Health Worker', 'SK Chairman', 'Barangay Resident'
        ];

        $residents = Residents::all();
        if ($residents->isEmpty()) return;

        foreach ($residents as $resident) {
            $type = Arr::random($types);
            $status = Arr::random($statuses);
            $desc = Arr::random($descriptions);
            $recipient = Arr::random($recipients);
            $createdAt = now()->subDays(rand(1, 90))->setTime(rand(7, 20), Arr::random([0, 15, 30, 45]));
            $approvedAt = $status !== 'pending' ? $createdAt->copy()->addDays(rand(1, 5)) : null;
            $completedAt = $status === 'completed' ? $approvedAt?->copy()->addDays(rand(1, 7)) : null;
            $media = rand(0, 1) ? [
                [
                    'path' => 'uploads/evidence_' . Str::random(6) . '.jpg',
                    'type' => 'image/jpeg',
                    'name' => 'evidence_' . Str::random(6) . '.jpg',
                    'size' => rand(50000, 500000)
                ]
            ] : null;

            BlotterRequest::create([
                'complainant_name' => $faker->name(),
                'resident_id' => $resident->id,
                'recipient_name' => $recipient,
                'type' => $type,
                'description' => $desc,
                'status' => $status,
                'is_read' => (bool)rand(0, 1),
                'media' => $media,
                'created_at' => $createdAt,
                'updated_at' => $completedAt ?? $approvedAt ?? $createdAt,
                'approved_at' => $approvedAt,
                'summon_date' => $status !== 'pending' ? $createdAt->copy()->addDays(rand(1, 3)) : null,
                'attempts' => rand(0, 3),
                'completed_at' => $completedAt,
            ]);
        }
    }
}
