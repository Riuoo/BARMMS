<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $rejectionReason;
    public $isDuplicate;

    public function __construct($rejectionReason, $isDuplicate = false)
    {
        $this->rejectionReason = $rejectionReason;
        $this->isDuplicate = $isDuplicate;
    }

    public function build()
    {
        return $this->view('emails.account-rejected')
                    ->subject('Account Request Rejected')
                    ->with([
                        'rejectionReason' => $this->rejectionReason,
                        'isDuplicate' => $this->isDuplicate,
                    ]);
    }
}
