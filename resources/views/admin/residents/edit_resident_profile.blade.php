@extends('admin.main.layout')

@section('title', 'Edit Resident Profile')

@section('content')
<!-- Header Skeleton -->
<div id="editResidentHeaderSkeleton" class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    @include('components.loading.edit-form-skeleton', ['type' => 'header', 'showButton' => false])
    @include('components.loading.edit-form-skeleton', ['type' => 'resident'])
</div>

<!-- Real Content (hidden initially) -->
<div id="editResidentContent" style="display: none;">
    <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
        <!-- Header Section -->
    <div class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Resident Profile</h1>
                <p class="text-gray-600">Update resident information and demographic data</p>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were some errors with your submission</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.residents.update', $resident->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information (Read Only) -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    <i class="fas fa-user mr-2 text-blue-600"></i>
                    Basic Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @php
                        // Use separate fields if they exist, otherwise fall back to name field (for backward compatibility)
                        $firstName = $resident->first_name ?? '';
                        $middleName = $resident->middle_name ?? '';
                        $lastName = $resident->last_name ?? '';
                        $suffix = $resident->suffix ?? '';
                    @endphp
                    <!-- First Name -->
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-gray-500">(Read Only)</span></label>
                        <input type="text" 
                               value="{{ old('first_name', $firstName) }}" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed" 
                               readonly
                               placeholder="Enter first name">
                    </div>
                    <!-- Middle Name (optional, can be disabled) -->
                    <div class="mb-2">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-gray-700 mb-0">Middle Name <span class="text-gray-500">(Read Only)</span></label>
                        </div>
                        <input type="text" 
                               value="{{ old('middle_name', $middleName) }}" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed" 
                               readonly
                               placeholder="Enter middle name (optional)">
                    </div>
                    <!-- Last Name -->
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-gray-500">(Read Only)</span></label>
                        <input type="text" 
                               value="{{ old('last_name', $lastName) }}" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed" 
                               readonly
                               placeholder="Enter last name">
                    </div>
                    <!-- Suffix (optional dropdown) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Suffix <span class="text-gray-500">(Read Only)</span></label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed" disabled>
                            <option value="">None</option>
                            <option value="Jr." {{ old('suffix', $suffix) == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                            <option value="Sr." {{ old('suffix', $suffix) == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                            <option value="II" {{ old('suffix', $suffix) == 'II' ? 'selected' : '' }}>II</option>
                            <option value="III" {{ old('suffix', $suffix) == 'III' ? 'selected' : '' }}>III</option>
                            <option value="IV" {{ old('suffix', $suffix) == 'IV' ? 'selected' : '' }}>IV</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-gray-500">(Read Only)</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $resident->email) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" 
                               readonly>
                        <p class="mt-1 text-sm text-gray-500">Contact email cannot be changed</p>
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                            Role <span class="text-gray-500">(Read Only)</span>
                        </label>
                        <input type="text" 
                               id="role" 
                               name="role" 
                               value="{{ old('role', $resident->role) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" 
                               readonly>
                        <p class="mt-1 text-sm text-gray-500">Resident's role is fixed</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Number
                        </label>
                        <input type="number" 
                               id="contact_number" 
                               name="contact_number" 
                               value="{{ old('contact_number', $resident->contact_number) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                               placeholder="Example: 9191234567"
                               min="0" 
                               pattern="[0-9]*" 
                               inputmode="numeric">
                    </div>
                </div>
            </div>

            <!-- Address / Location -->
            @php
                $defaultBarangay = config('app.default_barangay', 'Lower Malinao');
                $defaultCity = config('app.default_city', 'Padada');
                $defaultProvince = config('app.default_province', 'Davao Del Sur');
                $currentPurok = 'Purok 1';
                if (!empty($resident->address) && preg_match('/Purok\s*\d+/i', $resident->address, $m)) {
                    $currentPurok = $m[0];
                }
            @endphp
            <div class="border-b border-gray-200 pb-6 mt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    <i class="fas fa-map-marker-alt mr-2 text-indigo-600"></i>
                    Address
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Barangay
                        </label>
                        <input type="text"
                               value="{{ $defaultBarangay }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                               readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            City/Municipality
                        </label>
                        <input type="text"
                               value="{{ $defaultCity }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                               readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Province
                        </label>
                        <input type="text"
                               value="{{ $defaultProvince }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                               readonly>
                    </div>
                    <div>
                        <label for="purok" class="block text-sm font-medium text-gray-700 mb-2">
                            Purok <span class="text-red-500">*</span>
                        </label>
                        <select id="purok"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Select Purok</option>
                            @for($i = 1; $i <= 7; $i++)
                                @php $val = 'Purok '.$i; @endphp
                                <option value="{{ $val }}" {{ $currentPurok === $val ? 'selected' : '' }}>Purok {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <input type="hidden" id="address" name="address" value="{{ old('address', $resident->address) }}">
            </div>

                <!-- Personal Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                        <i class="fas fa-user-circle mr-2 text-green-600"></i>
                    Personal Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                            Gender <span class="text-gray-500">(Read Only)</span>
                        </label>
                        <select id="gender" 
                                name="gender" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed" 
                                disabled>
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender', $resident->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender', $resident->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                        <input type="hidden" name="gender" value="{{ old('gender', $resident->gender) }}">
                    </div>
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Birth Date <span class="text-gray-500">(Read Only)</span>
                        </label>
                        <input type="date" 
                               id="birth_date" 
                               name="birth_date" 
                               value="{{ old('birth_date', $resident->birth_date ? $resident->birth_date->format('Y-m-d') : '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed" 
                               readonly>
                    </div>
                    <div>
                        <label for="marital_status" class="block text-sm font-medium text-gray-700 mb-2">
                            Marital Status <span class="text-red-500">*</span>
                        </label>
                            <select id="marital_status" 
                                    name="marital_status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                    required>
                            <option value="">Select Marital Status</option>
                            <option value="Single" {{ old('marital_status', $resident->marital_status) == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('marital_status', $resident->marital_status) == 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Widowed" {{ old('marital_status', $resident->marital_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="Divorced" {{ old('marital_status', $resident->marital_status) == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                            <option value="Separated" {{ old('marital_status', $resident->marital_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                        </select>
                    </div>
                    </div>
                    @php
                        $knownOccupations = [
                            'Teacher',
                            'Student',
                            'Farmer',
                            'Fisherman',
                            'Vendor',
                            'Government Employee',
                            'Private Employee',
                            'Housewife',
                            'Construction Worker',
                            'Driver',
                        ];
                        $currentOccupation = old('occupation', $resident->occupation);
                        $occupationSelectValue = in_array($currentOccupation, $knownOccupations) ? $currentOccupation : '_other';
                    @endphp
                    @if($resident->canViewField('occupation'))
                    <div class="mt-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Occupation <span class="text-red-500">*</span></label>
                        <select id="occupation_select" class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Select Occupation</option>
                            @foreach($knownOccupations as $occ)
                                <option value="{{ $occ }}" {{ $occupationSelectValue === $occ ? 'selected' : '' }}>{{ $occ }}</option>
                            @endforeach
                            <option value="_other" {{ $occupationSelectValue === '_other' ? 'selected' : '' }}>Other (specify)</option>
                        </select>
                        <input type="text" 
                               id="occupation" 
                               name="occupation" 
                               value="{{ $currentOccupation }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                               placeholder="Enter occupation"
                               required
                               style="display: {{ $occupationSelectValue === '_other' ? 'block' : 'none' }};">
                </div>
                @endif
            </div>

                <!-- Demographic Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    <i class="fas fa-chart-pie mr-2 text-purple-600"></i>
                    Demographic Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700 mb-2">
                            Age <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="age" 
                               name="age" 
                               value="{{ old('age', $resident->age) }}" 
                               min="1" 
                               max="120" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed" 
                               readonly
                               required>
                    </div>
                    <div>
                        <label for="family_size" class="block text-sm font-medium text-gray-700 mb-2">
                            Family Size <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="family_size" 
                               name="family_size" 
                               value="{{ old('family_size', $resident->family_size) }}" 
                               min="1" 
                               max="20" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                               required>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                    @if($resident->canViewField('education_level'))
                    <div>
                        <label for="education_level" class="block text-sm font-medium text-gray-700 mb-2">
                            Education Level <span class="text-red-500">*</span>
                        </label>
                            <select id="education_level" 
                                    name="education_level" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                    required>
                            <option value="">Select Education Level</option>
                            <option value="No Education" {{ old('education_level', $resident->education_level) == 'No Education' ? 'selected' : '' }}>No Education</option>
                            <option value="Elementary" {{ old('education_level', $resident->education_level) == 'Elementary' ? 'selected' : '' }}>Elementary</option>
                            <option value="High School" {{ old('education_level', $resident->education_level) == 'High School' ? 'selected' : '' }}>High School</option>
                            <option value="Vocational" {{ old('education_level', $resident->education_level) == 'Vocational' ? 'selected' : '' }}>Vocational</option>
                            <option value="College" {{ old('education_level', $resident->education_level) == 'College' ? 'selected' : '' }}>College</option>
                            <option value="Post Graduate" {{ old('education_level', $resident->education_level) == 'Post Graduate' ? 'selected' : '' }}>Post Graduate</option>
                        </select>
                    </div>
                    @endif
                    @if($resident->canViewField('income_level'))
                    <div>
                        <label for="income_level" class="block text-sm font-medium text-gray-700 mb-2">
                            Income Level <span class="text-red-500">*</span>
                        </label>
                            <select id="income_level" 
                                    name="income_level" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                    required>
                            <option value="">Select Income Level</option>
                                <option value="Low" {{ old('income_level', $resident->income_level) == 'Low' ? 'selected' : '' }}>Low (₱0 – ₱10,000)</option>
                                <option value="Lower Middle" {{ old('income_level', $resident->income_level) == 'Lower Middle' ? 'selected' : '' }}>Lower Middle (₱10,001 – ₱20,000)</option>
                                <option value="Middle" {{ old('income_level', $resident->income_level) == 'Middle' ? 'selected' : '' }}>Middle (₱20,001 – ₱40,000)</option>
                                <option value="Upper Middle" {{ old('income_level', $resident->income_level) == 'Upper Middle' ? 'selected' : '' }}>Upper Middle (₱40,001 – ₱80,000)</option>
                                <option value="High" {{ old('income_level', $resident->income_level) == 'High' ? 'selected' : '' }}>High (₱80,001 and above)</option>
                        </select>
                        </div>
                    @endif
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                        <div>
                            <label for="employment_status" class="block text-sm font-medium text-gray-700 mb-2">
                                Employment Status <span class="text-red-500">*</span>
                            </label>
                                <select id="employment_status" 
                                        name="employment_status" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                        required>
                                <option value="">Select Employment Status</option>
                                    <option value="Unemployed" {{ old('employment_status', $resident->employment_status) == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                                <option value="Part-time" {{ old('employment_status', $resident->employment_status) == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                                    <option value="Self-employed" {{ old('employment_status', $resident->employment_status) == 'Self-employed' ? 'selected' : '' }}>Self-employed</option>
                                    <option value="Full-time" {{ old('employment_status', $resident->employment_status) == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <!-- Spacer to keep grid alignment without health status field -->
                        </div>
                    </div>
                <div class="mt-2">
                    <label for="is_pwd" class="block text-sm font-medium text-gray-700 mb-2">
                        Person with Disability (PWD) <span class="text-red-500">*</span>
                    </label>
                    @php $pwdVal = old('is_pwd', $resident->is_pwd ? '1' : '0'); @endphp
                    <select id="is_pwd" 
                            name="is_pwd" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            required>
                        <option value="0" {{ $pwdVal == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ $pwdVal == '1' ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
            </div>

            <!-- Emergency Contact Information -->
            <div class="border-b border-gray-200 pb-6 mt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    <i class="fas fa-phone-alt mr-2 text-red-600"></i>
                    Emergency Contact Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Name
                        </label>
                        <input type="text" 
                               id="emergency_contact_name" 
                               name="emergency_contact_name" 
                               value="{{ old('emergency_contact_name', $resident->emergency_contact_name) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    @php
                        $knownRelationships = [
                            'Spouse',
                            'Mother',
                            'Father',
                            'Parent',
                            'Sibling',
                            'Child',
                            'Relative',
                            'Neighbor',
                            'Friend',
                            'Guardian',
                        ];
                        $currentRelationship = old('emergency_contact_relationship', $resident->emergency_contact_relationship);
                        $relationshipSelectValue = in_array($currentRelationship, $knownRelationships) ? $currentRelationship : '_other';
                    @endphp
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Relationship
                        </label>
                        <select id="relationship_select" class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Select Relationship</option>
                            @foreach($knownRelationships as $rel)
                                <option value="{{ $rel }}" {{ $relationshipSelectValue === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                            @endforeach
                            <option value="_other" {{ $relationshipSelectValue === '_other' ? 'selected' : '' }}>Other (specify)</option>
                        </select>
                        <input type="text" 
                               id="emergency_contact_relationship" 
                               name="emergency_contact_relationship" 
                               value="{{ $currentRelationship }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                               placeholder="Enter relationship (Example: Spouse, Parent, Sibling)"
                               style="display: {{ $relationshipSelectValue === '_other' ? 'block' : 'none' }};">
                    </div>
                    <div>
                        <label for="emergency_contact_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Number
                        </label>
                        <input type="number" 
                               id="emergency_contact_number" 
                               name="emergency_contact_number" 
                               value="{{ old('emergency_contact_number', $resident->emergency_contact_number) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                               placeholder="Example: 9191234567"
                               min="0" 
                               pattern="[0-9]*" 
                               inputmode="numeric">
                    </div>
                </div>
            </div>

            <!-- Privacy Consent Section -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2 shadow-lg">
                <div class="flex items-start">
                    <input type="checkbox" id="privacy_consent" name="privacy_consent" value="1" required
                        class="mt-1 mr-3 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 bg-white rounded"
                        {{ old('privacy_consent') ? 'checked' : '' }}>
                    <label for="privacy_consent" class="text-sm text-gray-700 flex-1">
                        I confirm that the resident has been informed about and has consented to the 
                        <a href="{{ route('public.privacy') }}" target="_blank" 
                           class="text-blue-600 hover:text-blue-700 underline font-medium transition-colors">
                            Barangay Privacy Policy
                        </a>
                        regarding the collection, use, and storage of their personal data.
                        <span class="text-red-500">*</span>
                    </label>
                </div>
                <p class="text-xs text-gray-600 mt-3 ml-7 leading-relaxed">
                    <strong class="text-gray-700">Note:</strong> As the Secretary filling out this form, you are confirming that the resident has been informed about the Privacy Policy and has provided their consent for the processing of their personal information as described in the policy.
                </p>
            </div>

            <!-- Form Actions -->
                <div class="flex justify-between mt-2">
                    <a href="{{ route('admin.residents') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" id="submitBtn"
                        class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <i class="fas fa-save mr-2"></i>
                    Update Resident
                    </button>
                </div>
        </form>
        </div>
    </div>
</div>

<script>
    // Skeleton loading control for edit resident profile page
    document.addEventListener('DOMContentLoaded', function() {
        // Add 1 second delay to show skeleton effect
        setTimeout(() => {
            const headerSkeleton = document.getElementById('editResidentHeaderSkeleton');
            const content = document.getElementById('editResidentContent');
            
            if (headerSkeleton) headerSkeleton.style.display = 'none';
            if (content) content.style.display = 'block';
        }, 1000); // 1 second delay to show skeleton effect

        const birthInput = document.getElementById('birth_date');
        const ageInput = document.getElementById('age');
        const purokSelect = document.getElementById('purok');
        const addressInput = document.getElementById('address');
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

        // Note: birth_date is read-only in edit form, so age won't auto-update
        // Age is calculated from birth_date on initial load
        if (birthInput && ageInput) {
            // Calculate age on page load
            updateAge();
        }
        if (purokSelect) {
            purokSelect.addEventListener('change', updateAddress);
        }
        if (occupationSelect) {
            occupationSelect.addEventListener('change', handleOccupationChange);
            handleOccupationChange();
        }
        if (relationshipSelect) {
            relationshipSelect.addEventListener('change', handleRelationshipChange);
            handleRelationshipChange();
        }

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
                // Validate privacy consent
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
@endsection 