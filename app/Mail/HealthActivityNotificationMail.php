<?php

namespace App\Mail;

use App\Models\HealthCenterActivity;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HealthActivityNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public HealthCenterActivity $activity;

    public function __construct(HealthCenterActivity $activity)
    {
        $this->activity = $activity;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Health Activity: ' . $this->activity->activity_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.health-activity-notification',
            with: [
                'activity' => $this->activity,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}


