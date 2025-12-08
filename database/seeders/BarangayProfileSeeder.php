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
            'first_name' => 'Romeo Ignacio',
            'middle_name' => null,
            'last_name' => 'Centina',
            'suffix' => 'Jr.',
            'email' => 'brgy.captain@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'captain',
            'address' => 'Purok 1, Lower Malinao, Padada City',
            'contact_number' => '09191234501',
        ]);

        BarangayProfile::create([
            'first_name' => 'Prija Patricia',
            'middle_name' => null,
            'last_name' => 'Bancure',
            'suffix' => null,
            'email' => 'brgy.secretary@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'secretary',
            'address' => 'Purok 2, Lower Malinao, Zamboanga City',
            'contact_number' => '09191234502',
        ]);

        BarangayProfile::create([
            'first_name' => 'Evelyn Patricia',
            'middle_name' => null,
            'last_name' => 'Villanueva',
            'suffix' => null,
            'email' => 'brgy.treasurer@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'treasurer',
            'address' => 'Purok 3, Lower Malinao, Padada City',
            'contact_number' => '09191234503',
        ]);

        BarangayProfile::create([
            'first_name' => 'Joel Roberto',
            'middle_name' => null,
            'last_name' => 'Enghog',
            'suffix' => null,
            'email' => 'brgy.councilor1@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 4, Lower Malinao, Padada City',
            'contact_number' => '09191234504',
        ]);

        BarangayProfile::create([
            'first_name' => 'Ariel',
            'middle_name' => 'Santos',
            'last_name' => 'Loro',
            'suffix' => null,
            'email' => 'brgy.councilor2@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 5, Lower Malinao, Zamboanga City',
            'contact_number' => '09191234505',
        ]);

        BarangayProfile::create([
            'first_name' => 'Raphy Chris',
            'middle_name' => 'Villanueva',
            'last_name' => 'Carpentero',
            'suffix' => null,
            'email' => 'brgy.councilor3@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 6, Lower Malinao, Zamboanga City',
            'contact_number' => '09191234506',
        ]);

        BarangayProfile::create([
            'first_name' => 'Mary Jean Elena',
            'middle_name' => null,
            'last_name' => 'Berjame',
            'suffix' => null,
            'email' => 'brgy.councilor4@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 7, Lower Malinao, Zamboanga City',
            'contact_number' => '09191234507',
        ]);

        BarangayProfile::create([
            'first_name' => 'Ivy',
            'middle_name' => 'Santos',
            'last_name' => 'Go',
            'suffix' => null,
            'email' => 'brgy.councilor5@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 8, Lower Malinao, Padada City',
            'contact_number' => '09191234508',
        ]);

        BarangayProfile::create([
            'first_name' => 'Marlown',
            'middle_name' => 'Cruz',
            'last_name' => 'Cabongcal',
            'suffix' => 'Sr.',
            'email' => 'brgy.councilor6@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 9, Lower Malinao, Padada City',
            'contact_number' => '09191234509',
        ]);

        BarangayProfile::create([
            'first_name' => 'Rey Antonio',
            'middle_name' => null,
            'last_name' => 'Abon',
            'suffix' => null,
            'email' => 'brgy.councilor7@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'councilor',
            'address' => 'Purok 10, Lower Malinao, Padada City',
            'contact_number' => '09191234510',
        ]);

        BarangayProfile::create([
            'first_name' => 'Lourdes',
            'middle_name' => 'Dela Cruz',
            'last_name' => 'Tubalad',
            'suffix' => null,
            'email' => 'brgy.nurse@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'nurse',
            'address' => 'Barangay Health Center, Lower Malinao, Padada City',
            'contact_number' => '09191234511',
        ]);

        BarangayProfile::create([
            'first_name' => 'Barangay',
            'middle_name' => null,
            'last_name' => 'Health Worker',
            'suffix' => null,
            'email' => 'brgy.bhw@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'bhw',
            'address' => 'Purok 1, Lower Malinao, Padada City',
            'contact_number' => '09191234512',
        ]);

        BarangayProfile::create([
            'first_name' => 'JC James',
            'middle_name' => 'Cruz',
            'last_name' => 'Fernandez',
            'suffix' => null,
            'email' => 'sk.chair@lowermalinao.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'sk_chairman',
            'address' => 'Purok 2, Lower Malinao, Zamboanga City',
            'contact_number' => '09191234513',
        ]);
    }
}
 