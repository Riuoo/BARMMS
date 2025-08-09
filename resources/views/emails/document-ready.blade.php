@component('mail::message')
# Hello {{ $residentName }},

Your requested document ({{ $documentType }}) has been approved and is now ready for pickup at the barangay office.

Please bring a valid ID when claiming your document.

Thanks,
{{ config('app.name') }}
@endcomponent


