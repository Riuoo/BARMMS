<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentTemplate;

class DocumentTemplateSeeder extends Seeder
{
    public function run()
    {
        $documentTypes = [
            'Barangay Clearance',
            'Certificate of Residency',
            'Certificate of Indigency',
            'Business Permit',
            'Certificate of Good Moral Character',
            'Certificate of Live Birth',
            'Certificate of Death',
            'Certificate of Marriage',
            'Barangay ID',
            'Certificate of No Pending Case',
            'Certificate of No Derogatory Record'
        ];

        foreach ($documentTypes as $type) {
            $default = DocumentTemplate::getDefaultTemplate($type);
            if ($default) {
                DocumentTemplate::create([
                    'document_type' => $type,
                    'header_content' => $default['header_content'],
                    'body_content' => $default['body_content'],
                    'footer_content' => $default['footer_content'],
                    'placeholders' => $default['placeholders'],
                    'is_active' => true
                ]);
            }
        }
    }
} 