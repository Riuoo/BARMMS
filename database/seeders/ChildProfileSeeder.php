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
            ->whereIn('role', ['nurse'])
            ->inRandomOrder()
            ->value('id');

        if (!$healthWorkerId) {
            return;
        }

        $children = [
            [
                'first_name' => 'Elena',
                'last_name' => 'Reyes',
                'birth_date' => now()->subYears(5)->toDateString(),
                'gender' => 'Female',
                'mother_name' => 'Consuelo Reyes',
                'contact_number' => '09191234567',
                'purok' => 'Purok 3',
            ]
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


