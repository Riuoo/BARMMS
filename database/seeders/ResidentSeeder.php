<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Residents;

class ResidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Residents::create([
            'name' => 'Sample Residence',
            'email' => 'residence@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '456 Residence Ave.',
        ]);

        Residents::create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '789 John St.',
        ]);

        Residents::create([
            'name' => 'Jane Smith',
            'email' => 'janesmith@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '101 Jane Ave.',
        ]);
        Residents::create([
            'name' => 'John Smith',
            'email' => 'johnsmith@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '102 Jane Ave.',
        ]);
        Residents::create([
            'name' => 'Test 123',
            'email' => 'test1@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '103 Jane Ave.',
        ]);
        Residents::create([
            'name' => 'Test 12345',
            'email' => 'test2@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '104 Jane Ave.',
        ]);
    }
}
