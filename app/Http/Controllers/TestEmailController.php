<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;
use Exception;

class TestEmailController extends Controller
{
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $data = [
            'title' => 'Test Email from BARMMS',
            'content' => 'This is a test email sent using Mailtrap!',
        ];

        try {
            Mail::send('emails.test', $data, function ($message) use ($request, $data) {
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                $message->to($request->email);
                $message->subject($data['title']);
            });
            return redirect()->route('test.email.form')->with('success', 'Test email sent successfully!');
        } catch (Exception $e) {
            return "Error sending test email: " . $e->getMessage();
        }
    }
}
