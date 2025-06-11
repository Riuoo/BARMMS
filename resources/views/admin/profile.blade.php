@extends('admin.layout')

@section('title', 'Profile')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Profile Page</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-2">User Information</h2>
        <div class="mb-1"><strong>Name:</strong> {{ auth()->user()->name }}</div>
        <div class="mb-1"><strong>Email:</strong> {{ auth()->user()->email }}</div>
        <div class="mb-1"><strong>Address:</strong> {{ auth()->user()->address ?? 'N/A' }}</div>
    </div>

    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Update Email</h2>
        <form method="POST" action="{{ route('admin.profile.update') }}">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="email" class="block font-medium mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Email</button>
        </form>
    </div>

    <div>
        <h2 class="text-xl font-semibold mb-4">Change Password</h2>
        <form method="POST" action="{{ route('admin.profile.update') }}">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="password" class="block font-medium mb-1">New Password</label>
                <input type="password" id="password" name="password" autocomplete="new-password"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div class="mb-4">
                <label for="password_confirmation" class="block font-medium mb-1">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Change Password</button>
        </form>
    </div>
</div>
@endsection
