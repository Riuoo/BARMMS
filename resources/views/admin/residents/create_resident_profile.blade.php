@extends('admin.main.layout')

@section('title', 'Add New Resident')

@section('content')
    <!-- Header Skeleton -->
    <div id="createResidentHeaderSkeleton" class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8 mt-8 animate-pulse">
        <div class="h-8 w-48 bg-gray-200 rounded mx-auto mb-8"></div>
        <div class="space-y-6">
            <!-- Basic Information Section Skeleton -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
                <div class="h-6 w-40 bg-gray-200 rounded mb-4"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="h-4 w-24 bg-gray-200 rounded mb-1"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-24 bg-gray-200 rounded mb-1"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-36 bg-gray-200 rounded mb-1"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
            
            <!-- Personal Information Section Skeleton -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
                <div class="h-6 w-44 bg-gray-200 rounded mb-4"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="h-4 w-28 bg-gray-200 rounded mb-1"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="h-4 w-28 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
            
            <!-- Demographic Information Section Skeleton -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
                <div class="h-6 w-52 bg-gray-200 rounded mb-4"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="h-4 w-20 bg-gray-200 rounded mb-1"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <div class="h-4 w-36 bg-gray-200 rounded mb-1"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <div class="h-4 w-40 bg-gray-200 rounded mb-1"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="h-4 w-36 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
            
            <!-- Emergency Contact Section Skeleton -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
                <div class="h-6 w-44 bg-gray-200 rounded mb-4"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="h-4 w-40 bg-gray-200 rounded mb-1"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-36 bg-gray-200 rounded mb-1"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions Skeleton -->
            <div class="flex justify-between mt-8">
                <div class="h-10 w-24 bg-gray-200 rounded"></div>
                <div class="h-10 w-32 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="createResidentContent" style="display: none;">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8 mt-8">
            <h1 class="text-3xl font-semibold text-gray-800 mb-8 text-center">Add New Resident</h1>

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
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                            <p id="email-warning" class="mt-2 text-sm text-red-600 hidden"></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                            <select id="gender" name="gender" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div>
                            <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number <span class="text-red-500">*</span></label>
                            <input type="number" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="e.g., 9191234567" min="0" pattern="[0-9]*" inputmode="numeric" required>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                        <input type="text" id="address" name="address" value="{{ old('address') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Full Address (include Purok if applicable)" required>
                    </div>
                </div>

                <!-- Personal Information Section -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Personal Information</h3>
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
                    <div class="mt-4">
                        <label for="occupation" class="block text-sm font-medium text-gray-700 mb-1">Occupation</label>
                        <input type="text" id="occupation" name="occupation" value="{{ old('occupation') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="e.g., Teacher, Business Owner, Student">
                    </div>
                </div>

                <!-- Demographic Information Section -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Demographic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="age" class="block text-sm font-medium text-gray-700 mb-1">Age <span class="text-red-500">*</span></label>
                            <input type="number" id="age" name="age" value="{{ old('age') }}" min="1" max="120" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>
                        <div>
                            <label for="family_size" class="block text-sm font-medium text-gray-700 mb-1">Family Size <span class="text-red-500">*</span></label>
                            <input type="number" id="family_size" name="family_size" value="{{ old('family_size') }}" min="1" max="20" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label for="education_level" class="block text-sm font-medium text-gray-700 mb-1">Education Level <span class="text-red-500">*</span></label>
                            <select id="education_level" name="education_level" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                <option value="">Select Education Level</option>
                                <option value="Elementary" {{ old('education_level') == 'Elementary' ? 'selected' : '' }}>Elementary</option>
                                <option value="High School" {{ old('education_level') == 'High School' ? 'selected' : '' }}>High School</option>
                                <option value="College" {{ old('education_level') == 'College' ? 'selected' : '' }}>College</option>
                                <option value="Post Graduate" {{ old('education_level') == 'Post Graduate' ? 'selected' : '' }}>Post Graduate</option>
                                <option value="No Formal Education" {{ old('education_level') == 'No Formal Education' ? 'selected' : '' }}>No Formal Education</option>
                            </select>
                        </div>
                        <div>
                            <label for="income_level" class="block text-sm font-medium text-gray-700 mb-1">Income Level <span class="text-red-500">*</span></label>
                            <select id="income_level" name="income_level" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                <option value="">Select Income Level</option>
                                <option value="Below Poverty Line" {{ old('income_level') == 'Below Poverty Line' ? 'selected' : '' }}>Below Poverty Line</option>
                                <option value="Low Income" {{ old('income_level') == 'Low Income' ? 'selected' : '' }}>Low Income</option>
                                <option value="Middle Income" {{ old('income_level') == 'Middle Income' ? 'selected' : '' }}>Middle Income</option>
                                <option value="Upper Middle Income" {{ old('income_level') == 'Upper Middle Income' ? 'selected' : '' }}>Upper Middle Income</option>
                                <option value="High Income" {{ old('income_level') == 'High Income' ? 'selected' : '' }}>High Income</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label for="employment_status" class="block text-sm font-medium text-gray-700 mb-1">Employment Status <span class="text-red-500">*</span></label>
                            <select id="employment_status" name="employment_status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                <option value="">Select Employment Status</option>
                                <option value="Employed" {{ old('employment_status') == 'Employed' ? 'selected' : '' }}>Employed</option>
                                <option value="Unemployed" {{ old('employment_status') == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                                <option value="Self-Employed" {{ old('employment_status') == 'Self-Employed' ? 'selected' : '' }}>Self-Employed</option>
                                <option value="Student" {{ old('employment_status') == 'Student' ? 'selected' : '' }}>Student</option>
                                <option value="Retired" {{ old('employment_status') == 'Retired' ? 'selected' : '' }}>Retired</option>
                            </select>
                        </div>
                        <div>
                            <label for="health_status" class="block text-sm font-medium text-gray-700 mb-1">Health Status <span class="text-red-500">*</span></label>
                            <select id="health_status" name="health_status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                <option value="">Select Health Status</option>
                                <option value="Healthy" {{ old('health_status') == 'Healthy' ? 'selected' : '' }}>Healthy</option>
                                <option value="With Minor Health Issues" {{ old('health_status') == 'With Minor Health Issues' ? 'selected' : '' }}>With Minor Health Issues</option>
                                <option value="With Chronic Conditions" {{ old('health_status') == 'With Chronic Conditions' ? 'selected' : '' }}>With Chronic Conditions</option>
                                <option value="Disabled" {{ old('health_status') == 'Disabled' ? 'selected' : '' }}>Disabled</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Name <span class="text-red-500">*</span></label>
                        <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    </div>
                </div>

                <!-- Emergency Contact Section -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Emergency Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="emergency_contact_relationship" class="block text-sm font-medium text-gray-700 mb-1">Relationship <span class="text-red-500">*</span></label>
                            <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="e.g., Spouse, Parent, Sibling" required>
                        </div>
                        <div>
                            <label for="emergency_contact_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number <span class="text-red-500">*</span></label>
                            <input type="number" id="emergency_contact_number" name="emergency_contact_number" value="{{ old('emergency_contact_number') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="e.g., 9191234567" min="0" pattern="[0-9]*" inputmode="numeric" required>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between mt-8">
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
            // Add 1 second delay to show skeleton effect
            setTimeout(() => {
                const headerSkeleton = document.getElementById('createResidentHeaderSkeleton');
                const content = document.getElementById('createResidentContent');
                
                if (headerSkeleton) headerSkeleton.style.display = 'none';
                if (content) content.style.display = 'block';
            }, 1000); // 1 second delay to show skeleton effect
        });
    </script>
@endsection