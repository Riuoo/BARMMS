<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendTestEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $email;
    public string $message;

    /**
     * Create a new job instance.
     */
    public function __construct(string $email, string $message)
    {
        $this->email = $email;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $apiKey = env('BREVO_API_KEY');
        
        if (empty($apiKey)) {
            Log::error('BREVO_API_KEY is not set');
            return;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'accept' => 'application/json',
                    'api-key' => $apiKey,
                    'content-type' => 'application/json',
                ])->post('https://api.brevo.com/v3/smtp/email', [
                    'sender' => [
                        'name' => env('MAIL_FROM_NAME', 'BARMMS'),
                        'email' => env('MAIL_FROM_ADDRESS', 'no-reply@barmmslowermalinao.app'),
                    ],
                    'to' => [
                        [
                            'email' => $this->email,
                        ],
                    ],
                    'subject' => 'Test Email from BARMMS',
                    'htmlContent' => '<html><body><h1>Test Email</h1><p>' . htmlspecialchars($this->message) . '</p><p>If you received this email, it means your Brevo API configuration is working correctly.</p></body></html>',
                    'textContent' => $this->message . "\n\nIf you received this email, it means your Brevo API configuration is working correctly.",
                ]);

            if ($response->successful()) {
                Log::info('Test email sent successfully to: ' . $this->email);
            } else {
                Log::error('Failed to send test email: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Error sending test email: ' . $e->getMessage());
            throw $e;
        }
    }
}

