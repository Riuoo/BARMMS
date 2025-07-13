@extends('admin.modals.layout')

@section('title', 'Medical Logbook Entry Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Medical Logbook Entry Details</h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.medical-logbooks.edit', $medicalLogbook->id) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    <i class="fas fa-edit mr-2"></i>Edit Entry
                </a>
                <a href="{{ route('admin.medical-logbooks.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
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
                    <h2 class="text-2xl font-bold text-gray-900">{{ $medicalLogbook->resident->name }}</h2>
                    <p class="text-gray-600">Consultation Date: {{ $medicalLogbook->consultation_date->format('M d, Y') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Patient Information</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Name:</span>
                            <span class="font-medium">{{ $medicalLogbook->resident->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-medium">{{ $medicalLogbook->resident->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Address:</span>
                            <span class="font-medium">{{ $medicalLogbook->resident->address }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Consultation Details</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date:</span>
                            <span class="font-medium">{{ $medicalLogbook->consultation_date->format('M d, Y') }}</span>
                        </div>
                        @if($medicalLogbook->healthcare_provider)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Provider:</span>
                            <span class="font-medium">{{ $medicalLogbook->healthcare_provider }}</span>
                        </div>
                        @endif
                        @if($medicalLogbook->follow_up_date)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Follow-up Date:</span>
                            <span class="font-medium">
                                @if($medicalLogbook->follow_up_date->isPast())
                                    <span class="text-red-600 font-bold">OVERDUE - {{ $medicalLogbook->follow_up_date->format('M d, Y') }}</span>
                                @elseif($medicalLogbook->follow_up_date->diffInDays(now()) <= 7)
                                    <span class="text-yellow-600 font-bold">Due Soon - {{ $medicalLogbook->follow_up_date->format('M d, Y') }}</span>
                                @else
                                    {{ $medicalLogbook->follow_up_date->format('M d, Y') }}
                                @endif
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Chief Complaint -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Chief Complaint</h3>
            <p class="text-gray-600">{{ $medicalLogbook->chief_complaint }}</p>
        </div>

        <!-- Vital Signs -->
        @if($medicalLogbook->blood_pressure || $medicalLogbook->temperature || $medicalLogbook->pulse_rate || $medicalLogbook->respiratory_rate)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Vital Signs</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @if($medicalLogbook->blood_pressure)
                <div>
                    <span class="text-sm text-gray-600">Blood Pressure</span>
                    <p class="font-medium">{{ $medicalLogbook->blood_pressure }}</p>
                </div>
                @endif
                @if($medicalLogbook->temperature)
                <div>
                    <span class="text-sm text-gray-600">Temperature</span>
                    <p class="font-medium">{{ $medicalLogbook->temperature }}°C</p>
                </div>
                @endif
                @if($medicalLogbook->pulse_rate)
                <div>
                    <span class="text-sm text-gray-600">Pulse Rate</span>
                    <p class="font-medium">{{ $medicalLogbook->pulse_rate }} bpm</p>
                </div>
                @endif
                @if($medicalLogbook->respiratory_rate)
                <div>
                    <span class="text-sm text-gray-600">Respiratory Rate</span>
                    <p class="font-medium">{{ $medicalLogbook->respiratory_rate }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Medical Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            @if($medicalLogbook->physical_examination)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Physical Examination</h3>
                <p class="text-gray-600">{{ $medicalLogbook->physical_examination }}</p>
            </div>
            @endif

            @if($medicalLogbook->diagnosis)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Diagnosis</h3>
                <p class="text-gray-600">{{ $medicalLogbook->diagnosis }}</p>
            </div>
            @endif
        </div>

        <!-- Treatment Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            @if($medicalLogbook->treatment_plan)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Treatment Plan</h3>
                <p class="text-gray-600">{{ $medicalLogbook->treatment_plan }}</p>
            </div>
            @endif

            @if($medicalLogbook->prescribed_medications)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Prescribed Medications</h3>
                <p class="text-gray-600">{{ $medicalLogbook->prescribed_medications }}</p>
            </div>
            @endif
        </div>

        <!-- Follow-up Information -->
        @if($medicalLogbook->follow_up_notes)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Follow-up Notes</h3>
            <p class="text-gray-600">{{ $medicalLogbook->follow_up_notes }}</p>
        </div>
        @endif

        <!-- Additional Notes -->
        @if($medicalLogbook->notes)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Notes</h3>
            <p class="text-gray-600">{{ $medicalLogbook->notes }}</p>
        </div>
        @endif

        @if(!$medicalLogbook->physical_examination && !$medicalLogbook->diagnosis && !$medicalLogbook->treatment_plan && !$medicalLogbook->prescribed_medications && !$medicalLogbook->follow_up_notes && !$medicalLogbook->notes)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center text-gray-500">
                <i class="fas fa-info-circle text-2xl mb-2"></i>
                <p>No additional medical information recorded for this consultation.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 