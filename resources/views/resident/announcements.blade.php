@extends('resident.layout')

@section('title', 'Community Bulletin Board')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Consolidated Skeleton -->
    <div id="residentAnnouncementsSkeleton">
        @include('components.loading.resident-announcements-skeleton')
    </div>

    <!-- Real Content Wrapper (hidden initially) -->
    <div id="residentAnnouncementsContent" style="display: none;">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Community Bulletin Board</h1>
                    <p class="text-gray-600">Stay updated with community projects and health activities</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('resident.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-project-diagram text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Projects</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalProjects }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-heartbeat text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Health Activities</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalActivities }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-star text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Featured</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $featuredCount }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-purple-600"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Upcoming</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $upcomingActivities }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <form method="GET" action="{{ route('resident.announcements') }}" class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <!-- Search Input -->
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="searchInput" placeholder="Search announcements..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                        value="{{ request('search') }}">
                    </div>
                </div>
                
                <!-- Type Filter -->
                <div class="sm:w-48">
                    <select name="type" id="typeFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                        <option value="">All Types</option>
                        <option value="project" {{ request('type') == 'project' ? 'selected' : '' }}>Projects</option>
                        <option value="activity" {{ request('type') == 'activity' ? 'selected' : '' }}>Health Activities</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="sm:w-48">
                    <select name="status" id="statusFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                        <option value="">All Status</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    </select>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-filter mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('resident.announcements') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-refresh mr-2"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Quick Filter Buttons -->
        <div class="flex flex-wrap gap-2 mb-6">
            <a href="{{ route('resident.announcements', ['featured' => 'true']) }}" 
               class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request('featured') == 'true' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                <i class="fas fa-star mr-2"></i>
                Featured Only
            </a>
            <a href="{{ route('resident.announcements', ['type' => 'project']) }}" 
               class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request('type') == 'project' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                <i class="fas fa-project-diagram mr-2"></i>
                Projects Only
            </a>
            <a href="{{ route('resident.announcements', ['type' => 'activity']) }}" 
               class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request('type') == 'activity' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                <i class="fas fa-heartbeat mr-2"></i>
                Health Activities Only
            </a>
        </div>

        <!-- Bulletin Board Grid -->
        @if($bulletin->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($bulletin as $item)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition duration-200">
                    @if($item->image_url)
                    <div class="h-48 bg-gray-200 overflow-hidden">
                        <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                    </div>
                    @endif

                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $item->category }}
                            </span>
                            <span class="text-xs {{ $item->type === 'activity' ? 'text-green-600' : 'text-blue-600' }}">
                                <i class="fas {{ $item->type === 'activity' ? 'fa-heartbeat' : 'fa-project-diagram' }} mr-1"></i>
                                {{ ucfirst($item->type) }}
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $item->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ Str::limit($item->description, 120) }}</p>

                        <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                            <span><i class="fas fa-calendar-alt mr-1"></i>{{ optional($item->date)->format('M d, Y') }}</span>
                            @if($item->is_featured)
                            <span class="text-yellow-600"><i class="fas fa-star mr-1"></i>Featured</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($item->status === 'completed') bg-green-100 text-green-800
                                @elseif($item->status === 'ongoing') bg-blue-100 text-blue-800
                                @elseif($item->status === 'planned') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                <i class="fas {{ $item->status === 'completed' ? 'fa-check-circle' : ($item->status === 'ongoing' ? 'fa-spinner' : 'fa-clock') }} mr-1"></i>
                                {{ ucfirst($item->status) }}
                            </span>
                            
                            <a href="{{ $item->type === 'project' ? route('resident.announcements.project', $item->id) : route('resident.announcements.activity', $item->id) }}" 
                               class="inline-flex items-center text-green-600 hover:text-green-700 font-medium text-sm">
                                View Details <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($bulletin->hasPages())
                <div class="mt-8">
                    <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
                        <div class="-mt-px flex w-0 flex-1">
                            @if($bulletin->onFirstPage())
                                <span class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500">
                                    <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                                    Previous
                                </span>
                            @else
                                <a href="{{ $bulletin->appends(request()->except('page'))->previousPageUrl() }}" 
                                   class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                    <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                                    Previous
                                </a>
                            @endif
                        </div>
                        
                        <div class="hidden md:-mt-px md:flex">
                            @php
                                $currentPage = $bulletin->currentPage();
                                $lastPage = $bulletin->lastPage();
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($lastPage, $currentPage + 2);
                            @endphp
                            
                            @for($page = $startPage; $page <= $endPage; $page++)
                                @if($page == $currentPage)
                                    <span class="inline-flex items-center border-t-2 border-green-500 px-4 pt-4 text-sm font-medium text-green-600" aria-current="page">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $bulletin->appends(request()->except('page'))->url($page) }}" 
                                       class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endfor
                        </div>
                        
                        <div class="-mt-px flex w-0 flex-1 justify-end">
                            @if($bulletin->hasMorePages())
                                <a href="{{ $bulletin->appends(request()->except('page'))->nextPageUrl() }}" 
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
                    
                    <!-- Results Info -->
                    <div class="mt-4 text-center text-sm text-gray-500">
                        Showing {{ $bulletin->firstItem() }} to {{ $bulletin->lastItem() }} of {{ $bulletin->total() }} results
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-bullhorn text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No items found</h3>
                <p class="text-gray-500">No projects or activities match your current filters.</p>
                <div class="mt-6">
                    <a href="{{ route('resident.announcements') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                        <i class="fas fa-refresh mr-2"></i>
                        Clear Filters
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const skeleton = document.getElementById('residentAnnouncementsSkeleton');
        const content = document.getElementById('residentAnnouncementsContent');
        if (skeleton) skeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endsection
