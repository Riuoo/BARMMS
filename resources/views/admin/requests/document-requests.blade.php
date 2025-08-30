@extends('admin.main.layout')

@php
    $userRole = session('user_role');
    $isAdmin = $userRole === 'admin';
    $isSecretary = $userRole === 'secretary';
    $canPerformTransactions = $isAdmin || $isSecretary;
@endphp

@section('title', 'Document Requests')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Header Section -->
    <div class="mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Document Requests</h1>
                <p class="text-gray-600">Manage and process document requests from residents</p>
            </div>
            <div class="mt-4 sm:mt-0">
                @if($canPerformTransactions)
                <a href="{{ route('admin.document-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Create New Request
                </a>
                @endif
                @if($canPerformTransactions)
                <a href="{{ route('admin.templates.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                    <i class="fas fa-file-code mr-2"></i>
                    Manage Templates
                </a>
                @endif
                <a href="{{ route('clustering.document.analysis') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition duration-200">
                    <i class="fas fa-chart-line mr-2"></i>
                    Analysis Dashboard
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

    <!-- Filters and Search -->
    <form method="GET" action="{{ route('admin.document-requests') }}" class="mb-3 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="searchInput" placeholder="Search requests..."
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
                <a href="{{ route('admin.document-requests') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
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
                        <i class="fas fa-file-signature text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Total Requests</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $totalRequests }}</p>
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

    <!-- Requests List -->
    @if($documentRequests->isEmpty())
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-file-signature text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No document requests found</h3>
            <p class="text-gray-500">No document requests have been submitted yet.</p>
            <div class="mt-6">
                @if($canPerformTransactions)
                <a href="{{ route('admin.document-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Create First Request
                </a>
                @endif
            </div>
        </div>
    @else
        @php
            $hasThreadActions = collect($documentRequests->items())
                ->contains(function ($r) { return in_array($r->status, ['pending','approved']); });
        @endphp
        <!-- Desktop Table (hidden on mobile) -->
        <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-user mr-2"></i>
                                    Requester
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-file-alt mr-2"></i>
                                    Document Type
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-align-left mr-2"></i>
                                    Description
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Status
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-2"></i>
                                    Requested
                                </div>
                            </th>
                            @if($hasThreadActions && $canPerformTransactions)
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    <div class="flex items-center justify-center">
                                        <i class="fas fa-cogs mr-2"></i>
                                        Actions
                                    </div>
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="documentTableBody">
                        @foreach($documentRequests as $request)
                        <tr class="document-item hover:bg-gray-50 transition duration-150" data-status="{{ $request->status }}">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex flex-row items-center gap-2">
                                     <span class="text-sm font-medium text-gray-900">{{ $request->resident->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900"><i class="fas fa-file-alt mr-1"></i>{{ $request->document_type }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900 max-w-xs">
                                    <div class="truncate" title="{{ $request->description }}">
                                        {{ Str::limit($request->description, 40) }}
                                    </div>
                                    @if(strlen($request->description) > 40)
                                        <button data-description="{{ $request->description }}" 
                                                 data-user-name="{{ $request->resident->name ?? 'N/A' }}"
                                                class="view-full-btn text-xs text-blue-600 hover:text-blue-800 underline mt-1">
                                            View Full
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 justify-center">
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
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900"><i class="fas fa-calendar mr-1"></i>{{ $request->created_at->format('M d, Y') }}</div>   
                            </td>
                            @if($hasThreadActions && $canPerformTransactions)
                                <td class="px-3 py-4 text-center">
                                    <div class="flex flex-col space-y-1">
                                        @if($request->status === 'pending')
                                            <form onsubmit="return approveAndDownload(event, '{{ $request->id }}')" class="w-full">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 transition duration-200 w-full">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Approve
                                                </button>
                                            </form>
                                        @endif
                                        @if($request->status === 'approved')
                                            <form onsubmit="return generatePdfAndComplete(event, '{{ $request->id }}')" class="w-full">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center px-2 py-1 border border-blue-500 text-xs font-medium rounded text-blue-600 bg-white hover:bg-blue-50 transition duration-200 w-full">
                                                    <i class="fas fa-file-pdf mr-1"></i>
                                                    Generate PDF
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.document-requests.complete', $request->id) }}" method="POST" class="w-full">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 transition duration-200 w-full">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Complete
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

        <!-- Mobile Cards (hidden on desktop) -->
        <div class="md:hidden space-y-4" id="mobileDocumentCards">
            @foreach($documentRequests as $request)
            <div class="document-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200" data-status="{{ $request->status }}">
                <!-- Header Section -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-file-signature text-blue-600"></i>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                             <h3 class="text-sm font-medium text-gray-900 truncate">{{ $request->resident->name ?? 'N/A' }}</h3>
                            <p class="text-sm text-gray-500 truncate">{{ $request->document_type }}</p>
                            <div class="flex items-center mt-1">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($request->status === 'approved') bg-blue-100 text-blue-800
                                    @elseif($request->status === 'completed') bg-green-100 text-green-800
                                    @endif">
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ ucfirst($request->status) }}
                                </span>
                                <span class="ml-2 text-xs text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $request->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="mb-3">
                    <div class="description-container">
                        <p class="text-sm text-gray-600 leading-relaxed description-text" id="description-{{ $request->id }}">
                            <i class="fas fa-align-left mr-1 text-gray-400"></i>
                            <span class="description-short">{{ Str::limit($request->description, 80) }}</span>
                            @if(strlen($request->description) > 80)
                                <span class="description-full hidden">{{ $request->description }}</span>
                                <button onclick="toggleDescription('{{ $request->id }}')" 
                                        class="text-blue-600 hover:text-blue-800 underline text-xs ml-1 toggle-desc-btn">
                                    Read More
                                </button>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Document Type Badge -->
                <div class="mb-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-file-alt mr-1"></i>
                        {{ $request->document_type }}
                    </span>
                </div>

                <!-- Actions Section -->
                <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100">
                    <button onclick="viewDocumentDetails('{{ $request->id }}')"
                            type="button"
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                        <i class="fas fa-eye mr-1"></i>
                        View Details
                    </button>
                    
                    @if($request->status === 'pending')
                        @if($canPerformTransactions)
                        <form onsubmit="return approveAndDownload(event, '{{ $request->id }}')" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition duration-200">
                                <i class="fas fa-check mr-1"></i>
                                Approve
                            </button>
                        </form>
                        @endif
                    @endif
                    
                    @if($request->status === 'approved')
                        <form onsubmit="return generatePdfAndComplete(event, '{{ $request->id }}')" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-blue-500 text-xs font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                                <i class="fas fa-file-pdf mr-1"></i>
                                Generate PDF
                            </button>
                        </form>
                        <form action="{{ route('admin.document-requests.complete', $request->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition duration-200">
                                <i class="fas fa-check-circle mr-1"></i>
                                Complete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <!-- Modern Pagination -->
    @if($documentRequests->hasPages())
        <div class="mt-6">
            <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
                <div class="-mt-px flex w-0 flex-1">
                    @if($documentRequests->onFirstPage())
                        <span class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500">
                            <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                            Previous
                        </span>
                    @else
                        <a href="{{ $documentRequests->appends(request()->except('page'))->previousPageUrl() }}" 
                           class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                            Previous
                        </a>
                    @endif
                </div>
                
                <div class="hidden md:-mt-px md:flex">
                    @php
                        $currentPage = $documentRequests->currentPage();
                        $lastPage = $documentRequests->lastPage();
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($lastPage, $currentPage + 2);
                    @endphp
                    
                    @if($startPage > 1)
                        <a href="{{ $documentRequests->appends(request()->except('page'))->url(1) }}" 
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
                            <a href="{{ $documentRequests->appends(request()->except('page'))->url($page) }}" 
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
                        <a href="{{ $documentRequests->appends(request()->except('page'))->url($lastPage) }}" 
                           class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            {{ $lastPage }}
                        </a>
                    @endif
                </div>
                
                <div class="-mt-px flex w-0 flex-1 justify-end">
                    @if($documentRequests->hasMorePages())
                        <a href="{{ $documentRequests->appends(request()->except('page'))->nextPageUrl() }}" 
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
                @if($documentRequests->onFirstPage())
                    <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500">
                        Previous
                    </span>
                @else
                    <a href="{{ $documentRequests->appends(request()->except('page'))->previousPageUrl() }}" 
                       class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Previous
                    </a>
                @endif
                
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">
                    Page {{ $documentRequests->currentPage() }} of {{ $documentRequests->lastPage() }}
                </span>
                
                @if($documentRequests->hasMorePages())
                    <a href="{{ $documentRequests->appends(request()->except('page'))->nextPageUrl() }}" 
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
                Showing {{ $documentRequests->firstItem() }} to {{ $documentRequests->lastItem() }} of {{ $documentRequests->total() }} results
            </div>
        </div>
    @endif
    
    <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden"></p>
</div>

<!-- Modals -->
@include('admin.modals.document-modals')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterBtns = document.querySelectorAll('.filter-btn');
    const documentItems = document.querySelectorAll('.document-item');
    const documentCards = document.querySelectorAll('.document-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active button
            filterBtns.forEach(b => {
                b.classList.remove('active', 'bg-green-100', 'text-green-800');
                b.classList.add('bg-gray-100', 'text-gray-600');
            });
            this.classList.add('active', 'bg-green-100', 'text-green-800');
            this.classList.remove('bg-gray-100', 'text-gray-600');
            
            // Filter documents
            const allItems = [...documentItems, ...documentCards];
            allItems.forEach(item => {
                const status = item.dataset.status;
                if (filter === 'all' || status === filter) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Update counts
            function updateCounts() {
                // No longer update the statistics cards! The statistics cards always show the Blade-rendered totals.
                // This function is now empty or can be removed if not used elsewhere.
            }
            // Initial count update
            // updateCounts(); // No longer needed
            // Update counts on window resize
            // window.removeEventListener('resize', updateCounts); // No longer needed
        });
    });
    
    // Update counts
    function updateCounts() {
        // No longer update the statistics cards! The statistics cards always show the Blade-rendered totals.
        // This function is now empty or can be removed if not used elsewhere.
    }
    // Initial count update
    // updateCounts(); // No longer needed
    // Update counts on window resize
    // window.removeEventListener('resize', updateCounts); // No longer needed
});

// Function to toggle description visibility
function toggleDescription(requestId) {
    const descriptionContainer = document.getElementById(`description-${requestId}`);
    const shortDesc = descriptionContainer.querySelector('.description-short');
    const fullDesc = descriptionContainer.querySelector('.description-full');
    const toggleBtn = descriptionContainer.querySelector('.toggle-desc-btn');
    
    if (fullDesc.classList.contains('hidden')) {
        // Show full description
        shortDesc.classList.add('hidden');
        fullDesc.classList.remove('hidden');
        toggleBtn.textContent = 'Read Less';
    } else {
        // Show short description
        shortDesc.classList.remove('hidden');
        fullDesc.classList.add('hidden');
        toggleBtn.textContent = 'Read More';
    }
}

// Function to show full description in modal
function showFullDescription(description, userName) {
    // Create modal HTML
    const modalHTML = `
        <div id="descriptionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Full Description - ${userName}
                        </h3>
                        <button onclick="closeDescriptionModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">
                            ${description}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// Function to close description modal
function closeDescriptionModal() {
    const modal = document.getElementById('descriptionModal');
    if (modal) {
        modal.remove();
    }
}

// Add event listeners for view full buttons
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for view full buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-full-btn')) {
            const description = e.target.getAttribute('data-description');
            const userName = e.target.getAttribute('data-user-name');
            showFullDescription(description, userName);
        }
    });
});

// Document functions are defined in the partial file (admin.modals.document-modals)

function generatePdfAndComplete(event, requestId) {
    event.preventDefault();
    const form = event.target;
    const csrfToken = form.querySelector('input[name="_token"]').value;
    fetch(`/admin/document-requests/${requestId}/pdf`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/pdf'
        }
    })
    .then(async response => {
        const contentType = response.headers.get('content-type') || '';
        if (response.ok && contentType.includes('application/pdf')) {
            const blob = await response.blob();
            localStorage.setItem('showGeneratePdfNotify', '1');
            const url = window.URL.createObjectURL(blob);
            const disposition = response.headers.get('content-disposition');
            let filename = 'document.pdf';
            if (disposition && disposition.indexOf('filename=') !== -1) {
                let matches = disposition.match(/filename="?([^";]+)"?/);
                if (matches && matches[1]) filename = matches[1];
            }
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
            setTimeout(() => location.reload(), 1000);
        } else {
            let errorMsg = 'Error generating and downloading PDF.';
            try {
                const text = await response.text();
                if (text.includes('This user account is inactive')) {
                    errorMsg = 'This user account is inactive and cannot make transactions.';
                } else if (text.includes('<ul class="list-disc')) {
                    const match = text.match(/<li>(.*?)<\/li>/);
                    if (match) errorMsg = match[1];
                }
            } catch (e) {}
            alert(errorMsg);
        }
    })
    .catch(error => {
        alert('Error generating and downloading PDF.');
        console.error(error);
    });
    return false;
}

function approveAndDownload(event, requestId) {
    event.preventDefault();
    const form = event.target;
    const csrfToken = form.querySelector('input[name="_token"]').value;
    fetch(`/admin/document-requests/${requestId}/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/pdf'
        }
    })
    .then(async response => {
        const contentType = response.headers.get('content-type') || '';
        if (response.ok && contentType.includes('application/pdf')) {
            const blob = await response.blob();
            localStorage.setItem('showApproveNotify', '1');
            const url = window.URL.createObjectURL(blob);
            const disposition = response.headers.get('content-disposition');
            let filename = 'document.pdf';
            if (disposition && disposition.indexOf('filename=') !== -1) {
                let matches = disposition.match(/filename="?([^";]+)"?/);
                if (matches && matches[1]) filename = matches[1];
            }
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
            setTimeout(() => location.reload(), 1000);
        } else {
            let errorMsg = 'Error approving and downloading PDF.';
            try {
                const text = await response.text();
                if (text.includes('This user account is inactive')) {
                    errorMsg = 'This user account is inactive and cannot make transactions.';
                } else if (text.includes('<ul class="list-disc')) {
                    const match = text.match(/<li>(.*?)<\/li>/);
                    if (match) errorMsg = match[1];
                }
            } catch (e) {}
            alert(errorMsg);
        }
    })
    .catch(error => {
        alert('Error approving and downloading PDF.');
        console.error(error);
    });
    return false;
}

// Show notification after reload if flag is set
window.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('showApproveNotify') === '1') {
        localStorage.removeItem('showApproveNotify');
        if (typeof notify === 'function') {
            notify('success', 'Document approved and PDF downloaded.');
        }
    }
    if (localStorage.getItem('showGeneratePdfNotify') === '1') {
        localStorage.removeItem('showGeneratePdfNotify');
        if (typeof notify === 'function') {
            notify('success', 'PDF generated and downloaded.');
        }
    }
    if (localStorage.getItem('showDocumentCreateNotify') === '1') {
        localStorage.removeItem('showDocumentCreateNotify');
        if (typeof notify === 'function') {
            notify('success', 'Document request created and PDF downloaded.');
        }
    }
});
</script>
@endsection
