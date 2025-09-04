@extends('admin.main.layout')

@section('title', 'Vaccination Record Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Skeleton -->
    <div id="vaccinationShowHeaderSkeleton" class="animate-pulse mb-6">
        <div class="flex justify-between items-center">
            <div class="h-8 w-80 bg-gray-200 rounded"></div>
            <div class="flex space-x-2">
                <div class="h-10 w-32 bg-gray-200 rounded"></div>
                <div class="h-10 w-32 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Vaccination Information Header Skeleton -->
    <div id="vaccinationShowInfoSkeleton" class="animate-pulse bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-gray-200 rounded-full"></div>
                <div class="ml-4">
                    <div class="h-6 w-48 bg-gray-200 rounded mb-2"></div>
                    <div class="h-4 w-40 bg-gray-100 rounded mb-1"></div>
                    <div class="h-4 w-56 bg-gray-100 rounded"></div>
                </div>
            </div>
            <div class="h-8 w-24 bg-gray-200 rounded"></div>
        </div>
    </div>

    <!-- Detailed Information Grid Skeleton -->
    <div id="vaccinationShowGridSkeleton" class="animate-pulse grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Vaccine Details Skeleton -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <div class="w-6 h-6 bg-gray-200 rounded mr-2"></div>
                <div class="h-6 w-32 bg-gray-200 rounded"></div>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <div class="h-4 w-24 bg-gray-200 rounded"></div>
                    <div class="h-6 w-20 bg-gray-200 rounded"></div>
                </div>
                <div class="flex justify-between">
                    <div class="h-4 w-24 bg-gray-200 rounded"></div>
                    <div class="h-4 w-16 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>

        <!-- Vaccination Details Skeleton -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <div class="w-6 h-6 bg-gray-200 rounded mr-2"></div>
                <div class="h-6 w-40 bg-gray-200 rounded"></div>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <div class="h-4 w-32 bg-gray-200 rounded"></div>
                    <div class="h-4 w-24 bg-gray-200 rounded"></div>
                </div>
                <div class="flex justify-between">
                    <div class="h-4 w-28 bg-gray-200 rounded"></div>
                    <div class="h-4 w-32 bg-gray-200 rounded"></div>
                </div>
                <div class="flex justify-between">
                    <div class="h-4 w-28 bg-gray-200 rounded"></div>
                    <div class="h-6 w-24 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Information Skeleton -->
    <div id="vaccinationShowPatientSkeleton" class="animate-pulse bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center mb-4">
            <div class="w-6 h-6 bg-gray-200 rounded mr-2"></div>
            <div class="h-6 w-40 bg-gray-200 rounded"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <div class="h-4 w-20 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 w-32 bg-gray-200 rounded"></div>
            </div>
            <div>
                <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 w-40 bg-gray-200 rounded"></div>
            </div>
            <div>
                <div class="h-4 w-16 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 w-48 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Record Information Skeleton -->
    <div id="vaccinationShowRecordSkeleton" class="animate-pulse bg-gray-50 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="h-4 w-32 bg-gray-200 rounded"></div>
            <div class="h-4 w-36 bg-gray-200 rounded"></div>
            <div class="h-4 w-20 bg-gray-200 rounded"></div>
        </div>
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="vaccinationShowContent" style="display: none;">
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
                    <p class="text-gray-600">{{ $vaccinationRecord->patient_name }}</p>
                    @if($vaccinationRecord->resident)
                        <p class="text-gray-600">{{ $vaccinationRecord->resident->email }}</p>
                    @elseif($vaccinationRecord->childProfile)
                        <p class="text-gray-600">Child • Mother: {{ $vaccinationRecord->childProfile->mother_name }}</p>
                    @endif
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
                    <span class="font-medium">{{ optional($vaccinationRecord->administeredByProfile)->name ?? 'Not specified' }}</span>
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
                <p class="text-gray-700">{{ $vaccinationRecord->patient_name }}</p>
            </div>
            @if($vaccinationRecord->resident)
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Email Address</h4>
                    <p class="text-gray-700">{{ $vaccinationRecord->resident->email }}</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Address</h4>
                    <p class="text-gray-700">{{ $vaccinationRecord->resident->address }}</p>
                </div>
            @elseif($vaccinationRecord->childProfile)
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Mother</h4>
                    <p class="text-gray-700">{{ $vaccinationRecord->childProfile->mother_name }}</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Purok</h4>
                    <p class="text-gray-700">{{ $vaccinationRecord->childProfile->purok }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Additional Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
    </div>

    

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
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const skeletonElements = [
            'vaccinationShowHeaderSkeleton', 'vaccinationShowInfoSkeleton', 'vaccinationShowGridSkeleton',
            'vaccinationShowPatientSkeleton', 'vaccinationShowRecordSkeleton'
        ];
        skeletonElements.forEach(id => {
            const element = document.getElementById(id);
            if (element) element.style.display = 'none';
        });
        const content = document.getElementById('vaccinationShowContent');
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection
