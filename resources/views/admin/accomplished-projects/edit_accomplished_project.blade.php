@extends('admin.main.layout')

@section('title', 'Edit Accomplished Project')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
    <!-- Form Skeleton -->
    <div id="apEditFormSkeleton">
        @include('components.loading.edit-form-skeleton', ['type' => 'header', 'showButton' => false])
        @include('components.loading.edit-form-skeleton', ['type' => 'accomplished-project'])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="apEditContent" style="display: none;">
    <!-- Header -->
    <div class="flex items-center justify-between mb-2">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Accomplished Project</h1>
            <p class="text-gray-600 mt-2">Update project information and details</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.accomplished-projects.update', $project->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Project Title -->
            <div class="md:col-span-2">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Project Title *</label>
                <input type="text" id="title" name="title" value="{{ old('title', $project->title) }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('title') border-red-500 @enderror" 
                    placeholder="Enter project title" required />
                @error('title')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Project Image -->
            <div class="md:col-span-2">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Project Image</label>
                
                <!-- Current Image Display -->
                @if($project->image)
                <div class="mb-2">
                    <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                    <div class="inline-block">
                        <img src="{{ asset($project->image) }}" alt="Current project image" class="h-32 w-auto rounded-lg border border-gray-200">
                    </div>
                </div>
                @endif
                
                <!-- Image Upload Area -->
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                    <div class="space-y-1 text-center">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                        <div class="flex text-sm text-gray-600">
                            <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                <span>Upload a file</span>
                                <input id="image" name="image" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)" />
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                    </div>
                </div>
                
                <!-- Image Preview -->
                <div id="imagePreview" class="mt-2 hidden">
                    <p class="text-sm text-gray-600 mb-2">Selected Image:</p>
                    <div class="relative inline-block">
                        <img id="previewImg" src="" alt="Selected image preview" class="h-32 w-auto rounded-lg border border-gray-200">
                        <button type="button" onclick="removeSelectedImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                @error('image')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                <select name="category" id="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('category') border-red-500 @enderror" required>
                    <option value="">Select Category</option>
                    <option value="Infrastructure" {{ old('category', $project->category) == 'Infrastructure' ? 'selected' : '' }}>Infrastructure</option>
                    <option value="Health" {{ old('category', $project->category) == 'Health' ? 'selected' : '' }}>Health</option>
                    <option value="Education" {{ old('category', $project->category) == 'Education' ? 'selected' : '' }}>Education</option>
                    <option value="Agriculture" {{ old('category', $project->category) == 'Agriculture' ? 'selected' : '' }}>Agriculture</option>
                    <option value="Social Services" {{ old('category', $project->category) == 'Social Services' ? 'selected' : '' }}>Social Services</option>
                    <option value="Environment" {{ old('category', $project->category) == 'Environment' ? 'selected' : '' }}>Environment</option>
                    <option value="Livelihood" {{ old('category', $project->category) == 'Livelihood' ? 'selected' : '' }}>Livelihood</option>
                </select>
                @error('category')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                <input type="text" id="location" name="location" value="{{ old('location', $project->location) }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('location') border-red-500 @enderror" 
                    placeholder="Enter project location" />
                @error('location')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Budget -->
            <div>
                <label for="budget" class="block text-sm font-medium text-gray-700 mb-2">Budget (₱)</label>
                <input type="number" id="budget" name="budget" value="{{ old('budget', $project->budget) }}" step="0.01"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('budget') border-red-500 @enderror" 
                    placeholder="Enter project budget" />
                @error('budget')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Funding Source -->
            <div>
                <label for="funding_source" class="block text-sm font-medium text-gray-700 mb-2">Funding Source</label>
                <input type="text" id="funding_source" name="funding_source" value="{{ old('funding_source', $project->funding_source) }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('funding_source') border-red-500 @enderror" 
                    placeholder="e.g., Department of Health" />
                @error('funding_source')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Start Date -->
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('start_date') border-red-500 @enderror" required />
                @error('start_date')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Completion Date -->
            <div>
                <label for="completion_date" class="block text-sm font-medium text-gray-700 mb-2">Completion Date *</label>
                <input type="date" id="completion_date" name="completion_date" value="{{ old('completion_date', $project->completion_date->format('Y-m-d')) }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('completion_date') border-red-500 @enderror" required />
                @error('completion_date')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Implementing Agency -->
            <div>
                <label for="implementing_agency" class="block text-sm font-medium text-gray-700 mb-2">Implementing Agency</label>
                <input type="text" id="implementing_agency" name="implementing_agency" value="{{ old('implementing_agency', $project->implementing_agency) }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('implementing_agency') border-red-500 @enderror" 
                    placeholder="e.g., Barangay Council" />
                @error('implementing_agency')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Project Description *</label>
                <textarea id="description" name="description" rows="4" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('description') border-red-500 @enderror" 
                    placeholder="Describe the project in detail..." required>{{ old('description', $project->description) }}</textarea>
                @error('description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Beneficiaries -->
            <div class="md:col-span-2">
                <label for="beneficiaries" class="block text-sm font-medium text-gray-700 mb-2">Beneficiaries</label>
                <textarea id="beneficiaries" name="beneficiaries" rows="3" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('beneficiaries') border-red-500 @enderror" 
                    placeholder="Who benefited from this project?">{{ old('beneficiaries', $project->beneficiaries) }}</textarea>
                @error('beneficiaries')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Impact -->
            <div class="md:col-span-2">
                <label for="impact" class="block text-sm font-medium text-gray-700 mb-2">Impact</label>
                <textarea id="impact" name="impact" rows="3" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('impact') border-red-500 @enderror" 
                    placeholder="What was the impact of this project on the community?">{{ old('impact', $project->impact) }}</textarea>
                @error('impact')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Featured Checkbox -->
            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $project->is_featured) ? 'checked' : '' }} 
                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">Feature this project on the landing page</span>
                </label>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-between mt-2">
            <a href="{{ route('admin.accomplished-projects') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                <i class="fas fa-save mr-2"></i>
                Update Project
            </button>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Show preview
            const previewImg = document.getElementById('previewImg');
            const imagePreview = document.getElementById('imagePreview');
            
            if (previewImg && imagePreview) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('hidden');
            }
        };
        reader.readAsDataURL(file);
    }
}

function removeSelectedImage() {
    // Clear the file input
    document.getElementById('image').value = '';
    
    // Hide the preview
    const imagePreview = document.getElementById('imagePreview');
    if (imagePreview) {
        imagePreview.classList.add('hidden');
    }
}
</script>
@endsection 

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const formSkeleton = document.getElementById('apEditFormSkeleton');
        const content = document.getElementById('apEditContent');
        if (formSkeleton) formSkeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush 