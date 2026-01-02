<x-mail::message>
# Test Email

This is a test email from the BARMMS system.

{{ $message }}

If you received this email, it means your email configuration is working correctly.

Thanks,  
{{ config('app.name') }}
</x-mail::message>

