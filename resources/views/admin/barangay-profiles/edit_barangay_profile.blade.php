@extends('admin.main.layout')

@section('title', 'Edit Barangay Profile')

@section('content')
<!-- Header Skeleton -->
<div id="editHeaderSkeleton" class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    @include('components.loading.skeleton-barangay-profile-edit-header')
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        @include('components.loading.skeleton-barangay-profile-form')
    </div>
</div>

<!-- Real Content (hidden initially) -->
<div id="editContent" style="display: none;">
    <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
        <!-- Header Section -->
        <div class="mb-2">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Barangay Profile</h1>
                    <p class="text-gray-600">Update barangay official information and credentials</p>
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
            <form action="{{ route('admin.barangay-profiles.update', $barangayProfile->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        <i class="fas fa-user-tie mr-2 text-green-600"></i>
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
                                   value="{{ old('name', $barangayProfile->name) }}" 
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
                                   value="{{ old('email', $barangayProfile->email) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" 
                                   readonly>
                            <p class="mt-1 text-sm text-gray-500">Contact email cannot be changed</p>
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                Official Role <span class="text-gray-500">(Read Only)</span>
                            </label>
                            <input type="text" 
                                   id="role" 
                                   name="role" 
                                   value="{{ old('role', $barangayProfile->role) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed" 
                                   readonly>
                            <p class="mt-1 text-sm text-gray-500">Official position is fixed</p>
                        </div>
                    </div>
                </div>

                <!-- Credentials Section -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        <i class="fas fa-key mr-2 text-blue-600"></i>
                        Credentials
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                New Password <span class="text-gray-500">(Optional)</span>
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                   placeholder="Leave blank to keep current password">
                            <p class="mt-1 text-sm text-gray-500">Only fill if you want to change the password</p>
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm New Password <span class="text-gray-500">(Optional)</span>
                            </label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                   placeholder="Confirm the new password">
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
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Skeleton loading control for edit barangay profile page
    document.addEventListener('DOMContentLoaded', function() {
        // Add 1 second delay to show skeleton effect
        setTimeout(() => {
            const headerSkeleton = document.getElementById('editHeaderSkeleton');
            const content = document.getElementById('editContent');
            
            if (headerSkeleton) headerSkeleton.style.display = 'none';
            if (content) content.style.display = 'block';
        }, 1000); // 1 second delay to show skeleton effect
    });
</script>
@endsection 