@extends('admin.main.layout')

@section('title', 'Create Medical Record Entry')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">

    <!-- Header Skeleton -->
    <div id="createMedicalHeaderSkeleton">
        @include('components.loading.create-form-skeleton', ['type' => 'header', 'showButton' => false])
    </div>

    <!-- Form Skeleton -->
    <div id="createMedicalFormSkeleton">
        @include('components.loading.create-form-skeleton', ['type' => 'medical-record'])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="createMedicalContent" style="display: none;">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Create Medical Record Entry</h1>
                <p class="text-gray-600">Log a new medical consultation, treatment, or health center activity for a resident.</p>
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
        <form action="{{ route('admin.medical-records.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Patient Information -->
            <div class="border-b border-gray-200">
                <div class="flex items-center mb-4">
                    <i class="fas fa-user mr-3 text-blue-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Patient Information</h2>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="residentSearch" class="block text-sm font-medium text-gray-700 mb-2">Select Resident <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            id="residentSearch"
                            placeholder="Search residents..."
                            autocomplete="off"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            aria-label="Search for a resident"
                            required
                        />
                        <input type="hidden" id="resident_id" name="resident_id" required>
                        <div id="searchResults" class="absolute z-10 bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                        <p class="mt-1 text-sm text-gray-500">Search and select the resident for this patient record</p>
                    </div>
                    <div>
                        <!-- Consultation date and time merged into consultation_datetime field below -->
                    </div>
                </div>
            </div>

            <!-- Consultation Details -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-calendar-check mr-3 text-green-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Consultation Details</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="consultation_datetime" class="block text-sm font-medium text-gray-700 mb-2">Consultation Date & Time <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="consultation_datetime" id="consultation_datetime" 
                               value="{{ old('consultation_datetime', now()->format('Y-m-d\\TH:i')) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" required readonly>
                        <p class="mt-1 text-sm text-gray-500">Auto-set to current date and time</p>
                    </div>
                    <div>
                        <label for="consultation_type" class="block text-sm font-medium text-gray-700 mb-2">Consultation Type <span class="text-red-500">*</span></label>
                        <select name="consultation_type" id="consultation_type" class="w-full border border-gray-300 rounded px-3 py-2" required>
                            <option value="">Select type...</option>
                            <option value="Check-up" {{ old('consultation_type') == 'Check-up' ? 'selected' : '' }}>Check-up</option>
                            <option value="Emergency" {{ old('consultation_type') == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                            <option value="Follow-up" {{ old('consultation_type') == 'Follow-up' ? 'selected' : '' }}>Follow-up</option>
                            <option value="Consultation" {{ old('consultation_type') == 'Consultation' ? 'selected' : '' }}>Consultation</option>
                            <option value="Other" {{ old('consultation_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div id="consultation_type_other_container" class="hidden">
                        <label for="consultation_type_other" class="block text-sm font-medium text-gray-700 mb-2">Specify Consultation Type <span class="text-red-500">*</span></label>
                        <input type="text" name="consultation_type_other" id="consultation_type_other" 
                               value="{{ old('consultation_type_other') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="Example: Dental, Mental Health, Prenatal">
                        <p class="mt-1 text-sm text-gray-500">Please specify the type of consultation</p>
                    </div>
                </div>
                <div class="mt-6 grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Complaint <span class="text-red-500">*</span></label>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 space-y-3 sm:space-y-0">
                            <input type="text" name="complaint" id="complaint_display"
                                   value="{{ old('complaint') }}"
                                   class="flex-1 w-full border border-gray-300 rounded px-3 py-2 bg-gray-100"
                                   placeholder="Select a complaint" required readonly>
                            <button type="button" id="openComplaintModal"
                                    class="inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-lg text-gray-100 bg-gray-800 hover:bg-gray-700 hover:border-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 focus:ring-offset-gray-900 transition duration-200 dark:text-gray-700 dark:border-gray-300 dark:bg-white dark:hover:bg-gray-50 dark:hover:border-gray-300 dark:focus:ring-offset-white">
                                <i class="fas fa-list mr-2"></i>Choose Complaint
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Choose the primary complaint for this visit</p>
                    </div>
                </div>
            </div>

            <!-- Complaint Modal -->
            @php
                $complaintOptions = [
                    'Fever',
                    'Headache',
                    'Cough',
                    'Cold/Congestion',
                    'Sore Throat',
                    'Stomach Pain',
                    'Body Pain',
                    'Dizziness',
                    'Skin Rash',
                    'Hypertension Follow-up',
                    'Diabetes Follow-up',
                    'Prescription Refill',
                    'Prenatal Check-up',
                    'General Check-up',
                    'Other',
                ];
                $selectedComplaint = old('complaint');
            @endphp
            <div id="complaintModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-lg mx-4">
                    <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                        <h3 class="text-lg font-semibold text-gray-900">Select Complaint</h3>
                        <button type="button" id="closeComplaintModal" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <label for="complaint_modal_select" class="block text-sm font-medium text-gray-700 mb-2">Common Complaints</label>
                            <select id="complaint_modal_select" class="w-full border border-gray-300 rounded px-3 py-2">
                                <option value="">Select complaint...</option>
                                @foreach ($complaintOptions as $option)
                                    <option value="{{ $option }}" {{ $selectedComplaint === $option ? 'selected' : '' }}>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="complaint_modal_other" class="{{ $selectedComplaint === 'Other' ? '' : 'hidden' }}">
                            <label for="complaint_other_input" class="block text-sm font-medium text-gray-700 mb-2">Specify Complaint <span class="text-red-500">*</span></label>
                            <input type="text" name="complaint_other" id="complaint_other_input"
                                   value="{{ old('complaint_other') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2"
                                   placeholder="Enter complaint or symptoms when selecting Other">
                            <p class="mt-1 text-sm text-gray-500">Provide details when selecting "Other"</p>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 px-4 py-3 border-t border-gray-200">
                        <button type="button" id="cancelComplaintSelection"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                            Cancel
                        </button>
                        <button type="button" id="saveComplaintSelection"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition duration-200">
                            Save Complaint
                        </button>
                    </div>
                </div>
            </div>

            <!-- Vital Signs -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-heartbeat mr-3 text-pink-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Vital Signs</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="blood_pressure" class="block text-sm font-medium text-gray-700 mb-2">Blood Pressure</label>
                        <input type="text" name="blood_pressure" id="blood_pressure" 
                               value="{{ old('blood_pressure') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="Example: 120/80">
                    </div>
                    <div>
                        <label for="temperature" class="block text-sm font-medium text-gray-700 mb-2">Temperature (°C)</label>
                        <input type="number" name="temperature" id="temperature" 
                               value="{{ old('temperature') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="Example: 36.5" step="0.1" min="30" max="45">
                    </div>
                    <div>
                        <label for="pulse_rate" class="block text-sm font-medium text-gray-700 mb-2">Pulse Rate (bpm)</label>
                        <input type="number" name="pulse_rate" id="pulse_rate" 
                               value="{{ old('pulse_rate') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="Example: 72" min="40" max="200">
                    </div>
                    <div>
                        <label for="respiratory_rate" class="block text-sm font-medium text-gray-700 mb-2">Respiratory Rate</label>
                        <input type="number" name="respiratory_rate" id="respiratory_rate" 
                               value="{{ old('respiratory_rate') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="Example: 16" min="8" max="50">
                    </div>
                </div>
            </div>

            <!-- Diagnosis & Treatment -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-stethoscope mr-3 text-purple-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Diagnosis & Treatment</h2>
                </div>
                <div>
                    <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">Diagnosis</label>
                    <textarea name="diagnosis" id="diagnosis" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="Medical diagnosis...">{{ old('diagnosis') }}</textarea>
                </div>
                <div>
                    <label for="prescribed_medications" class="block text-sm font-medium text-gray-700 mb-2">Prescribed Medications</label>
                    <textarea name="prescribed_medications" id="prescribed_medications" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="List of prescribed medications with dosages...">{{ old('prescribed_medications') }}</textarea>
                </div>
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                    <textarea name="notes" id="notes" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="Enter notes">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Follow-up & Health Worker -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-user-nurse mr-3 text-pink-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Follow-up & Health Worker</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="follow_up_date" class="block text-sm font-medium text-gray-700 mb-2">Follow-up Date</label>
                        <input type="date" name="follow_up_date" id="follow_up_date" 
                               value="{{ old('follow_up_date') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                    <div>
                        <label for="follow_up_notes" class="block text-sm font-medium text-gray-700 mb-2">Follow-up Notes</label>
                        <input type="text" name="follow_up_notes" id="follow_up_notes" 
                               value="{{ old('follow_up_notes') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="Follow-up instructions...">
                    </div>
                </div>
            </div>

            <!-- Privacy Consent Section -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2 shadow-lg">
                <div class="flex items-start">
                    <input type="checkbox" id="privacy_consent" name="privacy_consent" value="1" required
                        class="mt-1 mr-3 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 bg-white rounded"
                        {{ old('privacy_consent') ? 'checked' : '' }}>
                    <label for="privacy_consent" class="text-sm text-gray-700 flex-1">
                        I confirm that the resident has been informed about and has consented to the 
                        <a href="{{ route('public.privacy') }}" target="_blank" 
                           class="text-blue-600 hover:text-blue-700 underline font-medium transition-colors">
                            Barangay Privacy Policy
                        </a>
                        regarding the collection, use, and storage of their personal and health data.
                        <span class="text-red-500">*</span>
                    </label>
                </div>
                <p class="text-xs text-gray-600 mt-3 ml-7 leading-relaxed">
                    <strong class="text-gray-700">Note:</strong> As the Secretary filling out this form, you are confirming that the resident has been informed about the Privacy Policy and has provided their consent for the processing of their personal and health information as described in the policy.
                </p>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    The record entry will be created and can be managed from the medical records list.
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.medical-records.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" id="submitBtn"
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <i class="fas fa-save mr-2"></i>
                        Done
                    </button>
                </div>
            </div>
        </form>
    </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    function debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Consultation type conditional field
    const consultationTypeSelect = document.getElementById('consultation_type');
    const otherContainer = document.getElementById('consultation_type_other_container');
    const otherInput = document.getElementById('consultation_type_other');

    consultationTypeSelect.addEventListener('change', function() {
        if (this.value === 'Other') {
            otherContainer.classList.remove('hidden');
            otherInput.required = true;
        } else {
            otherContainer.classList.add('hidden');
            otherInput.required = false;
            otherInput.value = '';
        }
    });

    // Initialize on page load if "Other" is pre-selected (e.g., after validation errors)
    if (consultationTypeSelect.value === 'Other') {
        otherContainer.classList.remove('hidden');
        otherInput.required = true;
    }

    // Auto-set current datetime and prevent edits
    const consultationDatetimeInput = document.getElementById('consultation_datetime');
    if (consultationDatetimeInput && !consultationDatetimeInput.value) {
        const now = new Date();
        const pad = (n) => n.toString().padStart(2, '0');
        const local = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
        consultationDatetimeInput.value = local;
    }
    consultationDatetimeInput.addEventListener('keydown', (e) => e.preventDefault());
    consultationDatetimeInput.addEventListener('paste', (e) => e.preventDefault());

    // Complaint modal logic
    const complaintModal = document.getElementById('complaintModal');
    const openComplaintModalBtn = document.getElementById('openComplaintModal');
    const closeComplaintModalBtn = document.getElementById('closeComplaintModal');
    const cancelComplaintSelectionBtn = document.getElementById('cancelComplaintSelection');
    const saveComplaintSelectionBtn = document.getElementById('saveComplaintSelection');
    const complaintSelect = document.getElementById('complaint_modal_select');
    const complaintDisplay = document.getElementById('complaint_display');
    const complaintOtherContainer = document.getElementById('complaint_modal_other');
    const complaintOtherInput = document.getElementById('complaint_other_input');

    const showComplaintModal = () => {
        complaintModal.classList.remove('hidden');
        complaintModal.classList.add('flex');
    };

    const hideComplaintModal = () => {
        complaintModal.classList.add('hidden');
        complaintModal.classList.remove('flex');
    };

    const toggleComplaintOther = (value) => {
        const isOther = value === 'Other';
        complaintOtherContainer.classList.toggle('hidden', !isOther);
        complaintOtherInput.required = isOther;
        if (!isOther) {
            complaintOtherInput.value = '';
        }
    };

    const applyComplaintSelection = () => {
        const selectedValue = complaintSelect.value;
        complaintDisplay.value = selectedValue === 'Other' && complaintOtherInput.value
            ? complaintOtherInput.value
            : selectedValue;
        toggleComplaintOther(selectedValue);
        hideComplaintModal();
    };

    openComplaintModalBtn?.addEventListener('click', showComplaintModal);
    closeComplaintModalBtn?.addEventListener('click', hideComplaintModal);
    cancelComplaintSelectionBtn?.addEventListener('click', hideComplaintModal);
    saveComplaintSelectionBtn?.addEventListener('click', applyComplaintSelection);
    complaintSelect?.addEventListener('change', (e) => toggleComplaintOther(e.target.value));

    // Initialize complaint modal state using existing values
    toggleComplaintOther(complaintSelect.value);
    if (complaintDisplay.value && !complaintSelect.value) {
        // If display has value (e.g., from validation errors), mirror it into the select when possible
        const optionMatch = Array.from(complaintSelect.options).find(opt => opt.value === complaintDisplay.value);
        if (optionMatch) {
            complaintSelect.value = optionMatch.value;
        }
    }

    // Resident AJAX search for patient records
    const searchInput = document.getElementById('residentSearch');
    const searchResults = document.getElementById('searchResults');
    const residentIdInput = document.getElementById('resident_id');

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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const headerSkeleton = document.getElementById('createMedicalHeaderSkeleton');
        const formSkeleton = document.getElementById('createMedicalFormSkeleton');
        const content = document.getElementById('createMedicalContent');
        if (headerSkeleton) headerSkeleton.style.display = 'none';
        if (formSkeleton) formSkeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection 