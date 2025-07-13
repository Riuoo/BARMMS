@extends('admin.modals.layout')

@section('title', 'Edit User')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users') }}" class="inline-flex items-center text-gray-700 hover:text-gray-900 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Users
    </a>
</div>

<h1 class="text-2xl font-bold mb-4">Edit User</h1>

<form method="POST" action="{{ route('admin.users.update', $user->id) }}">
    @csrf
    @method('PUT')

    <div class="mb-4">
        <label for="name" class="block text-gray-700 font-semibold mb-2">Name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
    </div>

    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Update User</button>
</form>
@endsection
