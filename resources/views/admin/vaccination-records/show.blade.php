@extends('admin.modals.layout')

@section('title', 'Vaccination Record Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Vaccination Record Details</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.vaccination-records.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
            <a href="{{ route('admin.vaccination-records.edit', $vaccinationRecord->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i>Edit Record
            </a>
        </div>
    </div>

    <!-- Vaccination Information Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-16 w-16">
                    <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-syringe text-blue-600 text-2xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $vaccinationRecord->vaccine_name }}</h2>
                    <p class="text-gray-600">{{ $vaccinationRecord->resident->name }}</p>
                    <p class="text-gray-600">{{ $vaccinationRecord->resident->email }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-syringe mr-2"></i>{{ $vaccinationRecord->vaccine_type }}
                </span>
            </div>
        </div>
    </div>

    <!-- Detailed Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Vaccine Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-vial text-blue-500 mr-2"></i>Vaccine Details
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Vaccine Type:</span>
                    <span class="font-medium">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $vaccinationRecord->vaccine_type }}
                        </span>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Dose Number:</span>
                    <span class="font-medium">{{ $vaccinationRecord->dose_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Manufacturer:</span>
                    <span class="font-medium">{{ $vaccinationRecord->manufacturer ?? 'Not specified' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Batch Number:</span>
                    <span class="font-medium">{{ $vaccinationRecord->batch_number ?? 'Not specified' }}</span>
                </div>
            </div>
        </div>

        <!-- Vaccination Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-calendar-alt text-green-500 mr-2"></i>Vaccination Details
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Vaccination Date:</span>
                    <span class="font-medium">{{ $vaccinationRecord->vaccination_date->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Administered By:</span>
                    <span class="font-medium">{{ $vaccinationRecord->administered_by ?? 'Not specified' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Next Dose Date:</span>
                    <span class="font-medium">
                        @if($vaccinationRecord->next_dose_date)
                            @if($vaccinationRecord->next_dose_date->isPast())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    OVERDUE - {{ $vaccinationRecord->next_dose_date->format('M d, Y') }}
                                </span>
                            @elseif($vaccinationRecord->next_dose_date->diffInDays(now()) <= 30)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Due Soon - {{ $vaccinationRecord->next_dose_date->format('M d, Y') }}
                                </span>
                            @else
                                {{ $vaccinationRecord->next_dose_date->format('M d, Y') }}
                            @endif
                        @else
                            <span class="text-gray-400">Not scheduled</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Information -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-user text-purple-500 mr-2"></i>Patient Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Full Name</h4>
                <p class="text-gray-700">{{ $vaccinationRecord->resident->name }}</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Email Address</h4>
                <p class="text-gray-700">{{ $vaccinationRecord->resident->email }}</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Address</h4>
                <p class="text-gray-700">{{ $vaccinationRecord->resident->address }}</p>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        @if($vaccinationRecord->side_effects)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Side Effects
            </h3>
            <p class="text-gray-700">{{ $vaccinationRecord->side_effects }}</p>
        </div>
        @endif

        @if($vaccinationRecord->notes)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-sticky-note text-yellow-500 mr-2"></i>Additional Notes
            </h3>
            <p class="text-gray-700">{{ $vaccinationRecord->notes }}</p>
        </div>
        @endif
    </div>

    @if(!$vaccinationRecord->side_effects && !$vaccinationRecord->notes)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="text-center text-gray-500">
            <i class="fas fa-info-circle text-2xl mb-2"></i>
            <p>No additional information recorded for this vaccination.</p>
        </div>
    </div>
    @endif

    <!-- Record Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
            <div>
                <span class="font-medium">Created:</span> {{ $vaccinationRecord->created_at->format('M d, Y g:i A') }}
            </div>
            <div>
                <span class="font-medium">Last Updated:</span> {{ $vaccinationRecord->updated_at->format('M d, Y g:i A') }}
            </div>
            <div>
                <span class="font-medium">Record ID:</span> {{ $vaccinationRecord->id }}
            </div>
        </div>
    </div>
</div>
@endsection
