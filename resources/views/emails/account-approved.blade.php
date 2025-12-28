@component('mail::message')
# Account Approved

Dear User,

Your account request has been approved!

You can now complete your registration by clicking the button below:

@component('mail::button', ['url' => url('/register/' . $token)])
Sign Up Now
@endcomponent

If you have any questions, please contact the barangay office for assistance.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
