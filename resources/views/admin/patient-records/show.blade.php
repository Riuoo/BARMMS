@extends('admin.modals.layout')

@section('title', 'Patient Record Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Patient Record Details</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.patient-records.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
            <a href="{{ route('admin.patient-records.edit', $patientRecord->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i>Edit Record
            </a>
        </div>
    </div>

    <!-- Patient Information Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-16 w-16">
                    <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 text-2xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $patientRecord->resident->name ?? 'N/A' }}</h2>
                    <p class="text-gray-600">Patient Number: {{ $patientRecord->patient_number }}</p>
                    <p class="text-gray-600">{{ $patientRecord->resident->email ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="text-right">
                @if($patientRecord->risk_level)
                    @if($patientRecord->risk_level == 'high')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fas fa-exclamation-triangle mr-2"></i>High Risk
                        </span>
                    @elseif($patientRecord->risk_level == 'medium')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-exclamation-circle mr-2"></i>Medium Risk
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>Low Risk
                        </span>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Detailed Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Blood Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-tint text-red-500 mr-2"></i>Blood Information
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Blood Type:</span>
                    <span class="font-medium">
                        @if($patientRecord->blood_type)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $patientRecord->blood_type }}
                            </span>
                        @else
                            <span class="text-gray-400">Not specified</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Blood Pressure Status:</span>
                    <span class="font-medium">{{ $patientRecord->blood_pressure_status ?? 'Not assessed' }}</span>
                </div>
            </div>
        </div>

        <!-- Physical Measurements -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-ruler text-blue-500 mr-2"></i>Physical Measurements
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Height:</span>
                    <span class="font-medium">{{ $patientRecord->height_cm ? $patientRecord->height_cm . ' cm' : 'Not recorded' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Weight:</span>
                    <span class="font-medium">{{ $patientRecord->weight_kg ? $patientRecord->weight_kg . ' kg' : 'Not recorded' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">BMI:</span>
                    <span class="font-medium">
                        @if($patientRecord->bmi)
                            {{ $patientRecord->bmi }}
                            <span class="text-xs text-gray-500">({{ $patientRecord->getBMICategory() }})</span>
                        @else
                            <span class="text-gray-400">Not calculated</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical Information -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-stethoscope text-green-500 mr-2"></i>Medical Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Allergies</h4>
                <p class="text-gray-700">
                    @if($patientRecord->allergies)
                        <span class="text-red-600">{{ $patientRecord->allergies }}</span>
                    @else
                        <span class="text-green-600">None reported</span>
                    @endif
                </p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Current Medications</h4>
                <p class="text-gray-700">
                    @if($patientRecord->current_medications)
                        {{ $patientRecord->current_medications }}
                    @else
                        <span class="text-gray-400">None reported</span>
                    @endif
                </p>
            </div>
        </div>
        
        <div class="mt-6">
            <h4 class="font-medium text-gray-900 mb-2">Medical History</h4>
            <p class="text-gray-700">
                @if($patientRecord->medical_history)
                    {{ $patientRecord->medical_history }}
                @else
                    <span class="text-gray-400">No medical history recorded</span>
                @endif
            </p>
        </div>

        <div class="mt-6">
            <h4 class="font-medium text-gray-900 mb-2">Family Medical History</h4>
            <p class="text-gray-700">
                @if($patientRecord->family_medical_history)
                    {{ $patientRecord->family_medical_history }}
                @else
                    <span class="text-gray-400">No family medical history recorded</span>
                @endif
            </p>
        </div>
    </div>

    <!-- Risk Assessment -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-shield-alt text-yellow-500 mr-2"></i>Risk Assessment
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Risk Level</h4>
                <p class="text-gray-700">
                    @if($patientRecord->risk_level)
                        @if($patientRecord->risk_level == 'high')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                High Risk
                            </span>
                        @elseif($patientRecord->risk_level == 'medium')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Medium Risk
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Low Risk
                            </span>
                        @endif
                    @else
                        <span class="text-gray-400">Not assessed</span>
                    @endif
                </p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Lifestyle Factors</h4>
                <p class="text-gray-700">
                    @if($patientRecord->lifestyle_factors)
                        {{ $patientRecord->lifestyle_factors }}
                    @else
                        <span class="text-gray-400">No lifestyle factors recorded</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Emergency Contact -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-phone text-red-500 mr-2"></i>Emergency Contact
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Contact Name</h4>
                <p class="text-gray-700">{{ $patientRecord->emergency_contact_name ?? 'Not specified' }}</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Contact Number</h4>
                <p class="text-gray-700">{{ $patientRecord->emergency_contact_number ?? 'Not specified' }}</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Relationship</h4>
                <p class="text-gray-700">{{ $patientRecord->emergency_contact_relationship ?? 'Not specified' }}</p>
            </div>
        </div>
    </div>

    <!-- Related Records -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Medical Consultations -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-stethoscope text-blue-500 mr-2"></i>Medical Consultations
            </h3>
            @if($patientRecord->medicalLogbooks->count() > 0)
                <div class="space-y-3">
                    @foreach($patientRecord->medicalLogbooks->take(5) as $consultation)
                    <div class="border-l-4 border-blue-500 pl-4">
                        <p class="text-sm font-medium text-gray-900">{{ $consultation->consultation_date->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $consultation->chief_complaint }}</p>
                        <p class="text-xs text-gray-400">{{ $consultation->consultation_type }}</p>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.medical-logbooks.index') }}?resident_id={{ $patientRecord->resident_id }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All Consultations →
                    </a>
                </div>
            @else
                <p class="text-gray-400">No medical consultations recorded</p>
            @endif
        </div>

        <!-- Vaccination Records -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-syringe text-green-500 mr-2"></i>Vaccination Records
            </h3>
            @if($patientRecord->vaccinationRecords->count() > 0)
                <div class="space-y-3">
                    @foreach($patientRecord->vaccinationRecords->take(5) as $vaccination)
                    <div class="border-l-4 border-green-500 pl-4">
                        <p class="text-sm font-medium text-gray-900">{{ $vaccination->vaccination_date->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $vaccination->vaccine_name }}</p>
                        <p class="text-xs text-gray-400">{{ $vaccination->vaccine_type }}</p>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.vaccination-records.index') }}?resident_id={{ $patientRecord->resident_id }}" class="text-green-600 hover:text-green-800 text-sm font-medium">
                        View All Vaccinations →
                    </a>
                </div>
            @else
                <p class="text-gray-400">No vaccination records found</p>
            @endif
        </div>
    </div>

    <!-- Additional Notes -->
    @if($patientRecord->notes)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-sticky-note text-yellow-500 mr-2"></i>Additional Notes
        </h3>
        <p class="text-gray-700">{{ $patientRecord->notes }}</p>
    </div>
    @endif

    <!-- Record Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
            <div>
                <span class="font-medium">Created:</span> {{ $patientRecord->created_at->format('M d, Y g:i A') }}
            </div>
            <div>
                <span class="font-medium">Last Updated:</span> {{ $patientRecord->updated_at->format('M d, Y g:i A') }}
            </div>
            <div>
                <span class="font-medium">Record ID:</span> {{ $patientRecord->id }}
            </div>
        </div>
    </div>
</div>
@endsection 