@extends('admin.main.layout')

@section('title', 'Health Center Activities')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Consolidated Grid Dashboard Skeleton -->
    <div id="hcaSkeleton">
        @include('components.loading.grid-dashboard-skeleton', ['showWarning' => true, 'gridType' => 'activities', 'buttonCount' => 2])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="hcaContent" style="display: none;">
        <!-- Header Section -->
        <div class="mb-2">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Health Center Activities</h1>
                    <p class="text-sm md:text-base text-gray-600">Plan, manage, and review health center activities</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-2">
                    <a href="{{ route('admin.health-center-activities.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Add Activity
                    </a>
                </div>
            </div>
        </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-3 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-3 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Search -->
    <form action="{{ route('admin.health-center-activities.index') }}" method="GET" class="mb-2 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                           placeholder="Search by activity name, type, or organizer..." 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <!-- Featured Filter -->
            <div class="sm:w-48">
                <select name="featured" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                    <option value="">All Activities</option>
                    <option value="featured" {{ request('featured') == 'featured' ? 'selected' : '' }}>Featured Only</option>
                    <option value="non-featured" {{ request('featured') == 'non-featured' ? 'selected' : '' }}>Non-Featured</option>
                </select>
            </div>
            
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Filter
                </button>
                <a href="{{ route('admin.health-center-activities.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-2">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-check text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Total Activities</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $activities->total() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-spinner text-yellow-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Ongoing Activities</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ \App\Models\HealthCenterActivity::where('status', 'Ongoing')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Completed</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $activities->where('status', 'Completed')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 {{ $featuredCounts['total'] >= 6 ? 'bg-red-100' : 'bg-purple-100' }} rounded-full flex items-center justify-center">
                        <i class="fas fa-star {{ $featuredCounts['total'] >= 6 ? 'text-red-600' : 'text-purple-600' }} text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Total Featured</p>
                    <p class="text-lg lg:text-2xl font-bold {{ $featuredCounts['total'] >= 6 ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $featuredCounts['total'] }}/6
                    </p>
                    <p class="text-xs text-gray-500">{{ $featuredCounts['projects'] }} projects + {{ $featuredCounts['activities'] }} activities</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Items Warning -->
    @if($warningMessage)
        <div class="mb-2 bg-{{ $warningMessage['color'] }}-50 border border-{{ $warningMessage['color'] }}-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-{{ $warningMessage['icon'] }} text-{{ $warningMessage['color'] }}-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-{{ $warningMessage['color'] }}-800">
                        <strong>{{ $warningMessage['title'] }}:</strong> {{ $warningMessage['message'] }}
                    </p>
                    @if($warningMessage['type'] === 'error' && !empty($unfeatureSuggestions))
                        <div class="mt-3 pt-3 border-t border-{{ $warningMessage['color'] }}-200">
                            <p class="text-xs text-{{ $warningMessage['color'] }}-700 mb-2"><strong>Suggestions to unfeature:</strong></p>
                            <div class="space-y-1">
                                @if(isset($unfeatureSuggestions['projects']))
                                    @foreach($unfeatureSuggestions['projects']->take(2) as $suggestion)
                                        <div class="text-xs text-{{ $warningMessage['color'] }}-600">
                                            • Project: {{ $suggestion['name'] }} ({{ $suggestion['date']->format('M Y') }})
                                        </div>
                                    @endforeach
                                @endif
                                @if(isset($unfeatureSuggestions['activities']))
                                    @foreach($unfeatureSuggestions['activities']->take(2) as $suggestion)
                                        <div class="text-xs text-{{ $warningMessage['color'] }}-600">
                                            • Activity: {{ $suggestion['name'] }} ({{ $suggestion['date']->format('M Y') }})
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Activities Grid (Aligned with Accomplished Projects UI) -->
    @if($activities->isEmpty())
        <div class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                <i class="fas fa-calendar-alt text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No health center activities found</h3>
            <p class="text-gray-500">Get started by adding the first health activity.</p>
            <div class="mt-6">
                <a href="{{ route('admin.health-center-activities.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Add Health Activity
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach($activities as $activity)
            @php
                $statusBadge = match($activity->status) {
                    'Planned' => 'bg-blue-100 text-blue-800',
                    'Ongoing' => 'bg-yellow-100 text-yellow-800',
                    'Completed' => 'bg-green-100 text-green-800',
                    'Cancelled' => 'bg-red-100 text-red-800',
                    default => 'bg-gray-100 text-gray-800'
                };
            @endphp
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden flex flex-col h-full">
                <!-- Activity Image -->
                <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-50">
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
                    
                    <!-- Featured Badge -->
                    @if($activity->is_featured)
                        <div class="absolute top-3 right-3 bg-yellow-400 text-yellow-900 px-2 py-1 rounded-full text-xs font-semibold shadow-sm">
                            <i class="fas fa-star mr-1"></i>Featured
                        </div>
                    @endif
                    
                    <!-- Status Badge -->
                    <div class="absolute top-3 left-3">
                        <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusBadge }} shadow-sm">
                            <i class="fas fa-toggle-on mr-1.5 text-xs"></i>{{ $activity->status }}
                        </span>
                    </div>
                    
                    <!-- Activity Type Badge -->
                    <div class="absolute bottom-3 right-3">
                        <span class="bg-green-600 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-sm">
                            {{ $activity->activity_type }}
                        </span>
                    </div>
                </div>

                <!-- Card Content -->
                <div class="p-5 flex flex-col h-full">
                    <!-- Title -->
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2 leading-tight">{{ $activity->activity_name }}</h3>
                    
                    <!-- Description -->
                    <p class="text-gray-600 text-sm mb-2 leading-relaxed line-clamp-3">{{ Str::limit($activity->description, 120) }}</p>

                    <!-- Date, Time and Audience Row -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-calendar-alt mr-2 text-gray-400 w-4 text-center"></i>
                            <span>{{ $activity->activity_date->format('M d, Y') }}</span>
                        </div>
                        @if($activity->start_time && $activity->end_time)
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-clock mr-2 text-gray-400 w-4 text-center"></i>
                            <span>
                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $activity->start_time)->format('g:i A') }}
                                -
                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $activity->end_time)->format('g:i A') }}
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Location and Organizer Row -->
                    <div class="space-y-2 mb-2">
                        <div class="flex items-start text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt mr-2 text-gray-400 w-4 text-center mt-0.5"></i>
                            <span class="leading-relaxed">{{ $activity->location }}</span>
                        </div>
                        @if($activity->organizer)
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-user mr-2 text-gray-400 w-4 text-center"></i>
                            <span>{{ $activity->organizer }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="space-y-2 mb-2">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-users mr-2 text-gray-400 w-4 text-center"></i>
                            <span>
                                @if($activity->audience_scope === 'purok' && $activity->audience_purok)
                                    Audience: Purok {{ $activity->audience_purok }}
                                @else
                                    Audience: All Residents
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2 mt-auto pt-4">
                        <a href="{{ route('admin.health-center-activities.show', $activity) }}" 
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-3 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        <a href="{{ route('admin.health-center-activities.edit', $activity) }}" 
                           class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white text-center py-2 px-3 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>

                        <button type="button" 
                                data-activity-id="{{ $activity->id }}"
                                data-activity-name="{{ addslashes($activity->activity_name) }}"
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition duration-300 js-delete-activity">
                            <i class="fas fa-trash mr-1"></i>Delete
                        </button>
                        </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($activities->hasPages())
        <div class="mt-6">
            <nav class="flex items-center justify-between border-top border-gray-200 px-4 sm:px-0">
                <div class="-mt-px flex w-0 flex-1">
                    @if($activities->onFirstPage())
                        <span class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500">
                            <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                            Previous
                        </span>
                    @else
                        <a href="{{ $activities->appends(request()->except('page'))->previousPageUrl() }}" 
                           class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                            Previous
                        </a>
                    @endif
                </div>

                <div class="hidden md:-mt-px md:flex">
                    @php
                        $currentPage = $activities->currentPage();
                        $lastPage = $activities->lastPage();
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($lastPage, $currentPage + 2);
                    @endphp
                    @if($startPage > 1)
                        <a href="{{ $activities->appends(request()->except('page'))->url(1) }}" 
                           class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">1</a>
                        @if($startPage > 2)
                            <span class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500">...</span>
                        @endif
                    @endif
                    @for($page = $startPage; $page <= $endPage; $page++)
                        @if($page == $currentPage)
                            <span class="inline-flex items-center border-t-2 border-green-500 px-4 pt-4 text-sm font-medium text-green-600" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $activities->appends(request()->except('page'))->url($page) }}" 
                               class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">{{ $page }}</a>
                        @endif
                    @endfor
                    @if($endPage < $lastPage)
                        @if($endPage < $lastPage - 1)
                            <span class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500">...</span>
                        @endif
                        <a href="{{ $activities->appends(request()->except('page'))->url($lastPage) }}" 
                           class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">{{ $lastPage }}</a>
                    @endif
                </div>
                <div class="-mt-px flex w-0 flex-1 justify-end">
                    @if($activities->hasMorePages())
                        <a href="{{ $activities->appends(request()->except('page'))->nextPageUrl() }}" 
                           class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            Next
                            <i class="fas fa-arrow-right ml-3 text-gray-400"></i>
                        </a>
                    @else
                        <span class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-gray-500">
                            Next
                            <i class="fas fa-arrow-right ml-3 text-gray-400"></i>
                        </span>
                    @endif
                </div>
            </nav>
        </div>
        @endif
    @endif
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

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

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
        // Hide consolidated skeleton
        const skeleton = document.getElementById('hcaSkeleton');
        if (skeleton) skeleton.style.display = 'none';

        // Show content
        const content = document.getElementById('hcaContent');
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection 