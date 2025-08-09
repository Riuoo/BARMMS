<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentReadyForPickupMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $residentName;
    public string $documentType;

    public function __construct(string $residentName, string $documentType)
    {
        $this->residentName = $residentName;
        $this->documentType = $documentType;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your requested document is ready for pickup',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.document-ready',
            with: [
                'residentName' => $this->residentName,
                'documentType' => $this->documentType,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}


