@extends('admin.modals.layout')

@section('title', 'Medical Logbooks')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-6 md:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Medical Logbooks</h1>
                <p class="text-sm md:text-base text-gray-600">Manage medical consultations and logbook entries</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <a href="{{ route('admin.medical-logbooks.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Add Consultation
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Enhanced Search & Filters -->
    <form method="GET" action="{{ route('admin.medical-logbooks.index') }}" class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" placeholder="Search records..." 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500" 
                        value="{{ request('query') }}">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                    <option value="">All Statuses</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Referred" {{ request('status') == 'Referred' ? 'selected' : '' }}>Referred</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Filter
                </button>
                <a href="{{ route('admin.medical-logbooks.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <!-- Total Consultations -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-stethoscope"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Consultations</p>
                    <p class="text-2xl font-semibold">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Completed</p>
                    <p class="text-2xl font-semibold">{{ $stats['completed'] }}</p>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-2xl font-semibold">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>

        <!-- Referred -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-share-alt"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Referred</p>
                    <p class="text-2xl font-semibold">{{ $stats['referred'] }}</p>
                </div>
            </div>
        </div>

        <!-- Last 30 Days -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Last 30 Days</p>
                    <p class="text-2xl font-semibold">{{ $stats['last_month'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical Logbooks List -->
    @if($medicalLogbooks->isEmpty())
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-stethoscope text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No medical consultation records found</h3>
            <p class="text-gray-500">Get started by adding the first consultation record.</p>
            <div class="mt-6">
                <a href="{{ route('admin.medical-logbooks.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Add First Consultation
                </a>
            </div>
        </div>
    @else
        <!-- Desktop Table -->
        <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-user mr-2"></i>
                                    Patient Info
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-stethoscope mr-2"></i>
                                    Consultation Details
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-2"></i>
                                    Date & Time
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Status
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-cogs mr-2"></i>
                                    Actions
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($medicalLogbooks as $logbook)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="items-center gap-2">
                                        <div class="text-sm font-medium text-gray-900">{{ $logbook->resident->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $logbook->resident->email }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <div class="font-medium">{{ $logbook->consultation_type }}</div>
                                    <div class="text-gray-500">{{ Str::limit($logbook->chief_complaint, 50) }}</div>
                                    @if($logbook->diagnosis)
                                    <div class="text-xs text-gray-400 mt-1">Diagnosis: {{ Str::limit($logbook->diagnosis, 40) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $logbook->consultation_date->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $logbook->consultation_time->format('g:i A') }}</div>
                                <div class="text-xs text-gray-400">{{ $logbook->attending_health_worker }}</div>
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
                                <div class="text-xs text-gray-500 mt-1">Follow-up: {{ $logbook->follow_up_date->format('M d, Y') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.medical-logbooks.show', $logbook->id) }}" class="inline-flex items-center px-2 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200" title="Edit">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.medical-logbooks.edit', $logbook->id) }}" class="inline-flex items-center px-2 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.medical-logbooks.destroy', $logbook->id) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-2 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($medicalLogbooks->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $medicalLogbooks->links() }}
            </div>
            @endif
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden space-y-3">
            @foreach($medicalLogbooks as $logbook)
            <div class="document-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200">
                <!-- Header Section -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $logbook->resident->name }}</h3>
                            <p class="text-sm text-gray-500 truncate">{{ $logbook->consultation_type }}</p>
                            <div class="flex items-center mt-1">
                                @php
                                    $statusColors = [
                                        'Completed' => 'bg-green-100 text-green-800',
                                        'Pending' => 'bg-yellow-100 text-yellow-800',
                                        'Referred' => 'bg-blue-100 text-blue-800',
                                        'Cancelled' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$logbook->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ $logbook->status }}
                                </span>
                                <span class="ml-2 text-xs text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $logbook->consultation_date->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="mb-3">
                    <div class="description-container">
                        <p class="text-sm text-gray-600 leading-relaxed description-text" id="medical-description-{{ $logbook->id }}">
                            <i class="fas fa-align-left mr-1 text-gray-400"></i>
                            <span class="description-short">{{ Str::limit($logbook->chief_complaint, 80) }}</span>
                            @if(strlen($logbook->chief_complaint) > 80)
                                <span class="description-full hidden">{{ $logbook->chief_complaint }}</span>
                                <button onclick="toggleDescription('medical-{{ $logbook->id }}')" 
                                        class="text-blue-600 hover:text-blue-800 underline text-xs ml-1 toggle-desc-btn">
                                    Read More
                                </button>
                            @endif
                        </p>
                        @if($logbook->diagnosis)
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-notes-medical mr-1"></i>
                                Diagnosis: {{ Str::limit($logbook->diagnosis, 80) }}
                            </p>
                        @endif
                        @if($logbook->follow_up_date)
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-calendar-check mr-1"></i>
                                Follow-up: {{ $logbook->follow_up_date->format('M d, Y') }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Additional Details -->
                <div class="mb-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        <i class="fas fa-user-md mr-1"></i>
                        {{ $logbook->attending_health_worker }}
                    </span>
                </div>

                <!-- Actions Section -->
                <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ route('admin.medical-logbooks.show', $logbook->id) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200" title="View">
                        <i class="fas fa-eye mr-1"></i>
                        View
                    </a>
                    <a href="{{ route('admin.medical-logbooks.edit', $logbook->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200" title="Edit">
                        <i class="fas fa-edit mr-1"></i>
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.medical-logbooks.destroy', $logbook->id) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200" title="Delete">
                            <i class="fas fa-trash-alt mr-1"></i>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection