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
                @php
                    $isChildFlow = isset($ageGroup) && in_array(strtolower($ageGroup), ['child','infant','toddler','adolescent']);
                @endphp
                @if($isChildFlow)
                    <div class="grid grid-cols-1 gap-6">
                        <div class="relative">
                            <label for="child_search" class="block text-sm font-medium text-gray-700 mb-2">Search Child <span class="text-red-500">*</span></label>
                            <input type="text" id="child_search" placeholder="Type child's name..." autocomplete="off"
                                   value="{{ isset($prefillChild) ? ($prefillChild->first_name . ' ' . $prefillChild->last_name) : '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" />
                            <input type="hidden" id="child_profile_id" name="child_profile_id" value="{{ isset($prefillChild) ? $prefillChild->id : '' }}">
                            <input type="hidden" id="child_search_hidden" name="child_search" value="{{ isset($prefillChild) ? ($prefillChild->first_name . ' ' . $prefillChild->last_name) : '' }}">
                            <div id="child_results" class="absolute z-10 bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                            <p class="mt-1 text-xs text-gray-500">Search children from Child Profiles</p>
                            <p id="child_hint" class="mt-1 text-xs text-red-600 hidden">No children found</p>
                            <div class="mt-2">
                                <a href="{{ route('admin.vaccination-records.create-child-profile') }}" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-child mr-2"></i>
                                    Add New Child
                                </a>
                            </div>
                            @error('child_profile_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="residentSearch" class="block text-sm font-medium text-gray-700 mb-2">Select Resident <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                id="residentSearch"
                                placeholder="Search residents..."
                                autocomplete="off"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                aria-label="Search for a resident"
                            />
                            <input type="hidden" id="resident_id" name="resident_id">
                            <input type="hidden" id="resident_search_hidden" name="resident_search" value="">
                            <div id="searchResults" class="absolute z-10 bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                            <p class="mt-1 text-sm text-gray-500">Search and select the resident for this patient record</p>
                            @error('resident_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif
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
                               placeholder="Example: Pfizer-BioNTech COVID-19 Vaccine" required>
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

            

            <!-- Administered By (FK) will be auto-filled from session in controller if missing -->



            <!-- Privacy Consent Section -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2 shadow-lg">
                <div class="flex items-start">
                    <input type="checkbox" id="privacy_consent" name="privacy_consent" value="1" required
                        class="mt-1 mr-3 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 bg-white rounded"
                        {{ old('privacy_consent') ? 'checked' : '' }}>
                    <label for="privacy_consent" class="text-sm text-gray-700 flex-1">
                        I confirm that the resident/child has been informed about and has consented to the 
                        <a href="{{ route('public.privacy') }}" target="_blank" 
                           class="text-blue-600 hover:text-blue-700 underline font-medium transition-colors">
                            Barangay Privacy Policy
                        </a>
                        regarding the collection, use, and storage of their personal and health data.
                        <span class="text-red-500">*</span>
                    </label>
                </div>
                <p class="text-xs text-gray-600 mt-3 ml-7 leading-relaxed">
                    <strong class="text-gray-700">Note:</strong> As the Secretary filling out this form, you are confirming that the resident/child (or their guardian) has been informed about the Privacy Policy and has provided their consent for the processing of their personal and health information as described in the policy.
                </p>
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
                    <button type="submit" id="submitBtn"
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
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
    const childIdInput = document.getElementById('child_profile_id');
    const childSearchInput = document.getElementById('child_search');
    const childResults = document.getElementById('child_results');

    // Prevent selecting both Resident and Child simultaneously
    // Clear resident fields when child chosen via AJAX
    function clearResidentSelection() {
        if (residentIdInput) residentIdInput.value = '';
        if (searchInput) searchInput.value = '';
        if (searchResults) {
            searchResults.innerHTML = '';
            searchResults.classList.add('hidden');
        }
    }

    searchInput && searchInput.addEventListener('input', debounce(async () => {
        const term = searchInput ? searchInput.value.trim() : '';
        const resHidden = document.getElementById('resident_search_hidden');
        if (resHidden) resHidden.value = term;
        if (term.length < 2) {
            searchResults.innerHTML = '';
            searchResults.classList.add('hidden');
            return;
        }
        // Show searching… state immediately
        if (searchResults) {
            searchResults.innerHTML = '<div class="p-3 text-gray-500 text-center">Searching…</div>';
            searchResults.classList.remove('hidden');
        }
        try {
            const response = await fetch(`{{ route('admin.search.residents') }}?term=${encodeURIComponent(term)}` , {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const results = await response.json();
            if (Array.isArray(results) && results.length > 0) {
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
        } catch (e) {
            searchResults.innerHTML = '<div class="p-3 text-gray-500 text-center">Search unavailable</div>';
            searchResults.classList.remove('hidden');
        }
    }, 250));

    if (searchResults) {
        searchResults.addEventListener('click', (event) => {
            const target = event.target.closest('[data-id]');
            if (target && target.dataset.id) {
                if (residentIdInput) residentIdInput.value = target.dataset.id;
                if (searchInput) searchInput.value = target.dataset.name;
                // Clear child selection if resident selected
                if (childIdInput) childIdInput.value = '';
                searchResults.innerHTML = '';
                searchResults.classList.add('hidden');
            }
        });
    }
    // Child AJAX search
    if (childSearchInput && childResults) {
        childSearchInput.addEventListener('input', debounce(async () => {
            const term = childSearchInput.value.trim();
            const childHidden = document.getElementById('child_search_hidden') || document.getElementById('child_search_hidden');
            const hiddenSet = document.getElementById('child_search_hidden');
            if (hiddenSet) hiddenSet.value = term;
            if (term.length < 2) {
                childResults.innerHTML = '';
                childResults.classList.add('hidden');
                return;
            }
        // Show searching state immediately
        childResults.innerHTML = '<div class="p-3 text-gray-500 text-center">Searching…</div>';
        childResults.classList.remove('hidden');
            const url = `{{ route('admin.vaccination-records.child-profiles') }}?search=${encodeURIComponent(term)}`;
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }});
            try {
                const json = await response.json();
                let items = [];
                if (json && Array.isArray(json.data)) {
                    items = json.data.map(c => ({ id: c.id, name: `${c.first_name} ${c.last_name}` }));
                }
                const hint = document.getElementById('child_hint');
                if (items.length === 0) {
                    childResults.innerHTML = '<div class="p-3 text-gray-500 text-center">No children found</div>';
                    if (hint) hint.classList.add('hidden');
                } else {
                    childResults.innerHTML = items.map(c => `<div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0" data-id="${c.id}" data-name="${c.name}"><div class="font-medium text-gray-900">${c.name}</div></div>`).join('');
                    if (hint) hint.classList.add('hidden');
                }
                childResults.classList.remove('hidden');
            } catch(err) {
                childResults.innerHTML = '<div class="p-3 text-gray-500 text-center">Search unavailable.</div>';
                childResults.classList.remove('hidden');
            }
        }, 250));

        childResults.addEventListener('click', (event) => {
            const target = event.target.closest('[data-id]');
            if (target && target.dataset.id) {
                if (childIdInput) childIdInput.value = target.dataset.id;
                if (childSearchInput) childSearchInput.value = target.dataset.name;
                // Clear resident selection
                clearResidentSelection();
                childResults.innerHTML = '';
                childResults.classList.add('hidden');
            }
        });

        document.addEventListener('click', (event) => {
            if (!childSearchInput.contains(event.target) && !childResults.contains(event.target)) {
                childResults.innerHTML = '';
                childResults.classList.add('hidden');
            }
        });
    }

    document.addEventListener('click', (event) => {
        if (searchInput && searchResults) {
            if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                searchResults.innerHTML = '';
                searchResults.classList.add('hidden');
            }
        }
    });

    // Privacy consent checkbox validation
    const privacyConsentCheckbox = document.getElementById('privacy_consent');
    const submitBtn = document.getElementById('submitBtn');
    
    function updateSubmitButton() {
        if (privacyConsentCheckbox && submitBtn) {
            submitBtn.disabled = !privacyConsentCheckbox.checked;
        }
    }
    
    if (privacyConsentCheckbox) {
        privacyConsentCheckbox.addEventListener('change', updateSubmitButton);
        updateSubmitButton(); // Initial check
    }

    // Form submission validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!privacyConsentCheckbox || !privacyConsentCheckbox.checked) {
                e.preventDefault();
                alert('Please acknowledge and agree to the Privacy Policy by checking the consent box.');
                if (privacyConsentCheckbox) privacyConsentCheckbox.focus();
                return false;
            }
        });
    }
});
</script>
