@extends('admin.main.layout')

@section('title', 'Create Medical Logbook Entry')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Create Medical Logbook Entry</h1>
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
        <form action="{{ route('admin.medical-logbooks.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Patient Information -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-user mr-3 text-blue-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Patient Information</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="resident_id" class="block text-sm font-medium text-gray-700 mb-2">Select Patient <span class="text-red-500">*</span></label>
                        <select name="resident_id" id="resident_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                            <option value="">Select a patient...</option>
                            @foreach($residents as $resident)
                                <option value="{{ $resident->id }}" {{ old('resident_id') == $resident->id ? 'selected' : '' }}>
                                    {{ $resident->name }} ({{ $resident->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="consultation_date" class="block text-sm font-medium text-gray-700 mb-2">Consultation Date <span class="text-red-500">*</span></label>
                        <input type="date" name="consultation_date" id="consultation_date" 
                               value="{{ old('consultation_date', date('Y-m-d')) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" required>
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
                        <label for="consultation_time" class="block text-sm font-medium text-gray-700 mb-2">Consultation Time <span class="text-red-500">*</span></label>
                        <input type="time" name="consultation_time" id="consultation_time" value="{{ old('consultation_time') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
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
                </div>
                <div class="mt-6 grid grid-cols-1 gap-6">
                    <div>
                        <label for="symptoms" class="block text-sm font-medium text-gray-700 mb-2">Symptoms <span class="text-red-500">*</span></label>
                        <textarea name="symptoms" id="symptoms" rows="3" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="List symptoms..." required>{{ old('symptoms') }}</textarea>
                    </div>
                    <div>
                        <label for="chief_complaint" class="block text-sm font-medium text-gray-700 mb-2">Chief Complaint <span class="text-red-500">*</span></label>
                        <textarea name="chief_complaint" id="chief_complaint" rows="3" 
                                  class="w-full border border-gray-300 rounded px-3 py-2" 
                                  placeholder="Primary reason for visit..." required>{{ old('chief_complaint') }}</textarea>
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

            <!-- Physical Examination -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-user-md mr-3 text-indigo-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Physical Examination</h2>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="physical_examination" class="block text-sm font-medium text-gray-700 mb-2">Physical Examination</label>
                        <textarea name="physical_examination" id="physical_examination" rows="4" 
                                  class="w-full border border-gray-300 rounded px-3 py-2" 
                                  placeholder="Detailed physical examination findings...">{{ old('physical_examination') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Diagnosis & Treatment -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-stethoscope mr-3 text-purple-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Diagnosis & Treatment</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">Diagnosis</label>
                        <textarea name="diagnosis" id="diagnosis" rows="3" 
                                  class="w-full border border-gray-300 rounded px-3 py-2" 
                                  placeholder="Medical diagnosis...">{{ old('diagnosis') }}</textarea>
                    </div>
                    <div>
                        <label for="treatment_plan" class="block text-sm font-medium text-gray-700 mb-2">Treatment Plan</label>
                        <textarea name="treatment_plan" id="treatment_plan" rows="4" 
                                  class="w-full border border-gray-300 rounded px-3 py-2" 
                                  placeholder="Prescribed medications, procedures, follow-up instructions...">{{ old('treatment_plan') }}</textarea>
                    </div>
                </div>
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="prescribed_medications" class="block text-sm font-medium text-gray-700 mb-2">Prescribed Medications</label>
                        <textarea name="prescribed_medications" id="prescribed_medications" rows="3" 
                                  class="w-full border border-gray-300 rounded px-3 py-2" 
                                  placeholder="List of prescribed medications with dosages...">{{ old('prescribed_medications') }}</textarea>
                    </div>
                    <div>
                        <label for="lab_tests_ordered" class="block text-sm font-medium text-gray-700 mb-2">Lab Tests Ordered</label>
                        <textarea name="lab_tests_ordered" id="lab_tests_ordered" rows="3" 
                                  class="w-full border border-gray-300 rounded px-3 py-2" 
                                  placeholder="List of lab tests ordered...">{{ old('lab_tests_ordered') }}</textarea>
                    </div>
                </div>
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="lab_results" class="block text-sm font-medium text-gray-700 mb-2">Lab Results</label>
                        <textarea name="lab_results" id="lab_results" rows="3" 
                                  class="w-full border border-gray-300 rounded px-3 py-2" 
                                  placeholder="Lab results...">{{ old('lab_results') }}</textarea>
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                        <textarea name="notes" id="notes" rows="3" 
                                  class="w-full border border-gray-300 rounded px-3 py-2" 
                                  placeholder="Any additional notes or observations...">{{ old('notes') }}</textarea>
                    </div>
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
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="attending_health_worker" class="block text-sm font-medium text-gray-700 mb-2">Attending Health Worker <span class="text-red-500">*</span></label>
                        <input type="text" name="attending_health_worker" id="attending_health_worker" value="{{ old('attending_health_worker') }}" class="w-full border border-gray-300 rounded px-3 py-2" required placeholder="Name of attending health worker">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" class="w-full border border-gray-300 rounded px-3 py-2" required>
                            <option value="">Select status...</option>
                            <option value="Completed" {{ old('status', 'Completed') == 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Referred" {{ old('status') == 'Referred' ? 'selected' : '' }}>Referred</option>
                            <option value="Cancelled" {{ old('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    The logbook entry will be created and can be managed from the medical logbooks list.
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.medical-logbooks.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Create Medical Logbook Entry
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 