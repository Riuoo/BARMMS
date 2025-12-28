@component('mail::message')
# Account Request Rejected

Dear User,

We regret to inform you that your account request has been rejected.

## Rejection Reason

{{ $rejectionReason }}

@if($isDuplicate)
@component('mail::panel')
**Note:** It is recommended to visit the barangay office if you have forgotten your previous registration.
@endcomponent
@endif

If you believe this is an error or have any questions, please contact the barangay office for assistance.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
