<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $activity->activity_name }} - Health Activity</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
        <a href="{{ route('public.accomplishments') }}" class="inline-flex items-center text-green-700 hover:text-green-800 mb-4">
            <i class="fas fa-arrow-left mr-2"></i> Back to Accomplishments
        </a>
        <div class="bg-white rounded-2xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-3xl font-bold text-gray-900">{{ $activity->activity_name }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $activity->activity_type }}</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-1">Date</h4>
                    <p class="text-gray-600">{{ optional($activity->activity_date)->format('F j, Y') }}</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-1">Time</h4>
                    <p class="text-gray-600">
                        @if($activity->start_time)
                            {{ optional($activity->start_time)->format('g:i A') }}
                            @if($activity->end_time)
                                - {{ optional($activity->end_time)->format('g:i A') }}
                            @endif
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-1">Location</h4>
                    <p class="text-gray-600">{{ $activity->location ?? 'N/A' }}</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-1">Organizer</h4>
                    <p class="text-gray-600">{{ $activity->organizer ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Description</h3>
                    <p class="text-gray-700">{{ $activity->description }}</p>
                </div>
                @if($activity->objectives)
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Objectives</h3>
                    <p class="text-gray-700">{{ $activity->objectives }}</p>
                </div>
                @endif
                @if($activity->required_resources)
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Required Resources</h3>
                    <p class="text-gray-700">{{ $activity->required_resources }}</p>
                </div>
                @endif
                @if($activity->notes)
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Additional Notes</h3>
                    <p class="text-gray-700">{{ $activity->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>

