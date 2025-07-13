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
        // Senior Citizen - Low Income
        Residents::create([
            'name' => 'Maria Santos',
            'email' => 'maria.santos@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '123 Senior St., Barangay A',
            'age' => 68,
            'family_size' => 2,
            'education_level' => 'Elementary',
            'income_level' => 'Low',
            'employment_status' => 'Unemployed',
            'health_status' => 'Fair',
        ]);

        // Young Professional - Middle Income
        Residents::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan.delacruz@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '456 Professional Ave., Barangay B',
            'age' => 28,
            'family_size' => 3,
            'education_level' => 'College',
            'income_level' => 'Middle',
            'employment_status' => 'Full-time',
            'health_status' => 'Good',
        ]);

        // Unemployed Youth - Low Income
        Residents::create([
            'name' => 'Ana Reyes',
            'email' => 'ana.reyes@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '789 Youth St., Barangay C',
            'age' => 22,
            'family_size' => 4,
            'education_level' => 'High School',
            'income_level' => 'Low',
            'employment_status' => 'Unemployed',
            'health_status' => 'Good',
        ]);

        // Self-employed Entrepreneur - Upper Middle Income
        Residents::create([
            'name' => 'Pedro Martinez',
            'email' => 'pedro.martinez@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '101 Business Rd., Barangay D',
            'age' => 45,
            'family_size' => 5,
            'education_level' => 'Vocational',
            'income_level' => 'Upper Middle',
            'employment_status' => 'Self-employed',
            'health_status' => 'Excellent',
        ]);

        // Part-time Worker - Lower Middle Income
        Residents::create([
            'name' => 'Luz Garcia',
            'email' => 'luz.garcia@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '202 Part-time Ave., Barangay E',
            'age' => 35,
            'family_size' => 3,
            'education_level' => 'High School',
            'income_level' => 'Lower Middle',
            'employment_status' => 'Part-time',
            'health_status' => 'Fair',
        ]);

        // High-income Professional
        Residents::create([
            'name' => 'Dr. Carlos Lopez',
            'email' => 'carlos.lopez@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '303 Professional Blvd., Barangay F',
            'age' => 52,
            'family_size' => 4,
            'education_level' => 'Post Graduate',
            'income_level' => 'High',
            'employment_status' => 'Full-time',
            'health_status' => 'Excellent',
        ]);

        // Elderly with Health Issues
        Residents::create([
            'name' => 'Lola Remedios',
            'email' => 'remedios.santos@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '404 Elderly St., Barangay G',
            'age' => 75,
            'family_size' => 1,
            'education_level' => 'No Education',
            'income_level' => 'Low',
            'employment_status' => 'Unemployed',
            'health_status' => 'Poor',
        ]);

        // Young Family - Middle Income
        Residents::create([
            'name' => 'Roberto and Maria Flores',
            'email' => 'roberto.flores@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '505 Family Ave., Barangay H',
            'age' => 32,
            'family_size' => 6,
            'education_level' => 'College',
            'income_level' => 'Middle',
            'employment_status' => 'Full-time',
            'health_status' => 'Good',
        ]);

        // Student - Low Income
        Residents::create([
            'name' => 'Miguel Torres',
            'email' => 'miguel.torres@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '606 Student St., Barangay I',
            'age' => 19,
            'family_size' => 5,
            'education_level' => 'High School',
            'income_level' => 'Low',
            'employment_status' => 'Unemployed',
            'health_status' => 'Good',
        ]);

        // Critical Health Case
        Residents::create([
            'name' => 'Tatay Manuel',
            'email' => 'manuel.cruz@example.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => '707 Health Rd., Barangay J',
            'age' => 65,
            'family_size' => 2,
            'education_level' => 'Elementary',
            'income_level' => 'Low',
            'employment_status' => 'Unemployed',
            'health_status' => 'Critical',
        ]);
    }
}
