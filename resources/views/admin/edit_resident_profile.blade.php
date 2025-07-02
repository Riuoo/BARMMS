@extends('admin.layout')

@section('title', 'Edit Resident Profile')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Edit Resident Profile</h1>

        <form action="{{ route('admin.residents.update', $resident->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block font-medium mb-1">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $resident->name) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="email" class="block font-medium mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $resident->email) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="role" class="block font-medium mb-1">Role</label>
                <input type="text" id="role" name="role" value="{{ old('role', $resident->role) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="address" class="block font-medium mb-1">Address</label>
                <input type="text" id="address" name="address" value="{{ old('address', $resident->address) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block font-medium mb-1">Password (leave blank to keep current)</label>
                <input type="password" id="password" name="password" class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block font-medium mb-1">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.residents') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Update</button>
            </div>
        </form>
    </div>
@endsection