@extends('admin.main.layout')

@section('title', 'Edit Vaccination Record')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
        <!-- Header Skeleton (no buttons) -->
        <div id="editVaccinationHeaderSkeleton">
            @include('components.loading.edit-form-skeleton', ['type' => 'header', 'showButton' => false])
        </div>

        <!-- Form Skeleton -->
        <div id="editVaccinationFormSkeleton">
            @include('components.loading.edit-form-skeleton', ['type' => 'vaccination-edit'])
        </div>

        <!-- Real Content (hidden initially) -->
        <div id="editVaccinationContent" style="display: none;">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Edit Vaccination Record</h1>
        </div>

        <form action="{{ route('admin.vaccination-records.update', $vaccinationRecord->id) }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="mb-4 p-3 rounded border border-red-300 bg-red-50 text-red-700">
                    <div class="font-semibold mb-1">Please fix the following errors:</div>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-6">
                <!-- Patient Information Section -->
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-user text-blue-600 mr-2"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Patient Information</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="patient_name" class="block text-sm font-medium text-gray-700 mb-2">Patient Name</label>
                            <input type="text" id="patient_name" name="patient_name" value="{{ old('patient_name', $vaccinationRecord->patient_name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label for="patient_type" class="block text-sm font-medium text-gray-700 mb-2">Patient Type</label>
                            <select id="patient_type" name="patient_type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Select Patient Type</option>
                                <option value="child" {{ old('patient_type', $vaccinationRecord->patient_type) == 'child' ? 'selected' : '' }}>Child</option>
                                <option value="adult" {{ old('patient_type', $vaccinationRecord->patient_type) == 'adult' ? 'selected' : '' }}>Adult</option>
                                <option value="elderly" {{ old('patient_type', $vaccinationRecord->patient_type) == 'elderly' ? 'selected' : '' }}>Elderly</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Vaccine Information Section -->
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-vial text-green-600 mr-2"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Vaccine Information</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="vaccine_name" class="block text-sm font-medium text-gray-700 mb-2">Vaccine Name</label>
                            <input type="text" id="vaccine_name" name="vaccine_name" value="{{ old('vaccine_name', $vaccinationRecord->vaccine_name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                        </div>
                        <div>
                            <label for="manufacturer" class="block text-sm font-medium text-gray-700 mb-2">Manufacturer</label>
                            <input type="text" id="manufacturer" name="manufacturer" value="{{ old('manufacturer', $vaccinationRecord->manufacturer) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                </div>

                <!-- Dose Information Section -->
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-syringe text-purple-600 mr-2"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Dose Information</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="dose_number" class="block text-sm font-medium text-gray-700 mb-2">Dose Number</label>
                            <input type="number" id="dose_number" name="dose_number" value="{{ old('dose_number', $vaccinationRecord->dose_number) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" min="1">
                        </div>
                    </div>
                </div>

                <!-- Vaccination Details Section -->
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-calendar-alt text-orange-600 mr-2"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Vaccination Details</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="vaccination_date" class="block text-sm font-medium text-gray-700 mb-2">Vaccination Date</label>
                            <input type="date" id="vaccination_date" name="vaccination_date" value="{{ old('vaccination_date', $vaccinationRecord->vaccination_date ? $vaccinationRecord->vaccination_date->format('Y-m-d') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500" required>
                        </div>
                        <div>
                            <label for="next_due_date" class="block text-sm font-medium text-gray-700 mb-2">Next Due Date</label>
                            <input type="date" id="next_due_date" name="next_due_date" value="{{ old('next_due_date', $vaccinationRecord->next_due_date ? $vaccinationRecord->next_due_date->format('Y-m-d') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between items-center mt-6">
                    <p class="text-sm text-gray-600">Make sure all information is accurate before saving.</p>
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.vaccination-records.show', $vaccinationRecord->id) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                           <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Update Record
                        </button>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const headerSkeleton = document.getElementById('editVaccinationHeaderSkeleton');
        const formSkeleton = document.getElementById('editVaccinationFormSkeleton');
        const content = document.getElementById('editVaccinationContent');
        if (headerSkeleton) headerSkeleton.style.display = 'none';
        if (formSkeleton) formSkeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection