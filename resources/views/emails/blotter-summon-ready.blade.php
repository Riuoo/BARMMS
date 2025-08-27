@component('mail::message')
# Hello {{ $residentName }},

Your blotter report regarding "{{ $blotterTypeOrRecipient }}" has been approved by the barangay.
A hearing/summon is scheduled for: **{{ $summonDate }}**.

Please check your account for more details and bring any necessary documents to the barangay office on the scheduled date.

Thank you for helping keep our community safe.

Best regards,
{{ config('app.name') }}
@endcomponent
