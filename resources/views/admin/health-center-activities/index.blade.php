@extends('admin.main.layout')

@section('title', 'Health Center Activities')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Health Center Activities</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.health-center-activities.upcoming') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-calendar-alt mr-2"></i>Upcoming
            </a>
            <a href="{{ route('admin.health-center-activities.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Add Activity
            </a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form action="{{ route('admin.health-center-activities.search') }}" method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="query" value="{{ $query ?? '' }}" 
                       placeholder="Search by activity name, type, or organizer..." 
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-search mr-2"></i>Search
            </button>
        </form>
    </div>

    <!-- Health Center Activities Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Activity Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date & Time
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Location & Organizer
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($activities as $activity)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <div class="font-medium text-lg">{{ $activity->activity_name }}</div>
                                <div class="text-gray-500">{{ $activity->activity_type }}</div>
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ Str::limit($activity->description, 80) }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $activity->activity_date->format('M d, Y') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $activity->start_time->format('g:i A') }} - {{ $activity->end_time->format('g:i A') }}
                            </div>
                            @if($activity->duration)
                            <div class="text-xs text-gray-400">
                                Duration: {{ $activity->duration }} hours
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <div class="font-medium">{{ $activity->location }}</div>
                                <div class="text-gray-500">{{ $activity->organizer }}</div>
                                @if($activity->target_participants)
                                <div class="text-xs text-gray-400 mt-1">
                                    Target: {{ $activity->target_participants }} participants
                                    @if($activity->actual_participants)
                                        ({{ $activity->actual_participants }} attended)
                                    @endif
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'Planned' => 'bg-blue-100 text-blue-800',
                                    'Ongoing' => 'bg-yellow-100 text-yellow-800',
                                    'Completed' => 'bg-green-100 text-green-800',
                                    'Cancelled' => 'bg-red-100 text-red-800'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$activity->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $activity->status }}
                            </span>
                            @if($activity->budget)
                            <div class="text-xs text-gray-500 mt-1">
                                Budget: ₱{{ number_format($activity->budget, 2) }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.health-center-activities.show', $activity->id) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.health-center-activities.edit', $activity->id) }}" 
                                   class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.health-center-activities.destroy', $activity->id) }}" 
                                      method="POST" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this activity?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-calendar-alt text-4xl text-gray-300 mb-2"></i>
                                <p>No health center activities found.</p>
                                <a href="{{ route('admin.health-center-activities.create') }}" class="text-blue-600 hover:text-blue-800 mt-2">
                                    Add your first health activity
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($activities->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $activities->links() }}
        </div>
        @endif
    </div>
</div>
@endsection 