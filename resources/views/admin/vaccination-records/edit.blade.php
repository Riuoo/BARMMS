@extends('admin.main.layout')

@section('title', 'Edit Vaccination Record')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
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

                        <div>
                            <label for="manufacturer" class="block text-sm font-medium text-gray-700 mb-2">Manufacturer</label>
                            <input type="text" name="manufacturer" id="manufacturer" 
                               value="{{ old('manufacturer', $vaccinationRecord->manufacturer) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="e.g., Pfizer, Moderna, AstraZeneca">
                        </div>

                        <div>
                            <label for="batch_number" class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                            <input type="text" name="batch_number" id="batch_number" 
                               value="{{ old('batch_number', $vaccinationRecord->batch_number) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="Batch number">
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

                <!-- Administered By -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-user-md text-yellow-600 mr-2"></i>Administered By
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="administered_by" class="block text-sm font-medium text-gray-700 mb-2">Administered By</label>
                            <input type="text" name="administered_by" id="administered_by" 
                               value="{{ old('administered_by', $vaccinationRecord->administered_by) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="Name of healthcare provider">
                        </div>
                    </div>
                </div>

                <!-- Side Effects and Notes -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-indigo-600 mr-2"></i>Side Effects and Notes
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="side_effects" class="block text-sm font-medium text-gray-700 mb-2">Side Effects</label>
                            <textarea name="side_effects" id="side_effects" rows="4" 
                                  class="w-full border border-gray-300 rounded px-3 py-2" 
                                  placeholder="Any side effects experienced...">{{ old('side_effects', $vaccinationRecord->side_effects) }}</textarea>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                            <textarea name="notes" id="notes" rows="4" 
                                  class="w-full border border-gray-300 rounded px-3 py-2" 
                                  placeholder="Any additional notes...">{{ old('notes', $vaccinationRecord->notes) }}</textarea>
                        </div>
                    </div>
                </div>

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
@endsection 