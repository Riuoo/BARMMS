@extends('admin.main.layout')

@section('title', 'Edit Vaccination Record')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
        <!-- Header Skeleton -->
        <div id="editVaccinationHeaderSkeleton" class="animate-pulse mb-6">
            <div class="flex justify-between items-center">
                <div class="h-8 w-80 bg-gray-200 rounded"></div>
                <div class="h-10 w-36 bg-gray-200 rounded"></div>
            </div>
        </div>

        <!-- Form Skeleton -->
        <div id="editVaccinationFormSkeleton" class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <!-- Error Messages Skeleton -->
            <div class="mb-4 p-3 rounded border border-gray-200 bg-gray-50">
                <div class="h-4 w-48 bg-gray-200 rounded mb-2"></div>
                <div class="space-y-1">
                    <div class="h-3 w-64 bg-gray-100 rounded"></div>
                    <div class="h-3 w-56 bg-gray-100 rounded"></div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Patient Information Section -->
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex items-center mb-4">
                        <div class="w-6 h-6 bg-gray-200 rounded mr-2"></div>
                        <div class="h-6 w-40 bg-gray-200 rounded"></div>
                    </div>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                            <div class="h-10 w-full bg-gray-200 rounded"></div>
                        </div>
                        <div>
                            <div class="h-4 w-16 bg-gray-200 rounded mb-2"></div>
                            <div class="h-10 w-full bg-gray-200 rounded"></div>
                        </div>
                    </div>
                </div>

                <!-- Vaccine Information Section -->
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex items-center mb-4">
                        <div class="w-6 h-6 bg-gray-200 rounded mr-2"></div>
                        <div class="h-6 w-40 bg-gray-200 rounded"></div>
                    </div>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                            <div class="h-10 w-full bg-gray-200 rounded"></div>
                        </div>
                        <div>
                            <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                            <div class="h-10 w-full bg-gray-200 rounded"></div>
                        </div>
                    </div>
                </div>

                <!-- Dose Information Section -->
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex items-center mb-4">
                        <div class="w-6 h-6 bg-gray-200 rounded mr-2"></div>
                        <div class="h-6 w-32 bg-gray-200 rounded"></div>
                    </div>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                            <div class="h-10 w-full bg-gray-200 rounded"></div>
                        </div>
                    </div>
                </div>

                <!-- Vaccination Details Section -->
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex items-center mb-4">
                        <div class="w-6 h-6 bg-gray-200 rounded mr-2"></div>
                        <div class="h-6 w-40 bg-gray-200 rounded"></div>
                    </div>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                            <div class="h-10 w-full bg-gray-200 rounded"></div>
                        </div>
                        <div>
                            <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                            <div class="h-10 w-full bg-gray-200 rounded"></div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between items-center mt-6">
                    <div class="h-4 w-80 bg-gray-200 rounded"></div>
                    <div class="flex space-x-4">
                        <div class="h-10 w-24 bg-gray-200 rounded"></div>
                        <div class="h-10 w-48 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real Content (hidden initially) -->
        <div id="editVaccinationContent" style="display: none;">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Edit Vaccination Record</h1>
            <a href="{{ route('admin.vaccination-records.show', $vaccinationRecord->id) }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
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

            <form action="{{ route('admin.vaccination-records.update', $vaccinationRecord->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Patient Information -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-user-circle mr-2 text-blue-600"></i>Patient Information
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Patient Name</label>
                            <input type="text" value="{{ $vaccinationRecord->resident->name }}" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="text" value="{{ $vaccinationRecord->resident->email }}" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" disabled>
                        </div>
                    </div>
                </div>

                <!-- Vaccine Information -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-syringe text-red-600 mr-2"></i>Vaccine Information
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="vaccine_name" class="block text-sm font-medium text-gray-700 mb-2">Vaccine Name *</label>
                            <input type="text" name="vaccine_name" id="vaccine_name" 
                               value="{{ old('vaccine_name', $vaccinationRecord->vaccine_name) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="e.g., COVID-19 Pfizer" required>
                        </div>

                        <div>
                            <label for="vaccine_type" class="block text-sm font-medium text-gray-700 mb-2">Vaccine Type *</label>
                            <select name="vaccine_type" id="vaccine_type" class="w-full border border-gray-300 rounded px-3 py-2" required>
                                <option value="">Select vaccine type...</option>
                                <option value="COVID-19" {{ old('vaccine_type', $vaccinationRecord->vaccine_type) == 'COVID-19' ? 'selected' : '' }}>COVID-19</option>
                                <option value="Influenza" {{ old('vaccine_type', $vaccinationRecord->vaccine_type) == 'Influenza' ? 'selected' : '' }}>Influenza</option>
                                <option value="Hepatitis B" {{ old('vaccine_type', $vaccinationRecord->vaccine_type) == 'Hepatitis B' ? 'selected' : '' }}>Hepatitis B</option>
                                <option value="MMR" {{ old('vaccine_type', $vaccinationRecord->vaccine_type) == 'MMR' ? 'selected' : '' }}>MMR (Measles, Mumps, Rubella)</option>
                                <option value="DTaP" {{ old('vaccine_type', $vaccinationRecord->vaccine_type) == 'DTaP' ? 'selected' : '' }}>DTaP (Diphtheria, Tetanus, Pertussis)</option>
                                <option value="Varicella" {{ old('vaccine_type', $vaccinationRecord->vaccine_type) == 'Varicella' ? 'selected' : '' }}>Varicella (Chickenpox)</option>
                                <option value="Pneumococcal" {{ old('vaccine_type', $vaccinationRecord->vaccine_type) == 'Pneumococcal' ? 'selected' : '' }}>Pneumococcal</option>
                                <option value="HPV" {{ old('vaccine_type', $vaccinationRecord->vaccine_type) == 'HPV' ? 'selected' : '' }}>HPV (Human Papillomavirus)</option>
                                <option value="Other" {{ old('vaccine_type', $vaccinationRecord->vaccine_type) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Dose Information -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-flask text-green-600 mr-2"></i>Dose Information
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="dose_number" class="block text-sm font-medium text-gray-700 mb-2">Dose Number *</label>
                            <select name="dose_number" id="dose_number" class="w-full border border-gray-300 rounded px-3 py-2" required>
                                <option value="">Select dose...</option>
                                <option value="1st Dose" {{ old('dose_number', $vaccinationRecord->dose_number) == '1st Dose' ? 'selected' : '' }}>1st Dose</option>
                                <option value="2nd Dose" {{ old('dose_number', $vaccinationRecord->dose_number) == '2nd Dose' ? 'selected' : '' }}>2nd Dose</option>
                                <option value="3rd Dose" {{ old('dose_number', $vaccinationRecord->dose_number) == '3rd Dose' ? 'selected' : '' }}>3rd Dose</option>
                                <option value="Booster" {{ old('dose_number', $vaccinationRecord->dose_number) == 'Booster' ? 'selected' : '' }}>Booster</option>
                                <option value="Annual" {{ old('dose_number', $vaccinationRecord->dose_number) == 'Annual' ? 'selected' : '' }}>Annual</option>
                            </select>
                        </div>

                        
                    </div>
                </div>

                <!-- Vaccination Details -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>Vaccination Details
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="vaccination_date" class="block text-sm font-medium text-gray-700 mb-2">Vaccination Date *</label>
                            <input type="date" name="vaccination_date" id="vaccination_date" 
                               value="{{ old('vaccination_date', $vaccinationRecord->vaccination_date->format('Y-m-d')) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" required>
                        </div>

                        <div>
                            <label for="next_dose_date" class="block text-sm font-medium text-gray-700 mb-2">Next Dose Date</label>
                            <input type="date" name="next_dose_date" id="next_dose_date" 
                               value="{{ old('next_dose_date', $vaccinationRecord->next_dose_date ? $vaccinationRecord->next_dose_date->format('Y-m-d') : '') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2">
                        </div>
                    </div>
                </div>

                <!-- Administered By is bound to session account; not editable here -->

                

                <!-- Submit Button -->
                <div class="flex justify-between items-center mt-6">
                    <p class="text-sm text-gray-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        Click "Update Vaccination Record" to save your changes.
                    </p>
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.vaccination-records.show', $vaccinationRecord->id) }}" 
                           class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i>Update Vaccination Record
                        </button>
                    </div>
                </div>
            </form>
        </div>
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