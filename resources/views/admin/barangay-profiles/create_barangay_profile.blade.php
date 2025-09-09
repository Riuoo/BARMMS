@extends('admin.main.layout')

@section('title', 'Add New Barangay Profile')

@section('content')
    <!-- Header Skeleton -->
    <div id="createHeaderSkeleton" class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8 mt-8">
        @include('components.loading.skeleton-barangay-profile-form')
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="createContent" style="display: none;">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8 mt-8">
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

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>

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

                <!-- Form Actions -->
                <div class="flex justify-between mt-8">
                    <a href="{{ route('admin.barangay-profiles') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
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
        });
    </script>
@endsection