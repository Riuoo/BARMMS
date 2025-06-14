<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentRequest;
use App\Models\BarangayProfile;

class DocumentRequestSeeder extends Seeder
{
    public function run()
    {
        // Get some barangay profile IDs to associate with document requests
        $userIds = BarangayProfile::pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->info('No barangay profiles found, skipping document request seeding.');
            return;
        }

        $dummyData = [
            [
                'user_id' => $userIds[array_rand($userIds)],
                'document_type' => 'Birth Certificate',
                'description' => 'Requesting a copy of birth certificate for official use.',
                'status' => 'pending',
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'document_type' => 'Marriage Certificate',
                'description' => 'Need marriage certificate for legal purposes.',
                'status' => 'pending',
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'document_type' => 'Transcript of Records',
                'description' => 'Requesting transcript for job application.',
                'status' => 'pending',
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'document_type' => 'Barangay Clearance',
                'description' => 'For business permit application.',
                'status' => 'pending',
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'document_type' => 'Certificate of Residency',
                'description' => 'Needed for school enrollment.',
                'status' => 'pending',
            ],
        ];

        foreach ($dummyData as $data) {
            DocumentRequest::create($data);
        }

        $this->command->info('Seeded 5 dummy document requests.');
    }
}
