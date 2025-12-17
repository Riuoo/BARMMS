@extends('admin.main.layout')

@php
    $userRole = session('user_role');
    $isSecretary = $userRole === 'secretary';
    $canPerformTransactions = $isSecretary;
@endphp

@section('title', 'New Account Requests')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Consolidated Table Dashboard Skeleton -->
    <div id="accountSkeleton">
        @include('components.loading.table-dashboard-skeleton', ['showButton' => false])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="accountRequestsContent" style="display: none;">
    <!-- Header Section -->
    <div class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Account Requests</h1>
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
    <form method="GET" action="{{ route('admin.requests.new-account-requests') }}" class="mb-2 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="searchInput" placeholder="Search account requests..."
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
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
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
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 lg:gap-4 mb-2">
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
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-times-circle text-red-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs lg:text-sm font-medium text-gray-500">Rejected</p>
                        <p class="text-lg lg:text-2xl font-bold text-gray-900" id="rejected-count">{{ $rejectedCount ?? 0 }}</p>
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <i class="fas fa-file-alt mr-2"></i>
                                            Documents
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
                                            <div class="w-full">
                                                @if($request->full_name)
                                                    <div class="text-sm font-semibold text-gray-900 mb-1">{{ $request->full_name }}</div>
                                                @endif
                                                <div class="text-sm text-gray-600">{{ $request->email }}</div>
                                                @if($request->resident_id && $request->resident)
                                                    <div class="mt-2">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                            <i class="fas fa-link mr-1"></i>Linked to Existing Resident Profile
                                                        </span>
                                                    </div>
                                                @endif
                                                @if($request->status === 'pending')
                                                    @if(isset($request->duplicate_by_email) && $request->duplicate_by_email)
                                                        <div class="mt-2">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                                <i class="fas fa-exclamation-triangle mr-1"></i>Duplicate Email
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if(isset($request->duplicate_by_name) && $request->duplicate_by_name)
                                                        <div class="mt-2">
                                                            <div class="bg-red-50 border-l-4 border-red-400 p-3 rounded">
                                                                <div class="flex items-start">
                                                                    <div class="flex-shrink-0">
                                                                        <i class="fas fa-exclamation-circle text-red-400"></i>
                                                                    </div>
                                                                    <div class="ml-3">
                                                                        <p class="text-sm text-red-700 font-medium">
                                                                            This full name is already registered. Please visit the barangay office for verification.
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if(isset($request->residency_verified) && !$request->residency_verified && $request->address)
                                                        <div class="mt-2">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                <i class="fas fa-map-marker-alt mr-1"></i>Residency Unverified
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endif
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
                                        @elseif($request->status === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                Rejected
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
                                    <td class="px-6 py-4">
                                        @if($request->verification_documents && count($request->verification_documents) > 0)
                                            <div class="space-y-1">
                                                @foreach($request->verification_documents as $doc)
                                                    <div class="flex items-center space-x-2">
                                                        @if(str_contains($doc['type'] ?? '', 'pdf'))
                                                            <i class="fas fa-file-pdf text-red-500 text-xs"></i>
                                                        @elseif(str_contains($doc['type'] ?? '', 'image'))
                                                            <i class="fas fa-image text-blue-500 text-xs"></i>
                                                        @else
                                                            <i class="fas fa-file text-gray-500 text-xs"></i>
                                                        @endif
                                                        <a href="{{ asset('storage/' . $doc['path']) }}" target="_blank" 
                                                           class="text-xs text-blue-600 hover:text-blue-800 underline truncate max-w-xs"
                                                           title="{{ $doc['name'] }}">
                                                            {{ Str::limit($doc['name'], 20) }}
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">No documents</span>
                                        @endif
                                    </td>
                                    @if($hasThreadActions && $canPerformTransactions)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center justify-center space-x-2">
                                                @if($request->status === 'pending')
                                                    <button type="button"
                                                        data-request-id="{{ $request->id }}"
                                                        data-request-email="{{ addslashes($request->email) }}"
                                                        data-approve-url="{{ route('admin.account-requests.approve', $request->id) }}"
                                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200 js-approve-request">
                                                        <i class="fas fa-check mr-1"></i>
                                                        Approve
                                                    </button>
                                                    <button type="button" onclick="openRejectModal('{{ $request->id }}', '{{ $request->email }}')" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                                                        <i class="fas fa-times mr-1"></i>
                                                        Reject
                                                    </button>
                                                @elseif($request->status === 'rejected' && $request->rejection_reason)
                                                    <button type="button" onclick="showRejectionReason('{{ addslashes($request->rejection_reason) }}')" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200">
                                                        <i class="fas fa-info-circle mr-1"></i>
                                                        View Reason
                                                    </button>
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
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center flex-1 min-w-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-clock text-blue-600"></i>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                @if($request->full_name)
                                    <h3 class="text-sm font-semibold text-gray-900 truncate mb-1">{{ $request->full_name }}</h3>
                                @endif
                                <p class="text-sm text-gray-600 truncate mb-1">{{ $request->email }}</p>
                                <p class="text-xs text-gray-500 truncate">New Account Request</p>
                                @if($request->resident_id && $request->resident)
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-link mr-1"></i>Linked to Existing Resident Profile
                                        </span>
                                    </div>
                                @endif
                                @if($request->status === 'pending')
                                    @if(isset($request->duplicate_by_email) && $request->duplicate_by_email)
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Duplicate Email
                                            </span>
                                        </div>
                                    @endif
                                    @if(isset($request->duplicate_by_name) && $request->duplicate_by_name)
                                        <div class="mt-2">
                                            <div class="bg-red-50 border-l-4 border-red-400 p-2 rounded">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        <i class="fas fa-exclamation-circle text-red-400 text-xs"></i>
                                                    </div>
                                                    <div class="ml-2">
                                                        <p class="text-xs text-red-700 font-medium">
                                                            This full name is already registered. Please visit the barangay office for verification.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(isset($request->residency_verified) && !$request->residency_verified && $request->address)
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-map-marker-alt mr-1"></i>Residency Unverified
                                            </span>
                                        </div>
                                    @endif
                                @endif
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
                                    @elseif($request->status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Rejected
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

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

                    @if($request->verification_documents && count($request->verification_documents) > 0)
                    <div class="mb-3">
                        <p class="text-xs font-medium text-gray-700 mb-2">Verification Documents:</p>
                        <div class="space-y-1">
                            @foreach($request->verification_documents as $doc)
                                <div class="flex items-center space-x-2">
                                    @if(str_contains($doc['type'] ?? '', 'pdf'))
                                        <i class="fas fa-file-pdf text-red-500 text-xs"></i>
                                    @elseif(str_contains($doc['type'] ?? '', 'image'))
                                        <i class="fas fa-image text-blue-500 text-xs"></i>
                                    @else
                                        <i class="fas fa-file text-gray-500 text-xs"></i>
                                    @endif
                                    <a href="{{ asset('storage/' . $doc['path']) }}" target="_blank" 
                                       class="text-xs text-blue-600 hover:text-blue-800 underline truncate">
                                        {{ $doc['name'] }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100">
                        @if($request->status === 'pending')
                            @if($canPerformTransactions)
                                <button type="button"
                                    data-request-id="{{ $request->id }}"
                                    data-request-email="{{ addslashes($request->email) }}"
                                    data-approve-url="{{ route('admin.account-requests.approve', $request->id) }}"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition duration-200 js-approve-request">
                                    <i class="fas fa-check mr-1"></i>
                                    Approve Account
                                </button>
                                <button type="button" onclick="openRejectModal('{{ $request->id }}', '{{ $request->email }}')" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition duration-200">
                                    <i class="fas fa-times mr-1"></i>
                                    Reject Account
                                </button>
                            @endif
                        @elseif($request->status === 'rejected' && $request->rejection_reason)
                            <button type="button" onclick="showRejectionReason('{{ addslashes($request->rejection_reason) }}')" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                                <i class="fas fa-info-circle mr-1"></i>
                                View Rejection Reason
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach
            @endif
        </div>
        
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
                
                <div class="mt-4 text-center text-sm text-gray-500">
                    Showing {{ $accountRequests->firstItem() }} to {{ $accountRequests->lastItem() }} of {{ $accountRequests->total() }} results
                </div>
            </div>
        @endif
        
        <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden"></p>
    </div>
</div>

<!-- Approve Confirmation Modal -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900">Approve Account Request</h3>
                <p class="text-sm text-gray-500">Confirm before creating the account.</p>
            </div>
        </div>
        <p class="text-gray-700 mb-6">Are you sure you want to approve <span id="approveEmail" class="font-semibold"></span>? This will create their barangay resident account.</p>
        <form id="approveForm" method="POST" class="inline">
            @csrf
            @method('PUT')
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition duration-200">
                    Confirm Approval
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        // Hide consolidated skeleton
        const skeleton = document.getElementById('accountSkeleton');
        if (skeleton) skeleton.style.display = 'none';

        // Show content
        const content = document.getElementById('accountRequestsContent');
        if (content) content.style.display = 'block';
    }, 1000);
});

function openApproveModal(requestId, email, approveUrl) {
    const emailTarget = document.getElementById('approveEmail');
    if (emailTarget) emailTarget.textContent = email || 'this account request';

    const form = document.getElementById('approveForm');
    if (form) form.action = approveUrl || `/admin/account-requests/${requestId}/approve`;

    const modal = document.getElementById('approveModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeApproveModal() {
    const modal = document.getElementById('approveModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function openRejectModal(requestId, email) {
    // Create modal HTML
    const modalHTML = `
        <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: block;">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Reject Account Request</h3>
                        <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Email: <strong>${email}</strong></p>
                        <p class="text-sm text-gray-600 mb-4">Please provide a reason for rejection (minimum 10 characters):</p>
                        <form id="rejectForm" method="POST" action="/admin/new-account-requests/${requestId}/reject">
                            @csrf
                            <textarea 
                                id="rejection_reason" 
                                name="rejection_reason" 
                                rows="4" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                                placeholder="Enter rejection reason" 
                                required 
                                minlength="10" 
                                maxlength="500"></textarea>
                            <div class="mt-2 text-xs text-gray-500">
                                <span id="charCount">0</span>/500 characters
                            </div>
                            <div class="flex justify-end space-x-3 mt-4">
                                <button 
                                    type="button" 
                                    onclick="closeRejectModal()" 
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Cancel
                                </button>
                                <button 
                                    type="submit" 
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <i class="fas fa-times mr-1"></i>Reject Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Insert modal into body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Character counter
    const textarea = document.getElementById('rejection_reason');
    const charCount = document.getElementById('charCount');
    if (textarea && charCount) {
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }
    
    // Form validation
    const form = document.getElementById('rejectForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const reason = document.getElementById('rejection_reason').value.trim();
            if (reason.length < 10) {
                e.preventDefault();
                alert('Please provide a rejection reason with at least 10 characters.');
                return false;
            }
        });
    }
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    if (modal) {
        modal.remove();
    }
}

function showRejectionReason(reason) {
    // Create modal HTML
    const modalHTML = `
        <div id="reasonModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: block;">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Rejection Reason</h3>
                        <button onclick="closeReasonModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="mb-4">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">${reason}</p>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button 
                            onclick="closeReasonModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Insert modal into body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

function closeReasonModal() {
    const modal = document.getElementById('reasonModal');
    if (modal) {
        modal.remove();
    }
}

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.id === 'rejectModal' || e.target.id === 'reasonModal' || e.target.id === 'approveModal') {
        if (e.target.id === 'rejectModal') closeRejectModal();
        if (e.target.id === 'reasonModal') closeReasonModal();
        if (e.target.id === 'approveModal') closeApproveModal();
    }

    const approveBtn = e.target.closest('.js-approve-request');
    if (approveBtn) {
        const requestId = approveBtn.getAttribute('data-request-id');
        const email = approveBtn.getAttribute('data-request-email');
        const approveUrl = approveBtn.getAttribute('data-approve-url');
        openApproveModal(requestId, email, approveUrl);
    }
});
</script>
@endpush
