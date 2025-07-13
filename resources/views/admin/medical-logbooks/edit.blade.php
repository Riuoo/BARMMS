@extends('admin.modals.layout')

@section('title', 'Edit Medical Logbook Entry')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Edit Medical Logbook Entry</h1>
            <a href="{{ route('admin.medical-logbooks.show', $medicalLogbook->id) }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to Details
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

            <form action="{{ route('admin.medical-logbooks.update', $medicalLogbook->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Patient Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Patient Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Patient Name</label>
                            <input type="text" value="{{ $medicalLogbook->resident->name }}" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" disabled>
                        </div>
                        <div>
                            <label for="consultation_date" class="block text-sm font-medium text-gray-700 mb-2">Consultation Date *</label>
                            <input type="date" name="consultation_date" id="consultation_date" 
                                   value="{{ old('consultation_date', $medicalLogbook->consultation_date->format('Y-m-d')) }}" 
                                   class="w-full border border-gray-300 rounded px-3 py-2" required>
                        </div>
                    </div>
                </div>

                <!-- Chief Complaint -->
                <div class="mb-6">
                    <label for="chief_complaint" class="block text-sm font-medium text-gray-700 mb-2">Chief Complaint *</label>
                    <textarea name="chief_complaint" id="chief_complaint" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="Primary reason for visit..." required>{{ old('chief_complaint', $medicalLogbook->chief_complaint) }}</textarea>
                </div>

                <!-- Vital Signs -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Vital Signs</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="blood_pressure" class="block text-sm font-medium text-gray-700 mb-2">Blood Pressure</label>
                            <input type="text" name="blood_pressure" id="blood_pressure" 
                                   value="{{ old('blood_pressure', $medicalLogbook->blood_pressure) }}" 
                                   class="w-full border border-gray-300 rounded px-3 py-2" 
                                   placeholder="e.g., 120/80">
                        </div>
                        <div>
                            <label for="temperature" class="block text-sm font-medium text-gray-700 mb-2">Temperature (°C)</label>
                            <input type="number" name="temperature" id="temperature" 
                                   value="{{ old('temperature', $medicalLogbook->temperature) }}" 
                                   class="w-full border border-gray-300 rounded px-3 py-2" 
                                   placeholder="e.g., 36.5" step="0.1" min="30" max="45">
                        </div>
                        <div>
                            <label for="pulse_rate" class="block text-sm font-medium text-gray-700 mb-2">Pulse Rate (bpm)</label>
                            <input type="number" name="pulse_rate" id="pulse_rate" 
                                   value="{{ old('pulse_rate', $medicalLogbook->pulse_rate) }}" 
                                   class="w-full border border-gray-300 rounded px-3 py-2" 
                                   placeholder="e.g., 72" min="40" max="200">
                        </div>
                        <div>
                            <label for="respiratory_rate" class="block text-sm font-medium text-gray-700 mb-2">Respiratory Rate</label>
                            <input type="number" name="respiratory_rate" id="respiratory_rate" 
                                   value="{{ old('respiratory_rate', $medicalLogbook->respiratory_rate) }}" 
                                   class="w-full border border-gray-300 rounded px-3 py-2" 
                                   placeholder="e.g., 16" min="8" max="50">
                        </div>
                    </div>
                </div>

                <!-- Physical Examination -->
                <div class="mb-6">
                    <label for="physical_examination" class="block text-sm font-medium text-gray-700 mb-2">Physical Examination</label>
                    <textarea name="physical_examination" id="physical_examination" rows="4" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="Detailed physical examination findings...">{{ old('physical_examination', $medicalLogbook->physical_examination) }}</textarea>
                </div>

                <!-- Diagnosis -->
                <div class="mb-6">
                    <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">Diagnosis</label>
                    <textarea name="diagnosis" id="diagnosis" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="Medical diagnosis...">{{ old('diagnosis', $medicalLogbook->diagnosis) }}</textarea>
                </div>

                <!-- Treatment Plan -->
                <div class="mb-6">
                    <label for="treatment_plan" class="block text-sm font-medium text-gray-700 mb-2">Treatment Plan</label>
                    <textarea name="treatment_plan" id="treatment_plan" rows="4" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="Prescribed medications, procedures, follow-up instructions...">{{ old('treatment_plan', $medicalLogbook->treatment_plan) }}</textarea>
                </div>

                <!-- Prescribed Medications -->
                <div class="mb-6">
                    <label for="prescribed_medications" class="block text-sm font-medium text-gray-700 mb-2">Prescribed Medications</label>
                    <textarea name="prescribed_medications" id="prescribed_medications" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="List of prescribed medications with dosages...">{{ old('prescribed_medications', $medicalLogbook->prescribed_medications) }}</textarea>
                </div>

                <!-- Follow-up -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="follow_up_date" class="block text-sm font-medium text-gray-700 mb-2">Follow-up Date</label>
                        <input type="date" name="follow_up_date" id="follow_up_date" 
                               value="{{ old('follow_up_date', $medicalLogbook->follow_up_date ? $medicalLogbook->follow_up_date->format('Y-m-d') : '') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                    <div>
                        <label for="follow_up_notes" class="block text-sm font-medium text-gray-700 mb-2">Follow-up Notes</label>
                        <input type="text" name="follow_up_notes" id="follow_up_notes" 
                               value="{{ old('follow_up_notes', $medicalLogbook->follow_up_notes) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="Follow-up instructions...">
                    </div>
                </div>

                <!-- Healthcare Provider -->
                <div class="mb-6">
                    <label for="healthcare_provider" class="block text-sm font-medium text-gray-700 mb-2">Healthcare Provider</label>
                    <input type="text" name="healthcare_provider" id="healthcare_provider" 
                           value="{{ old('healthcare_provider', $medicalLogbook->healthcare_provider) }}" 
                           class="w-full border border-gray-300 rounded px-3 py-2" 
                           placeholder="Name of healthcare provider">
                </div>

                <!-- Additional Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                    <textarea name="notes" id="notes" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="Any additional notes or observations...">{{ old('notes', $medicalLogbook->notes) }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.medical-logbooks.show', $medicalLogbook->id) }}" 
                       class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Update Medical Logbook Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 