@extends('admin.main.layout')

@section('title', 'Add New Barangay Profile')

@section('content')
    <!-- Header Skeleton -->
    <div id="createHeaderSkeleton" class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8 mt-8">
        @include('components.loading.create-form-skeleton', ['type' => 'barangay-profile'])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="createContent" style="display: none;">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8 mt-8">
            <h1 class="text-3xl font-semibold text-gray-800 mb-2 text-center">Add New Barangay Profile</h1>

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

            <form action="{{ route('admin.barangay-profiles.store') }}" method="POST" class="space-y-6">
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>
                        <div>
                            <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number <span class="text-red-500">*</span></label>
                            <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="e.g., 09191234567" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                            <select id="role" name="role" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                <option value="">Select a role</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="captain" {{ old('role') == 'captain' ? 'selected' : '' }}>Barangay Captain</option>
                                <option value="councilor" {{ old('role') == 'councilor' ? 'selected' : '' }}>Barangay Councilor</option>
                                <option value="secretary" {{ old('role') == 'secretary' ? 'selected' : '' }}>Barangay Secretary</option>
                                <option value="treasurer" {{ old('role') == 'treasurer' ? 'selected' : '' }}>Barangay Treasurer</option>
                                <option value="nurse" {{ old('role') == 'nurse' ? 'selected' : '' }}>Barangay Nurse</option>
                                <option value="bhw" {{ old('role') == 'bhw' ? 'selected' : '' }}>Barangay Health Worker</option>
                                <option value="sk_chairman" {{ old('role') == 'sk_chairman' ? 'selected' : '' }}>SK Chairman</option>
                                <option value="sk_councilor" {{ old('role') == 'sk_councilor' ? 'selected' : '' }}>SK Councilor</option>
                                <option value="sk_treasurer" {{ old('role') == 'sk_treasurer' ? 'selected' : '' }}>SK Treasurer</option>
                                <option value="sk_secretary" {{ old('role') == 'sk_secretary' ? 'selected' : '' }}>SK Secretary</option>
                            </select>
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                            <input type="text" id="address" name="address" value="{{ old('address') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>
                    </div>
                </div>

                <!-- Credentials Section -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
                    <h3 class="text-lg font-semibold mb-2 text-gray-700">Credentials</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                            <input type="password" id="password" name="password" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between mt-8">
                    <a href="{{ route('admin.barangay-profiles') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Add Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Skeleton loading control for create barangay profile page
        document.addEventListener('DOMContentLoaded', function() {
            // Add 1 second delay to show skeleton effect
            setTimeout(() => {
                const headerSkeleton = document.getElementById('createHeaderSkeleton');
                const content = document.getElementById('createContent');
                if (headerSkeleton) headerSkeleton.style.display = 'none';
                if (content) content.style.display = 'block';
            }, 1000); // 1 second delay to show skeleton effect

            // Name field combination logic
            const firstNameInput = document.getElementById('first_name');
            const middleNameInput = document.getElementById('middle_name');
            const noMiddleCheckbox = document.getElementById('no_middle_name');
            const lastNameInput = document.getElementById('last_name');
            const suffixInput = document.getElementById('suffix');
            const fullNameInput = document.getElementById('name');

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
                    middleNameInput.disabled = true;
                    middleNameInput.value = '';
                } else {
                    middleNameInput.disabled = false;
                }
                updateFullName();
            }

            if (firstNameInput && lastNameInput) {
                firstNameInput.addEventListener('input', updateFullName);
                lastNameInput.addEventListener('input', updateFullName);
                if (middleNameInput) middleNameInput.addEventListener('input', updateFullName);
                if (suffixInput) suffixInput.addEventListener('change', updateFullName);
                updateFullName();
            }
            if (noMiddleCheckbox) {
                noMiddleCheckbox.addEventListener('change', handleNoMiddleToggle);
                handleNoMiddleToggle();
            }
        });
    </script>
@endsection