@extends('admin.modals.layout')

@section('title', 'Vaccination Records')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-6 md:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Vaccination Records</h1>
                <p class="text-sm md:text-base text-gray-600">Manage vaccination records and immunization schedules</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <a href="{{ route('admin.vaccination-records.due') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-200">
                    <i class="fas fa-clock mr-2"></i>
                    Due Vaccinations
                </a>
                <a href="{{ route('admin.vaccination-records.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Add Vaccination
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

    <!-- Vaccination Search & Filter Form -->
    <form method="GET" action="{{ route('admin.vaccination-records.index') }}" class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                        placeholder="Patient, vaccine name or type...">
                </div>
            </div>

            <!-- Dose Status Filter -->
            <div class="w-full sm:w-48">
                <select name="dose_status" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                    <option value="">All Statuses</option>
                    <option value="overdue" {{ request('dose_status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="due_soon" {{ request('dose_status') == 'due_soon' ? 'selected' : '' }}>Due Soon</option>
                    <option value="up_to_date" {{ request('dose_status') == 'up_to_date' ? 'selected' : '' }}>Up To Date</option>
                </select>
            </div>

            <!-- Form Actions -->
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Filter
                </button>
                <a href="{{ route('admin.vaccination-records.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <!-- Total Vaccinations -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-syringe"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Vaccinations</p>
                    <p class="text-2xl font-semibold">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <!-- Due Soon -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Due Soon</p>
                    <p class="text-2xl font-semibold">{{ $stats['due_soon'] }}</p>
                </div>
            </div>
        </div>

        <!-- Overdue -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Overdue</p>
                    <p class="text-2xl font-semibold">{{ $stats['overdue'] }}</p>
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

        <!-- Last 30 Days -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Last 30 Days</p>
                    <p class="text-2xl font-semibold">{{ $stats['last_month'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Vaccination Records List -->
    @if($vaccinationRecords->isEmpty())
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-syringe text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No vaccination records found</h3>
            <p class="text-gray-500">Get started by adding the first vaccination record.</p>
            <div class="mt-6">
                <a href="{{ route('admin.vaccination-records.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Add First Vaccination
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
                                    Patient
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-syringe mr-2"></i>
                                    Vaccine Details
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-2"></i>
                                    Vaccination Date
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-clock mr-2"></i>
                                    Next Dose
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
                        @foreach($vaccinationRecords as $record)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4">
                                <div class="items-center gap-2">
                                        <div class="text-sm font-medium text-gray-900">{{ $record->resident->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $record->resident->email }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <div class="font-medium">{{ $record->vaccine_name }}</div>
                                    <div class="text-gray-500">{{ $record->vaccine_type }}</div>
                                    <div class="text-xs text-gray-400">Dose {{ $record->dose_number }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $record->vaccination_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($record->next_dose_date)
                                    @if($record->next_dose_date->isPast())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Overdue
                                        </span>
                                    @elseif($record->next_dose_date->diffInDays(now()) <= 30)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Due Soon
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Scheduled
                                        </span>
                                    @endif
                                    <div class="text-sm text-gray-500 mt-1">{{ $record->next_dose_date->format('M d, Y') }}</div>
                                @else
                                    <span class="text-gray-400 text-sm">No follow-up</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.vaccination-records.show', $record->id) }}" class="inline-flex items-center px-2 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200" title="Edit">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.vaccination-records.edit', $record->id) }}" class="inline-flex items-center px-2 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.vaccination-records.destroy', $record->id) }}" style="display:inline;">
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
            @if($vaccinationRecords->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $vaccinationRecords->links() }}
            </div>
            @endif
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden space-y-3">
            @foreach($vaccinationRecords as $record)
            <div class="document-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200">
                <!-- Header Section -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-green-600"></i>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $record->resident->name }}</h3>
                            <p class="text-sm text-gray-500 truncate">{{ $record->vaccine_name }}</p>
                            <div class="flex items-center mt-1">
                                @php
                                    $doseStatusColors = [
                                        'Overdue' => 'bg-red-100 text-red-800',
                                        'Due Soon' => 'bg-yellow-100 text-yellow-800',
                                        'Scheduled' => 'bg-green-100 text-green-800',
                                        'Up To Date' => 'bg-green-100 text-green-800'
                                    ];
                                    $currentDoseStatus = '';
                                    if ($record->next_dose_date) {
                                        if ($record->next_dose_date->isPast()) {
                                            $currentDoseStatus = 'Overdue';
                                        } elseif ($record->next_dose_date->diffInDays(now()) <= 30) {
                                            $currentDoseStatus = 'Due Soon';
                                        } else {
                                            $currentDoseStatus = 'Scheduled';
                                        }
                                    } else {
                                        $currentDoseStatus = 'Up To Date'; // Assuming no next dose means up to date
                                    }
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $doseStatusColors[$currentDoseStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ $currentDoseStatus }}
                                </span>
                                <span class="ml-2 text-xs text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $record->vaccination_date->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Details -->
                <div class="mb-3">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-syringe mr-1 text-gray-400"></i>
                        Type: <span class="font-medium">{{ $record->vaccine_type }}</span>
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                        <i class="fas fa-hashtag mr-1 text-gray-400"></i>
                        Dose: <span class="font-medium">{{ $record->dose_number }}</span>
                    </p>
                    @if($record->next_dose_date)
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-calendar-check mr-1 text-gray-400"></i>
                            Next Dose: <span class="font-medium">{{ $record->next_dose_date->format('M d, Y') }}</span>
                        </p>
                    @endif
                </div>

                <!-- Actions Section -->
                <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ route('admin.vaccination-records.show', $record->id) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200" title="View">
                        <i class="fas fa-eye mr-1"></i>
                        View
                    </a>
                    <a href="{{ route('admin.vaccination-records.edit', $record->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200" title="Edit">
                        <i class="fas fa-edit mr-1"></i>
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.vaccination-records.destroy', $record->id) }}" style="display:inline;">
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