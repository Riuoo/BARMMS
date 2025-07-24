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

    <!-- Search and Filter Section (Server-side) -->
    <form method="GET" action="{{ route('admin.accomplished-projects') }}" class="bg-gray-50 rounded-lg p-4 sm:p-6 mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="searchInput" placeholder="Search projects by title, description, or category..." 
                        class="w-full pl-10 pr-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm sm:text-base"
                        value="{{ request('search') }}">
                </div>
            </div>
            
            <!-- Category Filter -->
            <div class="sm:w-48">
                <select name="category" id="categoryFilter" class="w-full px-3 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm sm:text-base">
                    <option value="">All Categories</option>
                    <option value="Infrastructure" {{ request('category') == 'Infrastructure' ? 'selected' : '' }}>Infrastructure</option>
                    <option value="Health" {{ request('category') == 'Health' ? 'selected' : '' }}>Health</option>
                    <option value="Education" {{ request('category') == 'Education' ? 'selected' : '' }}>Education</option>
                    <option value="Agriculture" {{ request('category') == 'Agriculture' ? 'selected' : '' }}>Agriculture</option>
                    <option value="Social Services" {{ request('category') == 'Social Services' ? 'selected' : '' }}>Social Services</option>
                    <option value="Environment" {{ request('category') == 'Environment' ? 'selected' : '' }}>Environment</option>
                    <option value="Livelihood" {{ request('category') == 'Livelihood' ? 'selected' : '' }}>Livelihood</option>
                </select>
            </div>
            
            <!-- Featured Filter -->
            <div class="sm:w-48">
                <select name="featured" id="featuredFilter" class="w-full px-3 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm sm:text-base">
                    <option value="">All Projects</option>
                    <option value="featured" {{ request('featured') == 'featured' ? 'selected' : '' }}>Featured Only</option>
                    <option value="non-featured" {{ request('featured') == 'non-featured' ? 'selected' : '' }}>Non-Featured</option>
                </select>
            </div>
            <div class="flex items-center">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-300">Search</button>
                <a href="{{ route('admin.accomplished-projects') }}" class="ml-2 text-green-600 hover:text-green-800 font-medium">Clear</a>
            </div>
        </div>
        <div class="mt-4 flex items-center justify-between text-sm text-gray-600">
            <span id="resultsInfo">Showing {{ $projects->count() }} project{{ $projects->count() === 1 ? '' : 's' }}</span>
        </div>
    </form>

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
    <div id="projectsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @include('admin.accomplished-projects.partials.project_cards', ['projects' => $projects])
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

