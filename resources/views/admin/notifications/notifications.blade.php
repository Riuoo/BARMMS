@extends('admin.main.layout')

@section('title', 'All Notifications')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
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

    <!-- Filters and Search -->
    <form method="GET" action="{{ route('admin.notifications') }}" class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="search-notifications" placeholder="Search notifications..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500" value="{{ request('search') }}">
                </div>
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
                    Filter
                </button>
                <a href="{{ route('admin.notifications') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Reset
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
                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200"
                                       onclick="markAsReadAndNavigate('{{ $notification->type }}', {{ $notification->id }}, '{{ $notification->link }}')">
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to update notification counts
    function updateNotificationCounts() {
        fetch('{{ route("admin.notifications.count") }}')
            .then(response => response.json())
            .then(data => {
                if (data && !data.error) {
                    const totalUnread = data.total || 0;
                    // This line assumes $notifications->count() is the total number of notifications displayed on the current page.
                    // If you want the total read notifications across all pages, you'd need a separate AJAX call or pass it from the backend.
                    const totalNotificationsOnPage = {{ $notifications->count() }}; 
                    const totalReadOnPage = totalNotificationsOnPage - totalUnread; // This calculation is only accurate if all notifications on the page are either read or unread.
                    
                    // Update unread count
                    document.getElementById('unread-count').textContent = `${totalUnread} unread`;
                    
                    // Update read count (this will only reflect read status of notifications currently displayed)
                    document.getElementById('read-count').textContent = `${totalReadOnPage} read`;
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

// Function to mark notification as read and navigate to the link
function markAsReadAndNavigate(type, id, link) {
    // Prevent default link behavior
    event.preventDefault();
    
    // Make AJAX call to mark notification as read
    fetch(`/admin/notifications/mark-as-read/${type}/${id}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            if (typeof toast !== 'undefined') {
                toast.success(data.message);
            }
        }
        // Navigate to the link after marking as read
        window.location.href = link;
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
        // Still navigate to the link even if marking as read fails
        window.location.href = link;
    });
}
</script>


@endsection
