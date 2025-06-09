<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@barangay.gov.ph',
            'role' => 'admin',
            'address' => '123 Admin St, Barangay',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Barangay Captain',
            'email' => 'captain@barangay.gov.ph',
            'role' => 'captain',
            'address' => '456 Captain Rd, Barangay',
            'password' => Hash::make('captain123'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Secretary',
            'email' => 'secretary@barangay.gov.ph',
            'role' => 'secretary',
            'address' => '789 Secretary Ln, Barangay',
            'password' => Hash::make('secretary123'),
            'email_verified_at' => now(),
        ]);
    }
}
