<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChildProfile;
use App\Models\BarangayProfile;

class ChildProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $healthWorkerId = BarangayProfile::query()
            ->whereIn('role', ['nurse', 'bhw'])
            ->inRandomOrder()
            ->value('id');

        if (!$healthWorkerId) {
            return;
        }

        $children = [
            [
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'birth_date' => now()->subYears(1)->subMonths(2)->toDateString(),
                'gender' => 'Female',
                'mother_name' => 'Ana Santos',
                'contact_number' => '09171234567',
                'purok' => 'Purok 1',
            ],
            [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'birth_date' => now()->subYears(3)->subMonths(5)->toDateString(),
                'gender' => 'Male',
                'mother_name' => 'Luz Dela Cruz',
                'contact_number' => '09181234567',
                'purok' => 'Purok 2',
            ],
            [
                'first_name' => 'Elena',
                'last_name' => 'Reyes',
                'birth_date' => now()->subYears(5)->toDateString(),
                'gender' => 'Female',
                'mother_name' => 'Consuelo Reyes',
                'contact_number' => '09191234567',
                'purok' => 'Purok 3',
            ],
            [
                'first_name' => 'Miguel',
                'last_name' => 'Lopez',
                'birth_date' => now()->subMonths(10)->toDateString(),
                'gender' => 'Male',
                'mother_name' => 'Teresa Lopez',
                'contact_number' => '09201234567',
                'purok' => 'Purok 4',
            ],
            [
                'first_name' => 'Patricia',
                'last_name' => 'Gonzales',
                'birth_date' => now()->subYears(2)->subMonths(1)->toDateString(),
                'gender' => 'Female',
                'mother_name' => 'Elena Gonzales',
                'contact_number' => '09211234567',
                'purok' => 'Purok 5',
            ],
            [
                'first_name' => 'Carlo',
                'last_name' => 'Aquino',
                'birth_date' => now()->subYears(7)->toDateString(),
                'gender' => 'Male',
                'mother_name' => 'Marites Aquino',
                'contact_number' => '09221234567',
                'purok' => 'Purok 6',
            ],
        ];

        foreach ($children as $child) {
            ChildProfile::create([
                'first_name' => $child['first_name'],
                'last_name' => $child['last_name'],
                'birth_date' => $child['birth_date'],
                'gender' => $child['gender'],
                'mother_name' => $child['mother_name'],
                'contact_number' => $child['contact_number'],
                'purok' => $child['purok'],
                'registered_by' => $healthWorkerId,
                'is_active' => true,
            ]);
        }
    }
}


