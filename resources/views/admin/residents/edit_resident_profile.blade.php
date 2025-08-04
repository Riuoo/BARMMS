@extends('admin.main.layout')

@section('title', 'Edit Resident Profile')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Resident Profile</h1>
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

            <!-- Basic Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-user mr-2 text-blue-600"></i>
                    Basic Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name <span class="text-gray-500">(Read Only)</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $resident->name) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" 
                               readonly>
                        <p class="mt-1 text-sm text-gray-500">Basic information cannot be modified</p>
                    </div>

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

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address <span class="text-gray-500">(Read Only)</span>
                        </label>
                        <input type="text" 
                               id="address" 
                               name="address" 
                               value="{{ old('address', $resident->address) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" 
                               readonly>
                        <p class="mt-1 text-sm text-gray-500">Address information is read-only</p>
                    </div>
                </div>
            </div>

            <!-- Demographic Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-user-friends mr-2 text-green-600"></i>
                    Demographic Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700 mb-2">Age</label>
                        <input type="number" 
                               id="age" 
                               name="age" 
                               value="{{ old('age', $resident->age) }}" 
                               min="1" 
                               max="120" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200">
                        <p class="mt-1 text-sm text-gray-500">Resident's age in years</p>
                    </div>

                    <div>
                        <label for="family_size" class="block text-sm font-medium text-gray-700 mb-2">Family Size</label>
                        <input type="number" 
                               id="family_size" 
                               name="family_size" 
                               value="{{ old('family_size', $resident->family_size) }}" 
                               min="1" 
                               max="20" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200">
                        <p class="mt-1 text-sm text-gray-500">Number of family members</p>
                    </div>

                    <div>
                        <label for="education_level" class="block text-sm font-medium text-gray-700 mb-2">Education Level</label>
                        <select id="education_level" 
                                name="education_level" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200">
                            <option value="">Select Education Level</option>
                            <option value="No Education" {{ old('education_level', $resident->education_level) == 'No Education' ? 'selected' : '' }}>No Education</option>
                            <option value="Elementary" {{ old('education_level', $resident->education_level) == 'Elementary' ? 'selected' : '' }}>Elementary</option>
                            <option value="High School" {{ old('education_level', $resident->education_level) == 'High School' ? 'selected' : '' }}>High School</option>
                            <option value="Vocational" {{ old('education_level', $resident->education_level) == 'Vocational' ? 'selected' : '' }}>Vocational</option>
                            <option value="College" {{ old('education_level', $resident->education_level) == 'College' ? 'selected' : '' }}>College</option>
                            <option value="Post Graduate" {{ old('education_level', $resident->education_level) == 'Post Graduate' ? 'selected' : '' }}>Post Graduate</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Highest level of education completed</p>
                    </div>

                    <div>
                        <label for="income_level" class="block text-sm font-medium text-gray-700 mb-2">Income Level</label>
                        <select id="income_level" 
                                name="income_level" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200">
                            <option value="">Select Income Level</option>
                            <option value="Low" {{ old('income_level', $resident->income_level) == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Lower Middle" {{ old('income_level', $resident->income_level) == 'Lower Middle' ? 'selected' : '' }}>Lower Middle</option>
                            <option value="Middle" {{ old('income_level', $resident->income_level) == 'Middle' ? 'selected' : '' }}>Middle</option>
                            <option value="Upper Middle" {{ old('income_level', $resident->income_level) == 'Upper Middle' ? 'selected' : '' }}>Upper Middle</option>
                            <option value="High" {{ old('income_level', $resident->income_level) == 'High' ? 'selected' : '' }}>High</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Household income category</p>
                    </div>

                    <div>
                        <label for="employment_status" class="block text-sm font-medium text-gray-700 mb-2">Employment Status</label>
                        <select id="employment_status" 
                                name="employment_status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200">
                            <option value="">Select Employment Status</option>
                            <option value="Unemployed" {{ old('employment_status', $resident->employment_status) == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                            <option value="Part-time" {{ old('employment_status', $resident->employment_status) == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                            <option value="Self-employed" {{ old('employment_status', $resident->employment_status) == 'Self-employed' ? 'selected' : '' }}>Self-employed</option>
                            <option value="Full-time" {{ old('employment_status', $resident->employment_status) == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Current employment situation</p>
                    </div>

                    <div>
                        <label for="health_status" class="block text-sm font-medium text-gray-700 mb-2">Health Status</label>
                        <select id="health_status" 
                                name="health_status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200">
                            <option value="">Select Health Status</option>
                            <option value="Critical" {{ old('health_status', $resident->health_status) == 'Critical' ? 'selected' : '' }}>Critical</option>
                            <option value="Poor" {{ old('health_status', $resident->health_status) == 'Poor' ? 'selected' : '' }}>Poor</option>
                            <option value="Fair" {{ old('health_status', $resident->health_status) == 'Fair' ? 'selected' : '' }}>Fair</option>
                            <option value="Good" {{ old('health_status', $resident->health_status) == 'Good' ? 'selected' : '' }}>Good</option>
                            <option value="Excellent" {{ old('health_status', $resident->health_status) == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Overall health condition</p>
                    </div>
                </div>
            </div>

            <!-- Security Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-lock mr-2 text-red-600"></i>
                    Security Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200">
                        <p class="mt-1 text-sm text-gray-500">Leave blank to keep current password</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200">
                        <p class="mt-1 text-sm text-gray-500">Confirm the new password</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    All changes will be saved immediately
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.residents') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Update Profile
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Information Card -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Profile Update Information</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Basic information is required for all residents</li>
                        <li>Demographic data helps with community planning and services</li>
                        <li>Password changes are optional and secure</li>
                        <li>All information is kept confidential and secure</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 