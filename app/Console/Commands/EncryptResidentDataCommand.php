<?php

namespace App\Console\Commands;

use App\Models\Residents;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EncryptResidentDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'residents:encrypt-data 
                            {--dry-run : Run without making changes to see what would be encrypted}
                            {--force : Force encryption even if data appears to be encrypted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt sensitive resident data fields (contact_number, emergency contacts) for existing records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        $this->info('Starting encryption of resident sensitive data...');
        $this->newLine();

        // Get all residents
        $residents = Residents::all();
        $total = $residents->count();
        $processed = 0;
        $encrypted = 0;
        $skipped = 0;
        $errors = 0;

        $this->info("Found {$total} resident(s) to process.");
        $this->newLine();

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($residents as $resident) {
            try {
                $needsUpdate = false;
                $updates = [];

                // Check and encrypt contact_number
                if (!empty($resident->getRawOriginal('contact_number'))) {
                    $rawContact = $resident->getRawOriginal('contact_number');
                    if ($force || !$this->isEncrypted($rawContact)) {
                        try {
                            Crypt::decryptString($rawContact);
                            // Already encrypted, skip unless force
                        } catch (\Exception $e) {
                            // Not encrypted, need to encrypt
                            $updates['contact_number'] = Crypt::encryptString($rawContact);
                            $needsUpdate = true;
                        }
                    }
                }

                // Check and encrypt emergency_contact_name
                if (!empty($resident->getRawOriginal('emergency_contact_name'))) {
                    $rawName = $resident->getRawOriginal('emergency_contact_name');
                    if ($force || !$this->isEncrypted($rawName)) {
                        try {
                            Crypt::decryptString($rawName);
                            // Already encrypted
                        } catch (\Exception $e) {
                            // Not encrypted
                            $updates['emergency_contact_name'] = Crypt::encryptString($rawName);
                            $needsUpdate = true;
                        }
                    }
                }

                // Check and encrypt emergency_contact_number
                if (!empty($resident->getRawOriginal('emergency_contact_number'))) {
                    $rawEmergencyContact = $resident->getRawOriginal('emergency_contact_number');
                    if ($force || !$this->isEncrypted($rawEmergencyContact)) {
                        try {
                            Crypt::decryptString($rawEmergencyContact);
                            // Already encrypted
                        } catch (\Exception $e) {
                            // Not encrypted
                            $updates['emergency_contact_number'] = Crypt::encryptString($rawEmergencyContact);
                            $needsUpdate = true;
                        }
                    }
                }

                // Check and encrypt emergency_contact_relationship
                if (!empty($resident->getRawOriginal('emergency_contact_relationship'))) {
                    $rawRelationship = $resident->getRawOriginal('emergency_contact_relationship');
                    if ($force || !$this->isEncrypted($rawRelationship)) {
                        try {
                            Crypt::decryptString($rawRelationship);
                            // Already encrypted
                        } catch (\Exception $e) {
                            // Not encrypted
                            $updates['emergency_contact_relationship'] = Crypt::encryptString($rawRelationship);
                            $needsUpdate = true;
                        }
                    }
                }

                if ($needsUpdate && !$dryRun) {
                    // Update using raw DB query to bypass model accessors/mutators
                    DB::table('residents')
                        ->where('id', $resident->id)
                        ->update($updates);
                    $encrypted++;
                } elseif ($needsUpdate && $dryRun) {
                    $encrypted++;
                } else {
                    $skipped++;
                }

                $processed++;
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("Error processing resident ID {$resident->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('Encryption process completed!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Total Processed', $processed],
                ['Encrypted', $encrypted],
                ['Skipped (already encrypted)', $skipped],
                ['Errors', $errors],
            ]
        );

        if ($dryRun) {
            $this->warn('This was a dry run. No data was actually encrypted.');
            $this->info('Run without --dry-run to apply encryption.');
        } else {
            $this->info('✅ Resident data encryption completed successfully!');
        }

        return Command::SUCCESS;
    }

    /**
     * Check if a value appears to be encrypted
     * Laravel's encrypted values start with "eyJpdiI6" (base64 encoded JSON)
     */
    private function isEncrypted($value): bool
    {
        if (empty($value) || !is_string($value)) {
            return false;
        }

        // Laravel encrypted strings are base64 encoded and start with specific pattern
        // They're typically longer and contain base64 characters
        if (strlen($value) > 50 && preg_match('/^[A-Za-z0-9+\/]+=*$/', $value)) {
            try {
                // Try to decrypt - if it works, it's encrypted
                Crypt::decryptString($value);
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }
}

