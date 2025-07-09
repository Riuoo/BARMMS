@extends('resident.layout')

@section('title', 'Profile')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-6">Profile</h1>

    <!-- Display user info -->
    <div class="mb-8 space-y-4">
        <div>
            <label class="block font-semibold mb-1">Name</label>
            <input type="text" value="{{ $resident->name }}" disabled
                class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed" />
        </div>
        <div>
            <label class="block font-semibold mb-1">Email</label>
            <input type="email" value="{{ $resident->email }}" disabled
                class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed" />
        </div>
        <div>
            <label class="block font-semibold mb-1">Address</label>
            <input type="text" value="{{ $resident->address ?? 'N/A' }}" disabled
                class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed" />
        </div>
    </div>

    <!-- Change Password Form -->
    <form method="POST" action="{{ route('resident.profile.update') }}">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="password" class="block font-semibold mb-1">New Password (leave blank to keep current)</label>
            <input type="password" id="password" name="password" autocomplete="new-password"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" />
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="block font-semibold mb-1">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" />
        </div>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
            Change Password
        </button>
    </form>
</div>
@endsection