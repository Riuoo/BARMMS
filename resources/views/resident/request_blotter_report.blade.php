@extends('resident.layout')

@section('title', 'Create Blotter Report')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Blotter Report</h1>
                <p class="text-gray-600">Submit an incident report for barangay resolution</p>
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
        <form action="{{ route('resident.request_blotter_report') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Recipient Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-user-tag mr-2 text-red-600"></i>
                    Respondent Information
                </h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="recipient_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Respondent Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="recipient_name" 
                               name="recipient_name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200" 
                               placeholder="Enter the name of the person involved"
                               value="{{ old('recipient_name') }}"
                               required>
                        <p class="mt-1 text-sm text-gray-500">Enter the full name of the person you are filing the report against</p>
                    </div>
                </div>
            </div>

            <!-- Incident Details -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-file-alt mr-2 text-red-600"></i>
                    Incident Details
                </h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Report Type <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200" 
                                id="type" 
                                name="type" 
                                required>
                            <option value="">Select a report type</option>
                            <option value="Complaint" {{ old('type') == 'Complaint' ? 'selected' : '' }}>Complaint</option>
                            <option value="Incident" {{ old('type') == 'Incident' ? 'selected' : '' }}>Incident</option>
                            <option value="Dispute" {{ old('type') == 'Dispute' ? 'selected' : '' }}>Dispute</option>
                            <option value="Harassment" {{ old('type') == 'Harassment' ? 'selected' : '' }}>Harassment</option>
                            <option value="Property Damage" {{ old('type') == 'Property Damage' ? 'selected' : '' }}>Property Damage</option>
                            <option value="Noise Complaint" {{ old('type') == 'Noise Complaint' ? 'selected' : '' }}>Noise Complaint</option>
                            <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Choose the most appropriate category for your report</p>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Detailed Description <span class="text-red-500">*</span>
                        </label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200" 
                                  id="description" 
                                  name="description" 
                                  rows="6" 
                                  placeholder="Provide a detailed description of the incident, including date, time, location, and any relevant details..."
                                  required>{{ old('description') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Include specific details about what happened, when it occurred, and any witnesses</p>
                    </div>
                </div>
            </div>

            <!-- Supporting Documents -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-paperclip mr-2 text-red-600"></i>
                    Supporting Documents
                </h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="media" class="block text-sm font-medium text-gray-700 mb-2">
                            Attach Evidence (Optional)
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-red-400 transition duration-200" id="uploadArea">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="media" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
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
                        
                        <p class="mt-1 text-sm text-gray-500">Upload photos, videos, documents, or other evidence to support your report</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Your report will be reviewed by barangay officials
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('resident.my-requests') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Report
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
                        <li>All reports are confidential and will be handled by barangay officials</li>
                        <li>Please provide accurate and truthful information</li>
                        <li>You may be contacted for additional details if needed</li>
                        <li>False reports may result in legal consequences</li>
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
        this.classList.add('border-red-400', 'bg-red-50');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-red-400', 'bg-red-50');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        fileInput.files = e.dataTransfer.files;
        handleFiles(e.dataTransfer.files);
    });

    function handleFiles(files) {
        if (files.length > 0) {
            uploadArea.classList.add('border-red-400', 'bg-red-50');
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

            for (let file of files) {
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

            // Show file previews
            filePreview.classList.remove('hidden');
            fileList.innerHTML = '';

            Array.from(files).forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
                
                const fileInfo = document.createElement('div');
                fileInfo.className = 'flex items-center space-x-3';
                
                // File icon based on type
                let icon = 'fas fa-file';
                if (file.type.startsWith('image/')) {
                    icon = 'fas fa-image';
                } else if (file.type.startsWith('video/')) {
                    icon = 'fas fa-video';
                } else if (file.type === 'application/pdf') {
                    icon = 'fas fa-file-pdf';
                }
                
                fileInfo.innerHTML = `
                    <i class="${icon} text-gray-400"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">${file.name}</p>
                        <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                    </div>
                `;
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'text-red-500 hover:text-red-700';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.onclick = function() {
                    // Remove file from input
                    const dt = new DataTransfer();
                    const input = document.getElementById('media');
                    const { files } = input;
                    
                    for (let i = 0; i < files.length; i++) {
                        if (i !== index) {
                            dt.items.add(files[i]);
                        }
                    }
                    
                    input.files = dt.files;
                    fileItem.remove();
                    
                    if (input.files.length === 0) {
                        filePreview.classList.add('hidden');
                        uploadArea.classList.remove('border-red-400', 'bg-red-50');
                        uploadArea.classList.add('border-gray-300');
                    }
                };
                
                fileItem.appendChild(fileInfo);
                fileItem.appendChild(removeBtn);
                fileList.appendChild(fileItem);
            });
        } else {
            uploadArea.classList.remove('border-red-400', 'bg-red-50');
            uploadArea.classList.add('border-gray-300');
            filePreview.classList.add('hidden');
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
@endsection