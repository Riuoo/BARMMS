<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DocumentTemplate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class MigrateTemplatesCommand extends Command
{
    protected $signature = 'templates:migrate';
    protected $description = 'Migrate blade templates to database templates';

    public function handle()
    {
        $this->info('Starting template migration...');

        $templateFiles = [
            'barangay_clearance_pdf' => 'Barangay Clearance',
            'certificate_of_residency_pdf' => 'Certificate of Residency',
            'certificate_of_indigency_pdf' => 'Certificate of Indigency',
            'business_permit_pdf' => 'Business Permit',
            'certificate_of_good_moral_character_pdf' => 'Certificate of Good Moral Character',
            'certificate_of_live_birth_pdf' => 'Certificate of Live Birth',
            'certificate_of_death_pdf' => 'Certificate of Death',
            'certificate_of_marriage_pdf' => 'Certificate of Marriage',
            'barangay_id_pdf' => 'Barangay ID',
            'certificate_of_no_pending_case_pdf' => 'Certificate of No Pending Case',
            'certificate_of_no_derogatory_record_pdf' => 'Certificate of No Derogatory Record'
        ];

        foreach ($templateFiles as $file => $documentType) {
            $this->info("Processing {$documentType}...");

            try {
                // Check if template already exists in database
                $existingTemplate = DocumentTemplate::where('document_type', $documentType)->first();
                if ($existingTemplate) {
                    $this->warn("Template for {$documentType} already exists in database. Skipping...");
                    continue;
                }

                // Get the blade template content
                $viewPath = resource_path("views/admin/pdfs/{$file}.blade.php");
                if (!File::exists($viewPath)) {
                    $this->warn("Template file not found: {$viewPath}");
                    continue;
                }

                $content = File::get($viewPath);

                // Extract different sections (this is a basic example, adjust based on your blade structure)
                preg_match('/<header.*?>(.*?)<\/header>/s', $content, $headerMatches);
                preg_match('/<main.*?>(.*?)<\/main>/s', $content, $bodyMatches);
                preg_match('/<footer.*?>(.*?)<\/footer>/s', $content, $footerMatches);
                preg_match('/<style.*?>(.*?)<\/style>/s', $content, $cssMatches);

                // Create database template
                $template = new DocumentTemplate();
                $template->document_type = $documentType;
                $template->header_content = $headerMatches[1] ?? '';
                $template->body_content = $bodyMatches[1] ?? $content; // Use full content if no main tag
                $template->footer_content = $footerMatches[1] ?? '';
                $template->custom_css = $cssMatches[1] ?? '';
                $template->placeholders = [
                    'resident_name' => 'Name of the resident',
                    'resident_address' => 'Address of the resident',
                    'civil_status' => 'Civil status of the resident',
                    'purpose' => 'Purpose of the document',
                    'day' => 'Day of issuance',
                    'month' => 'Month of issuance',
                    'year' => 'Year of issuance',
                    'barangay_name' => 'Name of the barangay',
                    'municipality_name' => 'Name of the municipality',
                    'province_name' => 'Name of the province',
                    'official_name' => 'Name of the barangay captain'
                ];
                $template->is_active = true;
                $template->save();

                $this->info("Successfully migrated {$documentType} template to database.");

                // Create backup of the blade file
                $backupPath = resource_path("views/admin/pdfs/backup_{$file}.blade.php");
                File::copy($viewPath, $backupPath);
                $this->info("Created backup of blade template: {$backupPath}");

            } catch (\Exception $e) {
                $this->error("Error processing {$documentType}: " . $e->getMessage());
            }
        }

        $this->info('Template migration completed!');
    }
} 