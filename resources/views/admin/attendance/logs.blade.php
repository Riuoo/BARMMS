@extends('admin.main.layout')

@section('title', 'Attendance Logs')

@section('content')
@include('components.loading.attendance-logs-skeleton')

<div class="max-w-7xl mx-auto pt-2" id="logsContent" style="display: none;">
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
                    <option value="event" {{ $eventType == 'event' ? 'selected' : '' }}>Barangay Activity / Project</option>
                    <option value="health_center_activity" {{ $eventType == 'health_center_activity' ? 'selected' : '' }}>Health Activity</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Barangay Activity / Project</label>
                <select name="event_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">All Activities/Projects</option>
                    @foreach($events as $evt)
                        <option value="{{ $evt->id }}" {{ $eventId == $evt->id ? 'selected' : '' }}>{{ $evt->title }}</option>
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
                                            {{ $log->event->title ?? 'Activity/Project #' . $log->event_id }}
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hide skeleton and show content after a minimum display time
    const skeleton = document.querySelector('[data-skeleton]');
    const content = document.getElementById('logsContent');
    
    // Minimum skeleton display time (1000ms) for better UX
    const minDisplayTime = 1000;
    const startTime = Date.now();
    
    function showContent() {
        const elapsed = Date.now() - startTime;
        const remaining = Math.max(0, minDisplayTime - elapsed);
        
        setTimeout(function() {
            if (skeleton) {
                skeleton.style.display = 'none';
            }
            if (content) {
                content.style.display = 'block';
            }
        }, remaining);
    }
    
    // Wait for page to be fully ready
    if (document.readyState === 'complete') {
        showContent();
    } else {
        window.addEventListener('load', showContent);
        // Fallback: show content after max 1 second even if load event doesn't fire
        setTimeout(showContent, 1000);
    }
});
</script>
@endpush
@endsection

