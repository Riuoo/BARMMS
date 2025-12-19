<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentTemplate;

class DocumentTemplateSeeder extends Seeder
{
    public function run()
    {
        // Seed only core barangay-issued document templates
        $documentTypes = [
            'Certificate of Residency',
            'Certificate of Indigency',
            'Certificate of Low Income',
            'Barangay Clearance',
            'Certification',
        ];

        foreach ($documentTypes as $type) {
            $default = DocumentTemplate::getDefaultTemplate($type);
            if ($default) {
                // Upsert to keep seeding idempotent
                DocumentTemplate::updateOrCreate(
                    ['document_type' => $type],
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