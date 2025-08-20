@extends('admin.main.layout')

@section('title', 'Create Document Request')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Document Request</h1>
                <p class="text-gray-600">Request official documents from the barangay office</p>
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
        <form id="createDocumentRequestForm" action="{{ route('admin.document-requests.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Resident Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-user mr-2 text-blue-600"></i>
                    Resident Information
                </h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="residentSearch" class="block text-sm font-medium text-gray-700 mb-2">
                            Resident (Requester) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="residentSearch"
                            placeholder="Type to search for a resident..."
                            autocomplete="off"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            aria-label="Search for a resident"
                            required
                        />
                        <input type="hidden" id="resident_id" name="resident_id" required>
                        <div id="searchResults" class="absolute z-10 bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                        <p class="mt-1 text-sm text-gray-500">Search and select the resident requesting the document</p>
                    </div>
                </div>
            </div>

            <!-- Document Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-file-signature mr-2 text-blue-600"></i>
                    Document Information
                </h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="document_template_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Template <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                id="document_template_id" 
                                name="document_template_id" 
                                required>
                            <option value="">Select a template</option>
                            @foreach(($templates ?? []) as $t)
                                <option value="{{ optional($t)->id }}" {{ old('document_template_id') == optional($t)->id ? 'selected' : '' }}>
                                    {{ optional($t)->document_type ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Choose the template to be used for this request</p>
                    </div>

                    <input type="hidden" id="document_type" name="document_type" value="">
                </div>
            </div>

            <!-- Purpose and Details -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-align-left mr-2 text-blue-600"></i>
                    Purpose and Details
                </h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Purpose <span class="text-red-500">*</span>
                        </label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                  id="description" 
                                  name="description" 
                                  rows="4" 
                                  placeholder="Please specify the purpose of the document request (e.g., For job application, school enrollment, financial assistance, government transaction, etc.)"
                                  required>{{ old('description') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Provide a clear and specific purpose for the document request</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    The request will be created and can be managed from the document requests list
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.document-requests') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Create Request
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('residentSearch');
    const searchResults = document.getElementById('searchResults');
    const userIdInput = document.getElementById('resident_id');
    const form = document.getElementById('createDocumentRequestForm');
    const templateSelect = document.getElementById('document_template_id');
    const documentTypeHidden = document.getElementById('document_type');

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
    }, 250));

    searchResults.addEventListener('click', (event) => {
        const target = event.target.closest('[data-id]');
        if (target && target.dataset.id) {
            userIdInput.value = target.dataset.id;
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

    function debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Sync hidden document_type with selected template to ensure type strictly matches an active template
    if (templateSelect && documentTypeHidden) {
        templateSelect.addEventListener('change', () => {
            const selected = templateSelect.options[templateSelect.selectedIndex];
            documentTypeHidden.value = selected ? (selected.textContent || '').trim() : '';
        });
        // Initialize on load
        const initSelected = templateSelect.options[templateSelect.selectedIndex];
        documentTypeHidden.value = initSelected ? (initSelected.textContent || '').trim() : '';
    }

    // Create -> success -> download -> back with notify()
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Basic client validation
            if (!userIdInput.value) {
                if (typeof notify === 'function') {
                    notify('error', 'Please select a resident.');
                } else {
                    alert('Please select a resident.');
                }
                return;
            }

            // Fill hidden document_type from selected template's label (for compatibility)
            try {
                const selected = templateSelect?.options?.[templateSelect.selectedIndex];
                if (selected && documentTypeHidden) {
                    documentTypeHidden.value = selected.textContent?.trim() || '';
                }
            } catch (_) {}

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
            }

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') || ''
                    },
                    body: formData,
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    // Attempt to parse validation or server error
                    let message = 'Failed to create document request.';
                    try {
                        const data = await response.json();
                        if (data?.error) message = data.error;
                        if (data?.message) message = data.message;
                        if (data?.errors) {
                            const firstErrorKey = Object.keys(data.errors)[0];
                            if (firstErrorKey && data.errors[firstErrorKey][0]) {
                                message = data.errors[firstErrorKey][0];
                            }
                        }
                    } catch (_) {}
                    if (typeof notify === 'function') {
                        notify('error', message);
                    } else {
                        alert(message);
                    }
                    return;
                }

                const data = await response.json();
                const newId = data?.id;
                if (!data?.success || !newId) {
                    if (typeof notify === 'function') {
                        notify('error', 'Unexpected response from server.');
                    } else {
                        alert('Unexpected response from server.');
                    }
                    return;
                }

                // Success notify
                if (typeof notify === 'function') {
                    notify('success', 'Document request created. Preparing download...');
                }

                // Download PDF
                await (async function downloadPdf(id) {
                    try {
                        const downloadRes = await fetch(`/admin/document-requests/download/${id}`, {
                            method: 'GET',
                            headers: { 'Accept': 'application/pdf' },
                            credentials: 'same-origin'
                        });

                        const contentType = downloadRes.headers.get('content-type') || '';
                        if (downloadRes.ok && contentType.includes('application/pdf')) {
                            const blob = await downloadRes.blob();
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = `document_request_${id}.pdf`;
                            document.body.appendChild(a);
                            a.click();
                            a.remove();
                            window.URL.revokeObjectURL(url);
                        } else {
                            // Try to read error text
                            let errorMsg = 'Error generating PDF.';
                            try { errorMsg = await downloadRes.text(); } catch (_) {}
                            if (typeof notify === 'function') {
                                notify('error', errorMsg || 'Error generating PDF.');
                            } else {
                                alert('Error generating PDF.');
                            }
                        }
                    } catch (err) {
                        if (typeof notify === 'function') {
                            notify('error', 'Network error while downloading PDF.');
                        } else {
                            alert('Network error while downloading PDF.');
                        }
                    }
                })(newId);

                // Mark to show notify on the list page and redirect back
                try { localStorage.setItem('showDocumentCreateNotify', '1'); } catch (_) {}
                window.location.href = `{{ route('admin.document-requests') }}`;

            } catch (error) {
                if (typeof notify === 'function') {
                    notify('error', 'Unexpected error. Please try again.');
                } else {
                    alert('Unexpected error. Please try again.');
                }
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            }
        });
    }
});
</script>
@endsection