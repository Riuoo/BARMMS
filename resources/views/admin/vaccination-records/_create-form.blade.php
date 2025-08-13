    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.vaccination-records.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Patient Information -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-user mr-3 text-blue-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Patient Information</h2>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="residentSearch" class="block text-sm font-medium text-gray-700 mb-2">Select Resident <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            id="residentSearch"
                            placeholder="Type to search for a resident..."
                            autocomplete="off"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            aria-label="Search for a resident"
                        />
                        <input type="hidden" id="resident_id" name="resident_id" required>
                        <div id="searchResults" class="absolute z-10 bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                        <p class="mt-1 text-sm text-gray-500">Search and select the resident for this patient record</p>
                        @error('resident_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Vaccine Information -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-capsules mr-3 text-red-500 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Vaccine Information</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="vaccine_name" class="block text-sm font-medium text-gray-700 mb-2">Vaccine Name <span class="text-red-500">*</span></label>
                        <input type="text" name="vaccine_name" id="vaccine_name" value="{{ old('vaccine_name') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="e.g., Pfizer-BioNTech COVID-19 Vaccine" required>
                    </div>
                    <div>
                        <label for="vaccine_type" class="block text-sm font-medium text-gray-700 mb-2">Vaccine Type <span class="text-red-500">*</span></label>
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
            </div>

            <!-- Vaccination Details -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-calendar-alt mr-3 text-green-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Vaccination Details</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="vaccination_date" class="block text-sm font-medium text-gray-700 mb-2">Vaccination Date <span class="text-red-500">*</span></label>
                        <input type="date" name="vaccination_date" id="vaccination_date" value="{{ old('vaccination_date') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label for="dose_number" class="block text-sm font-medium text-gray-700 mb-2">Dose Number <span class="text-red-500">*</span></label>
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
            </div>

            <!-- Vaccine Details -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-info-circle mr-3 text-purple-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Vaccine Details</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
            </div>

            <!-- Health Worker -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-user-md mr-3 text-yellow-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Health Worker</h2>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="administered_by" class="block text-sm font-medium text-gray-700 mb-2">Administered By</label>
                        <input type="text" name="administered_by" id="administered_by" value="{{ old('administered_by') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="Name of health worker who administered the vaccine">
                    </div>
                </div>
            </div>

            <!-- Side Effects -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-exclamation-triangle mr-3 text-indigo-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Side Effects</h2>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="side_effects" class="block text-sm font-medium text-gray-700 mb-2">Side Effects</label>
                        <textarea name="side_effects" id="side_effects" rows="3" 
                                  class="w-full border border-gray-300 rounded px-3 py-2" 
                                  placeholder="Any side effects observed...">{{ old('side_effects') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-sticky-note mr-3 text-gray-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Additional Notes</h2>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                        <textarea name="notes" id="notes" rows="3" 
                                  class="w-full border border-gray-300 rounded px-3 py-2" 
                                  placeholder="Any additional notes or observations...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    The vaccination record will be created and can be managed from the vaccination records list.
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.vaccination-records.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Add Vaccination Record
                    </button>
                </div>
            </div>
        </form>
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    function debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Resident AJAX search for patient records
    const searchInput = document.getElementById('residentSearch');
    const searchResults = document.getElementById('searchResults');
    const residentIdInput = document.getElementById('resident_id');

    searchInput.addEventListener('input', debounce(async () => {
        const term = searchInput.value.trim();
        if (term.length < 2) {
            searchResults.innerHTML = '';
            searchResults.classList.add('hidden');
            return;
        }
        const response = await fetch(`{{ route('admin.search.residents') }}?term=${term}`);
        const results = await response.json();
        if (results.length > 0) {
            searchResults.innerHTML = results.map(resident => `
                <div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0" data-id="${resident.id}" data-name="${resident.name}">
                    <div class="font-medium text-gray-900">${resident.name}</div>
                    <div class="text-sm text-gray-500">${resident.email || 'N/A'}</div>
                </div>
            `).join('');
            searchResults.classList.remove('hidden');
        } else {
            searchResults.innerHTML = '<div class="p-3 text-gray-500 text-center">No residents found</div>';
            searchResults.classList.remove('hidden');
        }
    }, 250));

    searchResults.addEventListener('click', (event) => {
        const target = event.target.closest('[data-id]');
        if (target && target.dataset.id) {
            residentIdInput.value = target.dataset.id;
            searchInput.value = target.dataset.name;
            searchResults.innerHTML = '';
            searchResults.classList.add('hidden');
        }
    });

    document.addEventListener('click', (event) => {
        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
            searchResults.innerHTML = '';
            searchResults.classList.add('hidden');
        }
    });
});
</script>
