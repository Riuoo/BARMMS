@extends('resident.layout')

@section('title', 'My Requests')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Header Section -->
    <div class="mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">My Requests</h1>
                <p class="text-gray-600">Track and manage all your submitted requests</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <a href="{{ route('resident.request_blotter_report') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    New Blotter Report
                </a>
                <a href="{{ route('resident.request_document_request') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    New Document Request
                </a>
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

    <!-- Notifications hidden on My Requests as per requirement -->

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

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 lg:gap-4 mb-3">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-red-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Blotter Reports</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $blotterRequests->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-signature text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Document Requests</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $documentRequests->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-indigo-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Community Complaints</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $communityComplaints->count() }}</p>
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
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $blotterRequests->where('status', 'pending')->count() + $documentRequests->where('status', 'pending')->count() + $communityComplaints->where('status', 'pending')->count() }}</p>
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
                    <p class="text-sm font-medium text-gray-500">Completed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $blotterRequests->where('status', 'completed')->count() + $documentRequests->where('status', 'completed')->count() + $communityComplaints->where('status', 'resolved')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <form method="GET" action="{{ route('resident.my-requests') }}" class="mb-3 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="searchInput" placeholder="Search requests..."
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                    value="{{ request('search') }}">
                </div>
            </div>
            <!-- Status Filter -->
            <div class="sm:w-48">
                <select name="status" id="statusFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Filter
                </button>
                <a href="{{ route('resident.my-requests') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Requests List -->
    @if($blotterRequests->isEmpty() && $documentRequests->isEmpty() && $communityComplaints->isEmpty())
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No requests found</h3>
            <p class="text-gray-500">You haven't submitted any requests yet.</p>
            <div class="mt-6 flex flex-col sm:flex-row gap-2 justify-center">
                <a href="{{ route('resident.request_blotter_report') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Submit Blotter Report
                </a>
                <a href="{{ route('resident.request_community_complaint') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Community Complaint
                </a>
                <a href="{{ route('resident.request_document_request') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Request Document
                </a>
            </div>
        </div>
    @else
        <!-- Desktop Table (hidden on mobile) -->
        <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-tag mr-2"></i>
                                    Type
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-align-left mr-2"></i>
                                    Details
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
                                    <i class="fas fa-calendar mr-2"></i>
                                    Submitted
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="requestsTableBody">
                        @foreach($blotterRequests as $request)
                        <tr class="request-item hover:bg-gray-50 transition duration-150" data-type="blotter" data-status="{{ $request->status }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-file-alt text-red-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">Blotter Report</div>
                                        <div class="text-sm text-gray-500">vs {{ $request->recipient_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($request->description, 50) }}</div>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-align-left mr-1"></i>
                                    Incident Details
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($request->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pending
                                    </span>
                                @elseif($request->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-check mr-1"></i>
                                        Approved
                                    </span>
                                @elseif($request->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Completed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $request->created_at->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $request->created_at->diffForHumans() }}
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        @foreach($documentRequests as $request)
                        <tr class="request-item hover:bg-gray-50 transition duration-150" data-type="document" data-status="{{ $request->status }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-file-signature text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $request->document_type }}</div>
                                        <div class="text-sm text-gray-500">Document Request</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($request->description, 50) }}</div>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-align-left mr-1"></i>
                                    Description
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($request->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pending
                                    </span>
                                @elseif($request->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-check mr-1"></i>
                                        Approved
                                    </span>
                                @elseif($request->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Completed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $request->created_at->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $request->created_at->diffForHumans() }}
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        @foreach($communityComplaints as $request)
                        <tr class="request-item hover:bg-gray-50 transition duration-150" data-type="complaint" data-status="{{ $request->status }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-clipboard-list text-indigo-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $request->title }}</div>
                                        <div class="text-sm text-gray-500">{{ $request->category }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($request->description, 50) }}</div>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $request->location ?: 'Location not specified' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($request->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pending
                                    </span>
                                @elseif($request->status === 'under_review')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-eye mr-1"></i>
                                        Under Review
                                    </span>
                                @elseif($request->status === 'in_progress')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-spinner mr-1"></i>
                                        In Progress
                                    </span>
                                @elseif($request->status === 'resolved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Resolved
                                    </span>
                                @elseif($request->status === 'closed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Closed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $request->created_at->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $request->created_at->diffForHumans() }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards (hidden on desktop) -->
        <div class="md:hidden space-y-4" id="requestsMobileCards">
            @foreach($blotterRequests as $request)
            <div class="request-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200" data-type="blotter" data-status="{{ $request->status }}">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-file-alt text-red-600"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">Blotter Report</h3>
                            <p class="text-sm text-gray-500">vs {{ $request->recipient_name }}</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-1
                                @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($request->status === 'approved') bg-blue-100 text-blue-800
                                @elseif($request->status === 'completed') bg-green-100 text-green-800
                                @endif">
                                <i class="fas fa-tag mr-1"></i>
                                {{ ucfirst($request->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-align-left mr-1"></i>
                        {{ Str::limit($request->description, 80) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ $request->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            @endforeach

            @foreach($documentRequests as $request)
            <div class="request-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200" data-type="document" data-status="{{ $request->status }}">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-file-signature text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">{{ $request->document_type }}</h3>
                            <p class="text-sm text-gray-500">Document Request</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-1
                                @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($request->status === 'approved') bg-blue-100 text-blue-800
                                @elseif($request->status === 'completed') bg-green-100 text-green-800
                                @endif">
                                <i class="fas fa-tag mr-1"></i>
                                {{ ucfirst($request->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-align-left mr-1"></i>
                        {{ Str::limit($request->description, 80) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ $request->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden">No requests match your search criteria.</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterBtns = document.querySelectorAll('.filter-btn');
    const requestItems = document.querySelectorAll('.request-item');
    const requestCards = document.querySelectorAll('.request-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active button
            filterBtns.forEach(b => {
                b.classList.remove('active', 'bg-green-100', 'text-green-800');
                b.classList.add('bg-gray-100', 'text-gray-600');
            });
            this.classList.add('active', 'bg-green-100', 'text-green-800');
            this.classList.remove('bg-gray-100', 'text-gray-600');
            
            // Filter requests
            const allItems = [...requestItems, ...requestCards];
            allItems.forEach(item => {
                const type = item.dataset.type;
                const status = item.dataset.status;
                
                let show = false;
                if (filter === 'all') {
                    show = true;
                } else if (filter === 'blotter' && type === 'blotter') {
                    show = true;
                } else if (filter === 'document' && type === 'document') {
                    show = true;
                } else if (filter === 'pending' && status === 'pending') {
                    show = true;
                } else if (filter === 'completed' && status === 'completed') {
                    show = true;
                }
                
                item.style.display = show ? '' : 'none';
            });
            
            updateCounts();
        });
    });
    
    // Update counts
    function updateCounts() {
        let totalVisible = 0, blotter = 0, document = 0, pending = 0, completed = 0;
        
        if (window.innerWidth >= 768) { // Desktop
            const visibleItems = Array.from(requestItems).filter(item => item.style.display !== 'none');
            totalVisible = visibleItems.length;
            blotter = visibleItems.filter(item => item.dataset.type === 'blotter').length;
            document = visibleItems.filter(item => item.dataset.type === 'document').length;
            pending = visibleItems.filter(item => item.dataset.status === 'pending').length;
            completed = visibleItems.filter(item => item.dataset.status === 'completed').length;
        } else { // Mobile
            const visibleCards = Array.from(requestCards).filter(item => item.style.display !== 'none');
            totalVisible = visibleCards.length;
            blotter = visibleCards.filter(item => item.dataset.type === 'blotter').length;
            document = visibleCards.filter(item => item.dataset.type === 'document').length;
            pending = visibleCards.filter(item => item.dataset.status === 'pending').length;
            completed = visibleCards.filter(item => item.dataset.status === 'completed').length;
        }
        
        // Update statistics cards
        document.querySelectorAll('.bg-white.rounded-lg.shadow-sm.border.border-gray-200.p-4').forEach((card, index) => {
            const countElement = card.querySelector('.text-2xl.font-bold.text-gray-900');
            if (countElement) {
                if (index === 0) countElement.textContent = blotter;
                else if (index === 1) countElement.textContent = document;
                else if (index === 2) countElement.textContent = pending;
                else if (index === 3) countElement.textContent = completed;
            }
        });
    }
    
    // Initial count update
    updateCounts();
    // Update counts on window resize
    window.addEventListener('resize', updateCounts);
});
</script>
@endsection