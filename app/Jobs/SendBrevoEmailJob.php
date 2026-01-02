<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\BrevoEmailService;
use Illuminate\Support\Facades\Log;

class SendBrevoEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // Retry up to 3 times
    public $timeout = 30; // 30 second timeout per attempt

    public string $to;
    public string $subject;
    public string $view;
    public array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(string $to, string $subject, string $view, array $data = [])
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->view = $view;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(BrevoEmailService $emailService): void
    {
        try {
            $result = $emailService->sendMarkdownEmail($this->to, $this->subject, $this->view, $this->data);
            
            if ($result === false) {
                try {
                    Log::error('Failed to send queued Brevo email', [
                        'to' => $this->to,
                        'subject' => $this->subject,
                        'view' => $this->view,
                        'data_keys' => array_keys($this->data),
                        'attempt' => $this->attempts(),
                    ]);
                } catch (\Exception $e) {
                    // Ignore logging errors
                }
                // Throw exception to trigger retry
                throw new \Exception('Failed to send email via Brevo API. Check logs for details.');
            }
        } catch (\Exception $e) {
            try {
                Log::error('Error in SendBrevoEmailJob', [
                    'message' => $e->getMessage(),
                    'to' => $this->to,
                    'subject' => $this->subject,
                    'view' => $this->view,
                    'attempt' => $this->attempts(),
                    'trace' => substr($e->getTraceAsString(), 0, 500),
                ]);
            } catch (\Exception $logError) {
                // Ignore logging errors
            }
            throw $e; // Re-throw to trigger retry mechanism
        }
    }
}

