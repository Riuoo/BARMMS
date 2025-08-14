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
            'address' => 'Purok 1, Lower Malinao, Padada City',
        ]);
        BarangayProfile::create([
            'name' => 'Hon. Romeo I. Centina Jr.',
            'email' => 'brgy.captain@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'captain',
            'address' => 'Purok 1, Lower Malinao, Padada City',
        ]);

        BarangayProfile::create([
            'name' => 'Mrs. Prija P. Bancure',
            'email' => 'brgy.secretary@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'secretary',
            'address' => 'Purok 2, Lower Malinao, Zamboanga City',
        ]);

        BarangayProfile::create([
            'name' => 'Mrs. Evelyn P. Villanueva',
            'email' => 'brgy.treasurer@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'treasurer',
            'address' => 'Purok 3, Lower Malinao, Padada City',
        ]);

        BarangayProfile::create([
            'name' => 'Hon. Joel R. Enghog',
            'email' => 'brgy.councilor1@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 4, Lower Malinao, Padada City',
        ]);

        BarangayProfile::create([
            'name' => 'Hon. Ariel S. Loro',
            'email' => 'brgy.councilor2@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 5, Lower Malinao, Zamboanga City',
        ]);

        BarangayProfile::create([
            'name' => 'Hon. Raphy Chris V. Carpentero',
            'email' => 'brgy.councilor3@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 6, Lower Malinao, Zamboanga City',
        ]);

        BarangayProfile::create([
            'name' => 'Hon. Mary Jean E. Berjame',
            'email' => 'brgy.councilor4@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 7, Lower Malinao, Zamboanga City',
        ]);

        BarangayProfile::create([
            'name' => 'Hon. Ivy S. Go',
            'email' => 'brgy.councilor5@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 8, Lower Malinao, Padada City',
        ]);

        BarangayProfile::create([
            'name' => 'Hon. Marlown C. Cabongcal Sr.',
            'email' => 'brgy.councilor6@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 9, Lower Malinao, Padada City',
        ]);

        BarangayProfile::create([
            'name' => 'Hon. Rey A. Abon',
            'email' => 'brgy.councilor7@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 10, Lower Malinao, Padada City',
        ]);

        BarangayProfile::create([
            'name' => 'Lourdes D. Tubalad',
            'email' => 'brgy.nurse@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'nurse',
            'address' => 'Barangay Health Center, Lower Malinao, Padada City',
        ]);

        BarangayProfile::create([
            'name' => 'Barangay Health Worker',
            'email' => 'brgy.bhw@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'bhw',
            'address' => 'Purok 1, Lower Malinao, Padada City',
        ]);

        BarangayProfile::create([
            'name' => 'Hon. JC James C. Fernandez',
            'email' => 'sk.chair@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'sk_chairman',
            'address' => 'Purok 2, Lower Malinao, Zamboanga City',
        ]);
    }
}
 