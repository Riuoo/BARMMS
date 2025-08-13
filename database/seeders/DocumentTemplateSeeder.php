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
            'Certificate of Good Moral Character',
            'Certificate of Live Birth',
            'Certificate of Death',
            'Certificate of Marriage',
            'Barangay ID',
            'Certificate of No Pending Case',
            'Certificate of No Derogatory Record',
            'Certificate of First Time Job Seeker',
            'Certificate of Solo Parent',
            'Certificate of Senior Citizen',
            'Certificate of PWD (Person with Disability)',
            'Certificate of Tribal Membership',
            'Certificate of Land Ownership',
            'Certificate of Business Operation',
            'Certificate of Community Tax Certificate',
            'Certificate of No Property',
            'Certificate of Low Income',
            'Certificate of Residency for School',
            'Certificate of Residency for Employment',
            'Certificate of Residency for Loan',
            'Certificate of Residency for Insurance',
            'Certificate of Residency for Travel',
            'Certificate of Residency for Medical',
            'Certificate of Residency for Legal',
            'Certificate of Residency for Government Service',
            'Certificate of Residency for SSS/GSIS',
            'Certificate of Residency for Pag-IBIG',
            'Certificate of Residency for PhilHealth',
            'Certificate of Residency for BIR',
            'Certificate of Residency for DTI',
            'Certificate of Residency for SEC',
            'Certificate of Residency for Bank',
            'Certificate of Residency for Credit Card',
            'Certificate of Residency for Housing Loan',
            'Certificate of Residency for Car Loan',
            'Certificate of Residency for Business Loan',
            'Certificate of Residency for Personal Loan',
            'Certificate of Residency for Investment',
            'Certificate of Residency for Insurance Claim',
            'Certificate of Residency for Medical Claim',
            'Certificate of Residency for Legal Claim',
            'Certificate of Residency for Government Claim',
            'Certificate of Residency for SSS Claim',
            'Certificate of Residency for GSIS Claim',
            'Certificate of Residency for Pag-IBIG Claim',
            'Certificate of Residency for PhilHealth Claim',
            'Certificate of Residency for BIR Claim',
            'Certificate of Residency for DTI Claim',
            'Certificate of Residency for SEC Claim',
            'Certificate of Residency for Bank Claim',
            'Certificate of Residency for Credit Card Claim',
            'Certificate of Residency for Housing Loan Claim',
            'Certificate of Residency for Car Loan Claim',
            'Certificate of Residency for Business Loan Claim',
            'Certificate of Residency for Personal Loan Claim',
            'Certificate of Residency for Investment Claim',
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