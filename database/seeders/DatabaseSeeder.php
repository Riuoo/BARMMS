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
            ChildProfileSeeder::class,
            MedicineSeeder::class,
            DocumentTemplateSeeder::class,
            AccomplishedProjectSeeder::class,
            HealthCenterActivitySeeder::class,
            MedicalRecordSeeder::class,
            MedicineTransactionSeeder::class,
        ]);
    }
}
