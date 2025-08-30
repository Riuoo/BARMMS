@extends('admin.main.layout')

@php
    $userRole = session('user_role');
    $isAdmin = $userRole === 'admin';
    $isSecretary = $userRole === 'secretary';
    $canPerformTransactions = $isAdmin || $isSecretary;
@endphp

@section('title', 'New Account Requests')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Header Section -->
    <div class="mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Account Requests</h1>
                <p class="text-gray-600">Review and approve new account applications</p>
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

    <!-- Filters and Search -->
    <form method="GET" action="{{ route('admin.requests.new-account-requests') }}" class="mb-3 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="searchInput" placeholder="Search by email..."
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                    value="{{ request('search') }}">
                </div>
            </div>
            <!-- Status Filter -->
            <div class="sm:w-48">
                <select name="status" id="statusFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.requests.new-account-requests') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-undo mr-2"></i>Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-3">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-clock text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Total Requests</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900" id="total-count">{{ $totalRequests }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900" id="pending-count">{{ $pendingCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Approved</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900" id="approved-count">{{ $approvedCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Completed</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900" id="completed-count">{{ $completedCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Table (Hidden on mobile) -->
    <div class="hidden sm:block">
        @if($accountRequests->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-user-clock text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No account requests found</h3>
                <p class="text-gray-500">No new account applications are waiting for approval.</p>
            </div>
        @else
            @php
                $hasThreadActions = $accountRequests->contains(function ($r) { return in_array($r->status, ['pending','approved']); });
            @endphp
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope mr-2"></i>
                                        Email Address
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Status
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar mr-2"></i>
                                        Requested
                                    </div>
                                </th>
                                @if($hasThreadActions && $canPerformTransactions)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center justify-center">
                                            <i class="fas fa-cogs mr-2"></i>
                                            Actions
                                        </div>
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="accountRequestsTableBody">
                            @foreach($accountRequests as $request)
                            <tr class="account-item hover:bg-gray-50 transition duration-150" data-status="{{ $request->status }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $request->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 justify-center">
                                    @if($request->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Pending
                                        </span>
                                    @elseif($request->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-check mr-1"></i>
                                            Approved
                                        </span>
                                    @elseif($request->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Completed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ optional($request->created_at)->format('M d, Y') ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ optional($request->created_at)->diffForHumans() ?? 'N/A' }}
                                    </div>
                                </td>
                                @if($hasThreadActions && $canPerformTransactions)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center justify-center space-x-2">
                                            @if($request->status === 'pending')
                                                <form method="POST" action="{{ route('admin.account-requests.approve', $request->id) }}" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                                                        <i class="fas fa-check mr-1"></i>
                                                        Approve
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Mobile Cards (Hidden on desktop) -->
    <div class="sm:hidden space-y-4" id="accountRequestsCards">
        @if($accountRequests->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-user-clock text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No account requests found</h3>
                <p class="text-gray-500">No new account applications are waiting for approval.</p>
            </div>
        @else
            @foreach($accountRequests as $request)
            <div class="account-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200" data-status="{{ $request->status }}">
                <!-- Header Section -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-clock text-blue-600"></i>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-gray-900 truncate">{{ $request->email }}</h3>
                            <p class="text-sm text-gray-500 truncate">New Account Request</p>
                            <div class="flex items-center mt-1">
                                @if($request->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Pending
                                </span>
                                @elseif($request->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-check mr-1"></i>
                                    Approved
                                </span>
                                @elseif($request->status === 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Completed
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Request Info Section -->
                <div class="mb-3">
                    <div class="items-center text-xs text-gray-500">
                        <span class="ml-2 text-xs text-gray-500">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ optional($request->created_at)->format('M d, Y') ?? 'N/A' }}
                        </span>
                        <span class="ml-2">
                            <i class="fas fa-clock mr-1"></i>
                            {{ optional($request->created_at)->diffForHumans() ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                <!-- Actions Section -->
                <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100">
                    @if($request->status === 'pending')
                        @if($canPerformTransactions)
                            <form method="POST" action="{{ route('admin.account-requests.approve', $request->id) }}" class="inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition duration-200">
                                    <i class="fas fa-check mr-1"></i>
                                    Approve Account
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
            @endforeach
        @endif
    </div>
    
    <!-- Modern Pagination -->
    @if($accountRequests->hasPages())
        <div class="mt-6">
            <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
                <div class="-mt-px flex w-0 flex-1">
                    @if($accountRequests->onFirstPage())
                        <span class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500">
                            <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                            Previous
                        </span>
                    @else
                        <a href="{{ $accountRequests->appends(request()->except('page'))->previousPageUrl() }}" 
                           class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                            Previous
                        </a>
                    @endif
                </div>
                
                <div class="hidden md:-mt-px md:flex">
                    @php
                        $currentPage = $accountRequests->currentPage();
                        $lastPage = $accountRequests->lastPage();
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($lastPage, $currentPage + 2);
                    @endphp
                    
                    @if($startPage > 1)
                        <a href="{{ $accountRequests->appends(request()->except('page'))->url(1) }}" 
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
                            <a href="{{ $accountRequests->appends(request()->except('page'))->url($page) }}" 
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
                        <a href="{{ $accountRequests->appends(request()->except('page'))->url($lastPage) }}" 
                           class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            {{ $lastPage }}
                        </a>
                    @endif
                </div>
                
                <div class="-mt-px flex w-0 flex-1 justify-end">
                    @if($accountRequests->hasMorePages())
                        <a href="{{ $accountRequests->appends(request()->except('page'))->nextPageUrl() }}" 
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
                @if($accountRequests->onFirstPage())
                    <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500">
                        Previous
                    </span>
                @else
                    <a href="{{ $accountRequests->appends(request()->except('page'))->previousPageUrl() }}" 
                       class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Previous
                    </a>
                @endif
                
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">
                    Page {{ $accountRequests->currentPage() }} of {{ $accountRequests->lastPage() }}
                </span>
                
                @if($accountRequests->hasMorePages())
                    <a href="{{ $accountRequests->appends(request()->except('page'))->nextPageUrl() }}" 
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
                Showing {{ $accountRequests->firstItem() }} to {{ $accountRequests->lastItem() }} of {{ $accountRequests->total() }} results
            </div>
        </div>
    @endif
    
    <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden"></p>
</div>

@endsection
