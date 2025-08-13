@extends('admin.main.layout')

@section('title', 'Vaccination Records')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Header Section -->
    <div class="mb-3">
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
                <a href="{{ route('admin.vaccination-records.child-profiles') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                    <i class="fas fa-child mr-2"></i>
                    Child Profiles
                </a>
                <button type="button" id="openAddVaccinationModal" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Add Vaccination
                </button>
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
    <form method="GET" action="{{ route('admin.vaccination-records.index') }}" class="mb-3 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
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
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 md:gap-4 mb-3">
        <!-- Total Vaccinations -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-syringe text-blue-600 text-sm md:text-base"></i>
                    </div>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">Total Vaccinations</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900   ">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <!-- Due Soon -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-sm md:text-base"></i>
                    </div>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">Due Soon</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['due_soon'] }}</p>
                </div>
            </div>
        </div>

        <!-- Overdue -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-600 text-sm md:text-base"></i>
                    </div>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">Overdue</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['overdue'] }}</p>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-sm md:text-base"></i>
                    </div>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">Completed</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['completed'] }}</p>
                </div>
            </div>
        </div>

        <!-- Last 30 Days -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-purple-600 text-sm md:text-base"></i>
                    </div>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">Last 30 Days</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['last_month'] }}</p>
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
            <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                <button type="button" id="openAddVaccinationModalEmpty" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Add First Vaccination
                </button>
                <a href="{{ route('admin.vaccination-records.child-profiles') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-child mr-2"></i>
                    Manage Child Profiles
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
                                        <div class="text-sm font-medium text-gray-900">{{ $record->patient_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $record->patient_type }}</div>
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
                            <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $record->patient_name }}</h3>
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

    <!-- Modern Pagination -->
    @if($vaccinationRecords->hasPages())
        <div class="mt-6">
            <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
                <div class="-mt-px flex w-0 flex-1">
                    @if($vaccinationRecords->onFirstPage())
                        <span class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500">
                            <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                            Previous
                        </span>
                    @else
                        <a href="{{ $vaccinationRecords->appends(request()->except('page'))->previousPageUrl() }}" 
                           class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                            Previous
                        </a>
                    @endif
                </div>
                
                <div class="hidden md:-mt-px md:flex">
                    @php
                        $currentPage = $vaccinationRecords->currentPage();
                        $lastPage = $vaccinationRecords->lastPage();
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($lastPage, $currentPage + 2);
                    @endphp
                    
                    @if($startPage > 1)
                        <a href="{{ $vaccinationRecords->appends(request()->except('page'))->url(1) }}" 
                           class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            1
                        </a>
                        @if($startPage > 2)
                            <span class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500">
                                ...
                            </span>
                        @endif
                    @endif
                    
                    @for($page = $startPage; $page <= $endPage; $page++)
                        @if($page == $currentPage)
                            <span class="inline-flex items-center border-t-2 border-green-500 px-4 pt-4 text-sm font-medium text-green-600" aria-current="page">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $vaccinationRecords->appends(request()->except('page'))->url($page) }}" 
                               class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor
                    
                    @if($endPage < $lastPage)
                        @if($endPage < $lastPage - 1)
                            <span class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500">
                                ...
                            </span>
                        @endif
                        <a href="{{ $vaccinationRecords->appends(request()->except('page'))->url($lastPage) }}" 
                           class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            {{ $lastPage }}
                        </a>
                    @endif
                </div>
                
                <div class="-mt-px flex w-0 flex-1 justify-end">
                    @if($vaccinationRecords->hasMorePages())
                        <a href="{{ $vaccinationRecords->appends(request()->except('page'))->nextPageUrl() }}" 
                           class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            Next
                            <i class="fas fa-arrow-right ml-3 text-gray-400"></i>
                        </a>
                    @else
                        <span class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-gray-500">
                            Next
                            <i class="fas fa-arrow-right ml-3 text-gray-400"></i>
                        </span>
                    @endif
                </div>
            </nav>
            
            <!-- Mobile Pagination -->
            <div class="mt-4 flex justify-between sm:hidden">
                @if($vaccinationRecords->onFirstPage())
                    <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500">
                        Previous
                    </span>
                @else
                    <a href="{{ $vaccinationRecords->appends(request()->except('page'))->previousPageUrl() }}" 
                       class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Previous
                    </a>
                @endif
                
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">
                    Page {{ $vaccinationRecords->currentPage() }} of {{ $vaccinationRecords->lastPage() }}
                </span>
                
                @if($vaccinationRecords->hasMorePages())
                    <a href="{{ $vaccinationRecords->appends(request()->except('page'))->nextPageUrl() }}" 
                       class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Next
                    </a>
                @else
                    <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500">
                        Next
                    </span>
                @endif
            </div>
            
            <!-- Results Info -->
            <div class="mt-4 text-center text-sm text-gray-500">
                Showing {{ $vaccinationRecords->firstItem() }} to {{ $vaccinationRecords->lastItem() }} of {{ $vaccinationRecords->total() }} results
            </div>
        </div>
    @endif
</div>

    <!-- Add Vaccination Modal -->
    <div id="addVaccinationModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        <div class="relative z-10 flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Select Patient Group</h3>
                    <button type="button" id="closeAddVaccinationModal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.vaccination-records.create.child') }}" class="flex items-center px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50">
                        <i class="fas fa-child text-blue-600 mr-3"></i>
                        <span class="font-medium text-gray-900">Child</span>
                    </a>
                    <a href="{{ route('admin.vaccination-records.create.adult') }}" class="flex items-center px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50">
                        <i class="fas fa-user text-green-600 mr-3"></i>
                        <span class="font-medium text-gray-900">Adult</span>
                    </a>
                    <a href="{{ route('admin.vaccination-records.create.elderly') }}" class="flex items-center px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50">
                        <i class="fas fa-blind text-yellow-600 mr-3"></i>
                        <span class="font-medium text-gray-900">Elderly</span>
                    </a>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 text-right">
                    <button type="button" id="closeAddVaccinationModalBottom" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function() {
        const modal = document.getElementById('addVaccinationModal');
        const overlay = modal ? modal.firstElementChild : null;
        const openBtns = [document.getElementById('openAddVaccinationModal'), document.getElementById('openAddVaccinationModalEmpty')].filter(Boolean);
        const closeBtns = [document.getElementById('closeAddVaccinationModal'), document.getElementById('closeAddVaccinationModalBottom')].filter(Boolean);

        function open() { if (modal) modal.classList.remove('hidden'); }
        function close() { if (modal) modal.classList.add('hidden'); }

        openBtns.forEach(function(b) { b.addEventListener('click', open); });
        closeBtns.forEach(function(b) { b.addEventListener('click', close); });
        if (overlay) {
            overlay.addEventListener('click', close);
        }
    })();
    </script>
@endsection