<x-mail::message>
# Password Reset Request

Click the button below to reset your password:

<x-mail::button :url="$resetUrl">
Reset Password
</x-mail::button>

This link will expire at {{ $expires }} (1 hour from now).

If you didn't request a password reset, you can safely ignore this email.

Thanks,  
{{ config('app.name') }}
</x-mail::message>