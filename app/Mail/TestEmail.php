<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $message;

    public function __construct(string $message = 'Hello from Laravel via Brevo!')
    {
        $this->message = $message;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Test Email from BARMMS',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.test-email',
            with: [
                'message' => $this->message,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

