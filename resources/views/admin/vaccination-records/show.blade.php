@extends('admin.modals.layout')

@section('title', 'Vaccination Record Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Vaccination Record Details</h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.vaccination-records.edit', $vaccinationRecord->id) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    <i class="fas fa-edit mr-2"></i>Edit Record
                </a>
                <a href="{{ route('admin.vaccination-records.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>

        <!-- Vaccination Information Card -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center mb-4">
                <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center mr-4">
                    <i class="fas fa-syringe text-green-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $vaccinationRecord->vaccine_name }}</h2>
                    <p class="text-gray-600">{{ $vaccinationRecord->resident->name }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Vaccine Details</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Vaccine Type:</span>
                            <span class="font-medium">{{ $vaccinationRecord->vaccine_type }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Dose Number:</span>
                            <span class="font-medium">{{ $vaccinationRecord->dose_number }}</span>
                        </div>
                        @if($vaccinationRecord->manufacturer)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Manufacturer:</span>
                            <span class="font-medium">{{ $vaccinationRecord->manufacturer }}</span>
                        </div>
                        @endif
                        @if($vaccinationRecord->batch_number)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Batch Number:</span>
                            <span class="font-medium">{{ $vaccinationRecord->batch_number }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Vaccination Details</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Vaccination Date:</span>
                            <span class="font-medium">{{ $vaccinationRecord->vaccination_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Administered By:</span>
                            <span class="font-medium">{{ $vaccinationRecord->administered_by ?? 'Not specified' }}</span>
                        </div>
                        @if($vaccinationRecord->next_dose_date)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Next Dose Date:</span>
                            <span class="font-medium">
                                @if($vaccinationRecord->next_dose_date->isPast())
                                    <span class="text-red-600 font-bold">OVERDUE - {{ $vaccinationRecord->next_dose_date->format('M d, Y') }}</span>
                                @elseif($vaccinationRecord->next_dose_date->diffInDays(now()) <= 30)
                                    <span class="text-yellow-600 font-bold">Due Soon - {{ $vaccinationRecord->next_dose_date->format('M d, Y') }}</span>
                                @else
                                    {{ $vaccinationRecord->next_dose_date->format('M d, Y') }}
                                @endif
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Patient Information</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Name:</span>
                            <span class="font-medium">{{ $vaccinationRecord->resident->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-medium">{{ $vaccinationRecord->resident->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Address:</span>
                            <span class="font-medium">{{ $vaccinationRecord->resident->address }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @if($vaccinationRecord->side_effects)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Side Effects</h3>
                <p class="text-gray-600">{{ $vaccinationRecord->side_effects }}</p>
            </div>
            @endif

            @if($vaccinationRecord->notes)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Notes</h3>
                <p class="text-gray-600">{{ $vaccinationRecord->notes }}</p>
            </div>
            @endif
        </div>

        @if(!$vaccinationRecord->side_effects && !$vaccinationRecord->notes)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center text-gray-500">
                <i class="fas fa-info-circle text-2xl mb-2"></i>
                <p>No additional information recorded for this vaccination.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 