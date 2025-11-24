@extends('admin.main.layout')

@section('title', 'Event Details')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <div class="mb-6">
        <a href="{{ route('admin.events.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Events
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h1 class="text-3xl font-bold mb-4">{{ $event->event_name }}</h1>
                <div class="space-y-4">
                    <div>
                        <span class="text-gray-500">Type:</span>
                        <span class="ml-2 font-medium">{{ $event->event_type }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Date:</span>
                        <span class="ml-2 font-medium">{{ $event->event_date->format('F d, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Location:</span>
                        <span class="ml-2 font-medium">{{ $event->location }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Status:</span>
                        <span class="ml-2 px-2 py-1 rounded-full {{ $event->status === 'Completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $event->status }}
                        </span>
                    </div>
                    @if($event->description)
                        <div>
                            <span class="text-gray-500">Description:</span>
                            <p class="mt-2">{{ $event->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Attendance List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
                <h2 class="text-xl font-semibold mb-4">Attendance ({{ $attendanceCount }})</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Resident</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scanned At</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($event->attendanceLogs as $log)
                                <tr>
                                    <td class="px-6 py-4">
                                        @if($log->resident_id)
                                            <div class="text-sm font-medium">{{ $log->resident->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $log->resident->email }}</div>
                                        @else
                                            <div class="text-sm font-medium">{{ $log->guest_name }} <span class="ml-2 px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded">Guest</span></div>
                                            <div class="text-sm text-gray-500">{{ $log->guest_contact ?? 'N/A' }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $log->scanned_at->format('M d, Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-4 text-center text-gray-500">No attendance yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.attendance.scanner', ['event_id' => $event->id, 'event_type' => 'event']) }}" 
                       class="block w-full px-4 py-2 bg-green-600 text-white text-center rounded-md hover:bg-green-700">
                        <i class="fas fa-qrcode mr-2"></i>Scan QR Codes
                    </a>
                    <a href="{{ route('admin.attendance.report', ['event_id' => $event->id, 'event_type' => 'event', 'format' => 'pdf']) }}" 
                       class="block w-full px-4 py-2 bg-blue-600 text-white text-center rounded-md hover:bg-blue-700">
                        <i class="fas fa-file-pdf mr-2"></i>Download Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

