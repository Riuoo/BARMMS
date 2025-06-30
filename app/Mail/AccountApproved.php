<?php
   namespace App\Mail;

   use Illuminate\Bus\Queueable;
   use Illuminate\Mail\Mailable;
   use Illuminate\Queue\SerializesModels;

   class AccountApproved extends Mailable
   {
       use Queueable, SerializesModels;

       public $token;

       public function __construct($token)
       {
           $this->token = $token;
       }

       public function build()
       {
           return $this->view('emails.account-approved')
                       ->subject('Account Approved')
                       ->with(['token' => $this->token]);
       }
   }