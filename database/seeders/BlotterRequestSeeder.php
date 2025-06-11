<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlotterRequest;
use App\Models\User;

class BlotterRequestSeeder extends Seeder
{
    public function run()
    {
        // Get some user IDs to associate with blotter requests
        $userIds = User::pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->info('No users found, skipping blotter request seeding.');
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
