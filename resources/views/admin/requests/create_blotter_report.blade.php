{{-- resources/views/admin/create_blotter_report.blade.php --}}

@extends('admin.modals.layout')

@section('title', 'Create Blotter Reports')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-6">Create New Blotter Report</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.blotter-reports.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Complainant Search -->
        <div class="mb-4">
            <label for="complainantSearch" class="block font-medium mb-1">Complainant</label>
            <input
                type="text"
                id="complainantSearch"
                placeholder="Type to search for a resident..."
                autocomplete="off"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                aria-label="Search for a resident"
            />
            <input type="hidden" id="resident_id" name="resident_id" required>
            <div id="searchResults" class="absolute z-10 bg-white border border-gray-300 rounded mt-1 hidden"></div>
        </div>

        <!-- Recipient Name -->
        <div class="mb-4">
            <label for="recipient_name" class="block font-medium mb-1">Recipient Name (Complainant's Enemy)</label>
            <input type="text" id="recipient_name" name="recipient_name" value="{{ old('recipient_name') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <!-- Report Type -->
        <div class="mb-4">
            <label for="type" class="block font-medium mb-1">Report Type</label>
            <select class="w-full border border-gray-300 rounded px-3 py-2" id="type" name="type" required>
                <option value="">Select Type</option>
                <option value="Complaint">Complaint</option>
                <option value="Incident">Incident</option>
                <option value="Dispute">Dispute</option>
            </select>
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label for="description" class="block font-medium mb-1">Description</label>
            <textarea class="w-full border border-gray-300 rounded px-3 py-2" id="description" name="description" rows="4" required></textarea>
        </div>

        <!-- File Upload -->
        <div class="mb-4">
            <label for="media" class="block font-medium mb-1">Attach Evidence (Optional)</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-green-400 transition duration-200" id="uploadArea">
                <div class="space-y-1 text-center">
                    <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                    <div class="flex text-sm text-gray-600">
                        <label for="media" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                            <span>Upload files</span>
                            <input class="sr-only" type="file" id="media" name="media[]" accept="image/*,video/*,.pdf" multiple>
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
            
            <div class="text-sm text-gray-500">Max 5 files, 10MB each (JPG, PNG, MP4, AVI, PDF)</div>
        </div>

        <!-- Add this inside your form, for example, after the Description field -->
        <div class="mb-4">
            <label for="summon_date" class="block font-medium mb-1">Summon Date</label>
            <input type="datetime-local" id="summon_date" name="summon_date"
                min="{{ now()->format('Y-m-d\TH:i') }}"
                class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.blotter-reports') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save Blotter</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('complainantSearch');
        const searchResults = document.getElementById('searchResults');
        const residentIdInput = document.getElementById('resident_id');

        // File upload functionality
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
            this.classList.add('border-green-400', 'bg-green-50');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-green-400', 'bg-green-50');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            fileInput.files = e.dataTransfer.files;
            handleFiles(e.dataTransfer.files);
        });

        function handleFiles(files) {
            if (files.length > 0) {
                uploadArea.classList.add('border-green-400', 'bg-green-50');
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
                            uploadArea.classList.remove('border-green-400', 'bg-green-50');
                            uploadArea.classList.add('border-gray-300');
                        }
                    };
                    
                    fileItem.appendChild(fileInfo);
                    fileItem.appendChild(removeBtn);
                    fileList.appendChild(fileItem);
                });
            } else {
                uploadArea.classList.remove('border-green-400', 'bg-green-50');
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

        // Resident search functionality
        searchInput.addEventListener('input', debounce(async () => {
            const term = searchInput.value.trim();
            if (term.length < 2) {
                searchResults.innerHTML = '';
                searchResults.classList.add('hidden');
                return;
            }

            const response = await fetch(`{{ route('admin.search.residents') }}?term=${term}`);
            const results = await response.json();

            if (results.length > 0) {
                searchResults.innerHTML = results.map(resident => `
                    <div class="p-2 hover:bg-gray-100 cursor-pointer" data-id="${resident.id}" data-name="${resident.name}">
                        ${resident.name} (${resident.email || 'N/A'})
                    </div>
                `).join('');
                searchResults.classList.remove('hidden');
            } else {
                searchResults.innerHTML = '<div class="p-2 text-gray-500">No results found</div>';
                searchResults.classList.remove('hidden');
            }
        }, 250));

        searchResults.addEventListener('click', (event) => {
            if (event.target.dataset.id) {
                residentIdInput.value = event.target.dataset.id;
                searchInput.value = event.target.dataset.name;
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

    function debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }
</script>
@endsection