<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccountRequest;
use Illuminate\Support\Carbon;

class AccountRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userIds = \App\Models\User::pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->info('No users found, skipping account request seeding.');
            return;
        }

        $dummyData = [
            [
                'user_id' => $userIds[array_rand($userIds)],
                'email' => 'user1@example.com',
                'status' => 'pending',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'email' => 'user2@example.com',
                'status' => 'pending',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'email' => 'user3@example.com',
                'status' => 'pending',
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'email' => 'user4@example.com',
                'status' => 'pending',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'user_id' => $userIds[array_rand($userIds)],
                'email' => 'user5@example.com',
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours(12),
                'updated_at' => Carbon::now()->subHours(12),
            ],
        ];

        foreach ($dummyData as $data) {
            AccountRequest::create($data);
        }

        $this->command->info('Seeded 5 dummy account requests.');
    }
}
