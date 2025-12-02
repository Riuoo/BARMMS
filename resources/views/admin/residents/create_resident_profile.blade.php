@extends('admin.main.layout')

@section('title', 'Add New Resident')

@section('content')
    <!-- Header Skeleton -->
    <div id="createResidentHeaderSkeleton" class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8 mt-8">
        @include('components.loading.create-form-skeleton', ['type' => 'resident'])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="createResidentContent" style="display: none;">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8 mt-8">
            <h1 class="text-3xl font-semibold text-gray-800 mb-2 text-center">Add New Resident</h1>

            @if(session('success'))
                <div class="mb-6">
                    <div class="bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6">
                    <div class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.residents.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Basic Information Section -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
                    <h3 class="text-lg font-semibold mb-2 text-gray-700">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name -->
                            <div class="mb-2">
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="First Name" required>
                            </div>
                            <!-- Middle Name (optional, can be disabled) -->
                            <div class="mb-2">
                                <div class="flex items-center justify-between mb-1">
                                    <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-0">Middle Name</label>
                                    <label class="inline-flex items-center text-xs text-gray-600">
                                        <input type="checkbox" id="no_middle_name" class="mr-1">
                                        No middle name
                                    </label>
                                </div>
                                <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Middle Name (optional)">
                            </div>
                            <!-- Last Name -->
                            <div class="mb-2">
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Last Name" required>
                            </div>
                            <!-- Suffix (optional dropdown) -->
                            <div>
                                <label for="suffix" class="block text-sm font-medium text-gray-700 mb-1">Suffix</label>
                                <select id="suffix" name="suffix" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">None</option>
                                    <option value="Jr." {{ old('suffix') == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                                    <option value="Sr." {{ old('suffix') == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                                    <option value="II" {{ old('suffix') == 'II' ? 'selected' : '' }}>II</option>
                                    <option value="III" {{ old('suffix') == 'III' ? 'selected' : '' }}>III</option>
                                    <option value="IV" {{ old('suffix') == 'IV' ? 'selected' : '' }}>IV</option>
                                </select>
                            </div>
                            <input type="hidden" id="name" name="name" value="{{ old('name') }}">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                            <p id="email-warning" class="mt-2 text-sm text-red-600 hidden"></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                            <select id="gender" name="gender" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div>
                            <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number <span class="text-red-500">*</span></label>
                            <input type="number" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="e.g., 9191234567" min="0" pattern="[0-9]*" inputmode="numeric" required>
                        </div>
                    </div>
                    @php
                        $defaultBarangay = config('app.default_barangay', 'Lower Malinao');
                        $defaultCity = config('app.default_city', 'Padada');
                        $defaultProvince = config('app.default_province', 'Davao Del Sur');
                    @endphp
                    <div class="mt-2 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                            <input type="text" value="{{ $defaultBarangay }}" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-600" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City/Municipality</label>
                            <input type="text" value="{{ $defaultCity }}" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-600" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                            <input type="text" value="{{ $defaultProvince }}" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-600" readonly>
                        </div>
                        <div>
                            <label for="purok" class="block text-sm font-medium text-gray-700 mb-1">Purok <span class="text-red-500">*</span></label>
                            <select id="purok" name="purok" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                <option value="">Select Purok</option>
                                @for($i = 1; $i <= 7; $i++)
                                    <option value="Purok {{ $i }}" {{ old('purok') == 'Purok '.$i ? 'selected' : '' }}>Purok {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="address" name="address" value="{{ old('address') }}">
                </div>

                <!-- Personal Information Section -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
                    <h3 class="text-lg font-semibold mb-2 text-gray-700">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Birth Date <span class="text-red-500">*</span></label>
                            <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>
                        <div>
                            <label for="marital_status" class="block text-sm font-medium text-gray-700 mb-1">Marital Status <span class="text-red-500">*</span></label>
                            <select id="marital_status" name="marital_status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                <option value="">Select Marital Status</option>
                                <option value="Single" {{ old('marital_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                <option value="Widowed" {{ old('marital_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="Divorced" {{ old('marital_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="Separated" {{ old('marital_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Occupation <span class="text-red-500">*</span></label>
                        <select id="occupation_select" class="w-full border border-gray-300 rounded-md px-3 py-2 mb-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Select Occupation</option>
                            <option value="Teacher">Teacher</option>
                            <option value="Student">Student</option>
                            <option value="Farmer">Farmer</option>
                            <option value="Fisherman">Fisherman</option>
                            <option value="Vendor">Vendor</option>
                            <option value="Government Employee">Government Employee</option>
                            <option value="Private Employee">Private Employee</option>
                            <option value="Housewife">Housewife</option>
                            <option value="Construction Worker">Construction Worker</option>
                            <option value="Driver">Driver</option>
                            <option value="_other">Other (specify)</option>
                        </select>
                        <input type="text" id="occupation" name="occupation" value="{{ old('occupation') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Start typing occupation" required style="display: none;">
                    </div>
                </div>

                <!-- Demographic Information Section -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
                    <h3 class="text-lg font-semibold mb-2 text-gray-700">Demographic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="age" class="block text-sm font-medium text-gray-700 mb-1">Age <span class="text-red-500">*</span></label>
                            <input type="number" id="age" name="age" value="{{ old('age') }}" min="1" max="120" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-700" readonly required>
                        </div>
                        <div>
                            <label for="family_size" class="block text-sm font-medium text-gray-700 mb-1">Family Size <span class="text-red-500">*</span></label>
                            <input type="number" id="family_size" name="family_size" value="{{ old('family_size') }}" min="1" max="20" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                        <div>
                            <label for="education_level" class="block text-sm font-medium text-gray-700 mb-1">Education Level <span class="text-red-500">*</span></label>
                            <select id="education_level" name="education_level" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                <option value="">Select Education Level</option>
                                <option value="No Education" {{ old('education_level') == 'No Education' ? 'selected' : '' }}>No Education</option>
                                <option value="Elementary" {{ old('education_level') == 'Elementary' ? 'selected' : '' }}>Elementary</option>
                                <option value="High School" {{ old('education_level') == 'High School' ? 'selected' : '' }}>High School</option>
                                <option value="Vocational" {{ old('education_level') == 'Vocational' ? 'selected' : '' }}>Vocational</option>
                                <option value="College" {{ old('education_level') == 'College' ? 'selected' : '' }}>College</option>
                                <option value="Post Graduate" {{ old('education_level') == 'Post Graduate' ? 'selected' : '' }}>Post Graduate</option>
                            </select>
                        </div>
                        <div>
                            <label for="income_level" class="block text-sm font-medium text-gray-700 mb-1">Income Level (Monthly, PHP) <span class="text-red-500">*</span></label>
                            <select id="income_level" name="income_level" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                <option value="">Select Income Range</option>
                                <option value="Low" {{ old('income_level') == 'Low' ? 'selected' : '' }}>Low (₱0 – ₱10,000)</option>
                                <option value="Lower Middle" {{ old('income_level') == 'Lower Middle' ? 'selected' : '' }}>Lower Middle (₱10,001 – ₱20,000)</option>
                                <option value="Middle" {{ old('income_level') == 'Middle' ? 'selected' : '' }}>Middle (₱20,001 – ₱40,000)</option>
                                <option value="Upper Middle" {{ old('income_level') == 'Upper Middle' ? 'selected' : '' }}>Upper Middle (₱40,001 – ₱80,000)</option>
                                <option value="High" {{ old('income_level') == 'High' ? 'selected' : '' }}>High (₱80,001 and above)</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                        <div>
                            <label for="employment_status" class="block text-sm font-medium text-gray-700 mb-1">Employment Status <span class="text-red-500">*</span></label>
                            <select id="employment_status" name="employment_status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                <option value="">Select Employment Status</option>
                                <option value="Unemployed" {{ old('employment_status') == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                                <option value="Part-time" {{ old('employment_status') == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                                <option value="Self-employed" {{ old('employment_status') == 'Self-employed' ? 'selected' : '' }}>Self-employed</option>
                                <option value="Full-time" {{ old('employment_status') == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                            </select>
                        </div>
                        <div>
                            <label for="is_pwd" class="block text-sm font-medium text-gray-700 mb-1">Person with Disability (PWD) <span class="text-red-500">*</span></label>
                            <select id="is_pwd" name="is_pwd" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                @php $pwdOld = old('is_pwd', '0'); @endphp
                                <option value="0" {{ $pwdOld == '0' ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $pwdOld == '1' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact Section -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
                    <h3 class="text-lg font-semibold mb-2 text-gray-700">Emergency Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                            <select id="relationship_select" class="w-full border border-gray-300 rounded-md px-3 py-2 mb-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Select Relationship</option>
                                <option value="Spouse">Spouse</option>
                                <option value="Mother">Mother</option>
                                <option value="Father">Father</option>
                                <option value="Parent">Parent</option>
                                <option value="Sibling">Sibling</option>
                                <option value="Child">Child</option>
                                <option value="Relative">Relative</option>
                                <option value="Neighbor">Neighbor</option>
                                <option value="Friend">Friend</option>
                                <option value="Guardian">Guardian</option>
                                <option value="_other">Other (specify)</option>
                            </select>
                            <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Start typing (e.g., Spouse, Parent)" style="display: none;">
                        </div>
                        <div>
                            <label for="emergency_contact_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                            <input type="number" id="emergency_contact_number" name="emergency_contact_number" value="{{ old('emergency_contact_number') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="e.g., 9191234567" min="0" pattern="[0-9]*" inputmode="numeric">
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between mt-2">
                    <a href="{{ route('admin.residents') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Add Resident
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Skeleton loading control for create resident profile page
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const headerSkeleton = document.getElementById('createResidentHeaderSkeleton');
                const content = document.getElementById('createResidentContent');
                
                if (headerSkeleton) headerSkeleton.style.display = 'none';
                if (content) content.style.display = 'block';
            }, 1000);

            const birthInput = document.getElementById('birth_date');
            const ageInput = document.getElementById('age');
            const purokSelect = document.getElementById('purok');
            const addressInput = document.getElementById('address');
            const firstNameInput = document.getElementById('first_name');
            const middleNameInput = document.getElementById('middle_name');
            const noMiddleCheckbox = document.getElementById('no_middle_name');
            const lastNameInput = document.getElementById('last_name');
            const suffixInput = document.getElementById('suffix');
            const fullNameInput = document.getElementById('name');
            const occupationSelect = document.getElementById('occupation_select');
            const occupationInput = document.getElementById('occupation');
            const relationshipSelect = document.getElementById('relationship_select');
            const relationshipInput = document.getElementById('emergency_contact_relationship');

            function updateAge() {
                if (!birthInput || !ageInput) return;
                const val = birthInput.value;
                if (!val) { ageInput.value = ''; return; }
                const birthDate = new Date(val);
                if (isNaN(birthDate.getTime())) { ageInput.value = ''; return; }
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                ageInput.value = age > 0 ? age : '';
            }

            function updateAddress() {
                if (!purokSelect || !addressInput) return;
                const purok = purokSelect.value || '';
                const barangay = "{{ $defaultBarangay }}";
                const city = "{{ $defaultCity }}";
                const province = "{{ $defaultProvince }}";
                if (!purok) {
                    addressInput.value = '';
                    return;
                }
                addressInput.value = `${purok}, ${barangay}, ${city}, ${province}`;
            }

            function updateFullName() {
                if (!firstNameInput || !lastNameInput || !fullNameInput) return;
                const middle = (middleNameInput && !middleNameInput.disabled)
                    ? middleNameInput.value.trim()
                    : '';
                const suffixVal = suffixInput ? suffixInput.value.trim() : '';
                const parts = [
                    firstNameInput.value.trim(),
                    middle,
                    lastNameInput.value.trim(),
                    suffixVal,
                ].filter(Boolean);
                fullNameInput.value = parts.join(' ');
            }

            function handleNoMiddleToggle() {
                if (!middleNameInput || !noMiddleCheckbox) return;
                if (noMiddleCheckbox.checked) {
                    middleNameInput.value = '';
                    middleNameInput.disabled = true;
                } else {
                    middleNameInput.disabled = false;
                }
                updateFullName();
            }

            function handleOccupationChange() {
                if (!occupationSelect || !occupationInput) return;
                const val = occupationSelect.value;
                if (!val) {
                    // No selection: show manual input only if it already has a value (from old input), otherwise hide
                    if (occupationInput.value && occupationInput.value.trim() !== '') {
                        occupationInput.style.display = 'block';
                        occupationInput.readOnly = false;
                    } else {
                        occupationInput.value = '';
                        occupationInput.readOnly = true;
                        occupationInput.style.display = 'none';
                    }
                    return;
                }
                if (val === '_other') {
                    occupationInput.readOnly = false;
                    occupationInput.style.display = 'block';
                    occupationInput.value = '';
                    occupationInput.focus();
                } else {
                    occupationInput.value = val;
                    occupationInput.readOnly = true;
                    occupationInput.style.display = 'none';
                }
            }

            function handleRelationshipChange() {
                if (!relationshipSelect || !relationshipInput) return;
                const val = relationshipSelect.value;
                if (!val) {
                    // No selection: show manual input only if it already has a value (from old input), otherwise hide
                    if (relationshipInput.value && relationshipInput.value.trim() !== '') {
                        relationshipInput.style.display = 'block';
                        relationshipInput.readOnly = false;
                    } else {
                        relationshipInput.value = '';
                        relationshipInput.readOnly = false;
                        relationshipInput.style.display = 'none';
                    }
                    return;
                }
                if (val === '_other') {
                    relationshipInput.readOnly = false;
                    relationshipInput.style.display = 'block';
                    relationshipInput.value = '';
                    relationshipInput.focus();
                } else {
                    relationshipInput.value = val;
                    relationshipInput.readOnly = true;
                    relationshipInput.style.display = 'none';
                }
            }

            if (birthInput) {
                birthInput.addEventListener('change', updateAge);
                birthInput.addEventListener('input', updateAge);
                updateAge();
            }
            if (purokSelect) {
                purokSelect.addEventListener('change', updateAddress);
                updateAddress();
            }
            if (firstNameInput && lastNameInput) {
                firstNameInput.addEventListener('input', updateFullName);
                lastNameInput.addEventListener('input', updateFullName);
                if (middleNameInput) middleNameInput.addEventListener('input', updateFullName);
                if (suffixInput) suffixInput.addEventListener('input', updateFullName);
                updateFullName();
            }
            if (noMiddleCheckbox) {
                noMiddleCheckbox.addEventListener('change', handleNoMiddleToggle);
                handleNoMiddleToggle();
            }
            if (occupationSelect) {
                occupationSelect.addEventListener('change', handleOccupationChange);
                handleOccupationChange();
            }
            if (relationshipSelect) {
                relationshipSelect.addEventListener('change', handleRelationshipChange);
                handleRelationshipChange();
            }
        });
    </script>
@endsection