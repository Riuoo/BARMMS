@extends('admin.modals.layout')

@section('title', 'Medical Logbooks')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Medical Logbooks</h1>
        <a href="{{ route('admin.medical-logbooks.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Add Consultation
        </a>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form action="{{ route('admin.medical-logbooks.search') }}" method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="query" value="{{ $query ?? '' }}" 
                       placeholder="Search by patient name, chief complaint, or diagnosis..." 
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-search mr-2"></i>Search
            </button>
        </form>
    </div>

    <!-- Medical Logbooks Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Patient Info
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Consultation Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date & Time
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
                    @forelse($medicalLogbooks as $logbook)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                        <i class="fas fa-stethoscope text-purple-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $logbook->resident->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $logbook->resident->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <div class="font-medium">{{ $logbook->consultation_type }}</div>
                                <div class="text-gray-500">{{ Str::limit($logbook->chief_complaint, 50) }}</div>
                                @if($logbook->diagnosis)
                                <div class="text-xs text-gray-400 mt-1">
                                    Diagnosis: {{ Str::limit($logbook->diagnosis, 40) }}
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $logbook->consultation_date->format('M d, Y') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $logbook->consultation_time->format('g:i A') }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $logbook->attending_health_worker }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'Completed' => 'bg-green-100 text-green-800',
                                    'Pending' => 'bg-yellow-100 text-yellow-800',
                                    'Referred' => 'bg-blue-100 text-blue-800',
                                    'Cancelled' => 'bg-red-100 text-red-800'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$logbook->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $logbook->status }}
                            </span>
                            @if($logbook->follow_up_date)
                            <div class="text-xs text-gray-500 mt-1">
                                Follow-up: {{ $logbook->follow_up_date->format('M d, Y') }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.medical-logbooks.show', $logbook->id) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.medical-logbooks.edit', $logbook->id) }}" 
                                   class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.medical-logbooks.destroy', $logbook->id) }}" 
                                      method="POST" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this consultation record?')">
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
                                <i class="fas fa-stethoscope text-4xl text-gray-300 mb-2"></i>
                                <p>No medical consultation records found.</p>
                                <a href="{{ route('admin.medical-logbooks.create') }}" class="text-blue-600 hover:text-blue-800 mt-2">
                                    Add your first consultation record
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($medicalLogbooks->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $medicalLogbooks->links() }}
        </div>
        @endif
    </div>
</div>
@endsection 