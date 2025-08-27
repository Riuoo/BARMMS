<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BlotterSummonReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $residentName;
    public string $blotterTypeOrRecipient;
    public string $summonDate;

    public function __construct(string $residentName, string $blotterTypeOrRecipient, string $summonDate)
    {
        $this->residentName = $residentName;
        $this->blotterTypeOrRecipient = $blotterTypeOrRecipient;
        $this->summonDate = $summonDate;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Blotter Report Has Been Approved – Summon Notice Ready',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.blotter-summon-ready',
            with: [
                'residentName' => $this->residentName,
                'blotterTypeOrRecipient' => $this->blotterTypeOrRecipient,
                'summonDate' => $this->summonDate,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
