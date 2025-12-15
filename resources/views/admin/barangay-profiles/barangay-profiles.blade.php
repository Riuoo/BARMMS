@extends('admin.main.layout')

@php
    $userRole = session('user_role');
    $isSecretary = $userRole === 'secretary';
    $canPerformTransactions = $isSecretary;
@endphp

@section('title', 'Barangay Profiles')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Shared User Management Skeleton -->
    <div id="userManagementSkeleton">
        @include('components.loading.user-management-skeleton')
    </div>
    
    <!-- Real Content (hidden initially) -->
    <div id="profilesHeaderContent" style="display: none;">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-2 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Barangay Officials</h1>
                <p class="text-sm md:text-base text-gray-600">Manage barangay officials and their profiles</p>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                @if($canPerformTransactions)
                <a href="{{ route('admin.barangay-profiles.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Official
                </a>
                @endif
            </div>
        </div>
        
        <!-- Enhanced Filters, Search, and Bulk Actions -->
        <form method="GET" action="{{ route('admin.barangay-profiles') }}" class="mt-2 bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-2">
            <div class="flex flex-col sm:flex-row gap-4">
                <!-- Search Input -->
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="searchInput" placeholder="Search officials..." 
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                            value="{{ request('search') }}">
                    </div>
                </div>
                <!-- Role Filter -->
                <div class="w-full sm:w-48">
                    <select name="role" id="roleFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                        <option value="">All Roles</option>
                        <option value="captain" {{ request('role') == 'captain' ? 'selected' : '' }}>Captain</option>
                        <option value="councilor" {{ request('role') == 'councilor' ? 'selected' : '' }}>Councilor</option>
                        <option value="secretary" {{ request('role') == 'secretary' ? 'selected' : '' }}>Secretary</option>
                        <option value="treasurer" {{ request('role') == 'treasurer' ? 'selected' : '' }}>Treasurer</option>
                    </select>
                </div>
                <!-- Status Filter -->
                <div class="sm:w-48">
                    <select name="status" id="statusFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <!-- Purok Filter -->
                <div class="sm:w-48">
                    <select name="purok" id="purokFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                        <option value="">All Puroks</option>
                        @for($i = 1; $i <= 7; $i++)
                            @php $purokValue = 'Purok ' . $i; @endphp
                            <option value="{{ $purokValue }}" {{ request('purok') == $purokValue ? 'selected' : '' }}>Purok {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.barangay-profiles') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-undo mr-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
    
    <div id="profilesStatsContent" style="display: none;">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-green-600 text-sm md:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-500">Total Officials</p>
                        <p class="text-lg md:text-2xl font-bold text-gray-900" id="total-count">{{ $totalOfficials }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-crown text-blue-600 text-sm md:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-500">Captains</p>
                        <p class="text-lg md:text-2xl font-bold text-gray-900" id="captain-count">{{ $captainCount }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie text-purple-600 text-sm md:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-500">Councilors</p>
                        <p class="text-lg md:text-2xl font-bold text-gray-900" id="councilor-count">{{ $councilorCount }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-cog text-orange-600 text-sm md:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-500">Other Staff</p>
                        <p class="text-lg md:text-2xl font-bold text-gray-900" id="other-count">{{ $otherCount }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="profilesTableContent" style="display: none;">
        <!-- Officials List Table -->
        @if($barangayProfiles->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No officials found</h3>
                <p class="text-gray-500">Get started by adding the first barangay official.</p>
                <div class="mt-6">
                    @if($canPerformTransactions)
                    <a href="{{ route('admin.barangay-profiles.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Add First Official
                    </a>
                    @endif
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
                                        Official
                                    </div>
                                </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope mr-2"></i>
                                    Email
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-phone mr-2"></i>
                                    Contact No.
                                </div>
                            </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-briefcase mr-2"></i>
                                        Role
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
                                        <i class="fas fa-toggle-on mr-2"></i>
                                        Status
                                    </div>
                                </th>
                                @if($canPerformTransactions)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-center">
                                        <i class="fas fa-cogs mr-2"></i>
                                        Actions
                                    </div>
                                </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="userTableBody">
                            @foreach ($barangayProfiles as $user)
                            @php
                                $statusBadgeClass = $user->active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600';
                                $statusIconClass = $user->active ? 'text-green-500' : 'text-gray-400';
                                $toggleBtnClass = $user->active ? 'text-gray-700 bg-white hover:bg-gray-50' : 'text-white bg-gray-400 hover:bg-gray-500';
                                $toggleIcon = $user->active ? 'on' : 'off';
                            @endphp
                            <tr class="official-item hover:bg-gray-50 transition duration-150" data-role="{{ strtolower(str_replace(' ', '-', $user->role)) }}" data-status="{{ $user->active ? 'active' : 'inactive' }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $user->full_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $user->contact_number ?: 'No contact provided' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center py-0.5 rounded-full text-xs font-medium 
                                        @if($user->role === 'Captain') bg-yellow-100 text-yellow-800
                                        @elseif($user->role === 'Councilor') bg-purple-100 text-purple-800
                                        @elseif($user->role === 'Secretary') bg-blue-100 text-blue-800
                                        @elseif($user->role === 'Treasurer') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        <i class="fas fa-briefcase mr-1"></i>
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $user->address ?: 'No address provided' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusBadgeClass }}">
                                        <i class="fas fa-circle mr-1 {{ $statusIconClass }}"></i>
                                        {{ $user->active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        @if($canPerformTransactions)
                                        <a href="{{ route('admin.barangay-profiles.edit', $user->id) }}" 
                                           class="inline-flex items-center px-2 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                                data-action="{{ $user->active ? 'deactivate' : 'activate' }}"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ addslashes($user->full_name) }}"
                                                class="inline-flex items-center px-2 py-1.5 border border-gray-300 text-xs font-medium rounded-md {{ $toggleBtnClass }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200 js-toggle-official" 
                                                title="{{ $user->active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-toggle-{{ $toggleIcon }}"></i>
                                        </button>
                                        <button type="button"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ addslashes($user->full_name) }}"
                                                class="inline-flex items-center px-2 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200 js-delete-official"
                                                title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
    
    <div id="profilesMobileContent" style="display: none;">
        <!-- Officials List Mobile Cards -->
        @if($barangayProfiles->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No officials found</h3>
                <p class="text-gray-500">Get started by adding the first barangay official.</p>
                <div class="mt-6">
                    @if($canPerformTransactions)
                    <a href="{{ route('admin.barangay-profiles.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Add First Official
                    </a>
                    @endif
                </div>
            </div>
        @else
            <!-- Mobile Cards (hidden on desktop) -->
            <div class="block md:hidden space-y-3" id="mobileUserCards">
                @foreach ($barangayProfiles as $user)
                @php
                    $statusBadgeClass = $user->active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600';
                    $statusIconClass = $user->active ? 'text-green-500' : 'text-gray-400';
                    $toggleBtnClass = $user->active ? 'text-gray-700 bg-white hover:bg-gray-50' : 'text-white bg-gray-400 hover:bg-gray-500';
                    $toggleIcon = $user->active ? 'on' : 'off';
                @endphp
                <div class="document-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200" data-role="{{ strtolower(str_replace(' ', '-', $user->role)) }}" data-status="{{ $user->active ? 'active' : 'inactive' }}">
                    <!-- Header with avatar and basic info -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center flex-1 min-w-0">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-green-600"></i>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $user->full_name }}</h3>
                                <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                                <p class="text-sm text-gray-500 truncate">
                                    <i class="fas fa-phone mr-1 text-gray-400"></i>
                                    {{ $user->contact_number ?: 'No contact provided' }}
                                </p>
                                <div class="flex items-center mt-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                        @if($user->role === 'Captain') bg-yellow-100 text-yellow-800
                                        @elseif($user->role === 'Councilor') bg-purple-100 text-purple-800
                                        @elseif($user->role === 'Secretary') bg-blue-100 text-blue-800
                                        @elseif($user->role === 'Treasurer') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        <i class="fas fa-briefcase mr-1"></i>
                                        {{ $user->role }}
                                    </span>
                                    <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusBadgeClass }}">
                                        <i class="fas fa-circle mr-1 {{ $statusIconClass }}"></i>
                                        {{ $user->active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address section -->
                    @if($user->address)
                    <div class="mb-3">
                        <p class="text-sm text-gray-600 leading-relaxed">
                            <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>
                            {{ $user->address }}
                        </p>
                    </div>
                    @endif

                    <!-- Action buttons -->
                    <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100">
                        @if($canPerformTransactions)
                        <a href="{{ route('admin.barangay-profiles.edit', $user->id) }}" 
                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200"
                           title="Edit">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </a>
                        <button type="button"
                                data-action="{{ $user->active ? 'deactivate' : 'activate' }}"
                                data-user-id="{{ $user->id }}"
                                data-user-name="{{ addslashes($user->full_name) }}"
                                class="inline-flex items-center px-2 py-1.5 border border-gray-300 text-xs font-medium rounded-md {{ $toggleBtnClass }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200 js-toggle-official" 
                                title="{{ $user->active ? 'Deactivate' : 'Activate' }}">
                            <i class="fas fa-toggle-{{ $toggleIcon }} mr-1"></i>
                            {{ $user->active ? 'Disable' : 'Enable' }}
                        </button>
                        <button type="button"
                                data-user-id="{{ $user->id }}"
                                data-user-name="{{ addslashes($user->full_name) }}"
                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200 js-delete-official"
                                title="Delete">
                            <i class="fas fa-trash-alt mr-1"></i>
                            Delete
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
    
    <div id="profilesPaginationContent" style="display: none;">
        <!-- Modern Pagination -->
        @if($barangayProfiles->hasPages())
            <div class="mt-6">
                <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
                    <div class="-mt-px flex w-0 flex-1">
                        @if($barangayProfiles->onFirstPage())
                            <span class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500">
                                <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                                Previous
                            </span>
                        @else
                            <a href="{{ $barangayProfiles->appends(request()->except('page'))->previousPageUrl() }}" 
                               class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                                Previous
                            </a>
                        @endif
                    </div>
                    
                    <div class="hidden md:-mt-px md:flex">
                        @php
                            $currentPage = $barangayProfiles->currentPage();
                            $lastPage = $barangayProfiles->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                        @endphp
                        
                        @if($startPage > 1)
                            <a href="{{ $barangayProfiles->appends(request()->except('page'))->url(1) }}" 
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
                                <a href="{{ $barangayProfiles->appends(request()->except('page'))->url($page) }}" 
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
                            <a href="{{ $barangayProfiles->appends(request()->except('page'))->url($lastPage) }}" 
                               class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                {{ $lastPage }}
                            </a>
                        @endif
                    </div>
                    
                    <div class="-mt-px flex w-0 flex-1 justify-end">
                        @if($barangayProfiles->hasMorePages())
                            <a href="{{ $barangayProfiles->appends(request()->except('page'))->nextPageUrl() }}" 
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
                    @if($barangayProfiles->onFirstPage())
                        <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500">
                            Previous
                        </span>
                    @else
                        <a href="{{ $barangayProfiles->appends(request()->except('page'))->previousPageUrl() }}" 
                           class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Previous
                        </a>
                    @endif
                    
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">
                        Page {{ $barangayProfiles->currentPage() }} of {{ $barangayProfiles->lastPage() }}
                    </span>
                    
                    @if($barangayProfiles->hasMorePages())
                        <a href="{{ $barangayProfiles->appends(request()->except('page'))->nextPageUrl() }}" 
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
                    Showing {{ $barangayProfiles->firstItem() }} to {{ $barangayProfiles->lastItem() }} of {{ $barangayProfiles->total() }} results
                </div>
            </div>
        @endif
        
        <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden"></p>
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
                <h3 class="text-lg font-medium text-gray-900">Activate Official</h3>
                <p class="text-sm text-gray-500">This will enable the official's profile.</p>
            </div>
        </div>
        <p class="text-gray-700 mb-6">Are you sure you want to activate <span id="activateOfficialName" class="font-semibold"></span>? This will make their profile active and visible in the system.</p>
        <form id="activateForm" method="POST" class="inline">
            @csrf
            @method('PUT')
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeActivateModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition duration-200">
                    Activate Official
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
                <h3 class="text-lg font-medium text-gray-900">Deactivate Official</h3>
                <p class="text-sm text-gray-500">This will disable the official's profile.</p>
            </div>
        </div>
        <p class="text-gray-700 mb-6">Are you sure you want to deactivate <span id="deactivateOfficialName" class="font-semibold"></span>? This will make their profile inactive and hidden from the system.</p>
        <form id="deactivateForm" method="POST" class="inline">
            @csrf
            @method('PUT')
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeactivateModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-md hover:bg-yellow-700 transition duration-200">
                    Deactivate Official
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
                <h3 class="text-lg font-medium text-gray-900">Delete Official</h3>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-gray-700 mb-6">Are you sure you want to delete <span id="officialName" class="font-semibold"></span>? This will permanently remove their profile from the system.</p>
        <form id="deleteForm" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition duration-200">
                    Delete Official
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function deleteOfficial(id, name) {
    document.getElementById('officialName').textContent = name;
    document.getElementById('deleteForm').action = `/admin/barangay-profiles/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}

function activateOfficial(id, name) {
    document.getElementById('activateOfficialName').textContent = name;
    document.getElementById('activateForm').action = `/admin/barangay-profiles/${id}/activate`;
    document.getElementById('activateModal').classList.remove('hidden');
    document.getElementById('activateModal').classList.add('flex');
}

function closeActivateModal() {
    document.getElementById('activateModal').classList.add('hidden');
    document.getElementById('activateModal').classList.remove('flex');
}

function deactivateOfficial(id, name) {
    document.getElementById('deactivateOfficialName').textContent = name;
    document.getElementById('deactivateForm').action = `/admin/barangay-profiles/${id}/deactivate`;
    document.getElementById('deactivateModal').classList.remove('hidden');
    document.getElementById('deactivateModal').classList.add('flex');
}

function closeDeactivateModal() {
    document.getElementById('deactivateModal').classList.add('hidden');
    document.getElementById('deactivateModal').classList.remove('flex');
}

// Delegated handlers to avoid inline JS with Blade values
document.addEventListener('click', function (event) {
    const toggleBtn = event.target.closest('.js-toggle-official');
    if (toggleBtn) {
        const id = toggleBtn.getAttribute('data-user-id');
        const name = toggleBtn.getAttribute('data-user-name');
        const action = toggleBtn.getAttribute('data-action');
        if (id && action === 'activate') activateOfficial(id, name);
        if (id && action === 'deactivate') deactivateOfficial(id, name);
        return;
    }
    const deleteBtn = event.target.closest('.js-delete-official');
    if (deleteBtn) {
        const id = deleteBtn.getAttribute('data-user-id');
        const name = deleteBtn.getAttribute('data-user-name');
        if (id) deleteOfficial(id, name);
    }
});

// Hide skeletons and show real content when page loads
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        // Hide the shared skeleton
        const skeleton = document.getElementById('userManagementSkeleton');
        if (skeleton) skeleton.style.display = 'none';

        // Show all real content elements
        const contentElements = [
            'profilesHeaderContent',
            'profilesStatsContent',
            'profilesTableContent',
            'profilesMobileContent',
            'profilesPaginationContent'
        ];
        contentElements.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'block';
        });
    }, 1000);
});
</script>

@endsection