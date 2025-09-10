@extends('admin.main.layout')

@section('title', 'Create Blotter Reports')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Page Skeleton (Header + Warning + Form) -->
    <div id="createBlotterSkeleton" class="mb-2">
        @include('components.loading.create-form-skeleton', ['type' => 'header', 'showButton' => false])
        @include('components.loading.create-form-skeleton', ['type' => 'blotter-report'])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="createBlotterContent" style="display: none;">
        <!-- Header Section -->
        <div class="mb-2">
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
        <form id="createBlotterForm" action="{{ route('admin.blotter-reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Complainant Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    <i class="fas fa-user mr-2 text-green-600"></i>
                    Complainant Information
                </h3>
                <div class="grid grid-cols-1 gap-6">
                    <div class="relative">
                        <label for="complainantSearch" class="block text-sm font-medium text-gray-700 mb-2">
                            Complainant <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="complainantSearch"
                            placeholder="Type to search for a resident..."
                            autocomplete="off"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                            aria-label="Search for a resident"
                            required
                        />
                        <input type="hidden" id="resident_id" name="resident_id" required>
                        <div id="searchResults" class="absolute z-10 bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto w-full"></div>
                        <p class="mt-1 text-sm text-gray-500">Search and select the resident filing the complaint</p>
                    </div>
                </div>
            </div>

            <!-- Respondent Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">
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
                        <p class="mt-1 text-sm text-gray-500">Enter the full name of the person being reported</p>
                    </div>
                </div>
            </div>

            <!-- Incident Details -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">
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
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Choose the most appropriate category for the report</p>
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
                <h3 class="text-lg font-medium text-gray-900 mb-2">
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
                        <div id="filePreview" class="mt-2 hidden">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Files:</h4>
                            <div id="fileList" class="space-y-2"></div>
                        </div>
                        
                        <p class="mt-1 text-sm text-gray-500">Upload photos, videos, documents, or other evidence to support the report</p>
                    </div>
                </div>
            </div>

            <!-- Summon Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                    Summon Information
                </h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="summon_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Summon Date <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" 
                               id="summon_date" 
                               name="summon_date"
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                               required>
                        <p class="mt-1 text-sm text-gray-500">Set the date and time for the summon hearing</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    The report will be created and can be managed from the blotter reports list
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.blotter-reports') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Create Report
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- End Real Content -->
    </div>

</div>

<script>
    // Skeleton loading control
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const pageSkeleton = document.getElementById('createBlotterSkeleton');
            const content = document.getElementById('createBlotterContent');
            if (pageSkeleton) pageSkeleton.style.display = 'none';
            if (content) content.style.display = 'block';
        }, 1000);
    });

    document.addEventListener('DOMContentLoaded', () => {
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

    // Blotter create form AJAX submit for download + redirect + notify
    const createForm = document.getElementById('createBlotterForm');
    if (createForm) {
        createForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const csrfToken = form.querySelector('input[name="_token"]').value;
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/pdf',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });
                const contentType = response.headers.get('content-type') || '';
                if (response.ok && contentType.includes('application/pdf')) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const disposition = response.headers.get('content-disposition');
                    let filename = 'blotter_report.pdf';
                    if (disposition && disposition.indexOf('filename=') !== -1) {
                        let matches = disposition.match(/filename="?([^";]+)"?/);
                        if (matches && matches[1]) filename = matches[1];
                    }
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                    // Set flag for notification
                    localStorage.setItem('showBlotterCreateNotify', '1');
                    // Redirect to reports page
                    window.location.href = "{{ route('admin.blotter-reports') }}";
                } else {
                    // Try to extract error message from response
                    let errorMsg = 'Error creating blotter report.';
                    try {
                        const text = await response.text();
                        if (text.includes('This user account is inactive')) {
                            errorMsg = 'This user account is inactive and cannot make transactions.';
                        } else if (text.includes('<ul class="list-disc')) {
                            const match = text.match(/<li>(.*?)<\/li>/);
                            if (match) errorMsg = match[1];
                        }
                    } catch (e) {}
                    alert(errorMsg);
                }
            } catch (err) {
                alert('Error creating blotter report.');
                console.error(err);
            }
        });
    }

    function debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Resident AJAX search for complainant
    const searchInput = document.getElementById('complainantSearch');
    const searchResults = document.getElementById('searchResults');
    const residentIdInput = document.getElementById('resident_id');

    // Debug: Check if elements exist
    if (!searchInput) {
        console.error('Search input not found');
    }
    if (!searchResults) {
        console.error('Search results container not found');
    }
    if (!residentIdInput) {
        console.error('Resident ID input not found');
    }

    searchInput.addEventListener('input', debounce(async () => {
        const term = searchInput.value.trim();
        console.log('Search term:', term); // Debug log
        
        if (term.length < 2) {
            searchResults.innerHTML = '';
            searchResults.classList.add('hidden');
            return;
        }
        
        try {
            // Show loading state
            searchResults.innerHTML = '<div class="p-3 text-gray-500 text-center">Searching...</div>';
            searchResults.classList.remove('hidden');
            
            const searchUrl = `{{ route('admin.search.residents') }}?term=${encodeURIComponent(term)}`;
            console.log('Search URL:', searchUrl); // Debug log
            
            const response = await fetch(searchUrl);
            console.log('Response status:', response.status); // Debug log
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const results = await response.json();
            console.log('Search results:', results); // Debug log
            
            if (results.length > 0) {
                searchResults.innerHTML = results.map(resident => `
                    <div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0" data-id="${resident.id}" data-name="${resident.name}">
                        <div class="font-medium text-gray-900">${resident.name}</div>
                        <div class="text-sm text-gray-500">${resident.email || 'N/A'}</div>
                    </div>
                `).join('');
                searchResults.classList.remove('hidden');
            } else {
                searchResults.innerHTML = '<div class="p-3 text-gray-500 text-center">No residents found</div>';
                searchResults.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Search error:', error);
            searchResults.innerHTML = '<div class="p-3 text-red-500 text-center">Search error. Please try again.</div>';
            searchResults.classList.remove('hidden');
        }
    }, 250));

    searchResults.addEventListener('click', (event) => {
        const target = event.target.closest('[data-id]');
        if (target && target.dataset.id) {
            residentIdInput.value = target.dataset.id;
            searchInput.value = target.dataset.name;
            searchResults.innerHTML = '';
            searchResults.classList.add('hidden');
        }
    });

    document.addEventListener('click', (event) => {
        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
            searchResults.innerHTML = '';
            searchResults.classList.add('hidden');
        }
    });
});
</script>
@endsection