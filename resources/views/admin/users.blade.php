@extends('admin.layout')

@section('title', 'Active Users')

@section('content')
    <div class="max-w-7xl mx-auto bg-white rounded shadow p-4 sm:p-6 lg:p-8 overflow-x-auto">
        <h1 class="text-2xl font-bold mb-6">Active Users</h1>
        <!-- Table for larger screens -->
        <table class="min-w-full border border-gray-300 table-auto hidden sm:table">
            <thead>
                <tr class="bg-green-600 text-white">
                    <th class="p-2 sm:p-3 text-left">ID</th>
                    <th class="p-2 sm:p-3 text-left">Name</th>
                    <th class="p-2 sm:p-3 text-left">Email</th>
                    <th class="p-2 sm:p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr class="border-t border-gray-300 hover:bg-gray-100">
                    <td class="p-2 sm:p-3">{{ $user->id }}</td>
                    <td class="p-2 sm:p-3">{{ $user->name }}</td>
                    <td class="p-2 sm:p-3">{{ $user->email }}</td>
                    <td class="p-2 sm:p-3 space-x-2 whitespace-nowrap">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">Edit</a>
                        <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if ($users->isEmpty())
                <tr>
                    <td colspan="4" class="p-3 text-center text-gray-500">No active users found.</td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- Card layout for mobile -->
        <div class="sm:hidden space-y-4">
            @foreach ($users as $user)
            <div class="border border-gray-300 rounded p-4 shadow hover:shadow-lg transition">
                <p><span class="font-semibold">ID:</span> {{ $user->id }}</p>
                <p><span class="font-semibold">Name:</span> {{ $user->name }}</p>
                <p><span class="font-semibold">Email:</span> {{ $user->email }}</p>
                <div class="mt-2 space-x-2">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">Edit</a>
                    <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition">Delete</button>
                    </form>
                </div>
            </div>
            @endforeach
            @if ($users->isEmpty())
            <p class="text-center text-gray-500">No active users found.</p>
            @endif
        </div>
    </div>
@endsection
