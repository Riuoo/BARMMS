@extends('admin.layout')

@section('title', 'All Notifications')

@section('content')
<div class="max-w-7xl mx-auto bg-white rounded shadow p-4 sm:p-6 lg:p-8 overflow-x-auto">
    <h1 class="text-2xl font-bold mb-6">All Notifications</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-4 flex justify-end">
        <form action="{{ route('admin.notifications.mark-all-as-read') }}" method="POST">
            @csrf
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Mark All as Read</button>
        </form>
    </div>

    @if($notifications->isEmpty())
        <p class="text-center text-gray-500">No notifications to display.</p>
    @else
        <table class="min-w-full border border-gray-300 table-auto">
            <thead>
                <tr class="bg-green-600 text-white">
                    <th class="p-2 sm:p-3 text-left">Message</th>
                    <th class="p-2 sm:p-3 text-left">Date Created</th>
                    <th class="p-2 sm:p-3 text-left">Status</th>
                    <th class="p-2 sm:p-3 text-left">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notifications as $notification)
                <tr class="border-t border-gray-300 {{ $notification->is_read ? 'text-gray-500' : 'font-semibold' }}">
                    <td class="p-2 sm:p-3">
                        <h3>{{ $notification->message }}</h3>
                    </td>
                    <td class="p-2 sm:p-3">
                        <small>{{ $notification->created_at->format('Y-m-d H:i') }}</small>
                    </td>
                    <td class="p-2 sm:p-3">
                        {{ $notification->is_read ? 'Read' : 'Unread' }}
                    </td>
                    <td class="p-2 sm:p-3">
                        {{-- You can add individual mark as read/unread buttons here if needed --}}
                        {{-- For now, clicking the message link implies it's viewed --}}
                        <a href="{{ $notification->link }}" class="text-teal-600 hover:underline">View Details</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection