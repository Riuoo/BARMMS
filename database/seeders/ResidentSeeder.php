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
            'name' => 'Lola Remedios Santos Dela Cruz',
            'email' => 'remedios.santos@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 1, Lower Malinao, Padada City',
            'age' => 72,
            'family_size' => 3,
            'education_level' => 'Elementary',
            'income_level' => 'Low',
            'employment_status' => 'Unemployed',
            'health_status' => 'Fair',
        ]);

        // Young Professional - Middle Income
        Residents::create([
            'name' => 'Juan Carlos Dela Cruz Santos',
            'email' => 'juan.santos@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 2, Lower Malinao, Padada City',
            'age' => 29,
            'family_size' => 4,
            'education_level' => 'College',
            'income_level' => 'Middle',
            'employment_status' => 'Full-time',
            'health_status' => 'Good',
        ]);

        // Unemployed Youth - Low Income
        Residents::create([
            'name' => 'Ana Patricia Reyes Cruz',
            'email' => 'ana.reyes@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 3, Lower Malinao, Padada City',
            'age' => 21,
            'family_size' => 5,
            'education_level' => 'High School',
            'income_level' => 'Low',
            'employment_status' => 'Unemployed',
            'health_status' => 'Good',
        ]);

        // Self-employed Entrepreneur - Upper Middle Income
        Residents::create([
            'name' => 'Pedro Antonio Martinez Flores',
            'email' => 'pedro.martinez@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 4, Lower Malinao, Padada City',
            'age' => 47,
            'family_size' => 6,
            'education_level' => 'Vocational',
            'income_level' => 'Upper Middle',
            'employment_status' => 'Self-employed',
            'health_status' => 'Excellent',
        ]);

        // Part-time Worker - Lower Middle Income
        Residents::create([
            'name' => 'Luz Maria Garcia Santos',
            'email' => 'luz.garcia@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 5, Lower Malinao, Padada City',
            'age' => 38,
            'family_size' => 4,
            'education_level' => 'High School',
            'income_level' => 'Lower Middle',
            'employment_status' => 'Part-time',
            'health_status' => 'Fair',
        ]);

        // High-income Professional
        Residents::create([
            'name' => 'Dr. Carlos Miguel Lopez Gonzales',
            'email' => 'carlos.lopez@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 6, Lower Malinao, Padada City',
            'age' => 54,
            'family_size' => 5,
            'education_level' => 'Post Graduate',
            'income_level' => 'High',
            'employment_status' => 'Full-time',
            'health_status' => 'Excellent',
        ]);

        // Elderly with Health Issues
        Residents::create([
            'name' => 'Tatay Manuel Cruz Santos',
            'email' => 'manuel.cruz@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 7, Lower Malinao, Padada City',
            'age' => 78,
            'family_size' => 2,
            'education_level' => 'No Education',
            'income_level' => 'Low',
            'employment_status' => 'Unemployed',
            'health_status' => 'Poor',
        ]);

        // Young Family - Middle Income
        Residents::create([
            'name' => 'Roberto Santos Flores',
            'email' => 'roberto.flores@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 3, Lower Malinao, Padada City',
            'age' => 35,
            'family_size' => 7,
            'education_level' => 'College',
            'income_level' => 'Middle',
            'employment_status' => 'Full-time',
            'health_status' => 'Good',
        ]);

        // Student - Low Income
        Residents::create([
            'name' => 'Miguel Angelo Torres Cruz',
            'email' => 'miguel.torres@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 1, Lower Malinao, Padada City',
            'age' => 18,
            'family_size' => 6,
            'education_level' => 'High School',
            'income_level' => 'Low',
            'employment_status' => 'Unemployed',
            'health_status' => 'Good',
        ]);

        // Critical Health Case
        Residents::create([
            'name' => 'Lolo Francisco Aquino Reyes',
            'email' => 'francisco.aquino@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 2, Lower Malinao, Padada City',
            'age' => 81,
            'family_size' => 3,
            'education_level' => 'Elementary',
            'income_level' => 'Low',
            'employment_status' => 'Unemployed',
            'health_status' => 'Critical',
        ]);

        // Middle-aged Professional - Upper Middle Income
        Residents::create([
            'name' => 'Maria Consuelo Lim Santos',
            'email' => 'consuelo.lim@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 3, Lower Malinao, Padada City',
            'age' => 42,
            'family_size' => 4,
            'education_level' => 'College',
            'income_level' => 'Upper Middle',
            'employment_status' => 'Full-time',
            'health_status' => 'Good',
        ]);

        // Young Parent - Lower Middle Income
        Residents::create([
            'name' => 'Jose Maria Dela Cruz Reyes',
            'email' => 'jose.delacruz@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 4, Lower Malinao, Padada City',
            'age' => 26,
            'family_size' => 3,
            'education_level' => 'High School',
            'income_level' => 'Lower Middle',
            'employment_status' => 'Full-time',
            'health_status' => 'Good',
        ]);

        // Senior Professional - High Income
        Residents::create([
            'name' => 'Atty. Elena Santos Gonzales',
            'email' => 'elena.gonzales@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 5, Lower Malinao, Padada City',
            'age' => 58,
            'family_size' => 4,
            'education_level' => 'Post Graduate',
            'income_level' => 'High',
            'employment_status' => 'Full-time',
            'health_status' => 'Excellent',
        ]);

        // Part-time Student - Low Income
        Residents::create([
            'name' => 'Carla Patricia Santos Cruz',
            'email' => 'carla.santos@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 6, Lower Malinao, Padada City',
            'age' => 20,
            'family_size' => 5,
            'education_level' => 'College',
            'income_level' => 'Low',
            'employment_status' => 'Part-time',
            'health_status' => 'Good',
        ]);

        // Elderly Couple - Low Income
        Residents::create([
            'name' => 'Lolo Tomas and Lola Felicidad Santos',
            'email' => 'tomas.santos@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 7, Lower Malinao, Padada City',
            'age' => 76,
            'family_size' => 2,
            'education_level' => 'Elementary',
            'income_level' => 'Low',
            'employment_status' => 'Unemployed',
            'health_status' => 'Fair',
        ]);

        // Young Entrepreneur - Middle Income
        Residents::create([
            'name' => 'Marco Antonio Santos Flores',
            'email' => 'marco.flores@email.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 2, Lower Malinao, Padada City',
            'age' => 31,
            'family_size' => 4,
            'education_level' => 'College',
            'income_level' => 'Middle',
            'employment_status' => 'Self-employed',
            'health_status' => 'Good',
        ]);
        
        Residents::create([
            'name' => 'Roderick P. Tajos',
            'email' => 'rodericktajos02@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'address' => 'Purok 1, Lower Malinao, Padada City',
            'age' => 21,
            'family_size' => 1,
            'education_level' => 'College',
            'income_level' => 'Low',
            'employment_status' => 'Unemployed',
            'health_status' => 'Good',
        ]);
    }
}
