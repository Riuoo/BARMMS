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
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-green-600 text-white fixed w-full top-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0">
                    <a href="{{ route('landing') }}" class="flex items-center space-x-3">
                        <img src="/images/lower-malinao-brgy-logo.png" alt="Lower Malinao Barangay Logo" class="h-10 w-auto" />
                        <h1 class="text-xl font-bold">Lower Malinao System</h1>
                    </a>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="{{ route('landing') }}" class="hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Home</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow pt-20 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-4xl w-full space-y-8 bg-white p-10 rounded-lg shadow">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Create your resident account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Please provide your information to complete your registration
            </p>
            @if(isset($resident) && $resident)
                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Your resident profile has been found!</strong> Most of your information has been pre-filled from your existing profile. Please review all information and provide your contact number to complete registration.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
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
                        @php
                            // Priority: resident data > accountRequest data
                            $firstName = '';
                            if (isset($resident) && $resident->first_name) {
                                $firstName = $resident->first_name;
                            } elseif (isset($accountRequest) && $accountRequest->first_name) {
                                $firstName = $accountRequest->first_name;
                            } elseif (isset($accountRequest) && $accountRequest->full_name) {
                                $nameParts = explode(' ', trim($accountRequest->full_name));
                                if (count($nameParts) > 1) {
                                    $firstName = implode(' ', array_slice($nameParts, 0, -1));
                                } else {
                                    $firstName = $nameParts[0] ?? '';
                                }
                            }
                        @endphp
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $firstName) }}" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md bg-gray-100 cursor-not-allowed sm:text-sm" placeholder="First Name" required readonly>
                    </div>
                    <!-- Middle Name (optional, can be disabled) -->
                    <div class="mb-2">
                        <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                        @php
                            // Priority: resident data > accountRequest data
                            $middleName = '';
                            if (isset($resident) && $resident->middle_name) {
                                $middleName = $resident->middle_name;
                            } elseif (isset($accountRequest) && $accountRequest->middle_name) {
                                $middleName = $accountRequest->middle_name;
                            }
                        @endphp
                        <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $middleName) }}" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md bg-gray-100 cursor-not-allowed sm:text-sm" placeholder="Middle Name" readonly>
                    </div>
                    <!-- Last Name -->
                    <div class="mb-2">
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                        @php
                            // Priority: resident data > accountRequest data
                            $lastName = '';
                            if (isset($resident) && $resident->last_name) {
                                $lastName = $resident->last_name;
                            } elseif (isset($accountRequest) && $accountRequest->last_name) {
                                $lastName = $accountRequest->last_name;
                            } elseif (isset($accountRequest) && $accountRequest->full_name) {
                                $nameParts = explode(' ', trim($accountRequest->full_name));
                                if (count($nameParts) > 0) {
                                    $lastName = end($nameParts);
                                }
                            }
                        @endphp
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $lastName) }}" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md bg-gray-100 cursor-not-allowed sm:text-sm" placeholder="Last Name" required readonly>
                    </div>
                    <!-- Suffix (optional dropdown) -->
                    <div>
                        <label for="suffix" class="block text-sm font-medium text-gray-700 mb-1">Suffix</label>
                        @php
                            // Priority: resident data > accountRequest data
                            $suffixValue = '';
                            if (isset($resident) && $resident->suffix) {
                                $suffixValue = $resident->suffix;
                            } elseif (isset($accountRequest) && $accountRequest->suffix) {
                                $suffixValue = $accountRequest->suffix;
                            }
                            $suffixValue = old('suffix', $suffixValue);
                        @endphp
                        <select id="suffix" name="suffix" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md bg-gray-100 cursor-not-allowed sm:text-sm" disabled>
                            <option value="">None</option>
                            <option value="Jr." {{ $suffixValue == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                            <option value="Sr." {{ $suffixValue == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                            <option value="II" {{ $suffixValue == 'II' ? 'selected' : '' }}>II</option>
                            <option value="III" {{ $suffixValue == 'III' ? 'selected' : '' }}>III</option>
                            <option value="IV" {{ $suffixValue == 'IV' ? 'selected' : '' }}>IV</option>
                        </select>
                        <input type="hidden" name="suffix" value="{{ $suffixValue }}">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm bg-gray-100 cursor-not-allowed"
                            placeholder="Email address" value="{{ $accountRequest->email ?? old('email') }}" readonly>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                        @php
                            $genderValue = old('gender');
                            if (isset($resident) && $resident->gender) {
                                $genderValue = $resident->gender;
                            }
                        @endphp
                        <select id="gender" name="gender" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm bg-gray-100 cursor-not-allowed" {{ isset($resident) ? 'disabled' : '' }}>
                            <option value="">Select Gender</option>
                            <option value="Male" {{ $genderValue == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $genderValue == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @if(isset($resident))
                            <input type="hidden" name="gender" value="{{ $genderValue }}">
                        @endif
                    </div>
                    <div>
                        <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number <span class="text-red-500">*</span></label>
                        @php
                            $contactNumber = old('contact_number');
                            if (isset($resident) && $resident->contact_number) {
                                $contactNumber = $resident->contact_number;
                            }
                        @endphp
                        <input id="contact_number" name="contact_number" type="number" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                            placeholder="Example: 09191234567" min="0" pattern="[0-9]*" inputmode="numeric" value="{{ $contactNumber }}">
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
                        @php
                            $purokValue = old('purok');
                            $addressValue = old('address');
                            if (isset($resident) && $resident->address) {
                                $addressValue = $resident->address;
                                // Extract purok from address (format: "Purok X, Barangay, City, Province")
                                if (preg_match('/Purok\s+(\d+)/i', $resident->address, $matches)) {
                                    $purokValue = 'Purok ' . $matches[1];
                                }
                            }
                        @endphp
                        <select id="purok" name="purok" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm bg-gray-100 cursor-not-allowed" required {{ isset($resident) ? 'disabled' : '' }}>
                            <option value="">Select Purok</option>
                            @for($i = 1; $i <= 7; $i++)
                                <option value="Purok {{ $i }}" {{ $purokValue == 'Purok '.$i ? 'selected' : '' }}>Purok {{ $i }}</option>
                            @endfor
                        </select>
                        @if(isset($resident))
                            <input type="hidden" name="purok" value="{{ $purokValue }}">
                        @endif
                    </div>
                </div>
                <input type="hidden" id="address" name="address" value="{{ $addressValue }}">
            </div>

            <!-- Personal Information Section -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Birth Date <span class="text-red-500">*</span></label>
                        @php
                            $birthDateValue = old('birth_date');
                            if (isset($resident) && $resident->birth_date) {
                                $birthDateValue = $resident->birth_date->format('Y-m-d');
                            }
                        @endphp
                        <input id="birth_date" name="birth_date" type="date" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm bg-gray-100 cursor-not-allowed"
                            value="{{ $birthDateValue }}" {{ isset($resident) ? 'readonly' : '' }}>
                    </div>
                    <div>
                        <label for="marital_status" class="block text-sm font-medium text-gray-700 mb-1">Marital Status <span class="text-red-500">*</span></label>
                        @php
                            $maritalStatusValue = old('marital_status');
                            if (isset($resident) && $resident->marital_status) {
                                $maritalStatusValue = $resident->marital_status;
                            }
                        @endphp
                        <select id="marital_status" name="marital_status" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm bg-gray-100 cursor-not-allowed" {{ isset($resident) ? 'disabled' : '' }}>
                            <option value="">Select Marital Status</option>
                            <option value="Single" {{ $maritalStatusValue == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ $maritalStatusValue == 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Widowed" {{ $maritalStatusValue == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="Divorced" {{ $maritalStatusValue == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                            <option value="Separated" {{ $maritalStatusValue == 'Separated' ? 'selected' : '' }}>Separated</option>
                        </select>
                        @if(isset($resident))
                            <input type="hidden" name="marital_status" value="{{ $maritalStatusValue }}">
                        @endif
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Occupation <span class="text-red-500">*</span></label>
                    @php
                        $occupationValue = old('occupation');
                        if (isset($resident) && $resident->occupation) {
                            $occupationValue = $resident->occupation;
                        }
                    @endphp
                    <select id="occupation_select" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm mb-2 bg-gray-100 cursor-not-allowed" {{ isset($resident) ? 'disabled' : '' }}>
                        <option value="">Select Occupation</option>
                        <option value="Teacher" {{ $occupationValue == 'Teacher' ? 'selected' : '' }}>Teacher</option>
                        <option value="Student" {{ $occupationValue == 'Student' ? 'selected' : '' }}>Student</option>
                        <option value="Farmer" {{ $occupationValue == 'Farmer' ? 'selected' : '' }}>Farmer</option>
                        <option value="Fisherman" {{ $occupationValue == 'Fisherman' ? 'selected' : '' }}>Fisherman</option>
                        <option value="Vendor" {{ $occupationValue == 'Vendor' ? 'selected' : '' }}>Vendor</option>
                        <option value="Government Employee" {{ $occupationValue == 'Government Employee' ? 'selected' : '' }}>Government Employee</option>
                        <option value="Private Employee" {{ $occupationValue == 'Private Employee' ? 'selected' : '' }}>Private Employee</option>
                        <option value="Housewife" {{ $occupationValue == 'Housewife' ? 'selected' : '' }}>Housewife</option>
                        <option value="Construction Worker" {{ $occupationValue == 'Construction Worker' ? 'selected' : '' }}>Construction Worker</option>
                        <option value="Driver" {{ $occupationValue == 'Driver' ? 'selected' : '' }}>Driver</option>
                        <option value="_other" {{ !in_array($occupationValue, ['Teacher', 'Student', 'Farmer', 'Fisherman', 'Vendor', 'Government Employee', 'Private Employee', 'Housewife', 'Construction Worker', 'Driver']) && $occupationValue ? 'selected' : '' }}>Other (specify)</option>
                    </select>
                    <input type="text" id="occupation" name="occupation" value="{{ $occupationValue }}" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm bg-gray-100 cursor-not-allowed" placeholder="Enter occupation" required {{ isset($resident) ? 'readonly' : '' }} style="{{ isset($resident) || $occupationValue ? 'display: block;' : 'display: none;' }}">
                    @if(isset($resident))
                        <input type="hidden" name="occupation" value="{{ $occupationValue }}">
                    @endif
                </div>
            </div>

            <!-- Demographic Information Section -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Demographic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700 mb-1">Age <span class="text-red-500">*</span></label>
                        @php
                            $ageValue = old('age');
                            if (isset($resident) && $resident->age) {
                                $ageValue = $resident->age;
                            }
                        @endphp
                        <input id="age" name="age" type="number" min="1" max="120" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 bg-gray-100 text-gray-700 rounded-md cursor-not-allowed"
                            placeholder="Age" value="{{ $ageValue }}" readonly>
                    </div>
                    <div>
                        <label for="family_size" class="block text-sm font-medium text-gray-700 mb-1">Family Size <span class="text-red-500">*</span></label>
                        @php
                            $familySizeValue = old('family_size');
                            if (isset($resident) && $resident->family_size) {
                                $familySizeValue = $resident->family_size;
                            }
                        @endphp
                        <input id="family_size" name="family_size" type="number" min="1" max="20" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm bg-gray-100 cursor-not-allowed"
                            placeholder="Number of family members" value="{{ $familySizeValue }}" {{ isset($resident) ? 'readonly' : '' }}>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="education_level" class="block text-sm font-medium text-gray-700 mb-1">Education Level <span class="text-red-500">*</span></label>
                        @php
                            $educationLevelValue = old('education_level');
                            if (isset($resident) && $resident->education_level) {
                                $educationLevelValue = $resident->education_level;
                            }
                        @endphp
                        <select id="education_level" name="education_level" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm bg-gray-100 cursor-not-allowed" {{ isset($resident) ? 'disabled' : '' }}>
                            <option value="">Select Education Level</option>
                            <option value="No Education" {{ $educationLevelValue == 'No Education' ? 'selected' : '' }}>No Education</option>
                            <option value="Elementary" {{ $educationLevelValue == 'Elementary' ? 'selected' : '' }}>Elementary</option>
                            <option value="High School" {{ $educationLevelValue == 'High School' ? 'selected' : '' }}>High School</option>
                            <option value="Vocational" {{ $educationLevelValue == 'Vocational' ? 'selected' : '' }}>Vocational</option>
                            <option value="College" {{ $educationLevelValue == 'College' ? 'selected' : '' }}>College</option>
                            <option value="Post Graduate" {{ $educationLevelValue == 'Post Graduate' ? 'selected' : '' }}>Post Graduate</option>
                        </select>
                        @if(isset($resident))
                            <input type="hidden" name="education_level" value="{{ $educationLevelValue }}">
                        @endif
                    </div>
                    <div>
                        <label for="income_level" class="block text-sm font-medium text-gray-700 mb-1">Income Level (Monthly, PHP) <span class="text-red-500">*</span></label>
                        @php
                            $incomeLevelValue = old('income_level');
                            if (isset($resident) && $resident->income_level) {
                                $incomeLevelValue = $resident->income_level;
                            }
                        @endphp
                        <select id="income_level" name="income_level" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm bg-gray-100 cursor-not-allowed" {{ isset($resident) ? 'disabled' : '' }}>
                            <option value="">Select Income Range</option>
                            <option value="Low" {{ $incomeLevelValue == 'Low' ? 'selected' : '' }}>Low (₱0 – ₱10,000)</option>
                            <option value="Lower Middle" {{ $incomeLevelValue == 'Lower Middle' ? 'selected' : '' }}>Lower Middle (₱10,001 – ₱20,000)</option>
                            <option value="Middle" {{ $incomeLevelValue == 'Middle' ? 'selected' : '' }}>Middle (₱20,001 – ₱40,000)</option>
                            <option value="Upper Middle" {{ $incomeLevelValue == 'Upper Middle' ? 'selected' : '' }}>Upper Middle (₱40,001 – ₱80,000)</option>
                            <option value="High" {{ $incomeLevelValue == 'High' ? 'selected' : '' }}>High (₱80,001 and above)</option>
                        </select>
                        @if(isset($resident))
                            <input type="hidden" name="income_level" value="{{ $incomeLevelValue }}">
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="employment_status" class="block text-sm font-medium text-gray-700 mb-1">Employment Status <span class="text-red-500">*</span></label>
                        @php
                            $employmentStatusValue = old('employment_status');
                            if (isset($resident) && $resident->employment_status) {
                                $employmentStatusValue = $resident->employment_status;
                            }
                        @endphp
                        <select id="employment_status" name="employment_status" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm bg-gray-100 cursor-not-allowed" {{ isset($resident) ? 'disabled' : '' }}>
                            <option value="">Select Employment Status</option>
                            <option value="Unemployed" {{ $employmentStatusValue == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                            <option value="Part-time" {{ $employmentStatusValue == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                            <option value="Self-employed" {{ $employmentStatusValue == 'Self-employed' ? 'selected' : '' }}>Self-employed</option>
                            <option value="Full-time" {{ $employmentStatusValue == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                        </select>
                        @if(isset($resident))
                            <input type="hidden" name="employment_status" value="{{ $employmentStatusValue }}">
                        @endif
                    </div>
                    <div>
                        <label for="is_pwd" class="block text-sm font-medium text-gray-700 mb-1">Person with Disability (PWD) <span class="text-red-500">*</span></label>
                        @php
                            $pwdValue = old('is_pwd', '0');
                            if (isset($resident) && $resident->is_pwd !== null) {
                                $pwdValue = $resident->is_pwd ? '1' : '0';
                            }
                        @endphp
                        <select id="is_pwd" name="is_pwd" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm bg-gray-100 cursor-not-allowed" {{ isset($resident) ? 'disabled' : '' }}>
                            <option value="0" {{ $pwdValue == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ $pwdValue == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                        @if(isset($resident))
                            <input type="hidden" name="is_pwd" value="{{ $pwdValue }}">
                        @endif
                    </div>
                </div>
            </div>

            <!-- Emergency Contact Section -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Emergency Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        @php
                            $emergencyContactName = old('emergency_contact_name');
                            if (isset($resident) && $resident->emergency_contact_name) {
                                $emergencyContactName = $resident->emergency_contact_name;
                            }
                        @endphp
                        <input id="emergency_contact_name" name="emergency_contact_name" type="text"
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm bg-gray-100 cursor-not-allowed"
                            placeholder="Full Name" value="{{ $emergencyContactName }}" {{ isset($resident) && $resident->emergency_contact_name ? 'readonly' : '' }}>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                        @php
                            $emergencyContactRelationship = old('emergency_contact_relationship');
                            if (isset($resident) && $resident->emergency_contact_relationship) {
                                $emergencyContactRelationship = $resident->emergency_contact_relationship;
                            }
                        @endphp
                        <select id="relationship_select" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm mb-2 bg-gray-100 cursor-not-allowed" {{ isset($resident) && $resident->emergency_contact_relationship ? 'disabled' : '' }}>
                            <option value="">Select Relationship</option>
                            <option value="Spouse" {{ $emergencyContactRelationship == 'Spouse' ? 'selected' : '' }}>Spouse</option>
                            <option value="Mother" {{ $emergencyContactRelationship == 'Mother' ? 'selected' : '' }}>Mother</option>
                            <option value="Father" {{ $emergencyContactRelationship == 'Father' ? 'selected' : '' }}>Father</option>
                            <option value="Parent" {{ $emergencyContactRelationship == 'Parent' ? 'selected' : '' }}>Parent</option>
                            <option value="Sibling" {{ $emergencyContactRelationship == 'Sibling' ? 'selected' : '' }}>Sibling</option>
                            <option value="Child" {{ $emergencyContactRelationship == 'Child' ? 'selected' : '' }}>Child</option>
                            <option value="Relative" {{ $emergencyContactRelationship == 'Relative' ? 'selected' : '' }}>Relative</option>
                            <option value="Neighbor" {{ $emergencyContactRelationship == 'Neighbor' ? 'selected' : '' }}>Neighbor</option>
                            <option value="Friend" {{ $emergencyContactRelationship == 'Friend' ? 'selected' : '' }}>Friend</option>
                            <option value="Guardian" {{ $emergencyContactRelationship == 'Guardian' ? 'selected' : '' }}>Guardian</option>
                            <option value="_other" {{ !in_array($emergencyContactRelationship, ['Spouse', 'Mother', 'Father', 'Parent', 'Sibling', 'Child', 'Relative', 'Neighbor', 'Friend', 'Guardian']) && $emergencyContactRelationship ? 'selected' : '' }}>Other (specify)</option>
                        </select>
                        <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship" value="{{ $emergencyContactRelationship }}" class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm bg-gray-100 cursor-not-allowed" placeholder="Enter relationship (Example: Spouse, Parent)" {{ isset($resident) && $resident->emergency_contact_relationship ? 'readonly' : '' }} style="{{ isset($resident) && $resident->emergency_contact_relationship ? 'display: block;' : ($emergencyContactRelationship ? 'display: block;' : 'display: none;') }}">
                        @if(isset($resident) && $resident->emergency_contact_relationship)
                            <input type="hidden" name="emergency_contact_relationship" value="{{ $emergencyContactRelationship }}">
                        @endif
                    </div>
                    <div>
                        <label for="emergency_contact_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                        @php
                            $emergencyContactNumber = old('emergency_contact_number');
                            if (isset($resident) && $resident->emergency_contact_number) {
                                $emergencyContactNumber = $resident->emergency_contact_number;
                            }
                        @endphp
                        <input id="emergency_contact_number" name="emergency_contact_number" type="number"
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm bg-gray-100 cursor-not-allowed"
                            placeholder="Example: 09191234567" min="0" pattern="[0-9]*" inputmode="numeric" value="{{ $emergencyContactNumber }}" {{ isset($resident) && $resident->emergency_contact_number ? 'readonly' : '' }}>
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

            <!-- Privacy Consent Section -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <input type="checkbox" id="privacy_consent" name="privacy_consent" value="1" required
                        class="mt-1 mr-3 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                        {{ old('privacy_consent') ? 'checked' : '' }}>
                    <label for="privacy_consent" class="text-sm text-gray-700 flex-1">
                        I acknowledge that I have read and agree to the 
                        <a href="{{ route('public.privacy') }}" target="_blank" 
                           class="text-blue-600 hover:text-blue-800 underline font-medium">
                            Barangay Privacy Policy
                        </a>
                        regarding the collection, use, and storage of my personal data.
                        <span class="text-red-500">*</span>
                    </label>
                </div>
                <p class="text-xs text-gray-600 mt-2 ml-7">
                    By checking this box, you consent to the processing of your personal information as described in our Privacy Policy.
                </p>
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
                <button type="submit" id="submitBtn"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
                    <i class="fas fa-user-plus mr-2"></i>
                    Complete Registration
                </button>
            </div>
        </form>
    </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const birthInput = document.getElementById('birth_date');
            const ageInput = document.getElementById('age');
            const purokSelect = document.getElementById('purok');
            const addressInput = document.getElementById('address');
            // Name fields are read-only, no need for JavaScript manipulation
            const firstNameInput = document.getElementById('first_name');
            const middleNameInput = document.getElementById('middle_name');
            const lastNameInput = document.getElementById('last_name');
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

            // Name fields are read-only, no JavaScript manipulation needed

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
            // No longer need to auto-update full name
            // No middle name checkbox removed - fields are read-only
            if (occupationSelect) {
                occupationSelect.addEventListener('change', handleOccupationChange);
                handleOccupationChange();
            }
            if (relationshipSelect) {
                relationshipSelect.addEventListener('change', handleRelationshipChange);
                handleRelationshipChange();
            }

            // Middle name validation function
            function validateMiddleName(value) {
                if (!value || !value.trim()) {
                    return true; // Empty is allowed (optional field)
                }
                const trimmed = value.trim();
                // Check if it's a single letter
                if (trimmed.length === 1) {
                    return false;
                }
                // Check if it's an initial with a period (e.g., "A." or "A. ")
                if (/^[A-Za-z]\.\s*$/.test(trimmed)) {
                    return false;
                }
                // Check if it's less than 2 characters after removing periods and spaces
                const cleaned = trimmed.replace(/[.\s]+/g, '');
                if (cleaned.length < 2) {
                    return false;
                }
                return true;
            }

            // Add validation on middle name input
            if (middleNameInput) {
                middleNameInput.addEventListener('blur', function() {
                    const value = this.value;
                    if (!validateMiddleName(value)) {
                        this.setCustomValidity('Please enter your full middle name. Initials are not allowed.');
                        this.classList.add('border-red-500');
                    } else {
                        this.setCustomValidity('');
                        this.classList.remove('border-red-500');
                    }
                });

                middleNameInput.addEventListener('input', function() {
                    const value = this.value;
                    if (validateMiddleName(value)) {
                        this.setCustomValidity('');
                        this.classList.remove('border-red-500');
                    }
                });
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

            // Form submit handler - ensure all fields are populated before submission
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

                    // Validate middle name before submission
                    if (middleNameInput && !middleNameInput.disabled && middleNameInput.value.trim()) {
                        if (!validateMiddleName(middleNameInput.value)) {
                            e.preventDefault();
                            alert('Please enter your full middle name. Initials are not allowed.');
                            middleNameInput.focus();
                            return false;
                        }
                    }

                    // Name fields are already separate, no need to combine
                    
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
                    
                    // Validate name fields
                    if (!firstNameInput || !firstNameInput.value || firstNameInput.value.trim() === '') {
                        e.preventDefault();
                        alert('Please fill in First Name.');
                        if (firstNameInput) firstNameInput.focus();
                        return false;
                    }
                    if (!lastNameInput || !lastNameInput.value || lastNameInput.value.trim() === '') {
                        e.preventDefault();
                        alert('Please fill in Last Name.');
                        if (lastNameInput) lastNameInput.focus();
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
    <script>
        // Ensure fields marked with * are required before submission
        document.addEventListener('DOMContentLoaded', function() {
            const isFormControl = (el) => el && ['INPUT', 'SELECT', 'TEXTAREA'].includes(el.tagName);
            const findControl = (label) => {
                const forId = label.getAttribute('for');
                if (forId) {
                    const byId = document.getElementById(forId);
                    if (isFormControl(byId)) return byId;
                }
                const sibling = label.nextElementSibling;
                if (isFormControl(sibling)) return sibling;
                return label.parentElement ? label.parentElement.querySelector('input, select, textarea') : null;
            };

            document.querySelectorAll('label').forEach((label) => {
                const text = (label.textContent || '').trim();
                const hasStar = label.querySelector('.text-red-500, .text-danger') || text.includes('*');
                if (!hasStar) return;

                const control = findControl(label);
                if (!isFormControl(control)) return;

                control.setAttribute('required', 'required');
                control.setAttribute('aria-required', 'true');

                if (control.type === 'radio' || control.type === 'checkbox') {
                    document.querySelectorAll(`input[name="${control.name}"]`).forEach((peer) => {
                        peer.setAttribute('required', 'required');
                        peer.setAttribute('aria-required', 'true');
                    });
                }
            });
        });
    </script>
</body>
</html>