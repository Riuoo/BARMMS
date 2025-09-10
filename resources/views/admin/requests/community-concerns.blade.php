@extends('admin.main.layout')

@php
    $userRole = session('user_role');
    $isAdmin = $userRole === 'admin';
    $isSecretary = $userRole === 'secretary';
    $canPerformTransactions = $isAdmin || $isSecretary;
@endphp

@section('title', 'Community Concerns')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Consolidated Table Dashboard Skeleton -->
    <div id="concernsSkeleton">
        @include('components.loading.table-dashboard-skeleton', ['showButton' => false])
    </div>
    <script>
    // Defensive hide in case other scripts fail
    (function(){
        function revealConcerns(){
            var w = document.getElementById('concernsSkeleton');
            if (w) w.style.display = 'none';
            var inner = document.getElementById('tableDashboardSkeleton');
            if (inner) inner.style.display = 'none';
            var c = document.getElementById('concernsContent');
            if (c) c.style.display = 'block';
        }
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(revealConcerns, 1000);
        } else {
            document.addEventListener('DOMContentLoaded', function(){ setTimeout(revealConcerns, 1000); });
        }
        // Absolute fallback after 3s
        setTimeout(revealConcerns, 3000);
    })();
    </script>

    <!-- Real Content (hidden initially) -->
    <div id="concernsContent" style="display: none;">
        <!-- Header Section -->
        <div class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Community Concerns</h1>
                <p class="text-gray-600">Manage and track community concerns from residents</p>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
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
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
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

    <!-- Filters and Search -->
    <form method="GET" action="{{ route('admin.community-concerns') }}" class="mb-2 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="concernSearchInput" placeholder="Search concerns..." 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                    value="{{ request('search') }}">
                </div>
            </div>
            <!-- Status Filter -->
            <div class="sm:w-48">
                <select name="status" id="statusFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.community-concerns') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-undo mr-2"></i>Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-3 lg:gap-4 mb-2">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Total Concerns</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-search text-orange-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Under Review</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['under_review'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-tasks text-yellow-700 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">In Progress</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['in_progress'] }}</p>
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
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Resolved</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['resolved'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times-circle text-purple-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Closed</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['closed'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Concerns List -->
    @if($concerns->count() > 0)
        @php
            $hasThreadActions = $concerns->contains(function ($c) {
                return !in_array($c->status, ['resolved','closed']);
            });
        @endphp
        <!-- Desktop Table (hidden on mobile) -->
        <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-clipboard-list mr-2"></i>
                                    Concern
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-tag mr-2"></i>
                                    Category
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Status
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-user mr-2"></i>
                                    Submitted By
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-2"></i>
                                    Date
                                </div>
                            </th>
                            @if($hasThreadActions && $canPerformTransactions)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-center">
                                        <i class="fas fa-cogs mr-2"></i>
                                        Actions
                                    </div>
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($concerns as $concern)
                        <tr class="concern-item hover:bg-gray-50 transition duration-150" data-status="{{ $concern->status }}">
                            <td class="px-6 py-4">
                                <button type="button"
                                        data-id="{{ $concern->id }}"
                                        class="js-concern-view hidden md:inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                                    <i class="fas fa-eye mr-1"></i>
                                    View Details
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ $concern->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'under_review' => 'bg-blue-100 text-blue-800',
                                        'in_progress' => 'bg-yellow-100 text-yellow-800',
                                        'resolved' => 'bg-green-100 text-green-800',
                                        'closed' => 'bg-purple-100 text-purple-800'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$concern->status] }}">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    {{ str_replace('_', ' ', ucfirst($concern->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $concern->resident->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $concern->created_at->format('M d, Y') }}</div>
                            </td>
                            @if($hasThreadActions && $canPerformTransactions)
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        @if($concern->status !== 'resolved' && $concern->status !== 'closed')
                                            <button type="button" data-id="{{ $concern->id }}" data-status="{{ $concern->status }}"
                                                    class="js-open-update inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                                                <i class="fas fa-edit mr-1"></i>
                                                Update
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards (hidden on desktop) -->
        <div class="md:hidden space-y-4">
            @foreach($concerns as $concern)
            <div class="concern-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200" data-status="{{ $concern->status }}">
                <!-- Header Section -->
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clipboard-list text-blue-600"></i>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-gray-900 truncate">{{ $concern->title }}</h3>
                            <p class="text-sm text-gray-500 truncate">{{ $concern->resident->name ?? 'N/A' }}</p>
                            <div class="flex items-center mt-1">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'under_review' => 'bg-blue-100 text-blue-800',
                                        'in_progress' => 'bg-yellow-100 text-yellow-800',
                                        'resolved' => 'bg-green-100 text-green-800',
                                        'closed' => 'bg-purple-100 text-purple-800'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$concern->status] }}">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    {{ str_replace('_', ' ', ucfirst($concern->status)) }}
                                </span>
                                <span class="ml-2 text-xs text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $concern->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Section -->
                <div class="mb-2 flex flex-wrap gap-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        <i class="fas fa-tag mr-1"></i>
                        {{ $concern->category }}
                    </span>
                </div>

                <!-- Actions Section -->
                <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100">
                    <button type="button" data-id="{{ $concern->id }}"
                            class="js-concern-view-mobile inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200 md:hidden">
                        <i class="fas fa-eye mr-1"></i>
                        View Full Details
                    </button>
                    @if($concern->status !== 'resolved' && $concern->status !== 'closed')
                        <button type="button" data-id="{{ $concern->id }}" data-status="{{ $concern->status }}"
                                class="js-open-update inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition duration-200">
                            <i class="fas fa-edit mr-1"></i>
                            Update
                        </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No concerns found</h3>
            <p class="text-gray-500">No community concerns have been submitted yet.</p>
        </div>
    @endif

    <!-- Modern Pagination -->
    @if($concerns->hasPages())
        <div class="mt-6">
            <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
                <div class="-mt-px flex w-0 flex-1">
                    @if($concerns->onFirstPage())
                        <span class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500">
                            <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                            Previous
                        </span>
                    @else
                        <a href="{{ $concerns->appends(request()->except('page'))->previousPageUrl() }}" 
                           class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                            Previous
                        </a>
                    @endif
                </div>
                
                <div class="hidden md:-mt-px md:flex">
                    @php
                        $currentPage = $concerns->currentPage();
                        $lastPage = $concerns->lastPage();
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($lastPage, $currentPage + 2);
                    @endphp
                    
                    @if($startPage > 1)
                        <a href="{{ $concerns->appends(request()->except('page'))->url(1) }}" 
                           class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            1
                        </a>
                        @if($startPage > 2)
                            <span class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500">
                                ...
                            </span>
                        @endif
                    @endif
                    
                    @for($page = $startPage; $page <= $endPage; $page++)
                        @if($page == $currentPage)
                            <span class="inline-flex items-center border-t-2 border-green-500 px-4 pt-4 text-sm font-medium text-green-600" aria-current="page">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $concerns->appends(request()->except('page'))->url($page) }}" 
                               class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor
                    
                    @if($endPage < $lastPage)
                        @if($endPage < $lastPage - 1)
                            <span class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500">
                                ...
                            </span>
                        @endif
                        <a href="{{ $concerns->appends(request()->except('page'))->url($lastPage) }}" 
                           class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            {{ $lastPage }}
                        </a>
                    @endif
                </div>
                
                <div class="-mt-px flex w-0 flex-1 justify-end">
                    @if($concerns->hasMorePages())
                        <a href="{{ $concerns->appends(request()->except('page'))->nextPageUrl() }}" 
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
            
            <!-- Mobile Pagination -->
            <div class="mt-4 flex justify-between sm:hidden">
                @if($concerns->onFirstPage())
                    <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500">
                        Previous
                    </span>
                @else
                    <a href="{{ $concerns->appends(request()->except('page'))->previousPageUrl() }}" 
                       class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Previous
                    </a>
                @endif
                
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">
                    Page {{ $concerns->currentPage() }} of {{ $concerns->lastPage() }}
                </span>
                
                @if($concerns->hasMorePages())
                    <a href="{{ $concerns->appends(request()->except('page'))->nextPageUrl() }}" 
                       class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Next
                    </a>
                @else
                    <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500">
                        Next
                    </span>
                @endif
            </div>
            
            <!-- Results Info -->
            <div class="mt-4 text-center text-sm text-gray-500">
                Showing {{ $concerns->firstItem() }} to {{ $concerns->lastItem() }} of {{ $concerns->total() }} results
            </div>
        </div>
    @endif
    
    <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden"></p>
    </div>
</div>

<!-- Modals -->
@include('admin.modals.community-concern-modals')

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Skeleton loading control
    setTimeout(() => {
        // Hide consolidated skeleton (wrapper and inner component id)
        const wrapperSkel = document.getElementById('concernsSkeleton');
        if (wrapperSkel) wrapperSkel.style.display = 'none';
        const innerSkel = document.getElementById('tableDashboardSkeleton');
        if (innerSkel) innerSkel.style.display = 'none';

        // Show content
        const content = document.getElementById('concernsContent');
        if (content) content.style.display = 'block';
    }, 1000);

    // Notification handling
    try {
        var shouldNotify = localStorage.getItem('showComplaintUpdateNotify');
        if (shouldNotify === '1') {
            setTimeout(function() {
                if (typeof notify === 'function') {
                    notify('success', 'Concern status updated successfully.');
                } else if (window.toast && typeof window.toast.success === 'function') {
                    window.toast.success('Concern status updated successfully.');
                }
            }, 250);
            localStorage.removeItem('showComplaintUpdateNotify');
        }
    } catch (e) {
        // no-op
    }
});
</script>
@endsection