<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Create your account</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome (if needed for icons in the form) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="icon" href="{{ asset('lower malinao logo.ico') }}" type="image/x-icon">
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full space-y-8 bg-white p-10 rounded-lg shadow">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Create your resident account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Please provide your information to complete your registration
            </p>
        </div>
        <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="role" value="resident">

            <!-- Basic Information Section -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- First Name -->
                    <div class="mb-2">
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm" placeholder="First Name" required>
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
                        <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm" placeholder="Middle Name">
                    </div>
                    <!-- Last Name -->
                    <div class="mb-2">
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm" placeholder="Last Name" required>
                    </div>
                    <!-- Suffix (optional dropdown) -->
                    <div>
                        <label for="suffix" class="block text-sm font-medium text-gray-700 mb-1">Suffix</label>
                        <select id="suffix" name="suffix" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm">
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm" disabled
                            placeholder="Email address" value="{{ $accountRequest->email ?? old('email') }}" @if($accountRequest->email) readonly @endif>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                        <select id="gender" name="gender" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div>
                        <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number <span class="text-red-500">*</span></label>
                        <input id="contact_number" name="contact_number" type="number" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                            placeholder="e.g., 09191234567" min="0" pattern="[0-9]*" inputmode="numeric" value="{{ old('contact_number') }}">
                    </div>
                </div>
                @php
                    $defaultBarangay = config('app.default_barangay', 'Lower Malinao');
                    $defaultCity = config('app.default_city', 'Padada');
                    $defaultProvince = config('app.default_province', 'Davao Del Sur');
                @endphp
                <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                        <input type="text" value="{{ $defaultBarangay }}" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 bg-gray-100 text-gray-600 rounded-md cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City/Municipality</label>
                        <input type="text" value="{{ $defaultCity }}" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 bg-gray-100 text-gray-600 rounded-md cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                        <input type="text" value="{{ $defaultProvince }}" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 bg-gray-100 text-gray-600 rounded-md cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label for="purok" class="block text-sm font-medium text-gray-700 mb-1">Purok <span class="text-red-500">*</span></label>
                        <select id="purok" name="purok" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm" required>
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
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Birth Date <span class="text-red-500">*</span></label>
                        <input id="birth_date" name="birth_date" type="date" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                            value="{{ old('birth_date') }}">
                    </div>
                    <div>
                        <label for="marital_status" class="block text-sm font-medium text-gray-700 mb-1">Marital Status <span class="text-red-500">*</span></label>
                        <select id="marital_status" name="marital_status" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm">
                            <option value="">Select Marital Status</option>
                            <option value="Single" {{ old('marital_status') == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Widowed" {{ old('marital_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="Divorced" {{ old('marital_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                            <option value="Separated" {{ old('marital_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Occupation <span class="text-red-500">*</span></label>
                    <select id="occupation_select" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm mb-2">
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
                    <input type="text" id="occupation" name="occupation" value="{{ old('occupation') }}" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm" placeholder="Start typing occupation" required style="display: none;">
                </div>
            </div>

            <!-- Demographic Information Section -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Demographic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700 mb-1">Age <span class="text-red-500">*</span></label>
                        <input id="age" name="age" type="number" min="1" max="120" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 bg-gray-100 text-gray-700 rounded-md cursor-not-allowed"
                            placeholder="Age" value="{{ old('age') }}" readonly>
                    </div>
                    <div>
                        <label for="family_size" class="block text-sm font-medium text-gray-700 mb-1">Family Size <span class="text-red-500">*</span></label>
                        <input id="family_size" name="family_size" type="number" min="1" max="20" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                            placeholder="Number of family members" value="{{ old('family_size') }}">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="education_level" class="block text-sm font-medium text-gray-700 mb-1">Education Level <span class="text-red-500">*</span></label>
                        <select id="education_level" name="education_level" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm">
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
                        <select id="income_level" name="income_level" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm">
                            <option value="">Select Income Range</option>
                            <option value="Low" {{ old('income_level') == 'Low' ? 'selected' : '' }}>Low (₱0 – ₱10,000)</option>
                            <option value="Lower Middle" {{ old('income_level') == 'Lower Middle' ? 'selected' : '' }}>Lower Middle (₱10,001 – ₱20,000)</option>
                            <option value="Middle" {{ old('income_level') == 'Middle' ? 'selected' : '' }}>Middle (₱20,001 – ₱40,000)</option>
                            <option value="Upper Middle" {{ old('income_level') == 'Upper Middle' ? 'selected' : '' }}>Upper Middle (₱40,001 – ₱80,000)</option>
                            <option value="High" {{ old('income_level') == 'High' ? 'selected' : '' }}>High (₱80,001 and above)</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="employment_status" class="block text-sm font-medium text-gray-700 mb-1">Employment Status <span class="text-red-500">*</span></label>
                        <select id="employment_status" name="employment_status" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm">
                            <option value="">Select Employment Status</option>
                            <option value="Unemployed" {{ old('employment_status') == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                            <option value="Part-time" {{ old('employment_status') == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                            <option value="Self-employed" {{ old('employment_status') == 'Self-employed' ? 'selected' : '' }}>Self-employed</option>
                            <option value="Full-time" {{ old('employment_status') == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                        </select>
                    </div>
                    <div>
                        <label for="is_pwd" class="block text-sm font-medium text-gray-700 mb-1">Person with Disability (PWD) <span class="text-red-500">*</span></label>
                        <select id="is_pwd" name="is_pwd" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm">
                            @php $pwdOld = old('is_pwd', '0'); @endphp
                            <option value="0" {{ $pwdOld == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ $pwdOld == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact Section -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Emergency Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input id="emergency_contact_name" name="emergency_contact_name" type="text"
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                            placeholder="Full Name" value="{{ old('emergency_contact_name') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                        <select id="relationship_select" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm mb-2">
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
                        <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm" placeholder="Start typing (e.g., Spouse, Parent)" style="display: none;">
                    </div>
                    <div>
                        <label for="emergency_contact_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                        <input id="emergency_contact_number" name="emergency_contact_number" type="number"
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                            placeholder="e.g., 09191234567" min="0" pattern="[0-9]*" inputmode="numeric" value="{{ old('emergency_contact_number') }}">
                    </div>
                </div>
            </div>

            <!-- Security Section -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Security</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                            placeholder="Password (minimum 8 characters)">
                </div>
                <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                        placeholder="Confirm Password">
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                    <i class="fas fa-user-plus mr-2"></i>
                    Complete Registration
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            // Auto-calculate age from birth date
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

            // Update address from purok selection
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

            // Update full name from parts
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

            // Handle no middle name checkbox
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

            // Handle occupation dropdown
            function handleOccupationChange() {
                if (!occupationSelect || !occupationInput) return;
                const val = occupationSelect.value;
                if (!val) {
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
                    occupationInput.value = '';
                    occupationInput.style.display = 'block';
                    occupationInput.focus();
                } else {
                    occupationInput.value = val;
                    occupationInput.readOnly = true;
                    occupationInput.style.display = 'none';
                }
            }

            // Handle relationship dropdown
            function handleRelationshipChange() {
                if (!relationshipSelect || !relationshipInput) return;
                const val = relationshipSelect.value;
                if (!val) {
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
                    relationshipInput.value = '';
                    relationshipInput.style.display = 'block';
                    relationshipInput.focus();
                } else {
                    relationshipInput.value = val;
                    relationshipInput.readOnly = true;
                    relationshipInput.style.display = 'none';
                }
            }

            // Event listeners
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

            // Form submit handler - ensure all fields are populated before submission
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Ensure name is populated
                    updateFullName();
                    
                    // Ensure address is populated
                    updateAddress();
                    
                    // Ensure occupation has a value and is visible for submission
                    if (occupationSelect && occupationInput) {
                        const occupationVal = occupationSelect.value;
                        if (occupationVal && occupationVal !== '_other') {
                            occupationInput.value = occupationVal;
                            occupationInput.style.display = 'block';
                            occupationInput.readOnly = true;
                        }
                        if (!occupationInput.value || occupationInput.value.trim() === '') {
                            e.preventDefault();
                            alert('Please select or enter an occupation.');
                            occupationSelect.focus();
                            return false;
                        }
                    }
                    
                    // Ensure relationship has a value and is visible for submission
                    if (relationshipSelect && relationshipInput) {
                        const relationshipVal = relationshipSelect.value;
                        if (relationshipVal && relationshipVal !== '_other') {
                            relationshipInput.value = relationshipVal;
                            relationshipInput.style.display = 'block';
                            relationshipInput.readOnly = true;
                        }
                        if (!relationshipInput.value || relationshipInput.value.trim() === '') {
                            e.preventDefault();
                            alert('Please select or enter an emergency contact relationship.');
                            relationshipSelect.focus();
                            return false;
                        }
                    }
                    
                    // Validate name field
                    if (!fullNameInput || !fullNameInput.value || fullNameInput.value.trim() === '') {
                        e.preventDefault();
                        alert('Please fill in at least First Name and Last Name.');
                        if (firstNameInput) firstNameInput.focus();
                        return false;
                    }
                    
                    // Validate address field
                    if (!addressInput || !addressInput.value || addressInput.value.trim() === '') {
                        e.preventDefault();
                        alert('Please select a Purok.');
                        if (purokSelect) purokSelect.focus();
                        return false;
                    }
                });
            }
        });
    </script>
</body>
</html>