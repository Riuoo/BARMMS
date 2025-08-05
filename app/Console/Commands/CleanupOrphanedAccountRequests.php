<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AccountRequest;
use App\Models\Residents;

class CleanupOrphanedAccountRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account-requests:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup orphaned account requests that no longer have corresponding resident accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of orphaned account requests...');

        // Find account requests that have no corresponding resident
        $orphanedRequests = AccountRequest::whereNotIn('email', function ($query) {
            $query->select('email')->from('residents');
        })->get();

        $count = $orphanedRequests->count();

        if ($count === 0) {
            $this->info('No orphaned account requests found.');
            return Command::SUCCESS;
        }

        $this->warn("Found {$count} orphaned account requests.");

        // Show details of orphaned requests
        $this->table(
            ['ID', 'Email', 'Status', 'Created At'],
            $orphanedRequests->map(function ($request) {
                return [
                    $request->id,
                    $request->email,
                    $request->status,
                    $request->created_at->format('Y-m-d H:i:s')
                ];
            })->toArray()
        );

        if ($this->confirm('Do you want to delete these orphaned account requests?')) {
            $deleted = AccountRequest::whereNotIn('email', function ($query) {
                $query->select('email')->from('residents');
            })->delete();

            $this->info("Successfully deleted {$deleted} orphaned account requests.");
        } else {
            $this->info('Cleanup cancelled.');
        }

        return Command::SUCCESS;
    }
}
