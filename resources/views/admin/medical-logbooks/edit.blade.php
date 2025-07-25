@extends('admin.modals.layout')

@section('title', 'Edit Medical Logbook Entry')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Edit Medical Logbook Entry</h1>
            <a href="{{ route('admin.medical-logbooks.show', $medicalLogbook->id) }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to Details
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
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
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-user-md mr-2 text-blue-600"></i>Patient Information
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
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

                <!-- Consultation Time & Type -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-clock mr-2 text-red-600"></i>Consultation Time & Type
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="consultation_time" class="block text-sm font-medium text-gray-700 mb-2">Consultation Time *</label>
                            <input type="time" name="consultation_time" id="consultation_time" value="{{ old('consultation_time', $medicalLogbook->consultation_time ? $medicalLogbook->consultation_time->format('H:i') : '') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label for="consultation_type" class="block text-sm font-medium text-gray-700 mb-2">Consultation Type *</label>
                            <select name="consultation_type" id="consultation_type" class="w-full border border-gray-300 rounded px-3 py-2" required>
                                <option value="">Select type...</option>
                                <option value="Check-up" {{ old('consultation_type', $medicalLogbook->consultation_type) == 'Check-up' ? 'selected' : '' }}>Check-up</option>
                                <option value="Emergency" {{ old('consultation_type', $medicalLogbook->consultation_type) == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                                <option value="Follow-up" {{ old('consultation_type', $medicalLogbook->consultation_type) == 'Follow-up' ? 'selected' : '' }}>Follow-up</option>
                                <option value="Consultation" {{ old('consultation_type', $medicalLogbook->consultation_type) == 'Consultation' ? 'selected' : '' }}>Consultation</option>
                                <option value="Other" {{ old('consultation_type', $medicalLogbook->consultation_type) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Symptoms -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-head-side-virus mr-2 text-green-600"></i>Symptoms
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="symptoms" class="block text-sm font-medium text-gray-700 mb-2">Symptoms *</label>
                            <textarea name="symptoms" id="symptoms" rows="3" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="List symptoms..." required>{{ old('symptoms', $medicalLogbook->symptoms) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Chief Complaint -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2 text-purple-600"></i>Chief Complaint
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="chief_complaint" class="block text-sm font-medium text-gray-700 mb-2">Chief Complaint *</label>
                            <textarea name="chief_complaint" id="chief_complaint" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="Primary reason for visit..." required>{{ old('chief_complaint', $medicalLogbook->chief_complaint) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Vital Signs -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-heartbeat mr-2 text-yellow-600"></i>Vital Signs
                    </h3>
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
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-stethoscope mr-2 text-indigo-600"></i>Physical Examination
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="physical_examination" class="block text-sm font-medium text-gray-700 mb-2">Physical Examination</label>
                            <textarea name="physical_examination" id="physical_examination" rows="4" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="Detailed physical examination findings...">{{ old('physical_examination', $medicalLogbook->physical_examination) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Diagnosis -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-diagnoses mr-2 text-blue-600"></i>Diagnosis
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">Diagnosis</label>
                            <textarea name="diagnosis" id="diagnosis" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="Medical diagnosis...">{{ old('diagnosis', $medicalLogbook->diagnosis) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Treatment Plan -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-prescription-bottle-alt mr-2 text-red-600"></i>Treatment Plan
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="treatment_plan" class="block text-sm font-medium text-gray-700 mb-2">Treatment Plan</label>
                            <textarea name="treatment_plan" id="treatment_plan" rows="4" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="Prescribed medications, procedures, follow-up instructions...">{{ old('treatment_plan', $medicalLogbook->treatment_plan) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Prescribed Medications -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-pills mr-2 text-green-600"></i>Prescribed Medications
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="prescribed_medications" class="block text-sm font-medium text-gray-700 mb-2">Prescribed Medications</label>
                            <textarea name="prescribed_medications" id="prescribed_medications" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="List of prescribed medications with dosages...">{{ old('prescribed_medications', $medicalLogbook->prescribed_medications) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Follow-up -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-calendar-check mr-2 text-purple-600"></i>Follow-up
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                </div>

                <!-- Attending Health Worker -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-user-md mr-2 text-yellow-600"></i>Attending Health Worker
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="attending_health_worker" class="block text-sm font-medium text-gray-700 mb-2">Attending Health Worker *</label>
                            <input type="text" name="attending_health_worker" id="attending_health_worker" value="{{ old('attending_health_worker', $medicalLogbook->attending_health_worker) }}" class="w-full border border-gray-300 rounded px-3 py-2" required placeholder="Name of attending health worker">
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-check-circle mr-2 text-indigo-600"></i>Status
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select name="status" id="status" class="w-full border border-gray-300 rounded px-3 py-2" required>
                                <option value="">Select status...</option>
                                <option value="Completed" {{ old('status', $medicalLogbook->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                                <option value="Pending" {{ old('status', $medicalLogbook->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Referred" {{ old('status', $medicalLogbook->status) == 'Referred' ? 'selected' : '' }}>Referred</option>
                                <option value="Cancelled" {{ old('status', $medicalLogbook->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>Additional Notes
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                            <textarea name="notes" id="notes" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="Any additional notes or observations...">{{ old('notes', $medicalLogbook->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-between items-center mt-6">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        Click "Update Medical Logbook Entry" to save your changes.
                    </div>
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('admin.medical-logbooks.show', $medicalLogbook->id) }}" 
                           class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i>Update Medical Logbook Entry
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 