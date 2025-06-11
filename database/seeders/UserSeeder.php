<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userData = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => 'password',
                'role' => 'admin',
                'address' => 'Admin Address',
            ],
            [
                'name' => 'Captain User',
                'email' => 'captain@example.com',
                'password' => 'password',
                'role' => 'captain',
                'address' => 'Captain Address',
            ],
            [
                'name' => 'Secretary User',
                'email' => 'secretary@example.com',
                'password' => 'password',
                'role' => 'secretary',
                'address' => 'Secretary Address',
            ],
            [
                'name' => 'Normal User',
                'email' => 'user@example.com',
                'password' => 'password',
                'role' => 'user',
                'address' => 'User Address',
            ],
        ];

        foreach ($userData as $data) {
            $existingUser = User::where('email', $data['email'])->first();
            if (!$existingUser) {
                User::create($data);
            }
        }

        $this->command->info('Seeded 4 users successfully.');
    }
}
