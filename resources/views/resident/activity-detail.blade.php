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
        <!-- Hero / Header -->
        <div class="mb-6 bg-gradient-to-br from-green-50 via-white to-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-2">
                    <div class="flex flex-wrap gap-2 items-center">
                        @if($activity->is_featured)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                            <i class="fas fa-star mr-1"></i> Featured
                        </span>
                        @endif
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if($activity->status === 'Completed') bg-green-100 text-green-800 border border-green-200
                            @elseif($activity->status === 'Ongoing') bg-blue-100 text-blue-800 border border-blue-200
                            @elseif($activity->status === 'Planned') bg-yellow-100 text-yellow-800 border border-yellow-200
                            @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                            <i class="fas {{ $activity->status === 'Completed' ? 'fa-check-circle' : ($activity->status === 'Ongoing' ? 'fa-spinner' : 'fa-clock') }} mr-1"></i>
                            {{ $activity->status }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white text-gray-700 border border-gray-200">
                            <i class="fas fa-heart mr-1 text-pink-500"></i>{{ $activity->activity_type }}
                        </span>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $activity->activity_name }}</h1>
                    <p class="text-gray-600">Health activity overview with schedule, location, and objectives.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('resident.announcements') }}" class="inline-flex items-center px-4 py-2 bg-white text-sm font-medium rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Board
                    </a>
                    <a href="{{ route('resident.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-green-700">
                        <i class="fas fa-home mr-2"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Activity Image / Placeholder -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="relative">
                        <div class="h-64 bg-gradient-to-r from-gray-100 to-gray-50">
                            @if($activity->image)
                            <img src="{{ asset('storage/' . $activity->image) }}" alt="{{ $activity->activity_name }}" class="w-full h-full object-cover">
                            @else
                            <div class="flex h-full items-center justify-center text-gray-400">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-xl"></i>
                                    </div>
                                    <span class="text-sm font-medium">No image provided</span>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="absolute bottom-4 left-4 flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/90 text-gray-800 shadow-sm">
                                <i class="fas fa-map-marker-alt mr-1 text-green-600"></i>{{ $activity->location }}
                            </span>
                            @if($activity->activity_date)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/90 text-gray-800 shadow-sm">
                                <i class="fas fa-calendar-alt mr-1 text-green-600"></i>{{ optional($activity->activity_date)->format('F d, Y') }}
                            </span>
                            @endif
                            @if($activity->start_time && $activity->end_time)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/90 text-gray-800 shadow-sm">
                                <i class="fas fa-clock mr-1 text-green-600"></i>{{ $activity->start_time }} - {{ $activity->end_time }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                            <i class="fas fa-notes-medical"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Activity Overview</h2>
                            <p class="text-sm text-gray-500">What to expect during this activity</p>
                        </div>
                    </div>
                    <p class="text-gray-700 leading-relaxed">{{ $activity->description }}</p>
                </div>

                @if($activity->objectives)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Objectives</h2>
                            <p class="text-sm text-gray-500">Goals we want to achieve</p>
                        </div>
                    </div>
                    <p class="text-gray-700 leading-relaxed">{{ $activity->objectives }}</p>
                </div>
                @endif

                @if($activity->materials_needed)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Materials Needed</h2>
                            <p class="text-sm text-gray-500">Prepare these items ahead of time</p>
                        </div>
                    </div>
                    <p class="text-gray-700 leading-relaxed">{{ $activity->materials_needed }}</p>
                </div>
                @endif

                @if($activity->outcomes)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Outcomes</h2>
                            <p class="text-sm text-gray-500">Expected results after completion</p>
                        </div>
                    </div>
                    <p class="text-gray-700 leading-relaxed">{{ $activity->outcomes }}</p>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Activity Info Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Activity Information</h3>
                    </div>
                    
                    <dl class="grid grid-cols-1 gap-4 text-sm">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-user-tag text-green-500 mt-1"></i>
                            <div>
                                <dt class="text-gray-500">Organizer</dt>
                                <dd class="text-gray-900">{{ $activity->organizer ?? 'Barangay Health Office' }}</dd>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-green-500 mt-1"></i>
                            <div>
                                <dt class="text-gray-500">Location</dt>
                                <dd class="text-gray-900">{{ $activity->location }}</dd>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="fas fa-calendar-alt text-green-500 mt-1"></i>
                            <div>
                                <dt class="text-gray-500">Activity Date</dt>
                                <dd class="text-gray-900">{{ optional($activity->activity_date)->format('F d, Y') }}</dd>
                            </div>
                        </div>

                        @if($activity->start_time && $activity->end_time)
                        <div class="flex items-start gap-3">
                            <i class="fas fa-clock text-green-500 mt-1"></i>
                            <div>
                                <dt class="text-gray-500">Time</dt>
                                <dd class="text-gray-900">{{ $activity->start_time }} - {{ $activity->end_time }}</dd>
                            </div>
                        </div>
                        @endif

                        @if($activity->target_participants)
                        <div class="flex items-start gap-3">
                            <i class="fas fa-users text-green-500 mt-1"></i>
                            <div>
                                <dt class="text-gray-500">Target Participants</dt>
                                <dd class="text-gray-900">{{ $activity->target_participants }} people</dd>
                            </div>
                        </div>
                        @endif

                        @if($activity->actual_participants)
                        <div class="flex items-start gap-3">
                            <i class="fas fa-user-check text-green-500 mt-1"></i>
                            <div>
                                <dt class="text-gray-500">Actual Participants</dt>
                                <dd class="text-gray-900">{{ $activity->actual_participants }} people</dd>
                            </div>
                        </div>
                        @endif

                        @if($activity->budget)
                        <div class="flex items-start gap-3">
                            <i class="fas fa-wallet text-green-500 mt-1"></i>
                            <div>
                                <dt class="text-gray-500">Budget</dt>
                                <dd class="text-gray-900">₱{{ number_format($activity->budget, 2) }}</dd>
                            </div>
                        </div>
                        @endif
                    </dl>
                </div>

                <!-- Activity Timeline -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                            <i class="fas fa-stream"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Timeline</h3>
                    </div>
                    
                    <div class="space-y-5">
                        <div class="flex items-start gap-3">
                            <div class="w-3 h-3 mt-1 rounded-full bg-blue-500"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Activity Created</p>
                                <p class="text-xs text-gray-500">{{ $activity->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-3">
                            <div class="w-3 h-3 mt-1 rounded-full {{ $activity->status === 'Completed' ? 'bg-green-500' : ($activity->status === 'Ongoing' ? 'bg-blue-500' : 'bg-yellow-400') }}"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Activity Date</p>
                                <p class="text-xs text-gray-500">{{ optional($activity->activity_date)->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Participation Information -->
                @if($activity->status === 'Planned' && $activity->activity_date >= now())
                <div class="bg-green-50 rounded-2xl border border-green-200 p-6">
                    <div class="flex items-center gap-2 mb-3 text-green-800">
                        <i class="fas fa-user-plus"></i>
                        <h3 class="text-lg font-semibold">Join This Activity</h3>
                    </div>
                    <p class="text-sm text-green-700 mb-4">This activity is open for participation. Contact the barangay office if you want to volunteer or need more details.</p>
                    <a href="{{ route('resident.request_community_concern') }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-green-700">
                        Express Interest
                    </a>
                </div>
                @elseif($activity->status === 'Ongoing')
                <div class="bg-blue-50 rounded-2xl border border-blue-200 p-6">
                    <div class="flex items-center gap-2 mb-3 text-blue-800">
                        <i class="fas fa-broadcast-tower"></i>
                        <h3 class="text-lg font-semibold">Activity in Progress</h3>
                    </div>
                    <p class="text-sm text-blue-700 mb-4">This health activity is currently ongoing. Reach out to the barangay office to see how you can help or participate.</p>
                    <a href="{{ route('resident.request_community_concern') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-blue-700">
                        Contact Barangay
                    </a>
                </div>
                @else
                <div class="bg-gray-50 rounded-2xl border border-gray-200 p-6">
                    <div class="flex items-center gap-2 mb-3 text-gray-800">
                        <i class="fas fa-check-circle"></i>
                        <h3 class="text-lg font-semibold">Activity Completed</h3>
                    </div>
                    <p class="text-sm text-gray-700 mb-4">This health activity has been completed. Thank you for your interest and support.</p>
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
