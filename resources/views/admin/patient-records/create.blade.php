@extends('admin.main.layout')

@section('title', 'Add Patient Record')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Add Patient Record</h1>
                <p class="text-gray-600">Register a new patient and record their medical information for the barangay health system.</p>
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
        <form action="{{ route('admin.patient-records.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Patient Information -->
            <div class="border-b border-gray-200 pb-6 mb-6">
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
                        />
                        <input type="hidden" id="resident_id" name="resident_id" required>
                        <div id="searchResults" class="absolute z-10 bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                        <p class="mt-1 text-sm text-gray-500">Search and select the resident for this patient record</p>
                        @error('resident_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Blood Information -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-tint mr-3 text-red-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Blood Information</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="blood_type" class="block text-sm font-medium text-gray-700 mb-2">Blood Type</label>
                        <select id="blood_type" name="blood_type" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select blood type</option>
                            <option value="A+" {{ old('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="AB+" {{ old('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                            <option value="O+" {{ old('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                        </select>
                        @error('blood_type')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="blood_pressure_status" class="block text-sm font-medium text-gray-700 mb-2">Blood Pressure Status</label>
                        <select id="blood_pressure_status" name="blood_pressure_status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select status</option>
                            <option value="Normal" {{ old('blood_pressure_status') == 'Normal' ? 'selected' : '' }}>Normal</option>
                            <option value="Elevated" {{ old('blood_pressure_status') == 'Elevated' ? 'selected' : '' }}>Elevated</option>
                            <option value="Stage 1 Hypertension" {{ old('blood_pressure_status') == 'Stage 1 Hypertension' ? 'selected' : '' }}>Stage 1 Hypertension</option>
                            <option value="Stage 2 Hypertension" {{ old('blood_pressure_status') == 'Stage 2 Hypertension' ? 'selected' : '' }}>Stage 2 Hypertension</option>
                        </select>
                        @error('blood_pressure_status')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Physical Measurements -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-ruler-vertical mr-3 text-green-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Physical Measurements</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="height_cm" class="block text-sm font-medium text-gray-700 mb-2">Height (cm)</label>
                        <input type="number" id="height_cm" name="height_cm" value="{{ old('height_cm') }}" step="0.1" min="0"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter height in cm">
                        @error('height_cm')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="weight_kg" class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                        <input type="number" id="weight_kg" name="weight_kg" value="{{ old('weight_kg') }}" step="0.1" min="0"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter weight in kg">
                        @error('weight_kg')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="bmi" class="block text-sm font-medium text-gray-700 mb-2">BMI</label>
                        <input type="number" id="bmi" name="bmi" value="{{ old('bmi') }}" step="0.01" min="0" readonly
                               class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50"
                               placeholder="Will be calculated automatically">
                        <p class="text-xs text-gray-500 mt-1">BMI will be calculated after saving.</p>
                        @error('bmi')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Medical Information -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-notes-medical mr-3 text-purple-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Medical Information</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">Allergies</label>
                        <textarea id="allergies" name="allergies" rows="3" 
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="List any allergies (separate with commas)">{{ old('allergies') }}</textarea>
                        @error('allergies')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="current_medications" class="block text-sm font-medium text-gray-700 mb-2">Current Medications</label>
                        <textarea id="current_medications" name="current_medications" rows="3" 
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="List current medications">{{ old('current_medications') }}</textarea>
                        @error('current_medications')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mt-6">
                    <label for="medical_history" class="block text-sm font-medium text-gray-700 mb-2">Medical History</label>
                    <textarea id="medical_history" name="medical_history" rows="4" 
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Enter medical history">{{ old('medical_history') }}</textarea>
                    @error('medical_history')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mt-6">
                    <label for="family_medical_history" class="block text-sm font-medium text-gray-700 mb-2">Family Medical History</label>
                    <textarea id="family_medical_history" name="family_medical_history" rows="4" 
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Enter family medical history">{{ old('family_medical_history') }}</textarea>
                    @error('family_medical_history')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Risk Assessment -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-exclamation-triangle mr-3 text-yellow-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Risk Assessment</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="risk_level" class="block text-sm font-medium text-gray-700 mb-2">Risk Level</label>
                        <select id="risk_level" name="risk_level" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select risk level</option>
                            <option value="low" {{ old('risk_level') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('risk_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('risk_level') == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                        @error('risk_level')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="lifestyle_factors" class="block text-sm font-medium text-gray-700 mb-2">Lifestyle Factors</label>
                        <textarea id="lifestyle_factors" name="lifestyle_factors" rows="3" 
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Enter lifestyle factors (smoking, exercise, diet, etc.)">{{ old('lifestyle_factors') }}</textarea>
                        @error('lifestyle_factors')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-phone-alt mr-3 text--600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Emergency Contact</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                        <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter emergency contact name">
                        @error('emergency_contact_name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="emergency_contact_number" class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                        <input type="text" id="emergency_contact_number" name="emergency_contact_number" value="{{ old('emergency_contact_number') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter emergency contact number">
                        @error('emergency_contact_number')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="emergency_contact_relationship" class="block text-sm font-medium text-gray-700 mb-2">Relationship</label>
                        <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter relationship">
                        @error('emergency_contact_relationship')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-sticky-note mr-3 text-gray-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Additional Notes</h2>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                        <textarea id="notes" name="notes" rows="4" 
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Enter any additional notes">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    The record will be created and can be managed from the patient records list.
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.patient-records.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Save Patient Record
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