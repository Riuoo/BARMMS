<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Residents;
use Carbon\Carbon;

class ResidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Senior Citizen - Low Income
        Residents::create([
            'name' => 'Lola Remedios Santos Dela Cruz',
            'email' => 'remedios.santos@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 1, Lower Malinao, Padada City',
            'gender' => 'Female',
            'contact_number' => '09191234567',
            'birth_date' => Carbon::now()->subYears(72),
            'marital_status' => 'Widowed',
            'occupation' => 'Retired',
            'age' => 72,
            'family_size' => 3,
            'education_level' => 'Elementary',
            'income_level' => 'Low',
            'employment_status' => 'Unemployed',
            'health_status' => 'Fair',
            'emergency_contact_name' => 'Maria Santos Dela Cruz',
            'emergency_contact_number' => '09191234568',
            'emergency_contact_relationship' => 'Daughter',
            'active' => true,
        ]);

        // Young Professional - Middle Income
        Residents::create([
            'name' => 'Juan Carlos Dela Cruz Santos',
            'email' => 'juan.santos@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 2, Lower Malinao, Padada City',
            'gender' => 'Male',
            'contact_number' => '09191234569',
            'birth_date' => Carbon::now()->subYears(29),
            'marital_status' => 'Single',
            'occupation' => 'Software Engineer',
            'age' => 29,
            'family_size' => 4,
            'education_level' => 'College',
            'income_level' => 'Middle',
            'employment_status' => 'Full-time',
            'health_status' => 'Good',
            'emergency_contact_name' => 'Carmen Santos',
            'emergency_contact_number' => '09191234570',
            'emergency_contact_relationship' => 'Mother',
            'active' => true,
        ]);

        // Unemployed Youth - Low Income
        Residents::create([
            'name' => 'Ana Patricia Reyes Cruz',
            'email' => 'ana.reyes@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 3, Lower Malinao, Padada City',
            'gender' => 'Female',
            'contact_number' => '09191234571',
            'birth_date' => Carbon::now()->subYears(21),
            'marital_status' => 'Single',
            'occupation' => 'Student',
            'age' => 21,
            'family_size' => 5,
            'education_level' => 'High School',
            'income_level' => 'Low',
            'employment_status' => 'Unemployed',
            'health_status' => 'Good',
            'emergency_contact_name' => 'Roberto Reyes',
            'emergency_contact_number' => '09191234572',
            'emergency_contact_relationship' => 'Father',
            'active' => true,
        ]);

        // Self-employed Entrepreneur - Upper Middle Income
        Residents::create([
            'name' => 'Pedro Antonio Martinez Flores',
            'email' => 'pedro.martinez@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 4, Lower Malinao, Padada City',
            'gender' => 'Male',
            'contact_number' => '09191234573',
            'birth_date' => Carbon::now()->subYears(47),
            'marital_status' => 'Married',
            'occupation' => 'Business Owner',
            'age' => 47,
            'family_size' => 6,
            'education_level' => 'Vocational',
            'income_level' => 'Upper Middle',
            'employment_status' => 'Self-employed',
            'health_status' => 'Excellent',
            'emergency_contact_name' => 'Isabella Martinez',
            'emergency_contact_number' => '09191234574',
            'emergency_contact_relationship' => 'Wife',
            'active' => true,
        ]);

        // Part-time Worker - Lower Middle Income
        Residents::create([
            'name' => 'Luz Maria Garcia Santos',
            'email' => 'luz.garcia@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 5, Lower Malinao, Padada City',
            'gender' => 'Female',
            'contact_number' => '09191234575',
            'birth_date' => Carbon::now()->subYears(38),
            'marital_status' => 'Married',
            'occupation' => 'Teacher',
            'age' => 38,
            'family_size' => 4,
            'education_level' => 'High School',
            'income_level' => 'Lower Middle',
            'employment_status' => 'Part-time',
            'health_status' => 'Fair',
            'emergency_contact_name' => 'Jose Garcia',
            'emergency_contact_number' => '09191234576',
            'emergency_contact_relationship' => 'Husband',
            'active' => true,
        ]);

        // High-income Professional
        Residents::create([
            'name' => 'Dr. Carlos Miguel Lopez Gonzales',
            'email' => 'carlos.lopez@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 6, Lower Malinao, Padada City',
            'gender' => 'Male',
            'contact_number' => '09191234577',
            'birth_date' => Carbon::now()->subYears(54),
            'marital_status' => 'Married',
            'occupation' => 'Medical Doctor',
            'age' => 54,
            'family_size' => 5,
            'education_level' => 'Post Graduate',
            'income_level' => 'High',
            'employment_status' => 'Full-time',
            'health_status' => 'Excellent',
            'emergency_contact_name' => 'Dr. Elena Lopez',
            'emergency_contact_number' => '09191234578',
            'emergency_contact_relationship' => 'Wife',
            'active' => true,
        ]);

        // Middle-aged Parent - Middle Income
        Residents::create([
            'name' => 'Sofia Isabel Rodriguez Torres',
            'email' => 'sofia.rodriguez@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 7, Lower Malinao, Padada City',
            'gender' => 'Female',
            'contact_number' => '09191234579',
            'birth_date' => Carbon::now()->subYears(42),
            'marital_status' => 'Married',
            'occupation' => 'Nurse',
            'age' => 42,
            'family_size' => 6,
            'education_level' => 'College',
            'income_level' => 'Middle',
            'employment_status' => 'Full-time',
            'health_status' => 'Good',
            'emergency_contact_name' => 'Miguel Rodriguez',
            'emergency_contact_number' => '09191234580',
            'emergency_contact_relationship' => 'Husband',
            'active' => true,
        ]);

        // Young Parent - Lower Middle Income
        Residents::create([
            'name' => 'Rafael Jose Hernandez Cruz',
            'email' => 'rafael.hernandez@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 8, Lower Malinao, Padada City',
            'gender' => 'Male',
            'contact_number' => '09191234581',
            'birth_date' => Carbon::now()->subYears(26),
            'marital_status' => 'Married',
            'occupation' => 'Construction Worker',
            'age' => 26,
            'family_size' => 3,
            'education_level' => 'High School',
            'income_level' => 'Lower Middle',
            'employment_status' => 'Full-time',
            'health_status' => 'Good',
            'emergency_contact_name' => 'Carmen Hernandez',
            'emergency_contact_number' => '09191234582',
            'emergency_contact_relationship' => 'Wife',
            'active' => true,
        ]);

        // Senior Professional - Upper Middle Income
        Residents::create([
            'name' => 'Prof. Elena Maria Santos Reyes',
            'email' => 'elena.santos@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 9, Lower Malinao, Padada City',
            'gender' => 'Female',
            'contact_number' => '09191234583',
            'birth_date' => Carbon::now()->subYears(58),
            'marital_status' => 'Widowed',
            'occupation' => 'University Professor',
            'age' => 58,
            'family_size' => 2,
            'education_level' => 'Post Graduate',
            'income_level' => 'Upper Middle',
            'employment_status' => 'Full-time',
            'health_status' => 'Good',
            'emergency_contact_name' => 'Maria Santos',
            'emergency_contact_number' => '09191234584',
            'emergency_contact_relationship' => 'Daughter',
            'active' => true,
        ]);

        // Young Adult - Low Income
        Residents::create([
            'name' => 'Roderick P. Tajos',
            'email' => 'rodericktajos02@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 10, Lower Malinao, Padada City',
            'gender' => 'Male',
            'contact_number' => '09191234585',
            'birth_date' => Carbon::now()->subYears(19),
            'marital_status' => 'Single',
            'occupation' => 'Student',
            'age' => 19,
            'family_size' => 4,
            'education_level' => 'High School',
            'income_level' => 'Low',
            'employment_status' => 'Unemployed',
            'health_status' => 'Excellent',
            'emergency_contact_name' => 'Rosa Morales',
            'emergency_contact_number' => '09191234586',
            'emergency_contact_relationship' => 'Mother',
            'active' => true,
        ]);
    }
}
