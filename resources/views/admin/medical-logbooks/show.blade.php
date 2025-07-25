@extends('admin.modals.layout')

@section('title', 'Medical Logbook Entry Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Medical Logbook Entry Details</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.medical-logbooks.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
            <a href="{{ route('admin.medical-logbooks.edit', $medicalLogbook->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i>Edit Entry
            </a>
        </div>
    </div>

    <!-- Patient Information Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-16 w-16">
                    <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-user-md text-blue-600 text-2xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $medicalLogbook->resident->name }}</h2>
                    <p class="text-gray-600">Consultation Date: {{ $medicalLogbook->consultation_date->format('M d, Y') }}</p>
                    <p class="text-gray-600">{{ $medicalLogbook->resident->email }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-stethoscope mr-2"></i>{{ $medicalLogbook->consultation_type }}
                </span>
            </div>
        </div>
    </div>

    <!-- Detailed Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Consultation Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>Consultation Details
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Consultation Date:</span>
                    <span class="font-medium">{{ $medicalLogbook->consultation_date->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Consultation Type:</span>
                    <span class="font-medium">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $medicalLogbook->consultation_type }}
                        </span>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Healthcare Provider:</span>
                    <span class="font-medium">{{ $medicalLogbook->healthcare_provider ?? 'Not specified' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Follow-up Date:</span>
                    <span class="font-medium">
                        @if($medicalLogbook->follow_up_date)
                            @if($medicalLogbook->follow_up_date->isPast())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    OVERDUE - {{ $medicalLogbook->follow_up_date->format('M d, Y') }}
                                </span>
                            @elseif($medicalLogbook->follow_up_date->diffInDays(now()) <= 7)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Due Soon - {{ $medicalLogbook->follow_up_date->format('M d, Y') }}
                                </span>
                            @else
                                {{ $medicalLogbook->follow_up_date->format('M d, Y') }}
                            @endif
                        @else
                            <span class="text-gray-400">Not scheduled</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Patient Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-user text-purple-500 mr-2"></i>Patient Information
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Full Name:</span>
                    <span class="font-medium">{{ $medicalLogbook->resident->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Email Address:</span>
                    <span class="font-medium">{{ $medicalLogbook->resident->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Address:</span>
                    <span class="font-medium">{{ $medicalLogbook->resident->address }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chief Complaint -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>Chief Complaint
        </h3>
        <p class="text-gray-700">{{ $medicalLogbook->chief_complaint }}</p>
    </div>

    <!-- Vital Signs -->
    @if($medicalLogbook->blood_pressure || $medicalLogbook->temperature || $medicalLogbook->pulse_rate || $medicalLogbook->respiratory_rate)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-heartbeat text-red-500 mr-2"></i>Vital Signs
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @if($medicalLogbook->blood_pressure)
            <div>
                <h4 class="text-sm font-medium text-gray-600 mb-1">Blood Pressure</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $medicalLogbook->blood_pressure }}</p>
            </div>
            @endif
            @if($medicalLogbook->temperature)
            <div>
                <h4 class="text-sm font-medium text-gray-600 mb-1">Temperature</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $medicalLogbook->temperature }}°C</p>
            </div>
            @endif
            @if($medicalLogbook->pulse_rate)
            <div>
                <h4 class="text-sm font-medium text-gray-600 mb-1">Pulse Rate</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $medicalLogbook->pulse_rate }} bpm</p>
            </div>
            @endif
            @if($medicalLogbook->respiratory_rate)
            <div>
                <h4 class="text-sm font-medium text-gray-600 mb-1">Respiratory Rate</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $medicalLogbook->respiratory_rate }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Medical Information -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-stethoscope text-green-500 mr-2"></i>Medical Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Physical Examination</h4>
                <p class="text-gray-700">
                    @if($medicalLogbook->physical_examination)
                        {{ $medicalLogbook->physical_examination }}
                    @else
                        <span class="text-gray-400">No physical examination recorded</span>
                    @endif
                </p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Diagnosis</h4>
                <p class="text-gray-700">
                    @if($medicalLogbook->diagnosis)
                        {{ $medicalLogbook->diagnosis }}
                    @else
                        <span class="text-gray-400">No diagnosis recorded</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Treatment Information -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-pills text-yellow-500 mr-2"></i>Treatment Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Treatment Plan</h4>
                <p class="text-gray-700">
                    @if($medicalLogbook->treatment_plan)
                        {{ $medicalLogbook->treatment_plan }}
                    @else
                        <span class="text-gray-400">No treatment plan recorded</span>
                    @endif
                </p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Prescribed Medications</h4>
                <p class="text-gray-700">
                    @if($medicalLogbook->prescribed_medications)
                        {{ $medicalLogbook->prescribed_medications }}
                    @else
                        <span class="text-gray-400">No medications prescribed</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Follow-up Information -->
    @if($medicalLogbook->follow_up_notes)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-calendar-check text-blue-500 mr-2"></i>Follow-up Information
        </h3>
        <p class="text-gray-700">{{ $medicalLogbook->follow_up_notes }}</p>
    </div>
    @endif

    <!-- Additional Notes -->
    @if($medicalLogbook->notes)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-sticky-note text-purple-500 mr-2"></i>Additional Notes
        </h3>
        <p class="text-gray-700">{{ $medicalLogbook->notes }}</p>
    </div>
    @endif

    @if(!$medicalLogbook->physical_examination && !$medicalLogbook->diagnosis && !$medicalLogbook->treatment_plan && !$medicalLogbook->prescribed_medications && !$medicalLogbook->follow_up_notes && !$medicalLogbook->notes)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="text-center text-gray-500">
            <i class="fas fa-info-circle text-2xl mb-2"></i>
            <p>No additional medical information recorded for this consultation.</p>
        </div>
    </div>
    @endif

    <!-- Record Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
            <div>
                <span class="font-medium">Created:</span> {{ $medicalLogbook->created_at->format('M d, Y g:i A') }}
            </div>
            <div>
                <span class="font-medium">Last Updated:</span> {{ $medicalLogbook->updated_at->format('M d, Y g:i A') }}
            </div>
            <div>
                <span class="font-medium">Record ID:</span> {{ $medicalLogbook->id }}
            </div>
        </div>
    </div>
</div>
@endsection
