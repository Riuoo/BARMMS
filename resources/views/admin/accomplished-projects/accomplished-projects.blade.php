@extends('admin.modals.layout')

@section('title', 'Accomplished Projects')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-6 md:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Accomplished Projects</h1>
                <p class="text-gray-600">Manage and showcase completed community projects</p>
            </div>
            <div>
                <a href="{{ route('admin.accomplished-projects.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Project
                </a>
            </div>
        </div>
    </div>
        
    <!-- Search and Filter Section (Server-side) -->
    <form method="GET" action="{{ route('admin.accomplished-projects') }}" class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="searchInput" placeholder="Search projects by title, description, or category..." 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                    value="{{ request('search') }}">
                </div>
            </div>
            
            <!-- Category Filter -->
            <div class="sm:w-48">
                <select name="category" id="categoryFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
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
                <select name="featured" id="featuredFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                    <option value="">All Projects</option>
                    <option value="featured" {{ request('featured') == 'featured' ? 'selected' : '' }}>Featured Only</option>
                    <option value="non-featured" {{ request('featured') == 'non-featured' ? 'selected' : '' }}>Non-Featured</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Filter
                </button>
                <a href="{{ route('admin.accomplished-projects') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Reset
                </a>
            </div>
        </div>
    </form>

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
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Total Projects</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['total_projects'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-yellow-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Total Budget</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900">₱ {{ number_format($stats['total_budget'], 2) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-star text-purple-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Featured Projects</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['featured_projects'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-green-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Recent Projects</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['recent_projects'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Grid -->
    <div id="projectsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($projects as $project)
        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden">
            <!-- Project Image -->
            <div class="relative">
                @if($project->image)
                    <img src="{{ $project->image_url }}" 
                         alt="{{ $project->title }}" 
                         class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                    </div>
                @endif
                
                <!-- Featured Badge -->
                @if($project->is_featured)
                    <div class="absolute top-3 right-3 bg-yellow-400 text-yellow-900 px-2 py-1 rounded-full text-xs font-semibold">
                        <i class="fas fa-star mr-1"></i>Featured
                    </div>
                @endif
                
                <!-- Category Badge -->
                <div class="absolute bottom-3 left-3">
                    <span class="bg-green-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                        {{ $project->category }}
                    </span>
                </div>
            </div>

            <!-- Project Content -->
            <div class="p-5">
                <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ $project->title }}</h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-3">{{ $project->description }}</p>
                
                <!-- Project Stats -->
                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                    <span><i class="fas fa-calendar-alt mr-1"></i>{{ $project->completion_date->format('M Y') }}</span>
                    <span><i class="fas fa-money-bill-wave mr-1"></i>₱{{ number_format($project->budget, 2) }}</span>
                </div>

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Progress</span>
                        <span>100%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <a href="{{ route('admin.accomplished-projects.show', $project) }}" 
                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-3 rounded-lg text-sm font-medium transition duration-300">
                        <i class="fas fa-eye mr-1"></i>View
                    </a>
                    <a href="{{ route('admin.accomplished-projects.edit', $project) }}" 
                       class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white text-center py-2 px-3 rounded-lg text-sm font-medium transition duration-300">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <button onclick="deleteProject({{ $project->id }})" 
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition duration-300">
                        <i class="fas fa-trash mr-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-8 sm:py-12">
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
        @endforelse
    </div>
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

function deleteProject(projectId) {
    if (confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
        // Create a form to submit the delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/accomplished-projects/' + projectId;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        // Add method spoofing for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection

