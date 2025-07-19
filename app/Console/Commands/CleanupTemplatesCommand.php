<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupTemplatesCommand extends Command
{
    protected $signature = 'templates:cleanup';
    protected $description = 'Clean up old blade templates';

    public function handle()
    {
        $this->info('Starting template cleanup...');

        $templateFiles = [
            'barangay_clearance_pdf',
            'certificate_of_residency_pdf',
            'certificate_of_indigency_pdf',
            'business_permit_pdf',
            'certificate_of_good_moral_character_pdf',
            'certificate_of_live_birth_pdf',
            'certificate_of_death_pdf',
            'certificate_of_marriage_pdf',
            'barangay_id_pdf',
            'certificate_of_no_pending_case_pdf',
            'certificate_of_no_derogatory_record_pdf',
            'document_request_pdf'
        ];

        foreach ($templateFiles as $file) {
            $viewPath = resource_path("views/admin/pdfs/{$file}.blade.php");
            
            if (File::exists($viewPath)) {
                // Check if backup exists
                $backupPath = resource_path("views/admin/pdfs/backup_{$file}.blade.php");
                if (!File::exists($backupPath)) {
                    $this->warn("No backup found for {$file}, creating backup before deletion...");
                    File::copy($viewPath, $backupPath);
                }

                // Delete the original file
                File::delete($viewPath);
                $this->info("Deleted {$file}.blade.php");
            } else {
                $this->warn("Template file not found: {$file}.blade.php");
            }
        }

        $this->info('Template cleanup completed!');
        $this->info('Note: Backup files are stored in resources/views/admin/pdfs/ with "backup_" prefix.');
        $this->info('You can safely delete the backup files once you verify everything is working correctly.');
    }
} 