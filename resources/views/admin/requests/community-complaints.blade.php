@extends('admin.modals.layout')

@section('title', 'Community Complaints')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Community Complaints</h1>
                <p class="text-gray-600">Manage and track community complaints from residents</p>
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
                <input type="text" id="complaintSearchInput" placeholder="Search complaints..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
            
            <!-- Filter Buttons -->
            <div class="flex flex-wrap gap-2">
                <button class="filter-btn active px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800" data-filter="all">
                    All Complaints
                </button>
                <button class="filter-btn px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="pending">
                    Pending
                </button>
                <button class="filter-btn px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="under_review">
                    Under Review
                </button>
                <button class="filter-btn px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="in_progress">
                    In Progress
                </button>
                <button class="filter-btn px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="resolved">
                    Resolved
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-3 lg:gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Total Complaints</p>
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

    <!-- Complaints List -->
    @if($complaints->count() > 0)
        <!-- Desktop Table (hidden on mobile) -->
        <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-clipboard-list mr-2"></i>
                                    Complaint
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-cogs mr-2"></i>
                                    Actions
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($complaints as $complaint)
                        <tr class="complaint-item hover:bg-gray-50 transition duration-150" data-status="{{ $complaint->status }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $complaint->title }}</div>
                                        <div class="text-sm text-gray-500 max-w-xs">
                                            <div class="truncate" title="{{ $complaint->description }}">
                                                {{ Str::limit($complaint->description, 50) }}
                                            </div>
                                            @if(strlen($complaint->description) > 50)
                                                <button onclick="showFullDescription('{{ addslashes($complaint->description) }}', '{{ $complaint->title }}')"
                                                        class="text-xs text-blue-600 hover:text-blue-800 underline mt-1 md:hidden">
                                                    View Full
                                                </button>
                                            @endif
                                        </div>
                                        @if($complaint->location)
                                            <div class="text-xs text-gray-400">
                                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $complaint->location }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ $complaint->category }}
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
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$complaint->status] }}">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    {{ str_replace('_', ' ', ucfirst($complaint->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $complaint->user->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $complaint->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    @if($complaint->status !== 'resolved' && $complaint->status !== 'closed')
                                        <button onclick="openUpdateStatusModal({{ $complaint->id }})"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                                            <i class="fas fa-edit mr-1"></i>
                                            Update
                                        </button>
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
        <div class="md:hidden space-y-4">
            @foreach($complaints as $complaint)
            <div class="complaint-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200" data-status="{{ $complaint->status }}">
                <!-- Header Section -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clipboard-list text-blue-600"></i>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-gray-900 truncate">{{ $complaint->title }}</h3>
                            <p class="text-sm text-gray-500 truncate">{{ $complaint->user->name ?? 'N/A' }}</p>
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
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$complaint->status] }}">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    {{ str_replace('_', ' ', ucfirst($complaint->status)) }}
                                </span>
                                <span class="ml-2 text-xs text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $complaint->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="mb-3">
                    <div class="description-container">
                        <p class="text-sm text-gray-600 leading-relaxed description-text" id="description-{{ $complaint->id }}">
                            <i class="fas fa-align-left mr-1 text-gray-400"></i>
                            <span class="description-short">{{ Str::limit($complaint->description, 80) }}</span>
                            @if(strlen($complaint->description) > 80)
                                <span class="description-full hidden">{{ $complaint->description }}</span>
                                <button onclick="toggleDescription({{ $complaint->id }})"
                                        class="text-blue-600 hover:text-blue-800 underline text-xs ml-1 toggle-desc-btn">
                                    Read More
                                </button>
                                <button onclick="showFullDescription('{{ addslashes($complaint->description) }}', '{{ $complaint->title }}')"
                                        class="text-xs text-blue-600 hover:text-blue-800 underline mt-1 md:hidden">
                                    View Full
                                </button>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Category Section -->
                <div class="mb-3 flex flex-wrap gap-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        <i class="fas fa-tag mr-1"></i>
                        {{ $complaint->category }}
                    </span>
                </div>

                <!-- Location Section -->
                @if($complaint->location)
                <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                        <span class="text-sm text-gray-600">{{ $complaint->location }}</span>
                    </div>
                </div>
                @endif

                <!-- Actions Section -->
                <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100">
                    <button onclick="viewComplaintDetails({{ $complaint->id }})"
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200 md:hidden">
                        <i class="fas fa-eye mr-1"></i>
                        View Details
                    </button>
                    @if($complaint->status !== 'resolved' && $complaint->status !== 'closed')
                        <button onclick="openUpdateStatusModal({{ $complaint->id }})"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition duration-200">
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
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No complaints found</h3>
            <p class="text-gray-500">No community complaints have been submitted yet.</p>
        </div>
    @endif

    <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden"></p>
</div>

<!-- Modals -->
@include('admin.modals.community-complaint-modals')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterBtns = document.querySelectorAll('.filter-btn');
    const complaintItems = document.querySelectorAll('.complaint-item');
    const complaintCards = document.querySelectorAll('.complaint-card');
    
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
            
            // Filter complaints
            const allItems = [...complaintItems, ...complaintCards];
            allItems.forEach(item => {
                const status = item.dataset.status;
                let show = false;
                if (filter === 'all') {
                    show = true;
                } else if (status === filter) {
                    show = true;
                }
                item.style.display = show ? '' : 'none';
            });
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('complaintSearchInput');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        const allItems = [...complaintItems, ...complaintCards];
        allItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
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
function showFullDescription(description, title) {
    // Create modal HTML
    const modalHTML = `
        <div id="descriptionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Full Description - ${title}
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

// Function to view complaint details
function viewComplaintDetails(id) {
    // Show loading state
    document.getElementById('complaintDetailsContent').innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600">Loading details...</span>
        </div>
    `;
    
    document.getElementById('complaintDetailsModal').classList.remove('hidden');
    
    // Fetch complaint details via AJAX
    fetch(`/admin/community-complaints/${id}/details`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('complaintDetailsContent').innerHTML = `
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Title</h4>
                        <p class="text-gray-600">${data.title}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Category</h4>
                        <p class="text-gray-600">${data.category}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Location</h4>
                        <p class="text-gray-600">${data.location || 'Not specified'}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Description</h4>
                        <p class="text-gray-600">${data.description}</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Submitted By</h4>
                            <p class="text-gray-600">${data.user_name}</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Status</h4>
                            <p class="text-gray-600">${data.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Submitted</h4>
                            <p class="text-gray-600">${data.created_at}</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Assigned</h4>
                            <p class="text-gray-600">${data.assigned_at}</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Resolved</h4>
                            <p class="text-gray-600">${data.resolved_at}</p>
                        </div>
                    </div>
                    ${data.media_files && data.media_files.length > 0 ? `
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Attached Files (${data.media_files.length})</h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                ${data.media_files.map(file => `
                                    <a href="${file.url}" target="_blank" class="flex items-center p-2 border border-gray-200 rounded hover:bg-gray-50">
                                        <i class="fas fa-file text-gray-400 mr-2"></i>
                                        <span class="text-sm text-gray-600 truncate">${file.name}</span>
                                    </a>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
        })
        .catch(error => {
            console.error('Error fetching complaint details:', error);
            document.getElementById('complaintDetailsContent').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-red-400 text-4xl mb-4"></i>
                    <p class="text-gray-600">Error loading complaint details</p>
                </div>
            `;
        });
}
</script>
@endsection 