@extends('admin.main.layout')

@section('title', 'Health Center Activity Details')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Health Activity Details</h1>
            <p class="text-gray-600 mt-2">View detailed information about this health center activity</p>
        </div>
        <a href="{{ route('admin.health-center-activities.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition duration-300 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Activities
        </a>
    </div>

    <!-- Activity Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Activity Image -->
            @if($activity->image)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <img src="{{ $activity->image_url }}" 
                     alt="{{ $activity->activity_name }}" 
                     class="w-full h-64 object-cover">
            </div>
            @endif

            <!-- Title and Status -->
            <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $activity->activity_name }}</h2>
                    <div class="flex items-center space-x-3">
                        @if($activity->is_featured)
                            <div class="flex items-center text-yellow-600">
                                <i class="fas fa-star mr-2"></i>
                                <span class="font-medium">Featured Activity</span>
                            </div>
                        @endif
                        @php
                            $statusBadge = match($activity->status) {
                                'Planned' => 'bg-blue-100 text-blue-800',
                                'Ongoing' => 'bg-yellow-100 text-yellow-800',
                                'Completed' => 'bg-green-100 text-green-800',
                                'Cancelled' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusBadge }}">
                            {{ $activity->status }}
                        </span>
                    </div>
                </div>
                <p class="text-gray-700 leading-relaxed">{{ $activity->description }}</p>
            </div>

            <!-- Details -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Activity Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Activity Type</h4>
                        <p class="text-gray-600">{{ $activity->activity_type }}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Date</h4>
                        <p class="text-gray-600">{{ $activity->activity_date->format('F j, Y') }}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Time</h4>
                        <p class="text-gray-600">
                            @if($activity->start_time)
                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $activity->start_time)->format('g:i A') }}
                                @if($activity->end_time)
                                    - {{ \Carbon\Carbon::createFromFormat('H:i:s', $activity->end_time)->format('g:i A') }}
                                @endif
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Location</h4>
                        <p class="text-gray-600">{{ $activity->location ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Organizer</h4>
                        <p class="text-gray-600">{{ $activity->organizer ?? 'N/A' }}</p>
                    </div>
                    @if($activity->budget)
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Budget</h4>
                        <p class="text-gray-600">₱ {{ number_format($activity->budget, 2) }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Objectives and Resources -->
            @if($activity->objectives || $activity->required_resources)
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Objectives & Resources</h3>
                <div class="space-y-4">
                    @if($activity->objectives)
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Objectives</h4>
                        <p class="text-gray-600">{{ $activity->objectives }}</p>
                    </div>
                    @endif
                    @if($activity->required_resources)
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Required Resources</h4>
                        <p class="text-gray-600">{{ $activity->required_resources }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($activity->staff_involved && !$activity->objectives && !$activity->required_resources)
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Staff Involved</h3>
                <p class="text-gray-600">{{ $activity->staff_involved }}</p>
            </div>
            @endif

            @if($activity->notes)
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Additional Notes</h3>
                <p class="text-gray-600">{{ $activity->notes }}</p>
            </div>
            @endif

            @if(!$activity->objectives && !$activity->required_resources && !$activity->staff_involved && !$activity->notes)
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="text-center text-gray-500">
                    <i class="fas fa-info-circle text-2xl mb-2"></i>
                    <p>No additional information recorded for this activity.</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.health-center-activities.edit', $activity->id) }}" 
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-300 flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Activity
                    </a>
                    <form action="{{ route('admin.health-center-activities.toggle-featured', $activity->id) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition duration-300 flex items-center justify-center">
                            <i class="fas fa-star mr-2"></i>
                            {{ $activity->is_featured ? 'Unfeature' : 'Feature' }} Activity
                        </button>
                    </form>
                    <form action="{{ route('admin.health-center-activities.destroy', $activity->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this activity? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition duration-300 flex items-center justify-center">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Activity
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 