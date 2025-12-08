@extends('admin.main.layout')

@section('title', 'All Notifications')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Consolidated Notifications Skeleton -->
    <div id="adminNotifSkeleton">
        @include('components.loading.skeleton-notification')
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="adminNotifContent" style="display: none;">
    <!-- Header Section -->
    <div class="mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Notifications</h1>
                <p class="text-gray-600">Manage and view all system notifications</p>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row gap-3">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                        <span class="text-sm text-gray-600" id="unread-count">0 unread</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-600" id="read-count">0 read</span>
                    </div>
                </div>
                <form action="{{ route('admin.notifications.mark-all-as-read') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-check-double mr-2"></i>
                        Mark All as Read
                    </button>
                </form>
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

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.notifications') }}" class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Type Filter -->
            <div class="sm:w-48">
                <select name="type" id="typeFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                    <option value="">All Types</option>
                    <option value="blotter_report" {{ request('type') == 'blotter_report' ? 'selected' : '' }}>Blotter</option>
                    <option value="document_request" {{ request('type') == 'document_request' ? 'selected' : '' }}>Document Request</option>
                    <option value="account_request" {{ request('type') == 'account_request' ? 'selected' : '' }}>Account Request</option>
                    <option value="community_complaint" {{ request('type') == 'community_complaint' ? 'selected' : '' }}>Community Concern</option>
                </select>
            </div>

            <!-- Date Range Filters -->
            <div class="sm:w-48">
                <input type="date" name="start_date" id="startDateFilter" 
                       class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md"
                       value="{{ request('start_date') }}" placeholder="Start Date">
            </div>
            <div class="sm:w-48">
                <input type="date" name="end_date" id="endDateFilter" 
                       class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md"
                       value="{{ request('end_date') }}" placeholder="End Date">
            </div>

            <div class="sm:w-48">
                <select name="read_status" id="readStatusFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                    <option value="">All</option>
                    <option value="unread" {{ request('read_status') == 'unread' ? 'selected' : '' }}>Unread</option>
                    <option value="read" {{ request('read_status') == 'read' ? 'selected' : '' }}>Read</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.notifications') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-undo mr-2"></i>Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Notifications List -->
    @if($notifications->isEmpty())
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-bell text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
            <p class="text-gray-500">You're all caught up! New notifications will appear here.</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-bell mr-2"></i>
                                    Notification
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-clock mr-2"></i>
                                    Date
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Status
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-cogs mr-2"></i>
                                    Actions
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="notifications-tbody">
                        @foreach($notifications as $notification)
                        <tr class="notification-item hover:bg-gray-50 transition duration-150 {{ $notification->is_read ? 'opacity-75' : 'bg-blue-50' }}" data-status="{{ $notification->is_read ? 'read' : 'unread' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        @if($notification->type === 'blotter_report')
                                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-file-alt text-red-600 text-sm"></i>
                                            </div>
                                        @elseif($notification->type === 'document_request')
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-file-signature text-blue-600 text-sm"></i>
                                            </div>
                                        @elseif($notification->type === 'account_request')
                                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user-plus text-green-600 text-sm"></i>
                                            </div>
                                        @elseif($notification->type === 'community_complaint')
                                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-exclamation-triangle text-yellow-600 text-sm"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900 {{ $notification->is_read ? '' : 'font-semibold' }}">
                                                {{ $notification->message }}
                                            </p>
                                            @if(!$notification->is_read)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    New
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            @if($notification->type === 'blotter_report')
                                                Blotter Report
                                            @elseif($notification->type === 'document_request')
                                                Document Request
                                            @elseif($notification->type === 'account_request')
                                                Account Request
                                            @elseif($notification->type === 'community_complaint')
                                                Community Complaint
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $notification->created_at->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $notification->created_at->format('g:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($notification->is_read)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Read
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 animate-pulse">
                                        <i class="fas fa-clock mr-1"></i>
                                        Unread
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex justify-center">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ $notification->link }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200 js-mark-read-and-navigate"
                                       data-type="{{ $notification->type }}" data-id="{{ $notification->id }}" data-link="{{ $notification->link }}">
                                        <i class="fas fa-eye mr-1"></i>
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="mt-6">
            <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
                <div class="-mt-px flex w-0 flex-1">
                    @if($notifications->onFirstPage())
                        <span class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500">
                            <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                            Previous
                        </span>
                    @else
                        <a href="{{ $notifications->appends(request()->except('page'))->previousPageUrl() }}" 
                           class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                            Previous
                        </a>
                    @endif
                </div>
                
                <div class="hidden md:-mt-px md:flex">
                    @php
                        $currentPage = $notifications->currentPage();
                        $lastPage = $notifications->lastPage();
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($lastPage, $currentPage + 2);
                    @endphp
                    
                    @if($startPage > 1)
                        <a href="{{ $notifications->appends(request()->except('page'))->url(1) }}" 
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
                            <a href="{{ $notifications->appends(request()->except('page'))->url($page) }}" 
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
                        <a href="{{ $notifications->appends(request()->except('page'))->url($lastPage) }}" 
                           class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            {{ $lastPage }}
                        </a>
                    @endif
                </div>
                
                <div class="-mt-px flex w-0 flex-1 justify-end">
                    @if($notifications->hasMorePages())
                        <a href="{{ $notifications->appends(request()->except('page'))->nextPageUrl() }}" 
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
                @if($notifications->onFirstPage())
                    <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500">
                        Previous
                    </span>
                @else
                    <a href="{{ $notifications->appends(request()->except('page'))->previousPageUrl() }}" 
                       class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Previous
                    </a>
                @endif
                
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">
                    Page {{ $notifications->currentPage() }} of {{ $notifications->lastPage() }}
                </span>
                
                @if($notifications->hasMorePages())
                    <a href="{{ $notifications->appends(request()->except('page'))->nextPageUrl() }}" 
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
                Showing {{ $notifications->firstItem() }} to {{ $notifications->lastItem() }} of {{ $notifications->total() }} results
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const skel = document.getElementById('adminNotifSkeleton');
        if (skel) skel.style.display = 'none';
        const content = document.getElementById('adminNotifContent');
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to update notification counts
    function updateNotificationCounts() {
        fetch('{{ route("admin.notifications.count") }}')
            .then(response => response.json())
            .then(data => {
                if (data && !data.error) {
                    // Prefer new fields if available
                    const totalUnread = (typeof data.total_unread !== 'undefined') ? data.total_unread : (data.total || 0);
                    const totalRead = (typeof data.total_read !== 'undefined') ? data.total_read : 0;

                    // Update unread count (global across all pages)
                    document.getElementById('unread-count').textContent = `${totalUnread} unread`;

                    // Update read count (global across all pages)
                    document.getElementById('read-count').textContent = `${totalRead} read`;
                }
            })
            .catch(error => {
                console.error('Error fetching notification counts:', error);
            });
    }
    

    // Initial load
    updateNotificationCounts();

    // Update counts every 30 seconds
    setInterval(updateNotificationCounts, 30000);
});

// Delegated click handler for mark-as-read and navigate
document.addEventListener('click', function (event) {
    const link = event.target.closest('.js-mark-read-and-navigate');
    if (!link) return;
    event.preventDefault();
    const type = link.getAttribute('data-type');
    const id = link.getAttribute('data-id');
    const href = link.getAttribute('data-link');

    fetch(`/admin/notifications/mark-as-read/${type}/${id}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(() => {
        window.location.href = href;
    })
    .catch(() => {
        window.location.href = href;
    });
});
</script>


@endsection
