@extends('resident.layout')

@section('title', 'Health Activity Details')

@section('content')
<div class="max-w-4xl mx-auto pt-2">
    <!-- Consolidated Skeleton -->
    <div id="residentActivitySkeleton">
        @include('components.loading.resident-activity-skeleton')
    </div>

    <!-- Real Content Wrapper (hidden initially) -->
    <div id="residentActivityContent" style="display: none;">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $activity->activity_name }}</h1>
                    <p class="text-gray-600">Health Activity Details</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-2">
                    <a href="{{ route('resident.announcements') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back
                    </a>
                    <a href="{{ route('resident.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-home mr-2"></i>
                        Dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Activity Image -->
                @if($activity->image)
                <div class="mb-6">
                    <img src="{{ asset('storage/' . $activity->image) }}" alt="{{ $activity->activity_name }}" class="w-full h-64 object-cover rounded-lg shadow-sm">
                </div>
                @endif

                <!-- Activity Description -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Activity Description</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $activity->description }}</p>
                    </div>
                </div>

                <!-- Activity Objectives -->
                @if($activity->objectives)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Objectives</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $activity->objectives }}</p>
                    </div>
                </div>
                @endif

                <!-- Materials Needed -->
                @if($activity->materials_needed)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Materials Needed</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $activity->materials_needed }}</p>
                    </div>
                </div>
                @endif

                <!-- Activity Outcomes -->
                @if($activity->outcomes)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Outcomes</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $activity->outcomes }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Activity Info Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Information</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Activity Type</label>
                            <p class="text-sm text-gray-900">{{ $activity->activity_type }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($activity->status === 'Completed') bg-green-100 text-green-800
                                @elseif($activity->status === 'Ongoing') bg-blue-100 text-blue-800
                                @elseif($activity->status === 'Planned') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                <i class="fas {{ $activity->status === 'Completed' ? 'fa-check-circle' : ($activity->status === 'Ongoing' ? 'fa-spinner' : 'fa-clock') }} mr-1"></i>
                                {{ $activity->status }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Activity Date</label>
                            <p class="text-sm text-gray-900">{{ optional($activity->activity_date)->format('F d, Y') }}</p>
                        </div>

                        @if($activity->start_time && $activity->end_time)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Time</label>
                            <p class="text-sm text-gray-900">{{ $activity->start_time }} - {{ $activity->end_time }}</p>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Location</label>
                            <p class="text-sm text-gray-900">{{ $activity->location }}</p>
                        </div>

                        @if($activity->organizer)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Organizer</label>
                            <p class="text-sm text-gray-900">{{ $activity->organizer }}</p>
                        </div>
                        @endif

                        @if($activity->target_participants)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Target Participants</label>
                            <p class="text-sm text-gray-900">{{ $activity->target_participants }} people</p>
                        </div>
                        @endif

                        @if($activity->actual_participants)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Actual Participants</label>
                            <p class="text-sm text-gray-900">{{ $activity->actual_participants }} people</p>
                        </div>
                        @endif

                        @if($activity->budget)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Budget</label>
                            <p class="text-sm text-gray-900">₱{{ number_format($activity->budget, 2) }}</p>
                        </div>
                        @endif

                        @if($activity->is_featured)
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-star mr-1"></i>
                                Featured Activity
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Activity Timeline -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Timeline</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-400 rounded-full mr-3"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Activity Created</p>
                                <p class="text-xs text-gray-500">{{ $activity->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="w-3 h-3 {{ $activity->status === 'Completed' ? 'bg-green-400' : ($activity->status === 'Ongoing' ? 'bg-blue-400' : 'bg-yellow-400') }} rounded-full mr-3"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Activity Date</p>
                                <p class="text-xs text-gray-500">{{ optional($activity->activity_date)->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Participation Information -->
                @if($activity->status === 'Planned' && $activity->activity_date >= now())
                <div class="bg-green-50 rounded-lg border border-green-200 p-6">
                    <h3 class="text-lg font-semibold text-green-900 mb-4">Join This Activity</h3>
                    <p class="text-sm text-green-700 mb-4">This health activity is open for participation. Contact the barangay office for more information.</p>
                    <a href="{{ route('resident.request_community_concern') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                        <i class="fas fa-user-plus mr-2"></i>
                        Express Interest
                    </a>
                </div>
                @elseif($activity->status === 'Ongoing')
                <div class="bg-blue-50 rounded-lg border border-blue-200 p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">Activity in Progress</h3>
                    <p class="text-sm text-blue-700 mb-4">This health activity is currently ongoing. Contact the barangay office for participation details.</p>
                    <a href="{{ route('resident.request_community_concern') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-phone mr-2"></i>
                        Contact Barangay
                    </a>
                </div>
                @else
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Completed</h3>
                    <p class="text-sm text-gray-700 mb-4">This health activity has been completed. Thank you for your interest.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const skeleton = document.getElementById('residentActivitySkeleton');
        const content = document.getElementById('residentActivityContent');
        if (skeleton) skeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endsection
