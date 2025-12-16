@component('mail::message')
# New Barangay Activity: {{ $activity->title }}

You are invited to participate in a new barangay activity.

**Activity:** {{ $activity->title }}  
**Category:** {{ $activity->category }}  
**Date:** {{ optional($activity->completion_date ?? $activity->start_date)->format('F d, Y') }}  
**Location:** {{ $activity->location ?? 'TBA' }}

@if($activity->audience_scope === 'purok' && $activity->audience_purok)
This activity is primarily intended for residents of **Purok {{ $activity->audience_purok }}**.
@else
This activity is open to **all residents**.
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent


