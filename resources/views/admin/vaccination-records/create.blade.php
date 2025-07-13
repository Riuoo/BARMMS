@extends('admin.modals.layout')

@section('title', 'Add Vaccination Record')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Add Vaccination Record</h1>
            <a href="{{ route('admin.vaccination-records.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.vaccination-records.store') }}" method="POST">
                @csrf
                
                <!-- Patient Selection -->
                <div class="mb-6">
                    <label for="resident_id" class="block text-sm font-medium text-gray-700 mb-2">Select Patient *</label>
                    <select name="resident_id" id="resident_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                        <option value="">Choose a patient...</option>
                        @foreach($residents as $resident)
                            <option value="{{ $resident->id }}" {{ old('resident_id') == $resident->id ? 'selected' : '' }}>
                                {{ $resident->name }} ({{ $resident->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Vaccine Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="vaccine_name" class="block text-sm font-medium text-gray-700 mb-2">Vaccine Name *</label>
                        <input type="text" name="vaccine_name" id="vaccine_name" value="{{ old('vaccine_name') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="e.g., Pfizer-BioNTech COVID-19 Vaccine" required>
                    </div>

                    <div>
                        <label for="vaccine_type" class="block text-sm font-medium text-gray-700 mb-2">Vaccine Type *</label>
                        <select name="vaccine_type" id="vaccine_type" class="w-full border border-gray-300 rounded px-3 py-2" required>
                            <option value="">Select vaccine type...</option>
                            <option value="COVID-19" {{ old('vaccine_type') == 'COVID-19' ? 'selected' : '' }}>COVID-19</option>
                            <option value="Influenza" {{ old('vaccine_type') == 'Influenza' ? 'selected' : '' }}>Influenza</option>
                            <option value="Pneumonia" {{ old('vaccine_type') == 'Pneumonia' ? 'selected' : '' }}>Pneumonia</option>
                            <option value="Tetanus" {{ old('vaccine_type') == 'Tetanus' ? 'selected' : '' }}>Tetanus</option>
                            <option value="Hepatitis B" {{ old('vaccine_type') == 'Hepatitis B' ? 'selected' : '' }}>Hepatitis B</option>
                            <option value="MMR" {{ old('vaccine_type') == 'MMR' ? 'selected' : '' }}>MMR</option>
                            <option value="Varicella" {{ old('vaccine_type') == 'Varicella' ? 'selected' : '' }}>Varicella</option>
                            <option value="HPV" {{ old('vaccine_type') == 'HPV' ? 'selected' : '' }}>HPV</option>
                            <option value="Other" {{ old('vaccine_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <!-- Vaccination Details -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="vaccination_date" class="block text-sm font-medium text-gray-700 mb-2">Vaccination Date *</label>
                        <input type="date" name="vaccination_date" id="vaccination_date" value="{{ old('vaccination_date') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" required>
                    </div>

                    <div>
                        <label for="dose_number" class="block text-sm font-medium text-gray-700 mb-2">Dose Number *</label>
                        <input type="number" name="dose_number" id="dose_number" value="{{ old('dose_number', 1) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               min="1" max="10" required>
                    </div>

                    <div>
                        <label for="next_dose_date" class="block text-sm font-medium text-gray-700 mb-2">Next Dose Date</label>
                        <input type="date" name="next_dose_date" id="next_dose_date" value="{{ old('next_dose_date') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                </div>

                <!-- Vaccine Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="batch_number" class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                        <input type="text" name="batch_number" id="batch_number" value="{{ old('batch_number') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="e.g., BNT162b2-001">
                    </div>

                    <div>
                        <label for="manufacturer" class="block text-sm font-medium text-gray-700 mb-2">Manufacturer</label>
                        <input type="text" name="manufacturer" id="manufacturer" value="{{ old('manufacturer') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="e.g., Pfizer-BioNTech">
                    </div>
                </div>

                <!-- Health Worker -->
                <div class="mb-6">
                    <label for="administered_by" class="block text-sm font-medium text-gray-700 mb-2">Administered By</label>
                    <input type="text" name="administered_by" id="administered_by" value="{{ old('administered_by') }}" 
                           class="w-full border border-gray-300 rounded px-3 py-2" 
                           placeholder="Name of health worker who administered the vaccine">
                </div>

                <!-- Side Effects -->
                <div class="mb-6">
                    <label for="side_effects" class="block text-sm font-medium text-gray-700 mb-2">Side Effects</label>
                    <textarea name="side_effects" id="side_effects" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="Any side effects observed...">{{ old('side_effects') }}</textarea>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                    <textarea name="notes" id="notes" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="Any additional notes or observations...">{{ old('notes') }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.vaccination-records.index') }}" 
                       class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Add Vaccination Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 