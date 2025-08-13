<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BarangayProfileSeeder::class,
            ResidentSeeder::class,
            MedicineSeeder::class,
            MedicalRecordSeeder::class,
            MedicineTransactionSeeder::class,
            MedicineRequestSeeder::class,
            DocumentTemplateSeeder::class,
            AccomplishedProjectSeeder::class,
            HealthCenterActivitySeeder::class,
            VaccinationScheduleSeeder::class,
        ]);
    }
}
