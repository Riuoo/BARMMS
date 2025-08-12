<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $project->title }} - Community Project</title>
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
                <h1 class="text-3xl font-bold text-gray-900">{{ $project->title }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $project->category_color }}">{{ $project->category }}</span>
            </div>
            @if($project->image_url)
                <img src="{{ $project->image_url }}" alt="{{ $project->title }}" class="w-full h-64 object-cover rounded-xl mb-6">
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-1">Location</h4>
                    <p class="text-gray-600">{{ $project->location ?? 'N/A' }}</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-1">Budget</h4>
                    <p class="text-gray-600">{{ $project->formatted_budget }}</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-1">Start Date</h4>
                    <p class="text-gray-600">{{ optional($project->start_date)->format('F j, Y') }}</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-1">Completion Date</h4>
                    <p class="text-gray-600">{{ optional($project->completion_date)->format('F j, Y') }}</p>
                </div>
            </div>
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Description</h3>
                    <p class="text-gray-700">{{ $project->description }}</p>
                </div>
                @if($project->impact)
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Impact</h3>
                    <p class="text-gray-700">{{ $project->impact }}</p>
                </div>
                @endif
                @if($project->beneficiaries)
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Beneficiaries</h3>
                    <p class="text-gray-700">{{ $project->beneficiaries }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>

