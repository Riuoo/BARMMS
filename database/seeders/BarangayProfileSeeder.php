<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BarangayProfile;

class BarangayProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BarangayProfile::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'address' => '123 Barangay St.',
        ]);

        BarangayProfile::create([
            'name' => 'Captain User',
            'email' => 'captain@example.com',
            'password' => bcrypt('password123'),
            'role' => 'captain',
            'address' => '456 Captain St.',
        ]);

        BarangayProfile::create([
            'name' => 'Secretary User',
            'email' => 'secretary@example.com',
            'password' => bcrypt('password123'),
            'role' => 'secretary',
            'address' => '789 Secretary St.',
        ]);

        BarangayProfile::create([
            'name' => 'Treasurer User',
            'email' => 'treasurer@example.com',
            'password' => bcrypt('password123'),
            'role' => 'treasurer',
            'address' => '101 Treasurer St.',
        ]);

        BarangayProfile::create([
            'name' => 'Councilor User',
            'email' => 'councilor@example.com',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => '202 Councilor St.',
        ]);
    }
}
 