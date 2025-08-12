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
            DocumentTemplateSeeder::class,
            AccomplishedProjectSeeder::class,
            HealthCenterActivitySeeder::class,
        ]);
    }
}
