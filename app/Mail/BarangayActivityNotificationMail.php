<?php

namespace App\Mail;

use App\Models\AccomplishedProject;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BarangayActivityNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public AccomplishedProject $activity;

    public function __construct(AccomplishedProject $activity)
    {
        $this->activity = $activity;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Barangay Activity: ' . $this->activity->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.barangay-activity-notification',
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


