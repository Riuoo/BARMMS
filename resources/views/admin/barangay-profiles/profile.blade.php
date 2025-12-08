@extends('admin.main.layout')

@section('title', 'Profile')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Consolidated Profile Skeleton -->
    <div id="adminProfileSkeleton">
        @include('components.loading.profile-skeleton')
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="adminProfileContent" style="display: none;">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">My Profile</h1>
                <p class="text-gray-600">Manage your personal information and account settings</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
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
            <div class="flex items-center mb-6">
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
                           value="{{ $currentUser->full_name }}" 
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
                           value="{{ $currentUser->email }}" 
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
                           value="{{ $currentUser->address ?? 'Not provided' }}" 
                           disabled
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" />
                    <p class="mt-1 text-xs text-gray-500">Your registered address</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2 text-gray-400"></i>
                        Member Since
                    </label>
                    <input type="text" 
                           value="{{ $currentUser->created_at ? $currentUser->created_at->format('F d, Y') : 'N/A' }}" 
                           disabled
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" />
                    <p class="mt-1 text-xs text-gray-500">Date when you registered</p>
                </div>
            </div>
        </div>

        <!-- Change Password Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
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

            <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4">
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

        @php
            $has2FA = $currentUser->hasTwoFactorEnabled();
        @endphp

        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3 {{ $has2FA ? 'bg-green-100' : 'bg-gray-200' }}">
                        <i class="fas {{ $has2FA ? 'fa-check-circle text-green-600' : 'fa-times-circle text-gray-400' }}"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">
                            {{ $has2FA ? '2FA is Enabled' : '2FA is Disabled' }}
                        </h3>
                        <p class="text-xs text-gray-500">
                            {{ $has2FA 
                                ? 'Your account is protected with two-factor authentication' 
                                : 'Enable 2FA to secure your account with an additional verification step' }}
                        </p>
                    </div>
                </div>
                <div>
                    @if($has2FA)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Protected
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Not Protected
                        </span>
                    @endif
                </div>
            </div>

            @if($has2FA)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-green-600 mt-1 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm text-green-800">
                                <strong>2FA Enabled:</strong> Your account is protected. You'll be asked for a verification code when logging in from untrusted devices or performing sensitive operations.
                            </p>
                            @if($currentUser->two_factor_enabled_at)
                                <p class="text-xs text-green-700 mt-2">
                                    Enabled on: {{ $currentUser->two_factor_enabled_at->format('F d, Y') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('2fa.disable') }}" class="mt-4" onsubmit="return confirm('Are you sure you want to disable two-factor authentication? This will make your account less secure.');">
                    @csrf
                    <div class="mb-4">
                        <label for="disable_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password to Disable 2FA
                        </label>
                        <input 
                            type="password" 
                            id="disable_password" 
                            name="password" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                            placeholder="Enter your password"
                            required
                        >
                    </div>
                    <button 
                        type="submit" 
                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200"
                    >
                        <i class="fas fa-times mr-2"></i>
                        Disable Two-Factor Authentication
                    </button>
                </form>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm text-yellow-800">
                                <strong>2FA Not Enabled:</strong> Your account is not protected with two-factor authentication. We recommend enabling it for better security.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a 
                        href="{{ route('2fa.setup') }}" 
                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200"
                    >
                        <i class="fas fa-shield-alt mr-2"></i>
                        Enable Two-Factor Authentication
                    </a>
                </div>
            @endif

            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-blue-900 mb-2">
                    <i class="fas fa-question-circle mr-2"></i>
                    How does 2FA work?
                </h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• You'll need an authenticator app (Google Authenticator, Authy, etc.)</li>
                    <li>• When logging in, you'll enter a 6-digit code from the app</li>
                    <li>• You can "remember this device" to skip 2FA for 30 days</li>
                    <li>• Sensitive operations always require 2FA verification</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Account Information Card -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center mb-6">
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
                    <div class="w-3 h-3 bg-green-400 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-600">Active</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">Your account is currently active and in good standing</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Last Login</h3>
                <div class="flex items-center">
                    <i class="fas fa-clock text-gray-400 mr-2"></i>
                    <span class="text-sm text-gray-600">{{ now()->format('M d, Y H:i') }}</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">Your most recent login activity</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Password Last Changed</h3>
                <div class="flex items-center">
                    <i class="fas fa-calendar text-gray-400 mr-2"></i>
                    <span class="text-sm text-gray-600">Not available</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">When you last updated your password</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Account Type</h3>
                <div class="flex items-center">
                    <i class="fas fa-user-tag text-gray-400 mr-2"></i>
                    <span class="text-sm text-gray-600">Administrator</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">Your role in the barangay system</p>
            </div>
        </div>
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
                        <li>Never share your password with anyone</li>
                        <li>Log out when using shared computers</li>
                        <li>Contact system administrators if you suspect unauthorized access</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const sk = document.getElementById('adminProfileSkeleton');
        if (sk) sk.style.display = 'none';
        const content = document.getElementById('adminProfileContent');
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection