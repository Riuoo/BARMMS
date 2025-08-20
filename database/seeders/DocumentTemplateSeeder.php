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
            'Barangay Clearance',
            'Certificate of Residency',
            'Certificate of Indigency',
            'Certificate of Good Moral Character',
            'Barangay ID',
            'Certificate of No Pending Case',
            'Certificate of No Derogatory Record',
            'Business Permit',
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