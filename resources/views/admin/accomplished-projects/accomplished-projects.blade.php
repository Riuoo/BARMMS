@extends('admin.modals.layout')

@section('title', 'Accomplished Projects')

@section('content')
<div class="max-w-7xl mx-auto bg-white rounded-lg shadow p-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Accomplished Projects</h1>
            <p class="text-gray-600">Manage and showcase completed community projects</p>
        </div>
        <a href="{{ route('admin.accomplished-projects.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition duration-300 flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Add New Project
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Projects</p>
                    <p class="text-3xl font-bold">{{ $totalProjects }}</p>
                </div>
                <div class="bg-blue-400 rounded-full p-3">
                    <i class="fas fa-project-diagram text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Budget</p>
                    <p class="text-3xl font-bold">₱{{ number_format($totalBudget, 2) }}</p>
                </div>
                <div class="bg-green-400 rounded-full p-3">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Featured Projects</p>
                    <p class="text-3xl font-bold">{{ $featuredProjects->count() }}</p>
                </div>
                <div class="bg-purple-400 rounded-full p-3">
                    <i class="fas fa-star text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($projects as $project)
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <!-- Project Image Placeholder -->
            <div class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 rounded-t-xl flex items-center justify-center">
                <i class="fas fa-image text-4xl text-gray-400"></i>
            </div>
            
            <!-- Project Content -->
            <div class="p-6">
                <!-- Category Badge -->
                <div class="flex items-center justify-between mb-3">
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $project->category_color }}">
                        {{ $project->category }}
                    </span>
                    @if($project->is_featured)
                        <span class="text-yellow-500">
                            <i class="fas fa-star"></i>
                        </span>
                    @endif
                </div>
                
                <!-- Project Title -->
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $project->title }}</h3>
                
                <!-- Project Description -->
                <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ Str::limit($project->description, 120) }}</p>
                
                <!-- Project Details -->
                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="fas fa-map-marker-alt mr-2 text-green-600"></i>
                        <span>{{ $project->location ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="fas fa-calendar mr-2 text-blue-600"></i>
                        <span>{{ $project->completion_date->format('M Y') }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="fas fa-money-bill mr-2 text-green-600"></i>
                        <span>{{ $project->formatted_budget }}</span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.accomplished-projects.show', $project->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center">
                        <i class="fas fa-eye mr-1"></i>
                        View Details
                    </a>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.accomplished-projects.edit', $project->id) }}" class="text-gray-600 hover:text-gray-800 p-2 rounded-lg hover:bg-gray-100 transition duration-200">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.accomplished-projects.toggle-featured', $project->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-yellow-600 hover:text-yellow-800 p-2 rounded-lg hover:bg-yellow-50 transition duration-200">
                                <i class="fas fa-star"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.accomplished-projects.destroy', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this project? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition duration-200">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($projects->isEmpty())
    <div class="text-center py-12">
        <div class="text-gray-400 mb-4">
            <i class="fas fa-project-diagram text-6xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Projects Yet</h3>
        <p class="text-gray-600 mb-6">Start by adding your first accomplished project to showcase community achievements.</p>
        <a href="{{ route('admin.accomplished-projects.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition duration-300 inline-flex items-center">
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

