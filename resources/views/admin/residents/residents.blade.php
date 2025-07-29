@extends('admin.modals.layout')

@section('title', 'Resident Information')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Header Section -->
    <div class="mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Resident Information</h1>
                <p class="text-sm md:text-base text-gray-600">Manage resident profiles and information</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.residents.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Resident
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

    <!-- Enhanced Filters, Search, and Bulk Actions -->
    <form method="GET" action="{{ route('admin.residents') }}" class="mb-3 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="searchInput" placeholder="Search residents by name, email, or address..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500" value="{{ request('search') }}">
                </div>
            </div>
            <!-- Status Filter -->
            <div class="sm:w-48">
                <select name="status" id="statusFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <!-- Recent Filter -->
            <div class="sm:w-48">
                <select name="recent" id="recentFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                    <option value="">All Residents</option>
                    <option value="recent" {{ request('recent') == 'recent' ? 'selected' : '' }}>Recently Added</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Filter
                </button>
                <a href="{{ route('admin.residents') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-3">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-home text-green-600 text-sm md:text-base"></i>
                    </div>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">Total Residents</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900" id="total-count">{{ $totalResidents }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-check text-blue-600 text-sm md:text-base"></i>
                    </div>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">Active Residents</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900" id="active-count">{{ $activeResidents }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-plus text-purple-600 text-sm md:text-base"></i>
                    </div>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">This Month</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900" id="month-count">{{ $recentResidents }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-orange-600 text-sm md:text-base"></i>
                    </div>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">With Address</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900" id="address-count">{{ $withAddress }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Residents List -->
    @if($residents->isEmpty())
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-home text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No residents found</h3>
            <p class="text-gray-500">Get started by adding the first resident to the system.</p>
            <div class="mt-6">
                <a href="{{ route('admin.residents.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Add First Resident
                </a>
            </div>
        </div>
    @else
        <!-- Desktop Table (hidden on mobile) -->
        <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-user mr-2"></i>
                                    Resident
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope mr-2"></i>
                                    Contact
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    Address
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-user-friends mr-2"></i>
                                    Demographics
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-toggle-on mr-2"></i>
                                    Status
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-2"></i>
                                    Registered
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
                    <tbody class="bg-white divide-y divide-gray-200" id="residentTableBody">
                        @foreach ($residents as $resident)
                        @php
                            $statusBadgeClass = $resident->active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600';
                            $statusIconClass = $resident->active ? 'text-green-500' : 'text-gray-400';
                            $toggleBtnClass = $resident->active ? 'text-gray-700 bg-white hover:bg-gray-50' : 'text-white bg-gray-400 hover:bg-gray-500';
                            $toggleIcon = $resident->active ? 'on' : 'off';
                        @endphp
                        <tr class="resident-item hover:bg-gray-50 transition duration-150" data-status="{{ $resident->active ? 'active' : 'inactive' }}" data-created="{{ $resident->created_at->format('Y-m-d') }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $resident->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $resident->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $resident->address ?: 'No address provided' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($resident->age || $resident->family_size || $resident->education_level || $resident->income_level || $resident->employment_status || $resident->health_status)
                                        <button onclick="showDemographicsModal({{ $resident->id }}, '{{ addslashes($resident->name) }}')" 
                                                class="text-green-600 hover:text-green-800 font-medium cursor-pointer">
                                            <i class="fas fa-eye mr-1"></i>
                                            View Demographics
                                        </button>
                                    @else
                                        <span class="text-gray-400">No demographic data</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusBadgeClass }}">
                                    <i class="fas fa-circle mr-1 {{ $statusIconClass }}"></i>
                                    {{ $resident->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $resident->created_at->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $resident->created_at->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.residents.edit', $resident->id) }}" 
                                       class="inline-flex items-center px-2 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="{{ $resident->active ? 'deactivateResident' : 'activateResident' }}({{ $resident->id }}, '{{ addslashes($resident->name) }}')" 
                                            class="inline-flex items-center px-2 py-1.5 border border-gray-300 text-xs font-medium rounded-md {{ $toggleBtnClass }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200" 
                                            title="{{ $resident->active ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas fa-toggle-{{ $toggleIcon }}"></i>
                                    </button>
                                    <button onclick="deleteResident({{ $resident->id }}, '{{ addslashes($resident->name) }}')" 
                                            class="inline-flex items-center px-2 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200"
                                            title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards (hidden on desktop) -->
        <div class="md:hidden space-y-3" id="mobileResidentCards">
            @foreach ($residents as $resident)
            @php
                $statusBadgeClass = $resident->active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600';
                $statusIconClass = $resident->active ? 'text-green-500' : 'text-gray-400';
                $toggleBtnClass = $resident->active ? 'text-gray-700 bg-white hover:bg-gray-50' : 'text-white bg-gray-400 hover:bg-gray-500';
                $toggleIcon = $resident->active ? 'on' : 'off';
            @endphp
            <div class="document-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200" data-status="{{ $resident->active ? 'active' : 'inactive' }}" data-created="{{ $resident->created_at->format('Y-m-d') }}">
                <!-- Header with avatar and basic info -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-home text-blue-600"></i>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $resident->name }}</h3>
                            <p class="text-sm text-gray-500 truncate">{{ $resident->email }}</p>
                            <div class="flex items-center mt-1">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusBadgeClass }}">
                                    <i class="fas fa-circle mr-1 {{ $statusIconClass }}"></i>
                                    {{ $resident->active ? 'Active' : 'Inactive' }}
                                </span>
                                <span class="ml-2 text-xs text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $resident->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address section -->
                @if($resident->address)
                <div class="mb-3">
                    <p class="text-sm text-gray-600 leading-relaxed">
                        <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>
                        {{ $resident->address }}
                    </p>
                </div>
                @endif

                <!-- Demographics section -->
                @if($resident->age || $resident->family_size || $resident->education_level || $resident->income_level || $resident->employment_status || $resident->health_status)
                <div class="mb-3">
                    <button onclick="showDemographicsModal({{ $resident->id }}, '{{ addslashes($resident->name) }}')" 
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200 transition duration-200">
                        <i class="fas fa-user-friends mr-1"></i>
                        View Demographics
                    </button>
                </div>
                @endif

                <!-- Action buttons -->
                <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ route('admin.residents.edit', $resident->id) }}" 
                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200"
                       title="Edit">
                        <i class="fas fa-edit mr-1"></i>
                        Edit
                    </a>
                    <button onclick="{{ $resident->active ? 'deactivateResident' : 'activateResident' }}({{ $resident->id }}, '{{ addslashes($resident->name) }}')" 
                            class="inline-flex items-center px-2 py-1.5 border border-gray-300 text-xs font-medium rounded-md {{ $toggleBtnClass }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200" 
                            title="{{ $resident->active ? 'Deactivate' : 'Activate' }}">
                        <i class="fas fa-toggle-{{ $toggleIcon }} mr-1"></i>
                        {{ $resident->active ? 'Disable' : 'Enable' }}
                    </button>
                    <button onclick="deleteResident({{ $resident->id }}, '{{ addslashes($resident->name) }}')" 
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200"
                            title="Delete">
                        <i class="fas fa-trash-alt mr-1"></i>
                        Delete
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    
    <!-- Modern Pagination -->
    @if($residents->hasPages())
        <div class="mt-6">
            <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
                <div class="-mt-px flex w-0 flex-1">
                    @if($residents->onFirstPage())
                        <span class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500">
                            <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                            Previous
                        </span>
                    @else
                        <a href="{{ $residents->appends(request()->except('page'))->previousPageUrl() }}" 
                           class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                            Previous
                        </a>
                    @endif
                </div>
                
                <div class="hidden md:-mt-px md:flex">
                    @php
                        $currentPage = $residents->currentPage();
                        $lastPage = $residents->lastPage();
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($lastPage, $currentPage + 2);
                    @endphp
                    
                    @if($startPage > 1)
                        <a href="{{ $residents->appends(request()->except('page'))->url(1) }}" 
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
                            <a href="{{ $residents->appends(request()->except('page'))->url($page) }}" 
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
                        <a href="{{ $residents->appends(request()->except('page'))->url($lastPage) }}" 
                           class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            {{ $lastPage }}
                        </a>
                    @endif
                </div>
                
                <div class="-mt-px flex w-0 flex-1 justify-end">
                    @if($residents->hasMorePages())
                        <a href="{{ $residents->appends(request()->except('page'))->nextPageUrl() }}" 
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
                @if($residents->onFirstPage())
                    <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500">
                        Previous
                    </span>
                @else
                    <a href="{{ $residents->appends(request()->except('page'))->previousPageUrl() }}" 
                       class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Previous
                    </a>
                @endif
                
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">
                    Page {{ $residents->currentPage() }} of {{ $residents->lastPage() }}
                </span>
                
                @if($residents->hasMorePages())
                    <a href="{{ $residents->appends(request()->except('page'))->nextPageUrl() }}" 
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
                Showing {{ $residents->firstItem() }} to {{ $residents->lastItem() }} of {{ $residents->total() }} results
            </div>
        </div>
    @endif
    
    <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden"></p>
</div>

<!-- Demographics Modal - Nicer & Simpler Design Only -->
<div id="demographicsModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50 p-4 sm:p-6">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl mx-auto max-h-[90vh] overflow-y-auto">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-user-friends text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-800" id="modalResidentName"></h3>
                    <p class="text-sm text-gray-500 mt-1">Demographic Information</p>
                </div>
            </div>
            <button onclick="closeDemographicsModal()" 
                    class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 rounded-full p-1 transition duration-200">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <!-- Modal Body - Demographics Content -->
        <div id="demographicsContent" class="p-5 space-y-4 text-gray-700">
            <!-- Content will be loaded dynamically -->
            <!-- Placeholder for content if not loaded yet -->
            <p class="text-center text-gray-500">Loading demographic data...</p>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex justify-end p-5 border-t border-gray-200">
            <button onclick="closeDemographicsModal()" 
                    class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 transition duration-200">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Activate Confirmation Modal -->
<div id="activateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900">Activate Resident</h3>
                <p class="text-sm text-gray-500">This will enable the resident's profile.</p>
            </div>
        </div>
        <p class="text-gray-700 mb-6">Are you sure you want to activate <span id="activateResidentName" class="font-semibold"></span>? This will make their profile active and visible in the system.</p>
        <form id="activateForm" method="POST" class="inline">
            @csrf
            @method('PUT')
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeActivateModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition duration-200">
                    Activate Resident
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Deactivate Confirmation Modal -->
<div id="deactivateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900">Deactivate Resident</h3>
                <p class="text-sm text-gray-500">This will disable the resident's profile.</p>
            </div>
        </div>
        <p class="text-gray-700 mb-6">Are you sure you want to deactivate <span id="deactivateResidentName" class="font-semibold"></span>? This will make their profile inactive and hidden from the system.</p>
        <form id="deactivateForm" method="POST" class="inline">
            @csrf
            @method('PUT')
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeactivateModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-md hover:bg-yellow-700 transition duration-200">
                    Deactivate Resident
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900">Delete Resident</h3>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-gray-700 mb-6">Are you sure you want to delete <span id="residentName" class="font-semibold"></span>? This will permanently remove their profile from the system.</p>
        <form id="deleteForm" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition duration-200">
                    Delete Resident
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showDemographicsModal(residentId, residentName) {
        document.getElementById('modalResidentName').textContent = residentName;
        const demographicsContent = document.getElementById('demographicsContent');
        demographicsContent.innerHTML = '<p class="text-center text-gray-500">Loading demographics...</p>';

        // Fetch demographics data via AJAX
        fetch('/admin/residents/' + residentId + '/demographics')
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { 
                        throw new Error('HTTP error! status: ' + response.status + ', message: ' + text); 
                    });
                }
                return response.json();
            })
            .then(data => {
                let contentHtml = '';
                if (Object.keys(data).length === 0 || Object.values(data).every(value => value === null || value === '')) {
                    contentHtml = '<p class="text-center text-gray-500">No demographic data available for this resident.</p>';
                } else {
                    contentHtml += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
                    // Dynamically add fields if they exist and are not null/empty
                    const fields = {
                        'age': { label: 'Age', icon: 'fas fa-birthday-cake' },
                        'family_size': { label: 'Family Size', icon: 'fas fa-users' },
                        'education_level': { label: 'Education Level', icon: 'fas fa-graduation-cap' },
                        'income_level': { label: 'Income Level', icon: 'fas fa-money-bill-wave' },
                        'employment_status': { label: 'Employment Status', icon: 'fas fa-briefcase' },
                        'health_status': { label: 'Health Status', icon: 'fas fa-heartbeat' }
                    };

                    let hasData = false;
                    for (const key in fields) {
                        if (data[key] !== undefined && data[key] !== null && data[key] !== '') {
                            contentHtml += '<div class="flex items-center"><i class="' + fields[key].icon + ' text-blue-500 mr-2"></i><span><strong>' + fields[key].label + ':</strong> ' + data[key] + '</span></div>';
                            hasData = true;
                        }
                    }
                    if (!hasData) {
                        contentHtml = '<p class="text-center text-gray-500">No demographic data available for this resident.</p>';
                    }
                    contentHtml += '</div>';
                }
                demographicsContent.innerHTML = contentHtml;
            })
            .catch(error => {
                console.error('Error fetching demographics:', error);
                demographicsContent.innerHTML = '<p class="text-center text-red-500">Failed to load demographics. Please try again. <br>Details: ' + (error.message || error) + '</p>';
            });

        const modal = document.getElementById('demographicsModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDemographicsModal() {
        const modal = document.getElementById('demographicsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function deleteResident(id, name) {
        document.getElementById('residentName').textContent = name;
        document.getElementById('deleteForm').action = `/admin/residents/${id}`;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }
    function activateResident(id, name) {
        document.getElementById('activateResidentName').textContent = name;
        document.getElementById('activateForm').action = `/admin/residents/${id}/activate`;
        document.getElementById('activateModal').classList.remove('hidden');
        document.getElementById('activateModal').classList.add('flex');
    }

    function closeActivateModal() {
        document.getElementById('activateModal').classList.add('hidden');
        document.getElementById('activateModal').classList.remove('flex');
    }

    function deactivateResident(id, name) {
        document.getElementById('deactivateResidentName').textContent = name;
        document.getElementById('deactivateForm').action = `/admin/residents/${id}/deactivate`;
        document.getElementById('deactivateModal').classList.remove('hidden');
        document.getElementById('deactivateModal').classList.add('flex');
    }

    function closeDeactivateModal() {
        document.getElementById('deactivateModal').classList.add('hidden');
        document.getElementById('deactivateModal').classList.remove('flex');
    }
</script>
@endsection