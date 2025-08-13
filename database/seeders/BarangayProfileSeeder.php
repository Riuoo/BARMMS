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
            'name' => 'Admin',
            'email' => 'admin@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'address' => 'Purok 1, Lower Malinao, Zamboanga City',
        ]);
        BarangayProfile::create([
            'name' => 'Hon. Roberto Santos Dela Cruz',
            'email' => 'brgy.captain@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'captain',
            'address' => 'Purok 1, Lower Malinao, Zamboanga City',
        ]);

        BarangayProfile::create([
            'name' => 'Maria Consuelo Reyes',
            'email' => 'brgy.secretary@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'secretary',
            'address' => 'Purok 2, Lower Malinao, Zamboanga City',
        ]);

        BarangayProfile::create([
            'name' => 'Antonio Mendoza Flores',
            'email' => 'brgy.treasurer@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'treasurer',
            'address' => 'Purok 3, Lower Malinao, Zamboanga City',
        ]);

        BarangayProfile::create([
            'name' => 'Hon. Lourdes Garcia Santos',
            'email' => 'brgy.councilor1@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 4, Lower Malinao, Zamboanga City',
        ]);

        BarangayProfile::create([
            'name' => 'Hon. Ramon Torres Cruz',
            'email' => 'brgy.councilor2@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 5, Lower Malinao, Zamboanga City',
        ]);

        BarangayProfile::create([
            'name' => 'Hon. Elena Martinez Lopez',
            'email' => 'brgy.councilor3@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 6, Lower Malinao, Zamboanga City',
        ]);

        BarangayProfile::create([
            'name' => 'Hon. Fernando Aquino Reyes',
            'email' => 'brgy.councilor4@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 7, Lower Malinao, Zamboanga City',
        ]);

        BarangayProfile::create([
            'name' => 'Hon. Rosario Lim Santos',
            'email' => 'brgy.councilor5@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 8, Lower Malinao, Zamboanga City',
        ]);

        BarangayProfile::create([
            'name' => 'Dr. Maria Teresa Gonzales',
            'email' => 'brgy.nurse@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'nurse',
            'address' => 'Barangay Health Center, Lower Malinao, Zamboanga City',
        ]);

        BarangayProfile::create([
            'name' => 'Nurse Ana Patricia Cruz',
            'email' => 'brgy.nurse2@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'nurse',
            'address' => 'Barangay Health Center, Lower Malinao, Zamboanga City',
        ]);

        BarangayProfile::create([
            'name' => 'Barangay Tanod Captain Manuel Santos',
            'email' => 'brgy.tanod@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'bhw',
            'address' => 'Purok 1, Lower Malinao, Zamboanga City',
        ]);

        BarangayProfile::create([
            'name' => 'SK Chairperson Juan Miguel Dela Cruz',
            'email' => 'sk.chair@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'sk_chairman',
            'address' => 'Purok 2, Lower Malinao, Zamboanga City',
        ]);
    }
}
 