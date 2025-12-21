<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Services\ProgramDefinitionsService;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $definitions = ProgramDefinitionsService::getProgramDefinitions();

        foreach ($definitions as $definition) {
            Program::updateOrCreate(
                ['name' => $definition['name']],
                $definition
            );
        }
    }
}
