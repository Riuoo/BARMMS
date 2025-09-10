{{-- resources/views/admin/blotter-reports.blade.php --}}

@extends('admin.main.layout')

@php
    $userRole = session('user_role');
    $isAdmin = $userRole === 'admin';
    $isSecretary = $userRole === 'secretary';
    $canPerformTransactions = $isAdmin || $isSecretary;
@endphp

@section('title', 'Blotter Reports')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Consolidated Table Dashboard Skeleton -->
    <div id="blotterSkeleton">
        @include('components.loading.table-dashboard-skeleton', ['buttonCount' => 2])
    </div>
    <!-- Real Content (hidden initially) -->
    <div id="blotterHeaderContent" style="display: none;">
        <!-- Header Section -->
        <div class="mb-2">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Blotter Reports</h1>
                    <p class="text-gray-600">Manage and review incident reports from residents</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-2">
                    @if($canPerformTransactions)
                    <a href="{{ route('admin.blotter-reports.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Create New Report
                    </a>
                    @endif
                    <a href="{{ route('clustering.blotter.analysis') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
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
        <form method="GET" action="{{ route('admin.blotter-reports') }}" class="mb-2 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <!-- Search Input -->
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="blotterSearchInput" placeholder="Search reports..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500" value="{{ request('search') }}">
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
                    <a href="{{ route('admin.blotter-reports') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-undo mr-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-file-alt text-red-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs lg:text-sm font-medium text-gray-500">Total Reports</p>
                        <p class="text-lg lg:text-2xl font-bold text-gray-900" id="total-count">{{ $totalReports }}</p>
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

        <!-- Reports List -->
        @if($blotterRequests->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No blotter reports found</h3>
                <p class="text-gray-500">No incident reports have been submitted yet.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.blotter-reports.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Create First Report
                    </a>
                </div>
            </div>
        @else
            @php
                $hasThreadActions = collect($blotterRequests->items())
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
                                        Complainant
                                    </div>
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-user-tag mr-2"></i>
                                        Recipient
                                    </div>
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-tag mr-2"></i>
                                        Type
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
                                        <i class="fas fa-image mr-2"></i>
                                        Media
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
                                        Created
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
                        <tbody class="bg-white divide-y divide-gray-200" id="blotterTableBody">
                            @foreach($blotterRequests as $request)
                            <tr class="blotter-item hover:bg-gray-50 transition duration-150" data-status="{{ $request->status }}" data-summon="{{ optional($request->summon_date)->format('Y-m-d\TH:i') }}" data-approved="{{ optional($request->approved_at)->format('Y-m-d\TH:i') }}">
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $request->resident->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-900">{{ $request->recipient_name }}</div>
                                    <div class="text-sm text-gray-500">
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-tag mr-1"></i>
                                        Incident Report
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs">
                                        <div class="truncate" title="{{ $request->description }}">
                                            {{ Str::limit($request->description, 40) }}
                                        </div>
                                        @if(strlen($request->description) > 40)
                                            <button 
                                                class="text-xs text-blue-600 hover:text-blue-800 underline mt-1 view-full-btn"
                                                data-description="{{ $request->description }}"
                                                data-user-name="{{ $request->resident->name ?? 'N/A' }}">
                                                View Full
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    @if($request->hasMedia())
                                        <div class="text-sm text-gray-900">{{ $request->media_count }} files</div>
                                        <div class="text-sm text-gray-500">
                                            @if($request->media_count <= 2)
                                                @foreach($request->media_files as $file)
                                                    <div class="flex items-center space-x-2 mb-1">
                                                        @if(str_contains($file['type'], 'image'))
                                                            <i class="fas fa-image text-blue-500"></i>
                                                        @elseif(str_contains($file['type'], 'video'))
                                                            <i class="fas fa-video text-purple-500"></i>
                                                        @elseif(str_contains($file['type'], 'pdf'))
                                                            <i class="fas fa-file-pdf text-red-500"></i>
                                                        @else
                                                            <i class="fas fa-file text-gray-500"></i>
                                                        @endif
                                                        <a href="{{ asset('storage/' . $file['path']) }}" target="_blank" class="underline text-blue-600 hover:text-blue-800 text-xs">
                                                            {{ $file['name'] }}
                                                        </a>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-paperclip text-gray-400"></i>
                                                    <span class="text-xs">{{ $request->media_count }} attachments</span>
                                                    <button onclick="viewAllMedia('{{ $request->id }}')" class="text-xs text-blue-600 hover:text-blue-800 underline">
                                                        View All
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">No files</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
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
                                    <div class="text-sm text-gray-900">{{ $request->created_at->format('M d, Y') }}</div>
                                </td>
                                @if($hasThreadActions && $canPerformTransactions)
                                    <td class="px-3 py-4 text-center">
                                        <div class="flex flex-col space-y-1">
                                            
                                            @if($request->status === 'pending')
                                                @if($canPerformTransactions)
                                                <button type="button" onclick="openApproveModal('{{ $request->id }}')" 
                                                        class="inline-flex items-center justify-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 transition duration-200 w-full">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Approve
                                                </button>
                                                @endif
                                            @elseif($request->status === 'approved')
                                                @if($request->attempts < 3)
                                                    <button type="button" onclick="openNewSummonModal('{{ $request->id }}')" 
                                                            class="inline-flex items-center justify-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-teal-600 hover:bg-teal-700 transition duration-200 w-full">
                                                        <i class="fas fa-file-alt mr-1"></i>
                                                        Summon
                                                    </button>
                                                @else
                                                    <button class="inline-flex items-center justify-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-gray-400 cursor-not-allowed w-full" disabled>
                                                        <i class="fas fa-file-alt mr-1"></i>
                                                        Limit
                                                    </button>
                                                @endif
                                                <form onsubmit="return completeAndDownload(event, '{{ $request->id }}')" class="w-full">
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
            <div class="md:hidden space-y-4" id="blotterMobileCards">
                @foreach($blotterRequests as $request)
                <div class="blotter-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200" data-status="{{ $request->status }}">
                    <!-- Header Section -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center flex-1 min-w-0">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-file-alt text-red-600"></i>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <h3 class="text-sm font-medium text-gray-900 truncate">{{ $request->resident->name ?? 'N/A' }}</h3>
                                <p class="text-sm text-gray-500 truncate">vs {{ $request->recipient_name }}</p>
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

                    <!-- Media Files Section -->
                    @if($request->hasMedia())
                    <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-gray-700">
                                <i class="fas fa-paperclip mr-1"></i>
                                {{ $request->media_count }} attachment{{ $request->media_count > 1 ? 's' : '' }}
                            </span>
                            <button onclick="viewAllMedia('{{ $request->id }}')" 
                                    class="text-xs text-blue-600 hover:text-blue-800 underline">
                                View All
                            </button>
                        </div>
                        @if($request->media_count <= 3)
                            <div class="space-y-1">
                                @foreach($request->media_files as $file)
                                    <div class="flex items-center space-x-2">
                                        @if(str_contains($file['type'], 'image'))
                                            <i class="fas fa-image text-blue-500 text-xs"></i>
                                        @elseif(str_contains($file['type'], 'video'))
                                            <i class="fas fa-video text-purple-500 text-xs"></i>
                                        @elseif(str_contains($file['type'], 'pdf'))
                                            <i class="fas fa-file-pdf text-red-500 text-xs"></i>
                                        @else
                                            <i class="fas fa-file text-gray-500 text-xs"></i>
                                        @endif
                                        <a href="{{ asset('storage/' . $file['path']) }}" target="_blank" 
                                           class="text-xs text-blue-600 hover:text-blue-800 underline truncate">
                                            {{ $file['name'] }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @endif

                    <!-- Actions Section -->
                    <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100">
                        <button onclick="viewBlotterDetails('{{ $request->id }}')" 
                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                            <i class="fas fa-eye mr-1"></i>
                            View Details
                        </button>
                        
                        @if($request->status === 'pending')
                            @if($canPerformTransactions)
                            <form onsubmit="return approveAndDownloadBlotter(event, '{{ $request->id }}')" class="inline">
                                @csrf
                                <input type="date" name="hearing_date" required class="hidden" value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition duration-200">
                                    <i class="fas fa-check mr-1"></i>
                                    Approve
                                </button>
                            </form>
                            @endif
                        @elseif($request->status === 'approved')
                            @if($canPerformTransactions)
                            @if($request->attempts < 3)
                                <form onsubmit="return generateNewSummonPdf(event, '{{ $request->id }}')" class="inline">
                                    @csrf
                                    <input type="date" name="new_summon_date" required class="hidden" value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 transition duration-200">
                                        <i class="fas fa-file-alt mr-1"></i>
                                        New Summon
                                    </button>
                                </form>
                            @else
                                <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-gray-400 cursor-not-allowed" disabled>
                                    <i class="fas fa-file-alt mr-1"></i>
                                    New Summon (Limit Reached)
                                </button>
                            @endif
                            <form onsubmit="return completeAndDownload(event, '{{ $request->id }}')" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition duration-200">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Complete
                                </button>
                            </form>
                            @endif
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif
        
        <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden"></p>
    </div>
    <div id="blotterPaginationContent" style="display: none;">
        <!-- Modern Pagination -->
        @if($blotterRequests->hasPages())
            <div class="mt-6">
                <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
                    <div class="-mt-px flex w-0 flex-1">
                        @if($blotterRequests->onFirstPage())
                            <span class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500">
                                <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                                Previous
                            </span>
                        @else
                            <a href="{{ $blotterRequests->appends(request()->except('page'))->previousPageUrl() }}" 
                               class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                                Previous
                            </a>
                        @endif
                    </div>
                    
                    <div class="hidden md:-mt-px md:flex">
                        @php
                            $currentPage = $blotterRequests->currentPage();
                            $lastPage = $blotterRequests->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                        @endphp
                        
                        @if($startPage > 1)
                            <a href="{{ $blotterRequests->appends(request()->except('page'))->url(1) }}" 
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
                                <a href="{{ $blotterRequests->appends(request()->except('page'))->url($page) }}" 
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
                            <a href="{{ $blotterRequests->appends(request()->except('page'))->url($lastPage) }}" 
                               class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                {{ $lastPage }}
                            </a>
                        @endif
                    </div>
                    
                    <div class="-mt-px flex w-0 flex-1 justify-end">
                        @if($blotterRequests->hasMorePages())
                            <a href="{{ $blotterRequests->appends(request()->except('page'))->nextPageUrl() }}" 
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
                    @if($blotterRequests->onFirstPage())
                        <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500">
                            Previous
                        </span>
                    @else
                        <a href="{{ $blotterRequests->appends(request()->except('page'))->previousPageUrl() }}" 
                           class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Previous
                        </a>
                    @endif
                    
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">
                        Page {{ $blotterRequests->currentPage() }} of {{ $blotterRequests->lastPage() }}
                    </span>
                    
                    @if($blotterRequests->hasMorePages())
                        <a href="{{ $blotterRequests->appends(request()->except('page'))->nextPageUrl() }}" 
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
                    Showing {{ $blotterRequests->firstItem() }} to {{ $blotterRequests->lastItem() }} of {{ $blotterRequests->total() }} results
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modals -->
@include('admin.modals.blotter-modals')

<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        // Hide consolidated skeleton
        const skeleton = document.getElementById('blotterSkeleton');
        if (skeleton) skeleton.style.display = 'none';

        // Show all real content elements
        const contents = [
            'blotterHeaderContent',
            'blotterPaginationContent'
        ];
        contents.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'block';
        });
    }, 1000);

    // Add event listeners for view full buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-full-btn')) {
            const description = e.target.getAttribute('data-description');
            const userName = e.target.getAttribute('data-user-name');
            showFullDescription(description, userName);
        }
    });
});

// Function to view all media files
function viewAllMedia(blotterId) {
    // Get the blotter request data
    fetch(`/admin/blotter-reports/${blotterId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.media_files && data.media_files.length > 0) {
                showMediaModal(data.media_files, data.user_name);
            } else {
                alert('No media files found');
            }
        })
        .catch(error => {
            console.error('Error fetching media files:', error);
            alert('Error loading media files');
        });
}

// Function to show media modal
function showMediaModal(mediaFiles, userName) {
    // Create modal HTML
    const modalHTML = `
        <div id="mediaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Media Files - ${userName}
                        </h3>
                        <button onclick="closeMediaModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            ${mediaFiles.map(file => `
                                <div class="border rounded-lg p-3">
                                    <div class="flex items-center space-x-3 mb-2">
                                        ${getFileIcon(file.type)}
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">${file.name}</p>
                                            <p class="text-xs text-gray-500">${formatFileSize(file.size || 0)}</p>
                                        </div>
                                    </div>
                                    <a href="${file.url}" target="_blank" 
                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition duration-200">
                                        <i class="fas fa-external-link-alt mr-1"></i>
                                        View File
                                    </a>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// Function to close media modal
function closeMediaModal() {
    const modal = document.getElementById('mediaModal');
    if (modal) {
        modal.remove();
    }
}

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

// Function to get file icon based on type
function getFileIcon(type) {
    if (type.includes('image')) {
        return '<i class="fas fa-image text-blue-500 text-lg"></i>';
    } else if (type.includes('video')) {
        return '<i class="fas fa-video text-purple-500 text-lg"></i>';
    } else if (type.includes('pdf')) {
        return '<i class="fas fa-file-pdf text-red-500 text-lg"></i>';
    } else {
        return '<i class="fas fa-file text-gray-500 text-lg"></i>';
    }
}

// Function to format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Modal functions are defined in the partial file (admin.modals.blotter-modals)

function completeAndDownload(event, blotterId) {
    event.preventDefault();
    const form = event.target;
    const csrfToken = form.querySelector('input[name="_token"]').value;
    
    fetch(`/admin/blotter-reports/${blotterId}/complete`, {
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
            localStorage.setItem('showBlotterCompleteNotify', '1');
            const url = window.URL.createObjectURL(blob);
            const disposition = response.headers.get('content-disposition');
            let filename = 'blotter_report.pdf';
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
            let errorMsg = 'Error completing and downloading PDF.';
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
        alert('Error completing and downloading PDF.');
        console.error(error);
    });
    return false;
}

function approveAndDownloadBlotter(event, blotterId) {
    event.preventDefault();
    const form = event.target;
    const csrfToken = form.querySelector('input[name="_token"]').value;
    const hearingDate = form.querySelector('input[name="hearing_date"]').value;
    
    fetch(`/admin/blotter-reports/${blotterId}/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/pdf',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ hearing_date: hearingDate })
    })
    .then(async response => {
        const contentType = response.headers.get('content-type') || '';
        if (response.ok && contentType.includes('application/pdf')) {
            const blob = await response.blob();
            localStorage.setItem('showBlotterApproveNotify', '1');
            const url = window.URL.createObjectURL(blob);
            const disposition = response.headers.get('content-disposition');
            let filename = 'blotter_report.pdf';
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

function generateNewSummonPdf(event, blotterId) {
    event.preventDefault();
    const form = event.target;
    const csrfToken = form.querySelector('input[name="_token"]').value;
    const newSummonDate = form.querySelector('input[name="new_summon_date"]').value;
    
    fetch(`/admin/blotter-reports/${blotterId}/new-summons`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/pdf',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ new_summon_date: newSummonDate })
    })
    .then(async response => {
        const contentType = response.headers.get('content-type') || '';
        if (response.ok && contentType.includes('application/pdf')) {
            const blob = await response.blob();
            localStorage.setItem('showBlotterSummonNotify', '1');
            const url = window.URL.createObjectURL(blob);
            const disposition = response.headers.get('content-disposition');
            let filename = 'blotter_report.pdf';
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
            let errorMsg = 'Error generating new summon PDF.';
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
        alert('Error generating new summon PDF.');
        console.error(error);
    });
    return false;
}

// Show notification after reload if flag is set
window.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('showBlotterCompleteNotify') === '1') {
        localStorage.removeItem('showBlotterCompleteNotify');
        if (typeof notify === 'function') {
            notify('success', 'Blotter report completed and PDF downloaded.');
        }
    }
    if (localStorage.getItem('showBlotterApproveNotify') === '1') {
        localStorage.removeItem('showBlotterApproveNotify');
        if (typeof notify === 'function') {
            notify('success', 'Blotter report approved and summon PDF downloaded.');
        }
    }
    if (localStorage.getItem('showBlotterSummonNotify') === '1') {
        localStorage.removeItem('showBlotterSummonNotify');
        if (typeof notify === 'function') {
            notify('success', 'New summon notice generated and downloaded.');
        }
    }
    if (localStorage.getItem('showBlotterCreateNotify') === '1') {
        localStorage.removeItem('showBlotterCreateNotify');
        if (typeof notify === 'function') {
            notify('success', 'Blotter report created and PDF downloaded.');
        }
    }
});
</script>
@endsection