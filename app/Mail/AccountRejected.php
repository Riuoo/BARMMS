<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountRejected extends Mailable
{
    use Queueable, SerializesModels;

    public string $rejectionReason;
    public bool $isDuplicate;

    public function __construct(string $rejectionReason, bool $isDuplicate = false)
    {
        $this->rejectionReason = $rejectionReason;
        $this->isDuplicate = $isDuplicate;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account Request Rejected',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.account-rejected',
            with: [
                'rejectionReason' => $this->rejectionReason,
                'isDuplicate' => $this->isDuplicate,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
