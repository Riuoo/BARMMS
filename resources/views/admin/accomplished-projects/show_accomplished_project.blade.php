@extends('admin.main.layout')

@section('title', 'Project Details')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
    <!-- Header Skeleton -->
    <div id="apShowHeaderSkeleton" class="animate-pulse mb-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="h-8 w-64 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 w-96 bg-gray-100 rounded"></div>
            </div>
            <div class="h-9 w-36 bg-gray-200 rounded"></div>
        </div>
    </div>

    <!-- Content Skeleton -->
    <div id="apShowBodySkeleton" class="animate-pulse">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="w-full h-64 bg-gray-200 rounded-xl"></div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <div class="h-6 w-48 bg-gray-200 rounded mb-4"></div>
                    <div class="h-4 w-24 bg-gray-200 rounded mb-4"></div>
                    <div class="h-4 w-full bg-gray-100 rounded mb-2"></div>
                    <div class="h-4 w-5/6 bg-gray-100 rounded"></div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="h-5 w-40 bg-gray-200 rounded mb-4"></div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @for ($i = 0; $i < 6; $i++)
                        <div>
                            <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                            <div class="h-4 w-40 bg-gray-100 rounded"></div>
                        </div>
                        @endfor
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="h-5 w-56 bg-gray-200 rounded mb-4"></div>
                    <div class="space-y-3">
                        <div class="h-4 w-full bg-gray-100 rounded"></div>
                        <div class="h-4 w-5/6 bg-gray-100 rounded"></div>
                        <div class="h-4 w-2/3 bg-gray-100 rounded"></div>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="h-5 w-40 bg-gray-200 rounded mb-4"></div>
                    <div class="space-y-3">
                        <div class="h-4 w-48 bg-gray-100 rounded"></div>
                        <div class="h-4 w-40 bg-gray-100 rounded"></div>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="h-5 w-28 bg-gray-200 rounded mb-4"></div>
                    <div class="space-y-3">
                        <div class="h-9 w-full bg-gray-200 rounded"></div>
                        <div class="h-9 w-full bg-gray-200 rounded"></div>
                        <div class="h-9 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="apShowContent" style="display: none;">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Project Details</h1>
            <p class="text-gray-600 mt-2">View detailed information about this accomplished project</p>
        </div>
        <a href="{{ route('admin.accomplished-projects') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition duration-300 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Projects
        </a>
    </div>

    <!-- Project Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Project Image -->
            @if($project->image)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <img src="{{ asset($project->image) }}" alt="{{ $project->title }}" class="w-full h-64 object-cover">
            </div>
            @endif
            <!-- Project Title and Category -->
            <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $project->title }}</h2>
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $project->category_color }}">
                        {{ $project->category }}
                    </span>
                </div>
                @if($project->is_featured)
                    <div class="flex items-center text-yellow-600 mb-4">
                        <i class="fas fa-star mr-2"></i>
                        <span class="font-medium">Featured Project</span>
                    </div>
                @endif
                <p class="text-gray-700 leading-relaxed">{{ $project->description }}</p>
            </div>

            <!-- Project Details -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Project Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Location</h4>
                        <p class="text-gray-600">{{ $project->location ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Budget</h4>
                        <p class="text-gray-600">{{ $project->formatted_budget }}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Start Date</h4>
                        <p class="text-gray-600">{{ $project->start_date->format('F j, Y') }}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Completion Date</h4>
                        <p class="text-gray-600">{{ $project->completion_date->format('F j, Y') }}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Duration</h4>
                        <p class="text-gray-600">{{ $project->duration }} days</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Status</h4>
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ ucfirst($project->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Impact and Beneficiaries -->
            @if($project->impact || $project->beneficiaries)
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Impact & Beneficiaries</h3>
                <div class="space-y-4">
                    @if($project->impact)
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Community Impact</h4>
                        <p class="text-gray-600">{{ $project->impact }}</p>
                    </div>
                    @endif
                    @if($project->beneficiaries)
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Beneficiaries</h4>
                        <p class="text-gray-600">{{ $project->beneficiaries }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Funding Information -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Funding Details</h3>
                <div class="space-y-3">
                    @if($project->funding_source)
                    <div>
                        <h4 class="font-medium text-gray-900 text-sm">Funding Source</h4>
                        <p class="text-gray-600 text-sm">{{ $project->funding_source }}</p>
                    </div>
                    @endif
                    @if($project->implementing_agency)
                    <div>
                        <h4 class="font-medium text-gray-900 text-sm">Implementing Agency</h4>
                        <p class="text-gray-600 text-sm">{{ $project->implementing_agency }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.accomplished-projects.edit', $project->id) }}" 
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-300 flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Project
                    </a>
                    <form action="{{ route('admin.accomplished-projects.toggle-featured', $project->id) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition duration-300 flex items-center justify-center">
                            <i class="fas fa-star mr-2"></i>
                            {{ $project->is_featured ? 'Unfeature' : 'Feature' }} Project
                        </button>
                    </form>
                    <form action="{{ route('admin.accomplished-projects.destroy', $project->id) }}" method="POST" class="w-full" onsubmit="return confirm('Are you sure you want to delete this project? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition duration-300 flex items-center justify-center">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Project
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const headerSkeleton = document.getElementById('apShowHeaderSkeleton');
        const bodySkeleton = document.getElementById('apShowBodySkeleton');
        const content = document.getElementById('apShowContent');
        if (headerSkeleton) headerSkeleton.style.display = 'none';
        if (bodySkeleton) bodySkeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection 