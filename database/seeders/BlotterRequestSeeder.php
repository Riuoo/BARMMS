<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlotterRequest;
use App\Models\BarangayProfile;

class BlotterRequestSeeder extends Seeder
{
    public function run()
    {
        // Get some barangay profile IDs to associate with blotter requests
        $userIds = BarangayProfile::pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->info('No barangay profiles found, skipping blotter request seeding.');
            return;
        }

        $dummyData = [
            [
                'user_id' => $userIds[array_rand($userIds)],
                'type' => 'Noise Complaint',
                'description' => 'Loud noise from neighbors during night hours.',
                'media' => null,
                'status' => 'pending',
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'type' => 'Vandalism',
                'description' => 'Graffiti on public property.',
                'media' => null,
                'status' => 'pending',
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'type' => 'Theft',
                'description' => 'Bicycle stolen from garage.',
                'media' => null,
                'status' => 'pending',
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'type' => 'Illegal Parking',
                'description' => 'Car parked blocking driveway.',
                'media' => null,
                'status' => 'pending',
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'type' => 'Illegal Parking',
                'description' => 'Car parked blocking driveway.',
                'media' => null,
                'status' => 'pending',
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'type' => 'Illegal Parking',
                'description' => 'Car parked blocking driveway.',
                'media' => null,
                'status' => 'pending',
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'type' => 'Illegal Parking',
                'description' => 'Car parked blocking driveway.',
                'media' => null,
                'status' => 'pending',
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'type' => 'Illegal Parking',
                'description' => 'Car parked blocking driveway.',
                'media' => null,
                'status' => 'pending',
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'type' => 'Illegal Parking',
                'description' => 'Car parked blocking driveway.',
                'media' => null,
                'status' => 'pending',
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'type' => 'Illegal Parking',
                'description' => 'Car parked blocking driveway.',
                'media' => null,
                'status' => 'pending',
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'type' => 'Illegal Parking',
                'description' => 'Car parked blocking driveway.',
                'media' => null,
                'status' => 'pending',
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'type' => 'Illegal Parking',
                'description' => 'Car parked blocking driveway.',
                'media' => null,
                'status' => 'pending',
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'type' => 'Illegal Parking',
                'description' => 'Car parked blocking driveway.',
                'media' => null,
                'status' => 'pending',
            ],
        ];

        foreach ($dummyData as $data) {
            BlotterRequest::create($data);
        }

        $this->command->info('Seeded 4 dummy blotter requests.');
    }
}
