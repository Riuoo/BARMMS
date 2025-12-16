@component('mail::message')
# New Health Activity: {{ $activity->activity_name }}

We are pleased to inform you about a new health activity in your barangay.

**Activity:** {{ $activity->activity_name }}  
**Type:** {{ $activity->activity_type }}  
**Date:** {{ optional($activity->activity_date)->format('F d, Y') }}  
@if($activity->start_time && $activity->end_time)
**Time:** {{ $activity->start_time }} - {{ $activity->end_time }}  
@endif
**Location:** {{ $activity->location }}

@if($activity->audience_scope === 'purok' && $activity->audience_purok)
This activity is primarily intended for residents of **Purok {{ $activity->audience_purok }}**.
@else
This activity is open to **all residents**.
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent


