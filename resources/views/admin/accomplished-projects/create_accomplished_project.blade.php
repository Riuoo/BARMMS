@extends('admin.main.layout')

@section('title', 'Add New Accomplished Project')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
    <!-- Form Skeleton -->
    <div id="apCreateFormSkeleton">
        @include('components.loading.create-form-skeleton', ['type' => 'header', 'buttonCount' => false])
        @include('components.loading.create-form-skeleton', ['type' => 'accomplished-project'])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="apCreateContent" style="display: none;">
    <!-- Header -->
    <div class="flex items-center justify-between mb-2">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Add New Accomplished Project</h1>
            <p class="text-gray-600 mt-2">Create a new accomplished project to showcase community achievements</p>
        </div>
    </div>

    <!-- Alerts -->
    @if ($errors->any())
        <div class="mb-3 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('admin.accomplished-projects.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Project Title -->
            <div class="md:col-span-2">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Project Title <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('title') border-red-500 @enderror" 
                    placeholder="Enter project title" required />
                @error('title')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                    Type <span class="text-red-500">*</span>
                </label>
                <select name="type" id="type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('type') border-red-500 @enderror" required>
                    <option value="">Select Type</option>
                    <option value="project" {{ old('type', 'project') == 'project' ? 'selected' : '' }}>Project</option>
                    <option value="activity" {{ old('type') == 'activity' ? 'selected' : '' }}>Activity</option>
                </select>
                @error('type')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Project Image -->
            <div class="md:col-span-2">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Project Image</label>
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
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                    Category <span class="text-red-500">*</span>
                </label>
                <select name="category" id="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('category') border-red-500 @enderror" required>
                    <option value="">Select Category</option>
                    <option value="Infrastructure" {{ old('category') == 'Infrastructure' ? 'selected' : '' }}>Infrastructure</option>
                    <option value="Health" {{ old('category') == 'Health' ? 'selected' : '' }}>Health</option>
                    <option value="Education" {{ old('category') == 'Education' ? 'selected' : '' }}>Education</option>
                    <option value="Agriculture" {{ old('category') == 'Agriculture' ? 'selected' : '' }}>Agriculture</option>
                    <option value="Social Services" {{ old('category') == 'Social Services' ? 'selected' : '' }}>Social Services</option>
                    <option value="Environment" {{ old('category') == 'Environment' ? 'selected' : '' }}>Environment</option>
                    <option value="Livelihood" {{ old('category') == 'Livelihood' ? 'selected' : '' }}>Livelihood</option>
                </select>
                @error('category')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                <input type="text" id="location" name="location" value="{{ old('location') }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('location') border-red-500 @enderror" 
                    placeholder="Enter project location" />
                @error('location')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Budget -->
            <div class="js-project-only">
                <label for="budget" class="block text-sm font-medium text-gray-700 mb-2">Budget (₱)</label>
                <input type="number" id="budget" name="budget" value="{{ old('budget') }}" step="0.01"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('budget') border-red-500 @enderror" 
                    placeholder="Enter project budget" />
                @error('budget')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Funding Source -->
            <div class="js-project-only">
                <label for="funding_source" class="block text-sm font-medium text-gray-700 mb-2">Funding Source</label>
                <input type="text" id="funding_source" name="funding_source" value="{{ old('funding_source') }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('funding_source') border-red-500 @enderror" 
                    placeholder="Example: Department of Health" />
                @error('funding_source')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Target Audience (for activities) -->
            <div class="md:col-span-2 js-activity-only">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Target Audience (Activities Only)</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Audience Scope</label>
                        <div class="space-y-1">
                            <label class="flex items-center">
                                <input type="radio" name="audience_scope" value="all"
                                       {{ old('audience_scope', 'all') === 'all' ? 'checked' : '' }}
                                       class="h-4 w-4 text-yellow-600 border-gray-300 focus:ring-yellow-500">
                                <span class="ml-2 text-sm text-gray-700">All Residents</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="audience_scope" value="purok"
                                       {{ old('audience_scope') === 'purok' ? 'checked' : '' }}
                                       class="h-4 w-4 text-yellow-600 border-gray-300 focus:ring-yellow-500">
                                <span class="ml-2 text-sm text-gray-700">Specific Purok</span>
                            </label>
                            <div id="activityAudiencePurokWrapper" class="mt-2 {{ old('audience_scope', 'all') === 'purok' ? '' : 'hidden' }}">
                                <label for="audience_purok" class="block text-sm font-medium text-gray-700 mb-1">Select Purok</label>
                                <select name="audience_purok" id="audience_purok"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                    <option value="">Select Purok...</option>
                                    @for($i = 1; $i <= 7; $i++)
                                        <option value="{{ $i }}" {{ old('audience_purok') == $i ? 'selected' : '' }}>Purok {{ $i }}</option>
                                    @endfor
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Used for targeting announcements, QR attendance context, and email notifications.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Start Date -->
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Start Date <span class="text-red-500">*</span>
                </label>
                <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('start_date') border-red-500 @enderror" required />
                @error('start_date')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Completion Date -->
            <div>
                <label for="completion_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Completion Date <span class="text-red-500">*</span>
                </label>
                <input type="date" id="completion_date" name="completion_date" value="{{ old('completion_date') }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('completion_date') border-red-500 @enderror" required />
                @error('completion_date')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Implementing Agency -->
            <div class="js-project-only">
                <label for="implementing_agency" class="block text-sm font-medium text-gray-700 mb-2">Implementing Agency</label>
                <input type="text" id="implementing_agency" name="implementing_agency" value="{{ old('implementing_agency') }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('implementing_agency') border-red-500 @enderror" 
                    placeholder="Example: Barangay Council" />
                @error('implementing_agency')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Project Description <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="4" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('description') border-red-500 @enderror" 
                    placeholder="Enter project description" required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Beneficiaries -->
            <div class="md:col-span-2">
                <label for="beneficiaries" class="block text-sm font-medium text-gray-700 mb-2">Beneficiaries</label>
                <textarea id="beneficiaries" name="beneficiaries" rows="3" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('beneficiaries') border-red-500 @enderror" 
                    placeholder="Enter beneficiaries">{{ old('beneficiaries') }}</textarea>
                @error('beneficiaries')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Impact -->
            <div class="md:col-span-2">
                <label for="impact" class="block text-sm font-medium text-gray-700 mb-2">Impact</label>
                <textarea id="impact" name="impact" rows="3" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('impact') border-red-500 @enderror" 
                    placeholder="Enter project impact">{{ old('impact') }}</textarea>
                @error('impact')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Featured Checkbox -->
            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} 
                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">Feature this project on the landing page</span>
                </label>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-between mt-2">
            <a href="{{ route('admin.accomplished-projects') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                <i class="fas fa-times mr-2"></i>
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                <i class="fas fa-save mr-2"></i>
                Create Project
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const headerSkeleton = document.getElementById('apCreateHeaderSkeleton');
        const formSkeleton = document.getElementById('apCreateFormSkeleton');
        const content = document.getElementById('apCreateContent');
        if (headerSkeleton) headerSkeleton.style.display = 'none';
        if (formSkeleton) formSkeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);

    const typeSelect = document.getElementById('type');
    const projectOnlyFields = document.querySelectorAll('.js-project-only');
    const activityOnlyFields = document.querySelectorAll('.js-activity-only');
    const audienceScopeInputs = document.querySelectorAll('input[name=\"audience_scope\"]');
    const purokWrapper = document.getElementById('activityAudiencePurokWrapper');

    function toggleTypeFields() {
        const isProject = typeSelect.value === 'project';
        projectOnlyFields.forEach((field) => {
            field.style.display = isProject ? 'block' : 'none';
        });
        activityOnlyFields.forEach((field) => {
            field.style.display = isProject ? 'none' : 'block';
        });
    }

    function updateAudienceVisibility() {
        if (!purokWrapper) return;
        const selected = document.querySelector('input[name=\"audience_scope\"]:checked');
        if (!selected || selected.value === 'all') {
            purokWrapper.classList.add('hidden');
        } else {
            purokWrapper.classList.remove('hidden');
        }
    }

    if (typeSelect) {
        typeSelect.addEventListener('change', toggleTypeFields);
        toggleTypeFields();
    }

    audienceScopeInputs.forEach(input => {
        input.addEventListener('change', updateAudienceVisibility);
    });
    updateAudienceVisibility();
});
</script>
@endpush
@endsection 