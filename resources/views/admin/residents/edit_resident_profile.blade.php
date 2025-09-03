@extends('admin.main.layout')

@section('title', 'Edit Resident Profile')

@section('content')
<!-- Header Skeleton -->
<div id="editResidentHeaderSkeleton" class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8 animate-pulse">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="h-8 w-56 bg-gray-200 rounded mb-2"></div>
                <div class="h-5 w-80 bg-gray-100 rounded"></div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="space-y-6">
            <!-- Basic Information Section Skeleton -->
            <div class="border-b border-gray-200 pb-6">
                <div class="h-6 w-40 bg-gray-200 rounded mb-4"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                        <div class="h-4 w-48 bg-gray-100 rounded mt-1"></div>
                    </div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                        <div class="h-4 w-48 bg-gray-100 rounded mt-1"></div>
                    </div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                        <div class="h-4 w-48 bg-gray-100 rounded mt-1"></div>
                    </div>
                </div>
            </div>
            
            <!-- Personal Information Section Skeleton -->
            <div class="border-b border-gray-200 pb-6">
                <div class="h-6 w-44 bg-gray-200 rounded mb-4"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
            
            <!-- Demographic Information Section Skeleton -->
            <div class="border-b border-gray-200 pb-6">
                <div class="h-6 w-52 bg-gray-200 rounded mb-4"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="h-4 w-20 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <div class="h-4 w-36 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <div class="h-4 w-40 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="h-4 w-36 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
            
            <!-- Emergency Contact Section Skeleton -->
            <div class="border-b border-gray-200 pb-6">
                <div class="h-6 w-44 bg-gray-200 rounded mb-4"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="h-4 w-40 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-36 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions Skeleton -->
            <div class="flex justify-end space-x-3">
                <div class="h-10 w-24 bg-gray-200 rounded"></div>
                <div class="h-10 w-32 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>
</div>

<!-- Real Content (hidden initially) -->
<div id="editResidentContent" style="display: none;">
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

            <!-- Basic Information (Read Only) -->
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
                </div>
            </div>

                <!-- Personal Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-user-circle mr-2 text-green-600"></i>
                    Personal Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Birth Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="birth_date" 
                               name="birth_date" 
                               value="{{ old('birth_date', $resident->birth_date ? $resident->birth_date->format('Y-m-d') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                               required>
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
                    <div class="mt-4">
                        <label for="occupation" class="block text-sm font-medium text-gray-700 mb-2">Occupation</label>
                        <input type="text" 
                               id="occupation" 
                               name="occupation" 
                               value="{{ old('occupation', $resident->occupation) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                               placeholder="e.g., Teacher, Business Owner, Student">
                </div>
            </div>

                <!-- Demographic Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
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
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="education_level" class="block text-sm font-medium text-gray-700 mb-2">
                            Education Level <span class="text-red-500">*</span>
                        </label>
                            <select id="education_level" 
                                    name="education_level" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                    required>
                            <option value="">Select Education Level</option>
                            <option value="Elementary" {{ old('education_level', $resident->education_level) == 'Elementary' ? 'selected' : '' }}>Elementary</option>
                            <option value="High School" {{ old('education_level', $resident->education_level) == 'High School' ? 'selected' : '' }}>High School</option>
                            <option value="College" {{ old('education_level', $resident->education_level) == 'College' ? 'selected' : '' }}>College</option>
                            <option value="Post Graduate" {{ old('education_level', $resident->education_level) == 'Post Graduate' ? 'selected' : '' }}>Post Graduate</option>
                                <option value="No Formal Education" {{ old('education_level', $resident->education_level) == 'No Formal Education' ? 'selected' : '' }}>No Formal Education</option>
                        </select>
                    </div>
                    <div>
                        <label for="income_level" class="block text-sm font-medium text-gray-700 mb-2">
                            Income Level <span class="text-red-500">*</span>
                        </label>
                            <select id="income_level" 
                                    name="income_level" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                    required>
                            <option value="">Select Income Level</option>
                                <option value="Below Poverty Line" {{ old('income_level', $resident->income_level) == 'Below Poverty Line' ? 'selected' : '' }}>Below Poverty Line</option>
                                <option value="Low Income" {{ old('income_level', $resident->income_level) == 'Low Income' ? 'selected' : '' }}>Low Income</option>
                                <option value="Middle Income" {{ old('income_level', $resident->income_level) == 'Middle Income' ? 'selected' : '' }}>Middle Income</option>
                                <option value="Upper Middle Income" {{ old('income_level', $resident->income_level) == 'Upper Middle Income' ? 'selected' : '' }}>Upper Middle Income</option>
                                <option value="High Income" {{ old('income_level', $resident->income_level) == 'High Income' ? 'selected' : '' }}>High Income</option>
                        </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="employment_status" class="block text-sm font-medium text-gray-700 mb-2">
                            Employment Status <span class="text-red-500">*</span>
                        </label>
                            <select id="employment_status" 
                                    name="employment_status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                    required>
                            <option value="">Select Employment Status</option>
                                <option value="Employed" {{ old('employment_status', $resident->employment_status) == 'Employed' ? 'selected' : '' }}>Employed</option>
                            <option value="Unemployed" {{ old('employment_status', $resident->employment_status) == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                                <option value="Self-Employed" {{ old('employment_status', $resident->employment_status) == 'Self-Employed' ? 'selected' : '' }}>Self-Employed</option>
                                <option value="Student" {{ old('employment_status', $resident->employment_status) == 'Student' ? 'selected' : '' }}>Student</option>
                                <option value="Retired" {{ old('employment_status', $resident->employment_status) == 'Retired' ? 'selected' : '' }}>Retired</option>
                        </select>
                    </div>
                    <div>
                        <label for="health_status" class="block text-sm font-medium text-gray-700 mb-2">
                            Health Status <span class="text-red-500">*</span>
                        </label>
                            <select id="health_status" 
                                    name="health_status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                    required>
                            <option value="">Select Health Status</option>
                                <option value="Healthy" {{ old('health_status', $resident->health_status) == 'Healthy' ? 'selected' : '' }}>Healthy</option>
                                <option value="With Minor Health Issues" {{ old('health_status', $resident->health_status) == 'With Minor Health Issues' ? 'selected' : '' }}>With Minor Health Issues</option>
                                <option value="With Chronic Conditions" {{ old('health_status', $resident->health_status) == 'With Chronic Conditions' ? 'selected' : '' }}>With Chronic Conditions</option>
                                <option value="Disabled" {{ old('health_status', $resident->health_status) == 'Disabled' ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                </div>
                    <div class="mt-4">
                        <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Emergency Contact Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="emergency_contact_name" 
                               name="emergency_contact_name" 
                               value="{{ old('emergency_contact_name', $resident->emergency_contact_name) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                               required>
                    </div>
                    </div>

                <!-- Emergency Contact Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-phone-alt mr-2 text-red-600"></i>
                        Emergency Contact Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="emergency_contact_relationship" class="block text-sm font-medium text-gray-700 mb-2">
                                Relationship <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="emergency_contact_relationship" 
                                   name="emergency_contact_relationship" 
                                   value="{{ old('emergency_contact_relationship', $resident->emergency_contact_relationship) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                   placeholder="e.g., Spouse, Parent, Sibling" 
                                   required>
                        </div>
                    <div>
                        <label for="emergency_contact_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Number <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="emergency_contact_number" 
                               name="emergency_contact_number" 
                               value="{{ old('emergency_contact_number', $resident->emergency_contact_number) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                               placeholder="e.g., 9191234567"
                                   min="0" 
                                   pattern="[0-9]*" 
                                   inputmode="numeric" 
                                   required>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.residents') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                        class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
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
    });
</script>
@endsection 