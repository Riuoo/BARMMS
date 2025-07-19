@extends('admin.modals.layout')

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
            <div class="mt-4 sm:mt-0 space-x-2">
                <a href="{{ route('admin.document-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Create New Request
                </a>
                <a href="{{ route('admin.templates.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                    <i class="fas fa-file-code mr-2"></i>
                    Manage Templates
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
        <div class="flex flex-col gap-4">
            <!-- Search Bar -->
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Search requests..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
            
            <!-- Filter Buttons -->
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
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-signature text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Total Requests</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900" id="total-count">{{ $documentRequests->count() }}</p>
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
                    <p class="text-lg lg:text-2xl font-bold text-gray-900" id="pending-count">{{ $documentRequests->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Approved</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900" id="approved-count">{{ $documentRequests->where('status', 'approved')->count() }}</p>
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
                    <p class="text-lg lg:text-2xl font-bold text-gray-900" id="completed-count">{{ $documentRequests->where('status', 'completed')->count() }}</p>
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
                                    Description
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
                                <div class="flex items-center justify-center">
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
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $request->user->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><i class="fas fa-file-alt mr-1"></i>{{ $request->document_type }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs">
                                    <div class="truncate" title="{{ $request->description }}">
                                        {{ Str::limit($request->description, 50) }}
                                    </div>
                                    @if(strlen($request->description) > 50)
                                        <button data-description="{{ $request->description }}" 
                                                data-user-name="{{ $request->user->name ?? 'N/A' }}"
                                                class="view-full-btn text-xs text-blue-600 hover:text-blue-800 underline mt-1">
                                            View Full
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 justify-center">
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
                                <div class="text-sm text-gray-900"><i class="fas fa-calendar mr-1"></i>{{ $request->created_at->format('M d, Y') }}</div>   
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($request->status === 'pending')
                                        <div class="inline-block">
                                            <form action="{{ route('admin.document-requests.approve', $request->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
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
                                        <div class="inline-block">
                                            <form action="{{ route('admin.document-requests.complete', $request->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Complete
                                                </button>
                                            </form>
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
                <!-- Header Section -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-file-signature text-blue-600"></i>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-gray-900 truncate">{{ $request->user->name ?? 'N/A' }}</h3>
                            <p class="text-sm text-gray-500 truncate">{{ $request->document_type }}</p>
                            <div class="flex items-center mt-1">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($request->status === 'approved') bg-blue-100 text-blue-800
                                    @elseif($request->status === 'completed') bg-green-100 text-green-800
                                    @endif">
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ ucfirst($request->status) }}
                                </span>
                                <span class="ml-2 text-xs text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $request->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="mb-3">
                    <div class="description-container">
                        <p class="text-sm text-gray-600 leading-relaxed description-text" id="description-{{ $request->id }}">
                            <i class="fas fa-align-left mr-1 text-gray-400"></i>
                            <span class="description-short">{{ Str::limit($request->description, 80) }}</span>
                            @if(strlen($request->description) > 80)
                                <span class="description-full hidden">{{ $request->description }}</span>
                                <button onclick="toggleDescription('{{ $request->id }}')" 
                                        class="text-blue-600 hover:text-blue-800 underline text-xs ml-1 toggle-desc-btn">
                                    Read More
                                </button>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Document Type Badge -->
                <div class="mb-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-file-alt mr-1"></i>
                        {{ $request->document_type }}
                    </span>
                </div>

                <!-- Actions Section -->
                <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100">
                    <button onclick="viewDocumentDetails('{{ $request->id }}')"
                            type="button"
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                        <i class="fas fa-eye mr-1"></i>
                        View Details
                    </button>
                    
                    @if($request->status === 'pending')
                        <form action="{{ route('admin.document-requests.approve', $request->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition duration-200">
                                <i class="fas fa-check mr-1"></i>
                                Approve
                            </button>
                        </form>
                    @endif
                    
                    @if($request->status === 'approved')
                        <a href="{{ route('admin.document-requests.pdf', $request->id) }}" target="_blank"
                           class="inline-flex items-center px-3 py-1.5 border border-blue-500 text-xs font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50 transition duration-200">
                            <i class="fas fa-file-pdf mr-1"></i>
                            Generate PDF
                        </a>
                        <form action="{{ route('admin.document-requests.complete', $request->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition duration-200">
                                <i class="fas fa-check-circle mr-1"></i>
                                Complete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden"></p>
</div>

<!-- Modals -->
@include('admin.modals.document-modals')

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
            
            // Update counts
            function updateCounts() {
                // No longer update the statistics cards! The statistics cards always show the Blade-rendered totals.
                // This function is now empty or can be removed if not used elsewhere.
            }
            // Initial count update
            // updateCounts(); // No longer needed
            // Update counts on window resize
            // window.removeEventListener('resize', updateCounts); // No longer needed
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
        // Update counts
        function updateCounts() {
            // No longer update the statistics cards! The statistics cards always show the Blade-rendered totals.
            // This function is now empty or can be removed if not used elsewhere.
        }
        // Initial count update
        // updateCounts(); // No longer needed
        // Update counts on window resize
        // window.removeEventListener('resize', updateCounts); // No longer needed
    });
    
    // Update counts
    function updateCounts() {
        // No longer update the statistics cards! The statistics cards always show the Blade-rendered totals.
        // This function is now empty or can be removed if not used elsewhere.
    }
    // Initial count update
    // updateCounts(); // No longer needed
    // Update counts on window resize
    // window.removeEventListener('resize', updateCounts); // No longer needed
});

// Function to toggle description visibility
function toggleDescription(requestId) {
    const descriptionContainer = document.getElementById(`description-${requestId}`);
    const shortDesc = descriptionContainer.querySelector('.description-short');
    const fullDesc = descriptionContainer.querySelector('.description-full');
    const toggleBtn = descriptionContainer.querySelector('.toggle-desc-btn');
    
    if (fullDesc.classList.contains('hidden')) {
        // Show full description
        shortDesc.classList.add('hidden');
        fullDesc.classList.remove('hidden');
        toggleBtn.textContent = 'Read Less';
    } else {
        // Show short description
        shortDesc.classList.remove('hidden');
        fullDesc.classList.add('hidden');
        toggleBtn.textContent = 'Read More';
    }
}

// Function to show full description in modal
function showFullDescription(description, userName) {
    // Create modal HTML
    const modalHTML = `
        <div id="descriptionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Full Description - ${userName}
                        </h3>
                        <button onclick="closeDescriptionModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">
                            ${description}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// Function to close description modal
function closeDescriptionModal() {
    const modal = document.getElementById('descriptionModal');
    if (modal) {
        modal.remove();
    }
}

// Add event listeners for view full buttons
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for view full buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-full-btn')) {
            const description = e.target.getAttribute('data-description');
            const userName = e.target.getAttribute('data-user-name');
            showFullDescription(description, userName);
        }
    });
});

// Document functions are defined in the partial file (admin.modals.document-modals)
</script>
@endsection
