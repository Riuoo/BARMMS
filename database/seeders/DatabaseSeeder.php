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
            DocumentTemplateSeeder::class,
            BlotterTemplateSeeder::class,
            AccomplishedProjectSeeder::class,
            HealthCenterActivitySeeder::class,
            MedicalRecordSeeder::class,
            MedicineRequestSeeder::class,      // <-- Requests first
            MedicineTransactionSeeder::class,  // <-- Transactions after
            BlotterRequestSeeder::class,
            DocumentRequestSeeder::class,
            CommunityConcernSeeder::class,
            FaqSeeder::class,
        ]);
    }
}
