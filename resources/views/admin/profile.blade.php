@extends('admin.layout')

@section('title', 'Profile')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-6">Profile</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Display user info -->
    <div class="mb-8 space-y-4">
        <div>
            <label class="block font-semibold mb-1">Name</label>
            <input type="text" value="{{ $currentUser->name }}" disabled
                class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed" />
        </div>
        <div>
            <label class="block font-semibold mb-1">Email</label>
            <input type="email" value="{{ $currentUser->email }}" disabled
                class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed" />
        </div>
        <div>
            <label class="block font-semibold mb-1">Address</label>
            <input type="text" value="{{ $currentUser->address ?? 'N/A' }}" disabled
                class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed" />
        </div>
    </div>

    <!-- Update Email Form -->
    <form method="POST" action="{{ route('admin.profile.update') }}" class="mb-8">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="email" class="block font-semibold mb-1">Update Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $currentUser->email) }}" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" />
        </div>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
            Update Email
        </button>
    </form>

    <!-- Change Password Form -->
    <form method="POST" action="{{ route('admin.profile.update') }}">
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