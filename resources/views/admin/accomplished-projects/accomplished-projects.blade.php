@extends('admin.modals.layout')

@section('title', 'Accomplished Projects')

@section('content')
<div class="max-w-7xl mx-auto bg-white rounded-lg shadow p-4 sm:p-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Accomplished Projects</h1>
            <p class="text-gray-600 text-sm sm:text-base">Manage and showcase completed community projects</p>
        </div>
        <a href="{{ route('admin.accomplished-projects.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-medium transition duration-300 flex items-center text-sm sm:text-base">
            <i class="fas fa-plus mr-2"></i>
            Add New Project
        </a>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-gray-50 rounded-lg p-4 sm:p-6 mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="searchInput" placeholder="Search projects by title, description, or category..." 
                        class="w-full pl-10 pr-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm sm:text-base">
                </div>
            </div>
            
            <!-- Category Filter -->
            <div class="sm:w-48">
                <select id="categoryFilter" class="w-full px-3 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm sm:text-base">
                    <option value="">All Categories</option>
                    <option value="Infrastructure">Infrastructure</option>
                    <option value="Health">Health</option>
                    <option value="Education">Education</option>
                    <option value="Agriculture">Agriculture</option>
                    <option value="Social Services">Social Services</option>
                    <option value="Environment">Environment</option>
                    <option value="Livelihood">Livelihood</option>
                </select>
            </div>
            
            <!-- Featured Filter -->
            <div class="sm:w-48">
                <select id="featuredFilter" class="w-full px-3 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm sm:text-base">
                    <option value="">All Projects</option>
                    <option value="featured">Featured Only</option>
                    <option value="non-featured">Non-Featured</option>
                </select>
            </div>
        </div>
        
        <!-- Results Info -->
        <div class="mt-4 flex items-center justify-between text-sm text-gray-600">
            <span id="resultsInfo">Showing {{ $projects->count() }} projects</span>
            <button id="clearFilters" class="text-green-600 hover:text-green-800 font-medium hidden">
                Clear Filters
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 sm:p-6 text-white h-full">
            <div class="flex items-center justify-between h-full">
                <div class="flex-1">
                    <p class="text-blue-100 text-xs sm:text-sm font-medium mb-1">Total Projects</p>
                    <p class="text-2xl sm:text-3xl font-bold">{{ $stats['total_projects'] }}</p>
                </div>
                <div class="bg-blue-400 rounded-full p-2 sm:p-3 flex-shrink-0">
                    <i class="fas fa-project-diagram text-lg sm:text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 sm:p-6 text-white h-full">
            <div class="flex items-center justify-between h-full">
                <div class="flex-1">
                    <p class="text-green-100 text-xs sm:text-sm font-medium mb-1">Total Budget</p>
                    <p class="text-2xl sm:text-3xl font-bold">₱{{ number_format($stats['total_budget'], 2) }}</p>
                </div>
                <div class="bg-green-400 rounded-full p-2 sm:p-3 flex-shrink-0">
                    <i class="fas fa-money-bill-wave text-lg sm:text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 sm:p-6 text-white h-full">
            <div class="flex items-center justify-between h-full">
                <div class="flex-1">
                    <p class="text-purple-100 text-xs sm:text-sm font-medium mb-1">Featured Projects</p>
                    <p class="text-2xl sm:text-3xl font-bold">{{ $stats['featured_projects'] }}</p>
                </div>
                <div class="bg-purple-400 rounded-full p-2 sm:p-3 flex-shrink-0">
                    <i class="fas fa-star text-lg sm:text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 sm:p-6 text-white h-full">
            <div class="flex items-center justify-between h-full">
                <div class="flex-1">
                    <p class="text-orange-100 text-xs sm:text-sm font-medium mb-1">Recent Projects</p>
                    <p class="text-2xl sm:text-3xl font-bold">{{ $stats['recent_projects'] }}</p>
                </div>
                <div class="bg-orange-400 rounded-full p-2 sm:p-3 flex-shrink-0">
                    <i class="fas fa-clock text-lg sm:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @foreach($projects as $project)
        <div class="project-card bg-white rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <!-- Project Image -->
            <div class="h-40 sm:h-48 rounded-t-xl overflow-hidden">
                @if($project->image)
                    <img src="{{ asset($project->image) }}" alt="{{ $project->title }}" class="w-full h-full object-cover">
                @else
                    <div class="h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                        <i class="fas fa-image text-3xl sm:text-4xl text-gray-400"></i>
                    </div>
                @endif
            </div>
            
            <!-- Project Content -->
            <div class="p-4 sm:p-6">
                <!-- Category Badge -->
                <div class="flex items-center justify-between mb-3">
                    <span class="category-badge px-2 sm:px-3 py-1 rounded-full text-xs font-medium {{ $project->category_color }}">
                        {{ $project->category }}
                    </span>
                    @if($project->is_featured)
                        <span class="featured-star text-yellow-500">
                            <i class="fas fa-star text-sm sm:text-base"></i>
                        </span>
                    @endif
                </div>
                
                <!-- Project Title -->
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">{{ $project->title }}</h3>
                
                <!-- Project Description -->
                <p class="text-gray-600 text-xs sm:text-sm mb-4 line-clamp-3">{{ Str::limit($project->description, 100) }}</p>
                
                <!-- Project Details -->
                <div class="space-y-1 sm:space-y-2 mb-4">
                    <div class="flex items-center text-xs sm:text-sm text-gray-500">
                        <i class="fas fa-map-marker-alt mr-1 sm:mr-2 text-green-600 text-xs sm:text-sm"></i>
                        <span class="truncate">{{ $project->location ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center text-xs sm:text-sm text-gray-500">
                        <i class="fas fa-calendar mr-1 sm:mr-2 text-blue-600 text-xs sm:text-sm"></i>
                        <span>{{ $project->completion_date->format('M Y') }}</span>
                    </div>
                    <div class="flex items-center text-xs sm:text-sm text-gray-500">
                        <i class="fas fa-money-bill mr-1 sm:mr-2 text-green-600 text-xs sm:text-sm"></i>
                        <span class="truncate">{{ $project->formatted_budget }}</span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-3 sm:pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.accomplished-projects.show', $project->id) }}" class="text-blue-600 hover:text-blue-800 text-xs sm:text-sm font-medium flex items-center">
                        <i class="fas fa-eye mr-1 text-xs sm:text-sm"></i>
                        <span class="hidden sm:inline">View Details</span>
                        <span class="sm:hidden">View</span>
                    </a>
                    <div class="flex items-center space-x-1 sm:space-x-2">
                        <a href="{{ route('admin.accomplished-projects.edit', $project->id) }}" class="text-gray-600 hover:text-gray-800 p-1.5 sm:p-2 rounded-lg hover:bg-gray-100 transition duration-200 flex items-center justify-center w-8 h-8">
                            <i class="fas fa-edit text-sm sm:text-base"></i>
                        </a>
                        <form action="{{ route('admin.accomplished-projects.toggle-featured', $project->id) }}" method="POST" class="inline m-0 p-0">
                            @csrf
                            <button type="submit" class="text-yellow-600 hover:text-yellow-800 p-1.5 sm:p-2 rounded-lg hover:bg-yellow-50 transition duration-200 flex items-center justify-center w-8 h-8">
                                <i class="fas fa-star text-sm sm:text-base"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.accomplished-projects.destroy', $project->id) }}" method="POST" class="inline m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this project? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 p-1.5 sm:p-2 rounded-lg hover:bg-red-50 transition duration-200 flex items-center justify-center w-8 h-8">
                                <i class="fas fa-trash text-sm sm:text-base"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($projects->isEmpty())
    <div class="text-center py-8 sm:py-12">
        <div class="text-gray-400 mb-4">
            <i class="fas fa-project-diagram text-4xl sm:text-6xl"></i>
        </div>
        <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">No Projects Yet</h3>
        <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base px-4">Start by adding your first accomplished project to showcase community achievements.</p>
        <a href="{{ route('admin.accomplished-projects.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-medium transition duration-300 inline-flex items-center text-sm sm:text-base">
            <i class="fas fa-plus mr-2"></i>
            Add First Project
        </a>
    </div>
    @endif
</div>



<!-- Project Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 id="detailsTitle" class="text-2xl font-bold text-gray-900">Project Details</h3>
                    <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div id="projectDetails" class="space-y-6">
                    <!-- Project details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function closeDetailsModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}
</script>

<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection

<script>


document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const featuredFilter = document.getElementById('featuredFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const resultsInfo = document.getElementById('resultsInfo');
    const projectCards = document.querySelectorAll('.project-card');
    
    let allProjects = Array.from(projectCards);
    let filteredProjects = [...allProjects];
    
    function filterProjects() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        const selectedFeatured = featuredFilter.value;
        
        // Check if any filters are active
        const hasFilters = searchTerm || selectedCategory || selectedFeatured;
        clearFiltersBtn.classList.toggle('hidden', !hasFilters);
        
        filteredProjects = allProjects.filter(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const description = card.querySelector('p').textContent.toLowerCase();
            const category = card.querySelector('.category-badge').textContent.trim();
            const isFeatured = card.querySelector('.featured-star') !== null;
            
            // Search filter
            const matchesSearch = !searchTerm || 
                title.includes(searchTerm) || 
                description.includes(searchTerm) || 
                category.toLowerCase().includes(searchTerm);
            
            // Category filter
            const matchesCategory = !selectedCategory || category === selectedCategory;
            
            // Featured filter
            let matchesFeatured = true;
            if (selectedFeatured === 'featured') {
                matchesFeatured = isFeatured;
            } else if (selectedFeatured === 'non-featured') {
                matchesFeatured = !isFeatured;
            }
            
            return matchesSearch && matchesCategory && matchesFeatured;
        });
        
        // Update display
        updateDisplay();
    }
    
    function updateDisplay() {
        projectCards.forEach(card => {
            card.style.display = 'none';
        });
        
        filteredProjects.forEach(card => {
            card.style.display = 'block';
        });
        
        // Update results info
        resultsInfo.textContent = `Showing ${filteredProjects.length} of ${allProjects.length} projects`;
        
        // Show empty state if no results
        const emptyState = document.querySelector('.empty-state');
        if (filteredProjects.length === 0 && allProjects.length > 0) {
            if (!emptyState) {
                showEmptyState();
            }
        } else {
            if (emptyState) {
                emptyState.remove();
            }
        }
    }
    
    function showEmptyState() {
        const projectsGrid = document.querySelector('.grid');
        const emptyState = document.createElement('div');
        emptyState.className = 'empty-state col-span-full text-center py-12';
        emptyState.innerHTML = `
            <div class="text-gray-400 mb-4">
                <i class="fas fa-search text-4xl sm:text-6xl"></i>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">No Projects Found</h3>
            <p class="text-gray-600 text-sm sm:text-base">Try adjusting your search criteria or filters.</p>
        `;
        projectsGrid.appendChild(emptyState);
    }
    
    function clearAllFilters() {
        searchInput.value = '';
        categoryFilter.value = '';
        featuredFilter.value = '';
        filterProjects();
    }
    
    // Event listeners
    searchInput.addEventListener('input', filterProjects);
    categoryFilter.addEventListener('change', filterProjects);
    featuredFilter.addEventListener('change', filterProjects);
    clearFiltersBtn.addEventListener('click', clearAllFilters);
    
    // Add data attributes to project cards for easier filtering
    projectCards.forEach(card => {
        const title = card.querySelector('h3').textContent;
        const description = card.querySelector('p').textContent;
        const category = card.querySelector('.category-badge').textContent.trim();
        const isFeatured = card.querySelector('.featured-star') !== null;
        
        card.setAttribute('data-title', title.toLowerCase());
        card.setAttribute('data-description', description.toLowerCase());
        card.setAttribute('data-category', category);
        card.setAttribute('data-featured', isFeatured);
    });
});
</script>

