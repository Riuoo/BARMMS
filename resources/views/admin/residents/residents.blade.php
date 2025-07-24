@extends('admin.modals.layout')

@section('title', 'Resident Information')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-6 md:mb-8">
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
    <form method="GET" action="{{ route('admin.residents') }}" class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="searchInput" placeholder="Search residents by name, email, or address..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm" value="{{ request('search') }}">
                </div>
            </div>
            <!-- Status Filter -->
            <div class="sm:w-48">
                <select name="status" id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <!-- Recent Filter -->
            <div class="sm:w-48">
                <select name="recent" id="recentFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                    <option value="">All Residents</option>
                    <option value="recent" {{ request('recent') == 'recent' ? 'selected' : '' }}>Recently Added</option>
                </select>
            </div>
            <div class="flex items-center">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-300">Search</button>
                <a href="{{ route('admin.residents') }}" class="ml-2 text-green-600 hover:text-green-800 font-medium">Clear</a>
            </div>
        </div>
    </form>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
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
                                                class="text-blue-600 hover:text-blue-800 font-medium cursor-pointer">
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
                                       class="inline-flex items-center px-2 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.residents.toggle', $resident->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-2 py-1.5 border border-gray-300 text-xs font-medium rounded-md {{ $toggleBtnClass }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200" title="{{ $resident->active ? 'Disable' : 'Enable' }}">
                                            <i class="fas fa-toggle-{{ $toggleIcon }}"></i>
                                        </button>
                                    </form>
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
            <div class="resident-card bg-white border border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition duration-200" data-status="{{ $resident->active ? 'active' : 'inactive' }}" data-created="{{ $resident->created_at->format('Y-m-d') }}">
                <!-- Header with avatar and basic info -->
                <div class="flex items-start space-x-4 mb-4">
                    <div class="flex-shrink-0">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center shadow-sm">
                            <i class="fas fa-home text-blue-600 text-lg"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-gray-900 truncate">{{ $resident->name }}</h3>
                        <p class="text-sm text-gray-500 truncate">{{ $resident->email }}</p>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusBadgeClass }}">
                                <i class="fas fa-circle mr-1 {{ $statusIconClass }}"></i>
                                {{ $resident->active ? 'Active Resident' : 'Inactive Resident' }}
                            </span>
                            @if($resident->age || $resident->family_size || $resident->education_level || $resident->income_level || $resident->employment_status || $resident->health_status)
                                <button 
                                    class="block md:hidden mt-2 text-blue-600 hover:text-blue-800 font-medium cursor-pointer text-xs underline"
                                    onclick="showDemographicsModal({{ $resident->id }}, '{{ addslashes($resident->name) }}')">
                                    <i class="fas fa-eye mr-1"></i>
                                    View Demographics
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Address section -->
                @if($resident->address)
                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-start space-x-2">
                        <i class="fas fa-map-marker-alt text-gray-400 mt-0.5 flex-shrink-0"></i>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $resident->address }}</p>
                    </div>
                </div>
                @endif

                <!-- Demographics section -->
                @if($resident->age || $resident->family_size || $resident->education_level || $resident->income_level || $resident->employment_status || $resident->health_status)
                <div class="mb-4 p-3 bg-blue-50 rounded-lg cursor-pointer hover:bg-blue-100 transition duration-200 hidden md:block" onclick="showDemographicsModal({{ $resident->id }}, '{{ addslashes($resident->name) }}')">
                    <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                        <i class="fas fa-user-friends mr-2 text-blue-600"></i>
                        Demographics
                        <i class="fas fa-external-link-alt ml-auto text-blue-500 text-xs"></i>
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                        @if($resident->age)
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-birthday-cake mr-2 text-blue-500 w-4"></i>
                                <span>Age: {{ $resident->age }}</span>
                            </div>
                        @endif
                        @if($resident->family_size)
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-users mr-2 text-blue-500 w-4"></i>
                                <span>Family: {{ $resident->family_size }}</span>
                            </div>
                        @endif
                        @if($resident->education_level)
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-graduation-cap mr-2 text-blue-500 w-4"></i>
                                <span>{{ $resident->education_level }}</span>
                            </div>
                        @endif
                        @if($resident->income_level)
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-money-bill mr-2 text-blue-500 w-4"></i>
                                <span>{{ $resident->income_level }}</span>
                            </div>
                        @endif
                        @if($resident->employment_status)
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-briefcase mr-2 text-blue-500 w-4"></i>
                                <span>{{ $resident->employment_status }}</span>
                            </div>
                        @endif
                        @if($resident->health_status)
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-heartbeat mr-2 text-blue-500 w-4"></i>
                                <span>{{ $resident->health_status }}</span>
                            </div>
                        @endif
                    </div>
                    <p class="text-xs text-blue-600 mt-2 font-medium">Click to view full details</p>
                </div>
                @endif

                <!-- Registration info -->
                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-calendar mr-2 text-gray-500"></i>
                        <span>Registered {{ $resident->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="flex space-x-2 pt-2">
                    <a href="{{ route('admin.residents.edit', $resident->id) }}" 
                       class="flex-1 inline-flex items-center justify-center px-2 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 shadow-sm"
                       title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.residents.toggle', $resident->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="flex-1 inline-flex items-center justify-center px-2 py-2.5 border border-gray-300 text-sm font-medium rounded-lg {{ $toggleBtnClass }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200 shadow-sm" title="{{ $resident->active ? 'Disable' : 'Enable' }}">
                            <i class="fas fa-toggle-{{ $toggleIcon }}"></i>
                        </button>
                    </form>
                    <button onclick="deleteResident({{ $resident->id }}, '{{ addslashes($resident->name) }}')" 
                            class="flex-1 inline-flex items-center justify-center px-2 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200 shadow-sm"
                            title="Delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden"></p>
</div>

<!-- Demographics Modal -->
<div id="demographicsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-user-friends text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900" id="modalResidentName"></h3>
                    <p class="text-sm text-gray-500">Demographic Information</p>
                </div>
            </div>
            <button onclick="closeDemographicsModal()" class="text-gray-400 hover:text-gray-600 transition duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="demographicsContent" class="space-y-6">
            <!-- Content will be loaded dynamically -->
        </div>
        
        <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
            <button onclick="closeDemographicsModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition duration-200">
                Close
            </button>
        </div>
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

@endsection