@extends('admin.main.layout')

@section('title', 'Events Management')

@section('content')
@include('components.loading.events-index-skeleton')

<div class="max-w-7xl mx-auto pt-2" id="eventsContent" style="display: none;">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Events Management</h1>
                <p class="text-gray-600">Manage general barangay events with QR attendance tracking</p>
                <p class="text-sm text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    For health activities, use <a href="{{ route('admin.health-center-activities.index') }}" class="text-blue-600 hover:underline">Health Center Activities</a>
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.events.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i>
                    Create Event
                </a>
            </div>
        </div>
    </div>

    <!-- Events Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($events as $event)
                        <tr>
                            <td class="px-6 py-4">{{ $event->event_name }}</td>
                            <td class="px-6 py-4">{{ $event->event_type }}</td>
                            <td class="px-6 py-4">{{ $event->event_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $event->status === 'Completed' ? 'bg-green-100 text-green-800' : ($event->status === 'Ongoing' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ $event->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.events.show', $event->id) }}" class="text-blue-600 hover:text-blue-800">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No events found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bg-white px-4 py-3 border-t border-gray-200">
            {{ $events->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hide skeleton and show content after a minimum display time
    const skeleton = document.querySelector('[data-skeleton]');
    const content = document.getElementById('eventsContent');
    
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

