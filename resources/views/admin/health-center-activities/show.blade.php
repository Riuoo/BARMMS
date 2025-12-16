@extends('admin.main.layout')

@section('title', 'Activity Details')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
    <!-- Skeleton Component -->
    <div id="hcaShowSkeleton">
        @include('components.loading.show-entity-skeleton', ['type' => 'health-activity', 'buttonCount' => 1])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="hcaShowContent" style="display: none;">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Activity Details</h1>
                <p class="text-gray-600 mt-2">View activity details</p>
            </div>
            <a href="{{ route('admin.health-center-activities.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Activities
            </a>
        </div>

    <!-- Activity Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Activity Image / Placeholder -->
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="relative h-64 bg-gradient-to-r from-gray-100 to-gray-50">
                    @if($activity->image)
                    <img src="{{ $activity->image_url }}" 
                         alt="{{ $activity->activity_name }}" 
                         class="w-full h-full object-cover">
                    @else
                    <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-xl"></i>
                            </div>
                            <span class="text-sm font-medium">No image available</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Title and Status -->
            <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $activity->activity_name }}</h2>
                    <div class="flex items-center space-x-3">
                        @if($activity->is_featured)
                            <div class="flex items-center text-yellow-600 dark:text-yellow-400">
                                <i class="fas fa-star mr-2"></i>
                                <span class="font-medium">Featured Activity</span>
                            </div>
                        @endif
                        @php
                            $statusBadge = match($activity->status) {
                                'Planned' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
                                'Ongoing' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
                                'Completed' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
                                'Cancelled' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
                                default => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200'
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusBadge }}">
                            {{ $activity->status }}
                        </span>
                    </div>
                </div>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $activity->description }}</p>
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
                        <h4 class="font-medium text-gray-900 mb-2">Target Audience</h4>
                        <p class="text-gray-600">
                            @if($activity->audience_scope === 'purok' && $activity->audience_purok)
                                Purok {{ $activity->audience_purok }}
                            @else
                                All Residents
                            @endif
                        </p>
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
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Notes</h3>
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
                       class="w-full inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Activity
                    </a>
                    <form action="{{ route('admin.health-center-activities.toggle-featured', $activity->id) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-200 flex items-center justify-center">
                            <i class="fas fa-star mr-2"></i>
                            {{ $activity->is_featured ? 'Remove Feature' : 'Feature' }} Activity
                        </button>
                    </form>
                    <button type="button" 
                            data-activity-id="{{ $activity->id }}"
                            data-activity-name="{{ addslashes($activity->activity_name) }}"
                            class="w-full inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200 flex items-center justify-center js-delete-activity">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Activity
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteActivityModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900">Delete Activity</h3>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-gray-700 mb-6">Are you sure you want to delete <span id="activityName" class="font-semibold"></span>? This will permanently remove the activity from the system.</p>
        <form id="deleteActivityForm" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteActivityModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition duration-200">
                    Delete Activity
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function deleteActivity(id, name) {
    document.getElementById('activityName').textContent = name;
    document.getElementById('deleteActivityForm').action = `/admin/health-center-activities/${id}`;
    document.getElementById('deleteActivityModal').classList.remove('hidden');
    document.getElementById('deleteActivityModal').classList.add('flex');
}

function closeDeleteActivityModal() {
    document.getElementById('deleteActivityModal').classList.add('hidden');
    document.getElementById('deleteActivityModal').classList.remove('flex');
}

document.addEventListener('DOMContentLoaded', function() {
    // Handle delete button clicks
    document.addEventListener('click', function(event) {
        const deleteBtn = event.target.closest('.js-delete-activity');
        if (deleteBtn) {
            const activityId = deleteBtn.dataset.activityId;
            const activityName = deleteBtn.dataset.activityName;
            deleteActivity(activityId, activityName);
            return;
        }
    });
    
    // Skeleton loading control
    setTimeout(() => {
        const skeleton = document.getElementById('hcaShowSkeleton');
        const content = document.getElementById('hcaShowContent');
        if (skeleton) skeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection 