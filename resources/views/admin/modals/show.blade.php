@extends('admin.modals.layout')

@section('title', 'Patient Record Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Patient Record Details</h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.patient-records.edit', $patientRecord->id) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    <i class="fas fa-edit mr-2"></i>Edit Record
                </a>
                <a href="{{ route('admin.patient-records.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>

        <!-- Patient Information Card -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center mb-4">
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                    <i class="fas fa-user-md text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $patientRecord->resident->name }}</h2>
                    <p class="text-gray-600">Patient Number: {{ $patientRecord->patient_number }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Basic Information</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-medium">{{ $patientRecord->resident->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Address:</span>
                            <span class="font-medium">{{ $patientRecord->resident->address }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Blood Type:</span>
                            <span class="font-medium">{{ $patientRecord->blood_type ?? 'Not specified' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Risk Level:</span>
                            <span class="font-medium px-2 py-1 rounded text-sm 
                                @if($patientRecord->risk_level == 'Low') bg-green-100 text-green-800
                                @elseif($patientRecord->risk_level == 'Medium') bg-yellow-100 text-yellow-800
                                @elseif($patientRecord->risk_level == 'High') bg-orange-100 text-orange-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $patientRecord->risk_level }}
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Physical Measurements</h3>
                    <div class="space-y-2">
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
                                    {{ $patientRecord->bmi }} ({{ $patientRecord->getBMICategory() }})
                                @else
                                    Not calculated
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Blood Pressure:</span>
                            <span class="font-medium">{{ $patientRecord->blood_pressure_status ?? 'Not recorded' }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Emergency Contact</h3>
                    <div class="space-y-2">
                        @if($patientRecord->emergency_contact_name)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Name:</span>
                            <span class="font-medium">{{ $patientRecord->emergency_contact_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phone:</span>
                            <span class="font-medium">{{ $patientRecord->emergency_contact_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Relationship:</span>
                            <span class="font-medium">{{ $patientRecord->emergency_contact_relationship }}</span>
                        </div>
                        @else
                        <div class="text-gray-500 italic">No emergency contact information provided</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Medical History -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Medical History</h3>
                <div class="space-y-4">
                    @if($patientRecord->medical_history)
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Past Medical Conditions</h4>
                        <p class="text-gray-600">{{ $patientRecord->medical_history }}</p>
                    </div>
                    @endif

                    @if($patientRecord->family_medical_history)
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Family Medical History</h4>
                        <p class="text-gray-600">{{ $patientRecord->family_medical_history }}</p>
                    </div>
                    @endif

                    @if($patientRecord->allergies)
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Allergies</h4>
                        <p class="text-gray-600">{{ $patientRecord->allergies }}</p>
                    </div>
                    @endif

                    @if($patientRecord->current_medications)
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Current Medications</h4>
                        <p class="text-gray-600">{{ $patientRecord->current_medications }}</p>
                    </div>
                    @endif

                    @if($patientRecord->lifestyle_factors)
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Lifestyle Factors</h4>
                        <p class="text-gray-600">{{ $patientRecord->lifestyle_factors }}</p>
                    </div>
                    @endif

                    @if(!$patientRecord->medical_history && !$patientRecord->family_medical_history && !$patientRecord->allergies && !$patientRecord->current_medications && !$patientRecord->lifestyle_factors)
                    <div class="text-gray-500 italic">No medical information recorded</div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                <div class="space-y-4">
                    @if($patientRecord->medicalLogbooks->count() > 0)
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Recent Consultations</h4>
                        <div class="space-y-2">
                            @foreach($patientRecord->medicalLogbooks->take(3) as $logbook)
                            <div class="border-l-4 border-blue-500 pl-3">
                                <div class="text-sm font-medium">{{ $logbook->consultation_date->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $logbook->chief_complaint }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($patientRecord->vaccinationRecords->count() > 0)
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Recent Vaccinations</h4>
                        <div class="space-y-2">
                            @foreach($patientRecord->vaccinationRecords->take(3) as $vaccination)
                            <div class="border-l-4 border-green-500 pl-3">
                                <div class="text-sm font-medium">{{ $vaccination->vaccine_name }}</div>
                                <div class="text-xs text-gray-500">{{ $vaccination->vaccination_date->format('M d, Y') }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($patientRecord->medicalLogbooks->count() == 0 && $patientRecord->vaccinationRecords->count() == 0)
                    <div class="text-gray-500 italic">No recent activity recorded</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($patientRecord->notes)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Notes</h3>
            <p class="text-gray-600">{{ $patientRecord->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection 