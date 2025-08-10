@extends('admin.main.layout')

@section('title', 'Create Medical Record Entry')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
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
                            placeholder="Type to search for a resident..."
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
                               value="{{ old('consultation_datetime') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" required>
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
                               placeholder="e.g., Dental, Mental Health, Prenatal, etc.">
                        <p class="mt-1 text-sm text-gray-500">Please specify the type of consultation</p>
                    </div>
                </div>
                <div class="mt-6 grid grid-cols-1 gap-6">
                    <div>
                        <label for="symptoms" class="block text-sm font-medium text-gray-700 mb-2">Symptoms</label>
                        <textarea name="symptoms" id="symptoms" rows="3" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="List symptoms...">{{ old('symptoms') }}</textarea>
                    </div>
                    <div>
                        <label for="chief_complaint" class="block text-sm font-medium text-gray-700 mb-2">Chief Complaint</label>
                        <textarea name="chief_complaint" id="chief_complaint" rows="3" 
                                  class="w-full border border-gray-300 rounded px-3 py-2" 
                                  placeholder="Primary reason for visit...">{{ old('chief_complaint') }}</textarea>
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
                               placeholder="e.g., 120/80">
                    </div>
                    <div>
                        <label for="temperature" class="block text-sm font-medium text-gray-700 mb-2">Temperature (°C)</label>
                        <input type="number" name="temperature" id="temperature" 
                               value="{{ old('temperature') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="e.g., 36.5" step="0.1" min="30" max="45">
                    </div>
                    <div>
                        <label for="pulse_rate" class="block text-sm font-medium text-gray-700 mb-2">Pulse Rate (bpm)</label>
                        <input type="number" name="pulse_rate" id="pulse_rate" 
                               value="{{ old('pulse_rate') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="e.g., 72" min="40" max="200">
                    </div>
                    <div>
                        <label for="respiratory_rate" class="block text-sm font-medium text-gray-700 mb-2">Respiratory Rate</label>
                        <input type="number" name="respiratory_rate" id="respiratory_rate" 
                               value="{{ old('respiratory_rate') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="e.g., 16" min="8" max="50">
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
                              placeholder="Any additional notes or observations...">{{ old('notes') }}</textarea>
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
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Done
                    </button>
                </div>
            </div>
        </form>
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
});
</script>
@endsection 