@extends('admin.main.layout')

@section('title', 'Health Center Activities')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Header Section -->
    <div class="mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Health Center Activities</h1>
                <p class="text-sm md:text-base text-gray-600">Plan, manage, and review health center activities</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <a href="{{ route('admin.health-center-activities.upcoming') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Upcoming
                </a>
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
    <form action="{{ route('admin.health-center-activities.index') }}" method="GET" class="mb-3 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
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

    <!-- Activities Grid (Aligned with Accomplished Projects UI) -->
    @if($activities->isEmpty())
        <div class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
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
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden">
                <!-- Header Badge -->
                <div class="relative">
                    <div class="w-full h-2 bg-gradient-to-r from-green-500 to-blue-500"></div>
                    <div class="absolute top-3 right-3">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusBadge }}">
                            <i class="fas fa-toggle-on mr-1"></i>{{ $activity->status }}
                        </span>
                    </div>
                </div>

                <!-- Card Content -->
                <div class="p-5">
                    <div class="flex items-start justify-between mb-2">
                        <h3 class="text-lg font-semibold text-gray-900 leading-snug line-clamp-2">{{ $activity->activity_name }}</h3>
                        <span class="ml-2 bg-green-600 text-white px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap">{{ $activity->activity_type }}</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-3">{{ Str::limit($activity->description, 180) }}</p>

                    <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                        <span><i class="fas fa-calendar-alt mr-1"></i>{{ $activity->activity_date->format('M d, Y') }}</span>
                        @if($activity->start_time && $activity->end_time)
                        <span><i class="fas fa-clock mr-1"></i>{{ $activity->start_time->format('g:i A') }} - {{ $activity->end_time->format('g:i A') }}</span>
                        @endif
                    </div>
                    <div class="text-sm text-gray-600 mb-4">
                        <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>{{ $activity->location }}
                        @if($activity->organizer)
                            <span class="ml-2"><i class="fas fa-user mr-1 text-gray-400"></i>{{ $activity->organizer }}</span>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <a href="{{ route('admin.health-center-activities.show', $activity) }}" 
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-3 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        <a href="{{ route('admin.health-center-activities.edit', $activity) }}" 
                           class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white text-center py-2 px-3 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <form action="{{ route('admin.health-center-activities.destroy', $activity) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this activity?')" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition duration-300">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </form>
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
@endsection 