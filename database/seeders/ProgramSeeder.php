<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Services\ProgramDefinitionsService;
use Illuminate\Support\Facades\Log;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding programs...');
        
        $definitions = ProgramDefinitionsService::getProgramDefinitions();
        $created = 0;
        $updated = 0;

        foreach ($definitions as $definition) {
            // Ensure all required fields have defaults
            $data = array_merge([
                'is_active' => true,
                'priority' => 0,
                'target_puroks' => null,
            ], $definition);

            // Check if program exists
            $exists = Program::where('name', $definition['name'])->exists();
            
            $program = Program::updateOrCreate(
                ['name' => $definition['name']],
                $data
            );

            if ($exists) {
                $updated++;
                $this->command->line("  Updated: {$program->name}");
            } else {
                $created++;
                $this->command->line("  Created: {$program->name}");
            }
        }

        $this->command->info("Programs seeded successfully! Created: {$created}, Updated: {$updated}");
    }
}
