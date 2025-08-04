@extends('admin.main.layout')

@section('title', 'Add New Barangay Profile')

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8 mt-8">
        <h1 class="text-3xl font-semibold text-gray-800 mb-8 text-center">Add New Barangay Profile</h1>

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
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <input type="text" id="role" name="role" value="{{ old('role') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input type="text" id="address" name="address" value="{{ old('address') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
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
@endsection