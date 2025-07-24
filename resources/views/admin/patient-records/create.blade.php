@extends('admin.modals.layout')

@section('title', 'Add Patient Record')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Add Patient Record</h1>
        <a href="{{ route('admin.patient-records.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.patient-records.store') }}" method="POST">
            @csrf
            
            <!-- Resident Selection -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Patient Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="resident_id" class="block text-sm font-medium text-gray-700 mb-2">Select Resident *</label>
                        <select id="resident_id" name="resident_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select a resident</option>
                            @foreach($residents as $resident)
                                <option value="{{ $resident->id }}" {{ old('resident_id') == $resident->id ? 'selected' : '' }}>
                                    {{ $resident->name }} ({{ $resident->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('resident_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="patient_number" class="block text-sm font-medium text-gray-700 mb-2">Patient Number *</label>
                        <input type="text" id="patient_number" name="patient_number" value="{{ old('patient_number') }}" required 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter patient number">
                        @error('patient_number')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Blood Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Blood Information</h3>
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
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Physical Measurements</h3>
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
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Medical Information</h3>
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
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Risk Assessment</h3>
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
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Emergency Contact</h3>
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
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                <textarea id="notes" name="notes" rows="4" 
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Enter any additional notes">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.patient-records.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Save Patient Record
                </button>
            </div>
        </form>
    </div>
</div>

@endsection 