@extends('admin.modals.layout')

@section('title', 'Create Patient Record')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Create Patient Record</h1>
            <a href="{{ route('admin.patient-records.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.patient-records.store') }}" method="POST">
                @csrf
                
                <!-- Resident Selection -->
                <div class="mb-6">
                    <label for="resident_id" class="block text-sm font-medium text-gray-700 mb-2">Select Resident *</label>
                    <select name="resident_id" id="resident_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                        <option value="">Choose a resident...</option>
                        @foreach($residents as $resident)
                            <option value="{{ $resident->id }}" {{ old('resident_id') == $resident->id ? 'selected' : '' }}>
                                {{ $resident->name }} ({{ $resident->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Basic Health Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="blood_type" class="block text-sm font-medium text-gray-700 mb-2">Blood Type</label>
                        <select name="blood_type" id="blood_type" class="w-full border border-gray-300 rounded px-3 py-2">
                            <option value="">Select blood type...</option>
                            <option value="A+" {{ old('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="AB+" {{ old('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                            <option value="O+" {{ old('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                        </select>
                    </div>

                    <div>
                        <label for="risk_level" class="block text-sm font-medium text-gray-700 mb-2">Risk Level *</label>
                        <select name="risk_level" id="risk_level" class="w-full border border-gray-300 rounded px-3 py-2" required>
                            <option value="">Select risk level...</option>
                            <option value="Low" {{ old('risk_level') == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ old('risk_level') == 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ old('risk_level') == 'High' ? 'selected' : '' }}>High</option>
                            <option value="Critical" {{ old('risk_level') == 'Critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                    </div>
                </div>

                <!-- Physical Measurements -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="height_cm" class="block text-sm font-medium text-gray-700 mb-2">Height (cm)</label>
                        <input type="number" name="height_cm" id="height_cm" value="{{ old('height_cm') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="e.g., 170" step="0.1" min="50" max="300">
                    </div>

                    <div>
                        <label for="weight_kg" class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                        <input type="number" name="weight_kg" id="weight_kg" value="{{ old('weight_kg') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="e.g., 65" step="0.1" min="1" max="500">
                    </div>

                    <div>
                        <label for="blood_pressure_status" class="block text-sm font-medium text-gray-700 mb-2">Blood Pressure Status</label>
                        <select name="blood_pressure_status" id="blood_pressure_status" class="w-full border border-gray-300 rounded px-3 py-2">
                            <option value="">Select status...</option>
                            <option value="Normal" {{ old('blood_pressure_status') == 'Normal' ? 'selected' : '' }}>Normal</option>
                            <option value="Pre-hypertension" {{ old('blood_pressure_status') == 'Pre-hypertension' ? 'selected' : '' }}>Pre-hypertension</option>
                            <option value="Hypertension" {{ old('blood_pressure_status') == 'Hypertension' ? 'selected' : '' }}>Hypertension</option>
                        </select>
                    </div>
                </div>

                <!-- Medical Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">Allergies</label>
                        <textarea name="allergies" id="allergies" rows="3" 
                                  class="w-full border border-gray-300 rounded px-3 py-2" 
                                  placeholder="List any known allergies...">{{ old('allergies') }}</textarea>
                    </div>

                    <div>
                        <label for="current_medications" class="block text-sm font-medium text-gray-700 mb-2">Current Medications</label>
                        <textarea name="current_medications" id="current_medications" rows="3" 
                                  class="w-full border border-gray-300 rounded px-3 py-2" 
                                  placeholder="List current medications...">{{ old('current_medications') }}</textarea>
                    </div>
                </div>

                <!-- Medical History -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="medical_history" class="block text-sm font-medium text-gray-700 mb-2">Medical History</label>
                        <textarea name="medical_history" id="medical_history" rows="4" 
                                  class="w-full border border-gray-300 rounded px-3 py-2" 
                                  placeholder="Past medical conditions, surgeries, etc...">{{ old('medical_history') }}</textarea>
                    </div>

                    <div>
                        <label for="family_medical_history" class="block text-sm font-medium text-gray-700 mb-2">Family Medical History</label>
                        <textarea name="family_medical_history" id="family_medical_history" rows="4" 
                                  class="w-full border border-gray-300 rounded px-3 py-2" 
                                  placeholder="Family history of diseases...">{{ old('family_medical_history') }}</textarea>
                    </div>
                </div>

                <!-- Lifestyle Factors -->
                <div class="mb-6">
                    <label for="lifestyle_factors" class="block text-sm font-medium text-gray-700 mb-2">Lifestyle Factors</label>
                    <textarea name="lifestyle_factors" id="lifestyle_factors" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="Smoking, alcohol consumption, exercise habits, etc...">{{ old('lifestyle_factors') }}</textarea>
                </div>

                <!-- Emergency Contact -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" id="emergency_contact_name" 
                               value="{{ old('emergency_contact_name') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="Full name">
                    </div>

                    <div>
                        <label for="emergency_contact_number" class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Number</label>
                        <input type="text" name="emergency_contact_number" id="emergency_contact_number" 
                               value="{{ old('emergency_contact_number') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="Phone number">
                    </div>

                    <div>
                        <label for="emergency_contact_relationship" class="block text-sm font-medium text-gray-700 mb-2">Relationship</label>
                        <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship" 
                               value="{{ old('emergency_contact_relationship') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="e.g., Spouse, Parent, Sibling">
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                    <textarea name="notes" id="notes" rows="4" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="Any additional notes or observations...">{{ old('notes') }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.patient-records.index') }}" 
                       class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Create Patient Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 