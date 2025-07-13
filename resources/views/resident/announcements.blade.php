@extends('resident.layout')

@section('title', 'Announcements')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Barangay Announcements</h1>
                <p class="text-gray-600">Stay updated with the latest news and important information from the barangay</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ now()->format('l, F d, Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-bullhorn text-yellow-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Announcements</p>
                    <p class="text-2xl font-bold text-gray-900">{{ isset($announcements) ? count($announcements) : 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-green-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">This Month</p>
                    <p class="text-2xl font-bold text-gray-900">{{ isset($announcements) ? $announcements->where('created_at', '>=', now()->startOfMonth())->count() : 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-star text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Important</p>
                    <p class="text-2xl font-bold text-gray-900">{{ isset($announcements) ? $announcements->where('priority', 'high')->count() : 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                <button class="filter-btn active px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800" data-filter="all">
                    All Announcements
                </button>
                <button class="filter-btn px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="important">
                    Important
                </button>
                <button class="filter-btn px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="recent">
                    Recent
                </button>
            </div>
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Search announcements..." class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements List -->
    @if(empty($announcements))
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-bullhorn text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No announcements available</h3>
            <p class="text-gray-500">Check back later for updates from the barangay office.</p>
        </div>
    @else
        <div class="space-y-6" id="announcementsList">
            @foreach($announcements as $announcement)
            <div class="announcement-item bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition duration-200" 
                 data-priority="{{ $announcement->priority ?? 'normal' }}" 
                 data-date="{{ $announcement->created_at->format('Y-m-d') }}">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-3">
                                @if(($announcement->priority ?? 'normal') === 'high')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-3">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Important
                                    </span>
                                @endif
                                <span class="text-sm text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $announcement->created_at->format('M d, Y') }}
                                </span>
                            </div>
                            
                            <h2 class="text-xl font-semibold text-gray-900 mb-3">{{ $announcement->title }}</h2>
                            
                            <div class="prose max-w-none">
                                <p class="text-gray-700 leading-relaxed mb-4">{{ $announcement->content }}</p>
                            </div>

                            @if($announcement->attachment)
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <a href="{{ Storage::url($announcement->attachment) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                                    <i class="fas fa-paperclip mr-2"></i>
                                    View Attachment
                                </a>
                            </div>
                            @endif

                            @if($announcement->author)
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-user mr-2"></i>
                                    <span>Posted by: {{ $announcement->author }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Load More Button (if needed) -->
        @if(isset($announcements) && count($announcements) > 5)
        <div class="mt-8 text-center">
            <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                <i class="fas fa-plus mr-2"></i>
                Load More Announcements
            </button>
        </div>
        @endif
    @endif

    <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden">No announcements match your search criteria.</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterBtns = document.querySelectorAll('.filter-btn');
    const announcementItems = document.querySelectorAll('.announcement-item');
    
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
            
            // Filter announcements
            announcementItems.forEach(item => {
                const priority = item.dataset.priority;
                const date = item.dataset.date;
                const now = new Date();
                const itemDate = new Date(date);
                const daysDiff = Math.floor((now - itemDate) / (1000 * 60 * 60 * 24));
                
                let show = false;
                if (filter === 'all') {
                    show = true;
                } else if (filter === 'important' && priority === 'high') {
                    show = true;
                } else if (filter === 'recent' && daysDiff <= 7) {
                    show = true;
                }
                
                item.style.display = show ? '' : 'none';
            });
            
            updateCounts();
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        announcementItems.forEach(item => {
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
        const visibleItems = Array.from(announcementItems).filter(item => item.style.display !== 'none');
        const totalVisible = visibleItems.length;
        const importantCount = visibleItems.filter(item => item.dataset.priority === 'high').length;
        const recentCount = visibleItems.filter(item => {
            const date = new Date(item.dataset.date);
            const now = new Date();
            const daysDiff = Math.floor((now - date) / (1000 * 60 * 60 * 24));
            return daysDiff <= 7;
        }).length;
        
        // Update statistics cards
        const statCards = document.querySelectorAll('.bg-white.rounded-lg.shadow-sm.border.border-gray-200.p-4');
        if (statCards.length >= 3) {
            const countElements = statCards[1].querySelector('.text-2xl.font-bold.text-gray-900');
            const importantElements = statCards[2].querySelector('.text-2xl.font-bold.text-gray-900');
            if (countElements) countElements.textContent = totalVisible;
            if (importantElements) importantElements.textContent = importantCount;
        }
    }
    
    // Initial count update
    updateCounts();
});
</script>
@endsection