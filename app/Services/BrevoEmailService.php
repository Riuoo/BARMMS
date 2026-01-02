<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoEmailService
{
    protected string $apiKey;
    protected string $fromEmail;
    protected string $fromName;

    public function __construct()
    {
        $this->apiKey = env('BREVO_API_KEY');
        $this->fromEmail = env('MAIL_FROM_ADDRESS', 'no-reply@barmmslowermalinao.app');
        $this->fromName = env('MAIL_FROM_NAME', 'BARMMS');
    }

    /**
     * Send email via Brevo API
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $htmlContent HTML content of the email
     * @param string|null $textContent Plain text content (optional)
     * @return array|false Returns response array on success, false on failure
     */
    public function sendEmail(string $to, string $subject, string $htmlContent, ?string $textContent = null)
    {
        if (empty($this->apiKey)) {
            try {
                Log::error('BREVO_API_KEY is not set');
            } catch (\Exception $e) {
                // Ignore logging errors
            }
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'accept' => 'application/json',
                    'api-key' => $this->apiKey,
                    'content-type' => 'application/json',
                ])->post('https://api.brevo.com/v3/smtp/email', [
                    'sender' => [
                        'name' => $this->fromName,
                        'email' => $this->fromEmail,
                    ],
                    'to' => [
                        [
                            'email' => $to,
                        ],
                    ],
                    'subject' => $subject,
                    'htmlContent' => $htmlContent,
                    'textContent' => $textContent ?? strip_tags($htmlContent),
                ]);

            if ($response->successful()) {
                try {
                    Log::info('Brevo email sent successfully to: ' . $to);
                } catch (\Exception $e) {
                    // Ignore logging errors
                }
                return $response->json();
            } else {
                try {
                    Log::error('Failed to send Brevo email: ' . $response->body());
                } catch (\Exception $e) {
                    // Ignore logging errors
                }
                return false;
            }
        } catch (\Exception $e) {
            try {
                Log::error('Error sending Brevo email: ' . $e->getMessage());
            } catch (\Exception $logError) {
                // Ignore logging errors
            }
            return false;
        }
    }

    /**
     * Send email to multiple recipients
     *
     * @param array $to Array of recipient email addresses
     * @param string $subject Email subject
     * @param string $htmlContent HTML content of the email
     * @param string|null $textContent Plain text content (optional)
     * @return array|false Returns response array on success, false on failure
     */
    public function sendBulkEmail(array $to, string $subject, string $htmlContent, ?string $textContent = null)
    {
        if (empty($this->apiKey)) {
            try {
                Log::error('BREVO_API_KEY is not set');
            } catch (\Exception $e) {
                // Ignore logging errors
            }
            return false;
        }

        $recipients = array_map(function($email) {
            return ['email' => $email];
        }, $to);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'accept' => 'application/json',
                    'api-key' => $this->apiKey,
                    'content-type' => 'application/json',
                ])->post('https://api.brevo.com/v3/smtp/email', [
                    'sender' => [
                        'name' => $this->fromName,
                        'email' => $this->fromEmail,
                    ],
                    'to' => $recipients,
                    'subject' => $subject,
                    'htmlContent' => $htmlContent,
                    'textContent' => $textContent ?? strip_tags($htmlContent),
                ]);

            if ($response->successful()) {
                try {
                    Log::info('Brevo bulk email sent successfully to ' . count($to) . ' recipients');
                } catch (\Exception $e) {
                    // Ignore logging errors
                }
                return $response->json();
            } else {
                try {
                    Log::error('Failed to send Brevo bulk email: ' . $response->body());
                } catch (\Exception $e) {
                    // Ignore logging errors
                }
                return false;
            }
        } catch (\Exception $e) {
            try {
                Log::error('Error sending Brevo bulk email: ' . $e->getMessage());
            } catch (\Exception $logError) {
                // Ignore logging errors
            }
            return false;
        }
    }
}

