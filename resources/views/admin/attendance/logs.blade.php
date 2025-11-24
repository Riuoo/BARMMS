@extends('admin.main.layout')

@section('title', 'Attendance Logs')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Attendance Logs</h1>
                <p class="text-gray-600">View and manage attendance records</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <a href="{{ route('admin.attendance.scanner') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                    <i class="fas fa-qrcode mr-2"></i>
                    QR Scanner
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.attendance.logs') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Name, email, or guest name" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Type</label>
                <select name="event_type" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">All Types</option>
                    <option value="event" {{ $eventType == 'event' ? 'selected' : '' }}>Event</option>
                    <option value="health_center_activity" {{ $eventType == 'health_center_activity' ? 'selected' : '' }}>Health Activity</option>
                    <option value="medical_consultation" {{ $eventType == 'medical_consultation' ? 'selected' : '' }}>Medical Consultation</option>
                    <option value="medicine_claim" {{ $eventType == 'medicine_claim' ? 'selected' : '' }}>Medicine Claim</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Event</label>
                <select name="event_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">All Events</option>
                    @foreach($events as $evt)
                        <option value="{{ $evt->id }}" {{ $eventId == $evt->id ? 'selected' : '' }}>{{ $evt->event_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resident</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scanned At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scanned By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $log->resident ? $log->resident->name : $log->guest_name }}
                                    @if($log->guest_name)
                                        <span class="ml-2 px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">Guest</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $log->resident ? $log->resident->email : ($log->guest_contact ?? 'N/A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($log->event_id)
                                        @if($log->event_type === 'health_center_activity')
                                            {{ $log->healthCenterActivity->activity_name ?? 'Health Activity #' . $log->event_id }}
                                        @elseif($log->event_type === 'event')
                                            {{ $log->event->event_name ?? 'Event #' . $log->event_id }}
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $log->event_type)) }} #{{ $log->event_id }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst(str_replace('_', ' ', $log->event_type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->scanned_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->scanner->name ?? 'System' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No attendance logs found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection

