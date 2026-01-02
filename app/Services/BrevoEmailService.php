<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Markdown;

class BrevoEmailService
{
    protected string $apiKey;
    protected string $fromEmail;
    protected string $fromName;

    public function __construct()
    {
        $this->apiKey = env('BREVO_API_KEY', '');
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
            // Prepare the request payload
            $payload = [
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
            ];
            
            // Log the request (without sensitive data)
            try {
                Log::debug('Sending email via Brevo API', [
                    'to' => $to,
                    'subject' => $subject,
                    'from_email' => $this->fromEmail,
                    'html_content_length' => strlen($htmlContent),
                    'text_content_length' => strlen($textContent ?? ''),
                ]);
            } catch (\Exception $e) {
                // Ignore logging errors
            }
            
            $response = Http::timeout(10)
                ->withHeaders([
                    'accept' => 'application/json',
                    'api-key' => $this->apiKey,
                    'content-type' => 'application/json',
                ])->post('https://api.brevo.com/v3/smtp/email', $payload);

            if ($response->successful()) {
                try {
                    Log::info('Brevo email sent successfully', [
                        'to' => $to,
                        'subject' => $subject,
                        'response' => $response->json(),
                    ]);
                } catch (\Exception $e) {
                    // Ignore logging errors
                }
                return $response->json();
            } else {
                try {
                    $statusCode = $response->status();
                    $responseBody = $response->body();
                    $responseJson = $response->json();
                    
                    Log::error('Failed to send Brevo email', [
                        'to' => $to,
                        'subject' => $subject,
                        'from_email' => $this->fromEmail,
                        'status_code' => $statusCode,
                        'response_body' => $responseBody,
                        'response_json' => $responseJson,
                        'api_key_set' => !empty($this->apiKey),
                        'api_key_length' => strlen($this->apiKey ?? ''),
                    ]);
                } catch (\Exception $e) {
                    // Ignore logging errors
                }
                return false;
            }
        } catch (\Exception $e) {
            try {
                Log::error('Exception sending Brevo email', [
                    'error' => $e->getMessage(),
                    'to' => $to,
                    'subject' => $subject,
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => substr($e->getTraceAsString(), 0, 500),
                ]);
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

    /**
     * Send email using a markdown view (similar to Laravel's Mail facade)
     * Converts markdown views to simple HTML emails for reliable Brevo API delivery
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $view Path to the markdown view (e.g., 'emails.account-approved')
     * @param array $data Data to pass to the view
     * @return array|false Returns response array on success, false on failure
     */
    public function sendMarkdownEmail(string $to, string $subject, string $view, array $data = [])
    {
        try {
            // Convert markdown view to simple HTML (like the test email that works)
            $htmlContent = $this->renderEmailToHtml($view, $data);
            
            // Validate content
            if (empty($htmlContent) || empty(trim($htmlContent))) {
                throw new \Exception('Rendered email content is empty for view: ' . $view . '. This may indicate a route/URL generation issue on DigitalOcean.');
            }
            
            // Generate plain text version
            $textContent = strip_tags($htmlContent);
            
            // Ensure we have text content
            if (empty(trim($textContent))) {
                $textContent = 'Email from ' . $this->fromName;
            }
            
            return $this->sendEmail($to, $subject, trim($htmlContent), $textContent);
        } catch (\Exception $e) {
            try {
                Log::error('Error rendering email view', [
                    'error' => $e->getMessage(),
                    'view' => $view,
                    'data_keys' => array_keys($data),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => substr($e->getTraceAsString(), 0, 1000),
                    'app_url' => config('app.url', 'not set'),
                    'env_app_url' => env('APP_URL', 'not set'),
                ]);
            } catch (\Exception $logError) {
                // Ignore logging errors
            }
            return false;
        }
    }
    
    /**
     * Safely generate URL with fallback for queue workers
     */
    protected function safeUrl(string $path): string
    {
        try {
            return url($path);
        } catch (\Exception $e) {
            // Fallback: construct URL manually using APP_URL
            $appUrl = config('app.url', env('APP_URL', 'http://localhost'));
            $appUrl = rtrim($appUrl, '/');
            $path = ltrim($path, '/');
            return $appUrl . '/' . $path;
        }
    }
    
    /**
     * Safely generate route URL with fallback for queue workers
     */
    protected function safeRoute(string $name, array $parameters = []): string
    {
        try {
            return route($name, $parameters);
        } catch (\Exception $e) {
            // Log that we're using fallback (common on DigitalOcean queue workers)
            try {
                Log::debug('Using route fallback (route() failed)', [
                    'route' => $name,
                    'error' => $e->getMessage(),
                    'parameters' => array_keys($parameters),
                ]);
            } catch (\Exception $logError) {
                // Ignore logging errors
            }
            
            // Fallback: construct URL manually based on route name
            $appUrl = config('app.url', env('APP_URL', 'http://localhost'));
            $appUrl = rtrim($appUrl, '/');
            
            // Map known routes to their paths
            $routeMap = [
                'password.reset' => '/reset-password/{token}',
                'register.form' => '/register/{token}',
            ];
            
            if (isset($routeMap[$name])) {
                $path = $routeMap[$name];
                $queryParams = [];
                
                // Replace path parameters
                foreach ($parameters as $key => $value) {
                    if (strpos($path, '{' . $key . '}') !== false) {
                        $path = str_replace('{' . $key . '}', urlencode($value), $path);
                    } else {
                        // Parameters not in path go to query string
                        $queryParams[$key] = $value;
                    }
                }
                
                // Add query string if there are additional parameters
                if (!empty($queryParams)) {
                    $path .= '?' . http_build_query($queryParams);
                }
                
                return $appUrl . $path;
            }
            
            // If route not in map, try to construct from name
            try {
                Log::warning('Route not in fallback map, using default path', ['route' => $name]);
            } catch (\Exception $logError) {
                // Ignore logging errors
            }
            return $appUrl . '/' . str_replace('.', '/', $name);
        }
    }
    
    /**
     * Render email view to HTML - converts Laravel mail markdown to simple HTML
     * This ensures reliable delivery via Brevo API (like the test email)
     */
    protected function renderEmailToHtml(string $view, array $data): string
    {
        // Get app name for use in emails
        $appName = config('app.name', 'BARMMS');
        
        // Map views to HTML templates
        switch ($view) {
            case 'emails.account-approved':
                $token = $data['token'] ?? '';
                $registerUrl = $this->safeRoute('register.form', ['token' => $token]);
                return $this->getEmailTemplate('Account Approved', "
                    <h1>Account Approved</h1>
                    <p>Dear User,</p>
                    <p>Your account request has been approved!</p>
                    <p>You can now complete your registration by clicking the button below:</p>
                    <p style='text-align: center; margin: 30px 0;'>
                        <a href='{$registerUrl}' style='display: inline-block; padding: 12px 24px; background-color: #10b981; color: white; text-decoration: none; border-radius: 4px;'>Sign Up Now</a>
                    </p>
                    <p>If you have any questions, please contact the barangay office for assistance.</p>
                    <p>Thanks,<br>{$appName}</p>
                ");
                
            case 'emails.account-rejected':
                $rejectionReason = htmlspecialchars($data['rejectionReason'] ?? 'No reason provided');
                $isDuplicate = $data['isDuplicate'] ?? false;
                $duplicateNote = $isDuplicate ? "
                    <div style='background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;'>
                        <strong>Note:</strong> It is recommended to visit the barangay office if you have forgotten your previous registration.
                    </div>
                " : '';
                return $this->getEmailTemplate('Account Request Rejected', "
                    <h1>Account Request Rejected</h1>
                    <p>Dear User,</p>
                    <p>We regret to inform you that your account request has been rejected.</p>
                    <h2>Rejection Reason</h2>
                    <p>{$rejectionReason}</p>
                    {$duplicateNote}
                    <p>If you believe this is an error or have any questions, please contact the barangay office for assistance.</p>
                    <p>Thanks,<br>{$appName}</p>
                ");
                
            case 'emails.password-reset':
                $token = $data['token'] ?? '';
                $email = $data['email'] ?? '';
                // Compute resetUrl and expires (same as PasswordResetMail does)
                $resetUrl = $this->safeRoute('password.reset', ['token' => $token, 'email' => $email]);
                // Safely get expiration time
                try {
                    $expires = htmlspecialchars(now()->addHours(1)->format('g:i A'));
                } catch (\Exception $e) {
                    // Fallback if now() fails
                    $expires = '1 hour from now';
                }
                return $this->getEmailTemplate('Password Reset Request', "
                    <h1>Password Reset Request</h1>
                    <p>Click the button below to reset your password:</p>
                    <p style='text-align: center; margin: 30px 0;'>
                        <a href='{$resetUrl}' style='display: inline-block; padding: 12px 24px; background-color: #10b981; color: white; text-decoration: none; border-radius: 4px;'>Reset Password</a>
                    </p>
                    <p>This link will expire at {$expires} (1 hour from now).</p>
                    <p>If you didn't request a password reset, you can safely ignore this email.</p>
                    <p>Thanks,<br>{$appName}</p>
                ");
                
            case 'emails.blotter-summon-ready':
                $residentName = htmlspecialchars($data['residentName'] ?? 'Resident');
                $blotterType = htmlspecialchars($data['blotterTypeOrRecipient'] ?? '');
                $summonDate = htmlspecialchars($data['summonDate'] ?? 'N/A');
                return $this->getEmailTemplate('Blotter Report Approved', "
                    <h1>Hello {$residentName},</h1>
                    <p>Your blotter report regarding \"{$blotterType}\" has been approved by the barangay.</p>
                    <p>A hearing/summon is scheduled for: <strong>{$summonDate}</strong>.</p>
                    <p>Please check your account for more details and bring any necessary documents to the barangay office on the scheduled date.</p>
                    <p>Thank you for helping keep our community safe.</p>
                    <p>Best regards,<br>{$appName}</p>
                ");
                
            case 'emails.document-ready':
                $residentName = htmlspecialchars($data['residentName'] ?? 'Resident');
                $documentType = htmlspecialchars($data['documentType'] ?? 'document');
                return $this->getEmailTemplate('Document Ready for Pickup', "
                    <h1>Hello {$residentName},</h1>
                    <p>Your requested document ({$documentType}) has been approved and is now ready for pickup at the barangay office.</p>
                    <p>Please bring a valid ID when claiming your document.</p>
                    <p>Thanks,<br>{$appName}</p>
                ");
                
            case 'emails.health-activity-notification':
                $activity = $data['activity'] ?? null;
                if (!$activity) {
                    return '';
                }
                $activityName = htmlspecialchars($activity->activity_name ?? '');
                $activityType = htmlspecialchars($activity->activity_type ?? '');
                $activityDate = $activity->activity_date ? $activity->activity_date->format('F d, Y') : 'TBA';
                $timeInfo = ($activity->start_time && $activity->end_time) 
                    ? "<p><strong>Time:</strong> {$activity->start_time} - {$activity->end_time}</p>" 
                    : '';
                $location = htmlspecialchars($activity->location ?? 'TBA');
                $audienceInfo = ($activity->audience_scope === 'purok' && $activity->audience_purok)
                    ? "<p>This activity is primarily intended for residents of <strong>Purok {$activity->audience_purok}</strong>.</p>"
                    : "<p>This activity is open to <strong>all residents</strong>.</p>";
                return $this->getEmailTemplate('New Health Activity: ' . $activityName, "
                    <h1>New Health Activity: {$activityName}</h1>
                    <p>We are pleased to inform you about a new health activity in your barangay.</p>
                    <p><strong>Activity:</strong> {$activityName}</p>
                    <p><strong>Type:</strong> {$activityType}</p>
                    <p><strong>Date:</strong> {$activityDate}</p>
                    {$timeInfo}
                    <p><strong>Location:</strong> {$location}</p>
                    {$audienceInfo}
                    <p>Thanks,<br>{$appName}</p>
                ");
                
            case 'emails.barangay-activity-notification':
                $activity = $data['activity'] ?? null;
                if (!$activity) {
                    return '';
                }
                $title = htmlspecialchars($activity->title ?? '');
                $category = htmlspecialchars($activity->category ?? '');
                $date = ($activity->completion_date ?? $activity->start_date) 
                    ? ($activity->completion_date ?? $activity->start_date)->format('F d, Y') 
                    : 'TBA';
                $location = htmlspecialchars($activity->location ?? 'TBA');
                $audienceInfo = ($activity->audience_scope === 'purok' && $activity->audience_purok)
                    ? "<p>This activity is primarily intended for residents of <strong>Purok {$activity->audience_purok}</strong>.</p>"
                    : "<p>This activity is open to <strong>all residents</strong>.</p>";
                return $this->getEmailTemplate('New Barangay Activity: ' . $title, "
                    <h1>New Barangay Activity: {$title}</h1>
                    <p>You are invited to participate in a new barangay activity.</p>
                    <p><strong>Activity:</strong> {$title}</p>
                    <p><strong>Category:</strong> {$category}</p>
                    <p><strong>Date:</strong> {$date}</p>
                    <p><strong>Location:</strong> {$location}</p>
                    {$audienceInfo}
                    <p>Thanks,<br>{$appName}</p>
                ");
                
            default:
                // Fallback: try View::make() for any unmapped views
                try {
                    return View::make($view, $data)->render();
                } catch (\Exception $e) {
                    Log::error('Unknown email view and View::make() failed: ' . $view . ' - ' . $e->getMessage());
                    throw new \Exception('Unknown email view: ' . $view);
                }
        }
    }
    
    /**
     * Get email HTML template with proper styling
     */
    protected function getEmailTemplate(string $title, string $content): string
    {
        return "<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>{$title}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #111827;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .email-header {
            background-color: #10b981;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .email-body {
            padding: 30px;
            background-color: #ffffff;
        }
        .email-body h1 {
            color: #10b981;
            margin-top: 0;
        }
        .email-body h2 {
            color: #111827;
            margin-top: 20px;
        }
        .email-body p {
            margin: 15px 0;
            color: #111827;
        }
        .email-footer {
            background-color: #f3f4f6;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        a {
            color: #10b981;
            text-decoration: none;
        }
        a:hover {
            color: #059669;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='email-header'>
            <h1 style='margin: 0; color: #ffffff;'>{$title}</h1>
        </div>
        <div class='email-body'>
            {$content}
        </div>
        <div class='email-footer'>
            <p style='margin: 0;'>This is an automated message from " . config('app.name', 'BARMMS') . "</p>
        </div>
    </div>
</body>
</html>";
    }
    
    /**
     * Queue an email to be sent (for compatibility with Mail::to()->queue() pattern)
     * This dispatches a job that will send the email via Brevo API
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $view Path to the markdown view (e.g., 'emails.account-approved')
     * @param array $data Data to pass to the view
     * @return void
     */
    public function queueEmail(string $to, string $subject, string $view, array $data = [])
    {
        // Dispatch a job to send the email asynchronously
        \App\Jobs\SendBrevoEmailJob::dispatch($to, $subject, $view, $data);
    }
}

