@extends('admin.main.layout')

@section('title', 'Create Event')

@section('content')
<div class="max-w-4xl mx-auto pt-2">
    <div class="mb-6">
        <a href="{{ route('admin.events.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Events
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-2xl font-bold mb-4">Create New Event</h1>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Note:</strong> This is for general barangay events (seminars, meetings, programs, relief distribution) with QR attendance tracking. 
                For health-related activities (vaccination, health education, medical missions), please use 
                <a href="{{ route('admin.health-center-activities.create') }}" class="underline font-medium">Health Center Activities</a> instead.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.events.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Name *</label>
                    <input type="text" name="event_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Event Type *</label>
                        <select name="event_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="Seminar">Seminar</option>
                            <option value="Barangay Program">Barangay Program</option>
                            <option value="Meeting">Meeting</option>
                            <option value="Relief Distribution">Relief Distribution</option>
                            <option value="Community Assembly">Community Assembly</option>
                            <option value="Training Workshop">Training Workshop</option>
                            <option value="Cultural Event">Cultural Event</option>
                            <option value="Sports Event">Sports Event</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="Planned">Planned</option>
                            <option value="Ongoing">Ongoing</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Event Date *</label>
                        <input type="date" name="event_date" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                        <input type="time" name="start_time" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                        <input type="time" name="end_time" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                    <input type="text" name="location" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="qr_attendance_enabled" value="1" checked>
                        <span class="ml-2 text-sm text-gray-700">Enable QR Code Attendance</span>
                    </label>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Create Event
                    </button>
                    <a href="{{ route('admin.events.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

