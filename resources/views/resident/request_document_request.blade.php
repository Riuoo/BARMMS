@extends('resident.layout')

@section('title', 'Request New Document')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Document Payment Terms Modal (access gate) -->
    <div
        id="documentPaymentModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]"
        role="dialog"
        aria-modal="true"
        aria-labelledby="documentPaymentModalTitle"
    >
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4 p-6 border border-gray-200">
            <div class="flex items-start mb-4">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                    <i class="fas fa-file-invoice-dollar text-blue-600"></i>
                </div>
                <div>
                    <h2 id="documentPaymentModalTitle" class="text-xl font-semibold text-gray-900">
                        Document Request Payment Terms
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Before you can request a document, please review and agree to the payment information for this service.
                    </p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-4 space-y-2 text-sm text-gray-700">
                <p class="font-medium text-gray-900">
                    Important details:
                </p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Some document types may have a standard processing fee.</li>
                    <li>Any applicable fees are usually <span class="font-semibold">non-refundable</span> once processing has started.</li>
                    <li>Additional charges may apply for certified copies or rush processing, depending on the document.</li>
                </ul>
                <p class="mt-2 text-xs text-gray-500">
                    <span class="font-semibold text-gray-700">Note:</span> Not all documents require a fee. The barangay staff will inform you if your selected document has any associated charges.
                </p>
            </div>

            <div class="flex items-start mb-4">
                <input
                    id="agreeDocumentPaymentTerms"
                    type="checkbox"
                    class="mt-1 mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                >
                <label for="agreeDocumentPaymentTerms" class="text-sm text-gray-700">
                    I have read and understood the payment information for document requests and I agree to any applicable fees that may be charged for my selected document.
                </label>
            </div>

            <div class="flex justify-end space-x-3">
                <a
                    href="{{ route('resident.my-requests') }}"
                    class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition duration-200"
                >
                    Cancel
                </a>
                <button
                    id="continueToDocumentBtn"
                    type="button"
                    class="px-5 py-2 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-400 disabled:cursor-not-allowed transition duration-200"
                    disabled
                >
                    I Agree &amp; Continue
                </button>
            </div>
        </div>
    </div>

    <!-- Consolidated Form Skeleton -->
    <div id="rdFormSkeleton">
        @include('components.loading.resident-request-form-skeleton', ['variant' => 'document'])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="rdContent" style="display: none;">
    <!-- Header Section -->
    <div class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Request New Document</h1>
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
        <form action="{{ route('resident.request_document_request') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Document Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    <i class="fas fa-file-signature mr-2 text-blue-600"></i>
                    Document Information
                </h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="document_template_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Document Type <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                id="document_template_id" 
                                name="document_template_id" 
                                required>
                            <option value="">Select a document type</option>
                            @foreach(($templates ?? []) as $t)
                                <option value="{{ optional($t)->id }}" {{ old('document_template_id') == optional($t)->id ? 'selected' : '' }}>
                                    {{ optional($t)->document_type ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Choose the type of document you need</p>
                    </div>

                    <input type="hidden" id="document_type" name="document_type" value="">
                </div>
            </div>

            <!-- Purpose and Details -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">
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
                                  placeholder="Enter purpose (Example: Job application, school enrollment, financial assistance)"
                                  required>{{ old('description') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Provide a clear and specific purpose for your document request</p>
                    </div>

                </div>

                <!-- Template-specific fields will be dynamically loaded here -->
                <div id="templateFieldsContainer" class="mt-4 space-y-4"></div>
            </div>

            <!-- Privacy Consent Section -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2 shadow-lg">
                <div class="flex items-start">
                    <input type="checkbox" id="privacy_consent" name="privacy_consent" value="1" required
                        class="mt-1 mr-3 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 bg-white rounded"
                        {{ old('privacy_consent') ? 'checked' : '' }}>
                    <label for="privacy_consent" class="text-sm text-gray-700 flex-1">
                        I acknowledge that I have read and agree to the 
                        <a href="{{ route('public.privacy') }}" target="_blank" 
                           class="text-blue-600 hover:text-blue-700 underline font-medium transition-colors">
                            Barangay Privacy Policy
                        </a>
                        regarding the collection, use, and storage of my personal data.
                        <span class="text-red-500">*</span>
                    </label>
                </div>
                <p class="text-xs text-gray-600 mt-3 ml-7 leading-relaxed">
                    <strong class="text-gray-700">Note:</strong> By checking this box, you consent to the processing of your personal information as described in our Privacy Policy.
                </p>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Your request will be processed by barangay officials
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('resident.my-requests') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" id="submitBtn"
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Request
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Requirements Information -->
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Important Requirements</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Additional requirements may vary by document type</li>
                        <li>Processing fees may apply depending on the document</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Document payment terms modal gate
    const documentPaymentModal = document.getElementById('documentPaymentModal');
    const agreeDocumentPaymentTerms = document.getElementById('agreeDocumentPaymentTerms');
    const continueToDocumentBtn = document.getElementById('continueToDocumentBtn');

    if (documentPaymentModal && agreeDocumentPaymentTerms && continueToDocumentBtn) {
        continueToDocumentBtn.disabled = !agreeDocumentPaymentTerms.checked;

        agreeDocumentPaymentTerms.addEventListener('change', function () {
            continueToDocumentBtn.disabled = !this.checked;
        });

        continueToDocumentBtn.addEventListener('click', function () {
            documentPaymentModal.classList.add('hidden');
            documentPaymentModal.setAttribute('aria-hidden', 'true');
        });
    }

    setTimeout(() => {
        const fs = document.getElementById('rdFormSkeleton');
        const content = document.getElementById('rdContent');
        if (fs) fs.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);

    // Template fields loading
    const templateSelect = document.getElementById('document_template_id');
    const documentTypeHidden = document.getElementById('document_type');
    const templateFieldsContainer = document.getElementById('templateFieldsContainer');

    // Load template-specific form fields
    async function loadTemplateFields(templateId) {
        if (!templateId || !templateFieldsContainer) {
            if (templateFieldsContainer) templateFieldsContainer.innerHTML = '';
            return;
        }

        try {
            templateFieldsContainer.innerHTML = '<div class="text-sm text-gray-500 flex items-center"><i class="fas fa-spinner fa-spin mr-2"></i>Loading template fields...</div>';

            const url = `{{ url('/resident/templates') }}/${templateId}/form-config`;
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            });

            if (!res.ok) {
                templateFieldsContainer.innerHTML = '<p class="text-sm text-red-500">Failed to load template fields.</p>';
                return;
            }

            const data = await res.json();
            const fields = data.manual_fields || [];

            if (!fields.length) {
                templateFieldsContainer.innerHTML = '<p class="text-sm text-gray-500">No additional details required for this template.</p>';
                return;
            }

            // Build form inputs dynamically
            let html = '<div class="mt-4 pt-4 border-t border-gray-200"><h4 class="text-sm font-semibold text-gray-700 mb-3">Additional Information</h4>';
            fields.forEach(field => {
                const inputName = `template_fields[${field.name}]`;
                const label = field.label || field.name.replace(/_/g, ' ');
                const requiredAttr = field.required ? 'required' : '';
                const fieldId = `template_field_${field.name}`;
                const autoFilled = field.auto_filled || false;
                const autoFillClass = autoFilled ? 'bg-blue-50 border-blue-200' : '';

                if (field.type === 'textarea') {
                    html += `
                        <div class="mb-4">
                            <label for="${fieldId}" class="block text-sm font-medium text-gray-700 mb-1">
                                ${label}${field.required ? ' <span class="text-red-500">*</span>' : ''}
                            </label>
                            <textarea
                                id="${fieldId}"
                                name="${inputName}"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${autoFillClass}"
                                ${requiredAttr}
                                placeholder="${field.placeholder || ''}"
                                data-auto-filled="${autoFilled}"
                                data-source="${field.source || ''}"
                            ></textarea>
                        </div>
                    `;
                } else if (field.type === 'select' && Array.isArray(field.options)) {
                    html += `
                        <div class="mb-4">
                            <label for="${fieldId}" class="block text-sm font-medium text-gray-700 mb-1">
                                ${label}${field.required ? ' <span class="text-red-500">*</span>' : ''}
                            </label>
                            <select
                                id="${fieldId}"
                                name="${inputName}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${autoFillClass}"
                                ${requiredAttr}
                                data-auto-filled="${autoFilled}"
                                data-source="${field.source || ''}"
                            >
                                <option value="">Select...</option>
                                ${field.options.map(opt => `
                                    <option value="${opt.value}">${opt.label}</option>
                                `).join('')}
                            </select>
                        </div>
                    `;
                } else {
                    const inputType = field.type === 'number' ? 'number' : (field.type === 'date' ? 'date' : (field.type === 'time' ? 'time' : 'text'));
                    const numberAttrs = field.type === 'number' ? `min="${field.min || 0}" step="${field.step || 1}"` : '';
                    html += `
                        <div class="mb-4">
                            <label for="${fieldId}" class="block text-sm font-medium text-gray-700 mb-1">
                                ${label}${field.required ? ' <span class="text-red-500">*</span>' : ''}
                            </label>
                            <input
                                type="${inputType}"
                                id="${fieldId}"
                                name="${inputName}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${autoFillClass}"
                                ${requiredAttr}
                                ${numberAttrs}
                                placeholder="${field.placeholder || ''}"
                                data-auto-filled="${autoFilled}"
                                data-source="${field.source || ''}"
                            />
                        </div>
                    `;
                }
            });
            html += '</div>';
            templateFieldsContainer.innerHTML = html;
            
            // Populate auto-filled fields from resident data
            await populateAutoFilledFields();
        } catch (e) {
            console.error('Error loading template fields', e);
            templateFieldsContainer.innerHTML = '<p class="text-sm text-red-500">Error loading template fields.</p>';
        }
    }

    // Populate auto-filled fields from resident demographics
    async function populateAutoFilledFields() {
        try {
            // Get all fields marked as auto-filled
            const autoFilledFields = document.querySelectorAll('[data-auto-filled="true"]');
            if (autoFilledFields.length === 0) return;

            // Fetch resident profile data (birth_date is available in profile)
            const res = await fetch('{{ route("resident.profile") }}', {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            });

            if (!res.ok) {
                // Try alternative: fetch from profile page data if available
                console.warn('Could not fetch resident profile for auto-fill');
                return;
            }

            const profileData = await res.json();

            // Populate each auto-filled field based on its source
            autoFilledFields.forEach(field => {
                const source = field.getAttribute('data-source');
                if (!source) return;

                let value = '';
                
                // Map source to profile field
                if (source === 'resident.birth_date') {
                    if (profileData.birth_date) {
                        // Format date for date input (YYYY-MM-DD)
                        const date = new Date(profileData.birth_date);
                        if (!isNaN(date.getTime())) {
                            value = date.toISOString().split('T')[0];
                        }
                    }
                }

                if (value && field.value === '') {
                    field.value = value;
                }
            });
        } catch (e) {
            console.error('Error populating auto-filled fields', e);
            // Silently fail - fields can still be filled manually
        }
    }

    // Sync hidden document_type with selected template and load template fields
    if (templateSelect && documentTypeHidden) {
        templateSelect.addEventListener('change', () => {
            const selected = templateSelect.options[templateSelect.selectedIndex];
            const templateId = templateSelect.value || '';
            documentTypeHidden.value = selected ? (selected.textContent || '').trim() : '';
            
            // Load template-specific fields
            loadTemplateFields(templateId);
        });
        // Initialize on load
        const initSelected = templateSelect.options[templateSelect.selectedIndex];
        documentTypeHidden.value = initSelected ? (initSelected.textContent || '').trim() : '';
        
        // Load fields if template is pre-selected
        if (templateSelect.value) {
            loadTemplateFields(templateSelect.value);
        }
    }

    // Privacy consent checkbox validation
    const privacyConsentCheckbox = document.getElementById('privacy_consent');
    const submitBtn = document.getElementById('submitBtn');
    
    function updateSubmitButton() {
        if (privacyConsentCheckbox && submitBtn) {
            submitBtn.disabled = !privacyConsentCheckbox.checked;
        }
    }
    
    if (privacyConsentCheckbox) {
        privacyConsentCheckbox.addEventListener('change', updateSubmitButton);
        updateSubmitButton(); // Initial check
    }

    // Form submission validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!privacyConsentCheckbox || !privacyConsentCheckbox.checked) {
                e.preventDefault();
                alert('Please acknowledge and agree to the Privacy Policy by checking the consent box.');
                if (privacyConsentCheckbox) privacyConsentCheckbox.focus();
                return false;
            }
        });
    }
});
</script>
@endpush