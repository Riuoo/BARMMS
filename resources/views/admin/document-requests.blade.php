@extends('admin.layout')

@section('title', 'Document Requests')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Document Requests</h1>
                <p class="text-gray-600">Manage and process document requests from residents</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.document-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Create New Request
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
    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                <button class="filter-btn active px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800" data-filter="all">
                    All Requests
                </button>
                <button class="filter-btn px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="pending">
                    Pending
                </button>
                <button class="filter-btn px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="approved">
                    Approved
                </button>
                <button class="filter-btn px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="completed">
                    Completed
                </button>
            </div>
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Search requests..." class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-signature text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Requests</p>
                    <p class="text-2xl font-bold text-gray-900" id="total-count">{{ $documentRequests->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-2xl font-bold text-gray-900" id="pending-count">{{ $documentRequests->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Approved</p>
                    <p class="text-2xl font-bold text-gray-900" id="approved-count">{{ $documentRequests->where('status', 'approved')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-purple-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Completed</p>
                    <p class="text-2xl font-bold text-gray-900" id="completed-count">{{ $documentRequests->where('status', 'completed')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Requests List -->
    @if($documentRequests->isEmpty())
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-file-signature text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No document requests found</h3>
            <p class="text-gray-500">No document requests have been submitted yet.</p>
            <div class="mt-6">
                <a href="{{ route('admin.document-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Create First Request
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
                                    <i class="fas fa-user mr-2"></i>
                                    Requester
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-file-alt mr-2"></i>
                                    Document Type
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-align-left mr-2"></i>
                                    Purpose
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
                                    Requested
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-cogs mr-2"></i>
                                    Actions
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="documentTableBody">
                        @foreach($documentRequests as $request)
                        <tr class="document-item hover:bg-gray-50 transition duration-150" data-status="{{ $request->status }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $request->user->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $request->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $request->document_type }}</div>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-file-alt mr-1"></i>
                                    Document
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($request->purpose, 50) }}</div>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-align-left mr-1"></i>
                                    Purpose
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($request->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pending
                                    </span>
                                @elseif($request->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>
                                        Approved
                                    </span>
                                @elseif($request->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex flex-wrap items-center gap-2">
                                    <div class="inline-block">
                                        <button onclick="viewDocumentDetails({{ $request->id }})"
                                                type="button"
                                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                                            <i class="fas fa-eye mr-1"></i>
                                            View
                                        </button>
                                    </div>
                                    @if($request->status === 'pending')
                                        <div class="inline-block">
                                            <form action="{{ route('admin.document-requests.approve', $request->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Approve
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                    @if($request->status === 'approved')
                                        <div class="inline-block">
                                            <a href="{{ route('admin.document-requests.pdf', $request->id) }}" target="_blank"
                                               class="inline-flex items-center px-3 py-1.5 border border-blue-500 text-xs font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                                                <i class="fas fa-file-pdf mr-1"></i>
                                                Generate PDF
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards (hidden on desktop) -->
        <div class="md:hidden space-y-4" id="mobileDocumentCards">
            @foreach($documentRequests as $request)
            <div class="document-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200" data-status="{{ $request->status }}">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-file-signature text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">{{ $request->user->name ?? 'N/A' }}</h3>
                            <p class="text-sm text-gray-500">{{ $request->document_type }}</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-1
                                @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($request->status === 'approved') bg-green-100 text-green-800
                                @elseif($request->status === 'completed') bg-purple-100 text-purple-800
                                @endif">
                                <i class="fas fa-tag mr-1"></i>
                                {{ ucfirst($request->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="inline-block">
                            <button onclick="viewDocumentDetails({{ $request->id }})"
                                    type="button"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                                <i class="fas fa-eye mr-1"></i>
                                View
                            </button>
                        </div>
                        @if($request->status === 'pending')
                            <div class="inline-block">
                                <form action="{{ route('admin.document-requests.approve', $request->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition duration-200">
                                        <i class="fas fa-check mr-1"></i>
                                        Approve
                                    </button>
                                </form>
                            </div>
                        @endif
                        @if($request->status === 'approved' || $request->status === 'completed')
                            <div class="inline-block">
                                <a href="{{ route('admin.document-requests.pdf', $request->id) }}" target="_blank"
                                   class="inline-flex items-center px-3 py-1.5 border border-blue-500 text-xs font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50 transition duration-200">
                                    <i class="fas fa-file-pdf mr-1"></i>
                                    Generate PDF
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-align-left mr-1"></i>
                        {{ Str::limit($request->purpose, 80) }}
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

    <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden"></p>
</div>

<!-- Modals -->
@include('admin.partials.document-modals')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterBtns = document.querySelectorAll('.filter-btn');
    const documentItems = document.querySelectorAll('.document-item');
    const documentCards = document.querySelectorAll('.document-card');
    
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
            
            // Filter documents
            const allItems = [...documentItems, ...documentCards];
            allItems.forEach(item => {
                const status = item.dataset.status;
                if (filter === 'all' || status === filter) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
            
            updateCounts();
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        const allItems = [...documentItems, ...documentCards];
        allItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
        updateCounts();
    });
    
    // Update counts
    function updateCounts() {
        let totalVisible = 0, pending = 0, approved = 0, completed = 0;
        if (window.innerWidth >= 768) { // Desktop
            const visibleItems = Array.from(documentItems).filter(item => item.style.display !== 'none');
            totalVisible = visibleItems.length;
            pending = visibleItems.filter(item => item.dataset.status === 'pending').length;
            approved = visibleItems.filter(item => item.dataset.status === 'approved').length;
            completed = visibleItems.filter(item => item.dataset.status === 'completed').length;
        } else { // Mobile
            const visibleCards = Array.from(documentCards).filter(item => item.style.display !== 'none');
            totalVisible = visibleCards.length;
            pending = visibleCards.filter(item => item.dataset.status === 'pending').length;
            approved = visibleCards.filter(item => item.dataset.status === 'approved').length;
            completed = visibleCards.filter(item => item.dataset.status === 'completed').length;
        }
        document.getElementById('total-count').textContent = totalVisible;
        document.getElementById('pending-count').textContent = pending;
        document.getElementById('approved-count').textContent = approved;
        document.getElementById('completed-count').textContent = completed;
    }
    // Initial count update
    updateCounts();
    // Update counts on window resize
    window.addEventListener('resize', updateCounts);
});
// Document functions are defined in the partial file (admin.partials.document-modals)
</script>
@endsection
