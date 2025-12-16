@extends('resident.layout')

@section('title', 'Project Details')

@section('content')
<div class="max-w-4xl mx-auto pt-2">
    <!-- Consolidated Skeleton -->
    <div id="residentProjectSkeleton">
        @include('components.loading.resident-project-skeleton')
    </div>

    <!-- Real Content Wrapper (hidden initially) -->
    <div id="residentProjectContent" style="display: none;">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $project->title }}</h1>
                    <p class="text-gray-600">Project Details</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-2">
                    <a href="{{ route('resident.announcements') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Bulletin
                    </a>
                    <a href="{{ route('resident.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-home mr-2"></i>
                        Dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Project Image / Placeholder -->
                <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="relative h-64 bg-gradient-to-r from-gray-100 to-gray-50">
                        @if($project->image_url)
                        <img src="{{ $project->image_url }}" alt="{{ $project->title }}" class="w-full h-full object-cover">
                        @else
                        <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-xl"></i>
                                </div>
                                <span class="text-sm font-medium">No image available</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Project Description -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Project Description</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $project->description }}</p>
                    </div>
                </div>

                <!-- Project Objectives -->
                @if($project->objectives)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Objectives</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $project->objectives }}</p>
                    </div>
                </div>
                @endif

                <!-- Project Outcomes -->
                @if($project->outcomes)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Outcomes</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $project->outcomes }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Project Info Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Project Information</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Category</label>
                            <p class="text-sm text-gray-900">{{ $project->category }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Completed
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Completion Date</label>
                            <p class="text-sm text-gray-900">{{ optional($project->completion_date)->format('F d, Y') }}</p>
                        </div>

                        @if($project->budget)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Budget</label>
                            <p class="text-sm text-gray-900">₱{{ number_format($project->budget, 2) }}</p>
                        </div>
                        @endif

                        @if($project->is_featured)
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-star mr-1"></i>
                                Featured Project
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Project Timeline -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Project Timeline</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Project Started</p>
                                <p class="text-xs text-gray-500">{{ $project->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Project Completed</p>
                                <p class="text-xs text-gray-500">{{ optional($project->completion_date)->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-green-50 rounded-lg border border-green-200 p-6">
                    <h3 class="text-lg font-semibold text-green-900 mb-4">Need More Information?</h3>
                    <p class="text-sm text-green-700 mb-4">For questions about this project, contact the barangay office.</p>
                    <a href="{{ route('resident.request_community_concern') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                        <i class="fas fa-envelope mr-2"></i>
                        Contact Barangay
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const skeleton = document.getElementById('residentProjectSkeleton');
        const content = document.getElementById('residentProjectContent');
        if (skeleton) skeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endsection
