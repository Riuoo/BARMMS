<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Residence;

class ResidenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Residence::create([
            'name' => 'Sample Residence',
            'email' => 'residence@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '456 Residence Ave.',
        ]);

        Residence::create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '789 John St.',
        ]);

        Residence::create([
            'name' => 'Jane Smith',
            'email' => 'janesmith@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '101 Jane Ave.',
        ]);
        Residence::create([
            'name' => 'John Smith',
            'email' => 'johnsmith@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '102 Jane Ave.',
        ]);
    }
}
