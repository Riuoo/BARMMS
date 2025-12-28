<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountApproved extends Mailable
{
    use Queueable, SerializesModels;

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account Approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.account-approved',
            with: [
                'token' => $this->token,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}