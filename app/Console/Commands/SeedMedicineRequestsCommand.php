<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\MedicineRequestSeeder;

class SeedMedicineRequestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:medicine-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed medicine requests with purok-based patterns for testing the dispense report';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Medicine Request Seeder...');
        
        try {
            $seeder = new MedicineRequestSeeder();
            $seeder->run();
            
            $this->info('Medicine requests seeded successfully!');
            $this->info('You can now view the purok-based grouping in the medicine dispense report.');
            
        } catch (\Exception $e) {
            $this->error('Error seeding medicine requests: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
