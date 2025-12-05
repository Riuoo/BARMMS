@extends('resident.layout')

@section('title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Consolidated Skeleton -->
    <div id="rpSkeleton">
        @include('components.loading.resident-profile-skeleton')
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="rpContent" style="display: none;">
    <!-- Header Section -->
    <div class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
                <p class="text-gray-600">Manage your personal information and account settings</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('resident.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
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

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Personal Information Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-2">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-semibold text-gray-900">Personal Information</h2>
                    <p class="text-sm text-gray-500">Your account details</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-gray-400"></i>
                        Full Name
                    </label>
                    <input type="text" 
                           value="{{ $resident->name }}" 
                           disabled
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" />
                    <p class="mt-1 text-xs text-gray-500">Your registered name in the system</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-gray-400"></i>
                        Email Address
                    </label>
                    <input type="email" 
                           value="{{ $resident->email }}" 
                           disabled
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" />
                    <p class="mt-1 text-xs text-gray-500">Your registered email address</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                        Address
                    </label>
                    <input type="text" 
                           value="{{ $resident->address ?? 'Not provided' }}" 
                           disabled
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" />
                    <p class="mt-1 text-xs text-gray-500">Your registered address</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-venus-mars mr-2 text-gray-400"></i>
                            Gender
                        </label>
                        <input type="text" 
                               value="{{ $resident->gender ?? 'Not provided' }}" 
                               disabled
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2 text-gray-400"></i>
                            Contact Number
                        </label>
                        <input type="text" 
                               value="{{ $resident->getMaskedContactNumber() }}" 
                               disabled
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-birthday-cake mr-2 text-gray-400"></i>
                            Birth Date
                        </label>
                        <input type="text" 
                               value="{{ $resident->birth_date ? $resident->birth_date->format('M d, Y') : 'Not provided' }}" 
                               disabled
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-2 text-gray-400"></i>
                            Age
                        </label>
                        <input type="text" 
                               value="{{ $resident->age ? $resident->age . ' years old' : 'Not provided' }}" 
                               disabled
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2 text-gray-400"></i>
                        Member Since
                    </label>
                    <input type="text" 
                           value="{{ $resident->created_at ? $resident->created_at->format('F d, Y') : 'N/A' }}" 
                           disabled
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" />
                    <p class="mt-1 text-xs text-gray-500">Date when you registered</p>
                </div>
            </div>
        </div>

        <!-- Change Password Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-2">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-lock text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-semibold text-gray-900">Change Password</h2>
                    <p class="text-sm text-gray-500">Update your account password</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Password update failed</h3>
                            <div class="mt-1 text-sm text-red-700">
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

            <form method="POST" action="{{ route('resident.profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2 text-gray-400"></i>
                        New Password
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           autocomplete="new-password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                           placeholder="Enter new password" />
                    <p class="mt-1 text-xs text-gray-500">Leave blank to keep your current password</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2 text-gray-400"></i>
                        Confirm New Password
                    </label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           autocomplete="new-password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                           placeholder="Confirm new password" />
                    <p class="mt-1 text-xs text-gray-500">Re-enter your new password to confirm</p>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Professional Information Card -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center mb-2">
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                <i class="fas fa-briefcase text-orange-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-xl font-semibold text-gray-900">Professional Information</h2>
                <p class="text-sm text-gray-500">Your work and education details</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-briefcase mr-2 text-gray-400"></i>
                        Occupation
                    </label>
                    <input type="text" 
                           value="{{ $resident->occupation ?? 'Not provided' }}" 
                           disabled
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user-tie mr-2 text-gray-400"></i>
                        Employment Status
                    </label>
                    <input type="text" 
                           value="{{ $resident->employment_status ?? 'Not provided' }}" 
                           disabled
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" />
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-graduation-cap mr-2 text-gray-400"></i>
                        Education Level
                    </label>
                    <input type="text" 
                           value="{{ $resident->education_level ?? 'Not provided' }}" 
                           disabled
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-dollar-sign mr-2 text-gray-400"></i>
                        Income Level
                    </label>
                    <input type="text" 
                           value="{{ $resident->income_level ?? 'Not provided' }}" 
                           disabled
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" />
                </div>
            </div>
        </div>
    </div>

    <!-- Emergency Contact Card -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center mb-2">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-phone-alt text-red-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-xl font-semibold text-gray-900">Emergency Contact</h2>
                <p class="text-sm text-gray-500">Your emergency contact information</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user mr-2 text-gray-400"></i>
                    Contact Name
                </label>
                <input type="text" 
                       value="{{ $resident->getMaskedEmergencyContactName() }}" 
                       disabled
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-phone mr-2 text-gray-400"></i>
                    Contact Number
                </label>
                <input type="text" 
                       value="{{ $resident->getMaskedEmergencyContactNumber() }}" 
                       disabled
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-heart mr-2 text-gray-400"></i>
                    Relationship
                </label>
                <input type="text" 
                       value="{{ $resident->getMaskedEmergencyContactRelationship() }}" 
                       disabled
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" />
            </div>
        </div>
    </div>

    <!-- QR Code Card -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-qrcode text-green-600 text-xl"></i>
            </div>
            <div class="ml-4 flex-1">
                <h2 class="text-xl font-semibold text-gray-900">QR Code Identity</h2>
                <p class="text-sm text-gray-500">Your unique QR code for attendance and check-in</p>
            </div>
            <div>
                <a href="{{ route('resident.qr-code') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                    <i class="fas fa-qrcode mr-2"></i>
                    View QR Code
                </a>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-sm text-green-800">
                <i class="fas fa-info-circle mr-2"></i>
                Use your QR code for quick check-in at barangay events, health center visits, and other activities.
            </p>
        </div>
    </div>

    <!-- Account Information Card -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center mb-2">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-info-circle text-purple-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-xl font-semibold text-gray-900">Account Information</h2>
                <p class="text-sm text-gray-500">Important details about your account</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Account Status</h3>
                <div class="flex items-center">
                    <div class="w-3 h-3 {{ $resident->active ? 'bg-green-400' : 'bg-red-400' }} rounded-full mr-2"></div>
                    <span class="text-sm text-gray-600">{{ $resident->active ? 'Active' : 'Inactive' }}</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">Your account status in the system</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Email Verified</h3>
                <div class="flex items-center">
                    <i class="fas {{ $resident->email_verified_at ? 'fa-check-circle text-green-400' : 'fa-times-circle text-red-400' }} mr-2"></i>
                    <span class="text-sm text-gray-600">{{ $resident->email_verified_at ? 'Verified' : 'Not Verified' }}</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">{{ $resident->email_verified_at ? 'Email verified on ' . $resident->email_verified_at->format('M d, Y') : 'Please verify your email address' }}</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Account Type</h3>
                <div class="flex items-center">
                    <i class="fas fa-user-tag text-gray-400 mr-2"></i>
                    <span class="text-sm text-gray-600">{{ ucfirst($resident->role ?? 'Resident') }}</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">Your role in the barangay system</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Family Size</h3>
                <div class="flex items-center">
                    <i class="fas fa-users text-gray-400 mr-2"></i>
                    <span class="text-sm text-gray-600">{{ $resident->family_size ?? 'Not specified' }}</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">Number of family members</p>
            </div>
        </div>
    </div>

    <!-- Two-Factor Authentication Card -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center mb-6">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-shield-alt text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-xl font-semibold text-gray-900">Two-Factor Authentication</h2>
                <p class="text-sm text-gray-500">Add an extra layer of security to your account</p>
            </div>
        </div>

        @if($resident->hasTwoFactorEnabled())
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-400 mr-2"></i>
                    <span class="text-sm font-medium text-green-800">Two-factor authentication is enabled</span>
                </div>
                <p class="text-sm text-green-700 mt-2">
                    Your account is protected with two-factor authentication. You'll need to enter a verification code from your authenticator app when logging in.
                </p>
                @if($resident->two_factor_enabled_at)
                    <p class="text-xs text-green-600 mt-1">
                        Enabled on {{ $resident->two_factor_enabled_at->format('F d, Y') }}
                    </p>
                @endif
            </div>

            <form method="POST" action="{{ route('2fa.disable') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="disable_password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2 text-gray-400"></i>
                        Enter Password to Disable
                    </label>
                    <input type="password" 
                           id="disable_password" 
                           name="password" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200" 
                           placeholder="Enter your password" />
                    <p class="mt-1 text-xs text-gray-500">You must enter your password to disable two-factor authentication</p>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Disable Two-Factor Authentication
                    </button>
                </div>
            </form>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                    <span class="text-sm font-medium text-yellow-800">Two-factor authentication is not enabled</span>
                </div>
                <p class="text-sm text-yellow-700 mt-2">
                    Enable two-factor authentication to add an extra layer of security to your account. You'll need an authenticator app (like Google Authenticator or Authy) to generate verification codes.
                </p>
            </div>

            <div class="pt-4">
                <a href="{{ route('2fa.setup') }}" 
                   class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Enable Two-Factor Authentication
                </a>
            </div>
        @endif
    </div>

    <!-- Security Information -->
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-shield-alt text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Security Tips</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Use a strong password with at least 8 characters</li>
                        <li>Include a mix of letters, numbers, and special characters</li>
                        <li>Enable two-factor authentication for extra security</li>
                        <li>Never share your password with anyone</li>
                        <li>Log out when using shared computers</li>
                        <li>Contact barangay officials if you suspect unauthorized access</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const sk = document.getElementById('residentProfileSkeleton');
        const content = document.getElementById('rpContent');
        if (sk) sk.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush