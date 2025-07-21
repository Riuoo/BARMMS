@extends('resident.layout')

@section('title', 'Submit Community Complaint')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Submit Community Complaint</h1>
                <p class="text-gray-600">Report infrastructure issues, utility problems, and other community concerns</p>
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

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were some errors with your submission</h3>
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

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('resident.request_community_complaint') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Complaint Title -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-heading mr-2 text-blue-600"></i>
                    Complaint Title
                </h3>
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Brief Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                           placeholder="e.g., Water shortage in Purok 3, Street light not working"
                           value="{{ old('title') }}"
                           required>
                    <p class="mt-1 text-sm text-gray-500">Provide a clear, concise title for your complaint</p>
                </div>
            </div>

            <!-- Category and Priority -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-tags mr-2 text-blue-600"></i>
                    Category & Priority
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                id="category" 
                                name="category" 
                                required>
                            <option value="">Select a category</option>
                            <option value="Water Supply" {{ old('category') == 'Water Supply' ? 'selected' : '' }}>Water Supply</option>
                            <option value="Electricity" {{ old('category') == 'Electricity' ? 'selected' : '' }}>Electricity</option>
                            <option value="Roads & Infrastructure" {{ old('category') == 'Roads & Infrastructure' ? 'selected' : '' }}>Roads & Infrastructure</option>
                            <option value="Garbage Collection" {{ old('category') == 'Garbage Collection' ? 'selected' : '' }}>Garbage Collection</option>
                            <option value="Street Lighting" {{ old('category') == 'Street Lighting' ? 'selected' : '' }}>Street Lighting</option>
                            <option value="Drainage & Sewage" {{ old('category') == 'Drainage & Sewage' ? 'selected' : '' }}>Drainage & Sewage</option>
                            <option value="Noise Pollution" {{ old('category') == 'Noise Pollution' ? 'selected' : '' }}>Noise Pollution</option>
                            <option value="Air Pollution" {{ old('category') == 'Air Pollution' ? 'selected' : '' }}>Air Pollution</option>
                            <option value="Public Safety" {{ old('category') == 'Public Safety' ? 'selected' : '' }}>Public Safety</option>
                            <option value="Health & Sanitation" {{ old('category') == 'Health & Sanitation' ? 'selected' : '' }}>Health & Sanitation</option>
                            <option value="Transportation" {{ old('category') == 'Transportation' ? 'selected' : '' }}>Transportation</option>
                            <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Choose the most appropriate category for your complaint</p>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                            Priority Level <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                id="priority" 
                                name="priority" 
                                required>
                            <option value="">Select priority level</option>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low - Minor inconvenience</option>
                            <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium - Moderate impact</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High - Significant impact</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent - Immediate attention needed</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Indicate how urgent this issue is</p>
                    </div>
                </div>
            </div>

            <!-- Location -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                    Location Details
                </h3>
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                        Specific Location (Optional)
                    </label>
                    <input type="text" 
                           id="location" 
                           name="location" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                           placeholder="e.g., Purok 3, Near the basketball court, Street name"
                           value="{{ old('location') }}">
                    <p class="mt-1 text-sm text-gray-500">Provide specific location details to help identify the issue</p>
                </div>
            </div>

            <!-- Description -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-file-alt mr-2 text-blue-600"></i>
                    Detailed Description
                </h3>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Detailed Description <span class="text-red-500">*</span>
                    </label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                              id="description" 
                              name="description" 
                              rows="6" 
                              placeholder="Please provide a detailed description of the issue, including when it started, how it affects the community, and any other relevant details..."
                              required>{{ old('description') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Include specific details about the problem, its impact, and when it started</p>
                </div>
            </div>

            <!-- Supporting Documents -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-paperclip mr-2 text-blue-600"></i>
                    Supporting Documents
                </h3>
                <div>
                    <label for="media" class="block text-sm font-medium text-gray-700 mb-2">
                        Attach Evidence (Optional)
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition duration-200" id="uploadArea">
                        <div class="space-y-1 text-center">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                            <div class="flex text-sm text-gray-600">
                                <label for="media" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload files</span>
                                    <input id="media" name="media[]" type="file" class="sr-only" accept="image/*,video/*,.pdf" multiple>
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, PDF, MP4, AVI up to 10MB each (max 5 files)</p>
                        </div>
                    </div>
                    
                    <!-- File Preview Area -->
                    <div id="filePreview" class="mt-4 hidden">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Files:</h4>
                        <div id="fileList" class="space-y-2"></div>
                    </div>
                    
                    <p class="mt-1 text-sm text-gray-500">Upload photos, videos, or documents to support your complaint</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Your complaint will be reviewed by barangay officials
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('resident.my-requests') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Complaint
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Information Card -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Important Information</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Community complaints help improve barangay services and infrastructure</li>
                        <li>Please provide accurate and detailed information</li>
                        <li>You may be contacted for additional details if needed</li>
                        <li>Urgent issues will be prioritized for immediate action</li>
                        <li>You can track the status of your complaint in "My Requests"</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // File upload preview
    const fileInput = document.getElementById('media');
    const uploadArea = document.getElementById('uploadArea');
    const filePreview = document.getElementById('filePreview');
    const fileList = document.getElementById('fileList');
    
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });

    // Drag and drop functionality
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-blue-400', 'bg-blue-50');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-400', 'bg-blue-50');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        fileInput.files = e.dataTransfer.files;
        handleFiles(e.dataTransfer.files);
    });

    function handleFiles(files) {
        if (files.length > 0) {
            uploadArea.classList.add('border-blue-400', 'bg-blue-50');
            uploadArea.classList.remove('border-gray-300');
            
            // Validate file count
            if (files.length > 5) {
                alert('Maximum 5 files allowed');
                fileInput.value = '';
                return;
            }

            // Validate file sizes and types
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf', 'video/mp4', 'video/avi', 'video/mov', 'video/wmv'];
            const maxSize = 10 * 1024 * 1024; // 10MB

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                if (!validTypes.includes(file.type)) {
                    alert(`Invalid file type: ${file.name}. Please upload images, videos, or PDF files only.`);
                    fileInput.value = '';
                    return;
                }
                
                if (file.size > maxSize) {
                    alert(`File too large: ${file.name}. Maximum size is 10MB.`);
                    fileInput.value = '';
                    return;
                }
            }

            // Show file preview
            filePreview.classList.remove('hidden');
            fileList.innerHTML = '';
            
            Array.from(files).forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center justify-between p-2 bg-gray-50 rounded border';
                fileItem.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-file text-gray-400 mr-2"></i>
                        <span class="text-sm text-gray-700">${file.name}</span>
                    </div>
                    <span class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
                `;
                fileList.appendChild(fileItem);
            });
        } else {
            uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
            uploadArea.classList.add('border-gray-300');
            filePreview.classList.add('hidden');
        }
    }
});
</script>
@endsection 