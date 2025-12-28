<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlotterTemplate;

class BlotterTemplateSeeder extends Seeder
{
    public function run()
    {
        // Seed blotter templates
        $templateTypes = [
            'Summons',
            'Resolution',
        ];

        foreach ($templateTypes as $type) {
            $default = BlotterTemplate::getDefaultTemplate($type);
            if ($default) {
                // Upsert to keep seeding idempotent
                BlotterTemplate::updateOrCreate(
                    ['template_type' => $type],
                    [
                        'header_content' => $default['header_content'],
                        'body_content' => $default['body_content'],
                        'footer_content' => $default['footer_content'],
                        'placeholders' => $default['placeholders'],
                        'is_active' => true
                    ]
                );
            }
        }
    }
}
