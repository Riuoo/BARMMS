@extends('resident.layout')

@section('title', 'My Requests')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Consolidated Skeleton -->
    <div id="residentRequestsSkeletonWrapper">
        @include('components.loading.resident-requests-skeleton')
    </div>

    <!-- Real Content Wrapper (hidden initially) -->
    <div id="residentRequestsContent" style="display: none;">
    <!-- Header Section -->
    <div class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Requests</h1>
                <p class="text-gray-600">Track and manage all your submitted requests</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <a href="{{ route('resident.request_blotter_report') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    New Blotter Report
                </a>
                <a href="{{ route('resident.request_document_request') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    New Document Request
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

    <!-- Notifications hidden on My Requests as per requirement -->

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

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 lg:gap-4 mb-2">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-red-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Blotter Reports</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $blotterRequests->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-signature text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Document Requests</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $documentRequests->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-indigo-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Community Concerns</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $communityConcerns->count() }}</p>
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
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $blotterRequests->where('status', 'pending')->count() + $documentRequests->where('status', 'pending')->count() + $communityConcerns->where('status', 'pending')->count() }}</p>
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
                    <p class="text-sm font-medium text-gray-500">Completed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $blotterRequests->where('status', 'completed')->count() + $documentRequests->where('status', 'completed')->count() + $communityConcerns->where('status', 'resolved')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <form method="GET" action="{{ route('resident.my-requests') }}" class="mb-2 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
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
                    Filter
                </button>
                <a href="{{ route('resident.my-requests') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Requests List -->
            @if($blotterRequests->isEmpty() && $documentRequests->isEmpty() && $communityConcerns->isEmpty())
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                        <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No requests found</h3>
                    <p class="text-gray-500">You haven't submitted any requests yet.</p>
                    <div class="mt-6 flex flex-col sm:flex-row gap-2 justify-center">
                        <a href="{{ route('resident.request_blotter_report') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Submit Blotter Report
                        </a>
                        <a href="{{ route('resident.request_community_concern') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Community Concern
                        </a>
                        <a href="{{ route('resident.request_document_request') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Request Document
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
                                    <i class="fas fa-tag mr-2"></i>
                                    Type
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-align-left mr-2"></i>
                                    Details
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
                                    Submitted
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="requestsTableBody">
                        @foreach($blotterRequests as $request)
                        <tr class="request-item hover:bg-gray-50 transition duration-150" data-type="blotter" data-status="{{ $request->status }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Blotter Report</div>
                                        <div class="text-sm text-gray-500">{{ $request->respondent->name ?? 'N/A' }}</div>
                                        <!-- Progress Tracker -->
                                        <div class="flex items-center space-x-2 mt-1">
                                            <div class="flex items-center">
                                                <span class="h-3 w-3 rounded-full {{ $request->status == 'pending' ? 'bg-yellow-400' : 'bg-gray-300' }}"></span>
                                                <span class="ml-1 text-xs {{ $request->status == 'pending' ? 'text-yellow-700 font-bold' : 'text-gray-500' }}">Pending</span>
                                            </div>
                                            <span class="h-0.5 w-4 bg-gray-300"></span>
                                            <div class="flex items-center">
                                                <span class="h-3 w-3 rounded-full {{ $request->status == 'approved' ? 'bg-blue-400' : ($request->status == 'completed' ? 'bg-blue-400' : 'bg-gray-300') }}"></span>
                                                <span class="ml-1 text-xs {{ $request->status == 'approved' ? 'text-blue-700 font-bold' : ($request->status == 'completed' ? 'text-blue-700' : 'text-gray-500') }}">Approved</span>
                                            </div>
                                            <span class="h-0.5 w-4 bg-gray-300"></span>
                                            <div class="flex items-center">
                                                <span class="h-3 w-3 rounded-full {{ $request->status == 'completed' ? 'bg-green-400' : 'bg-gray-300' }}"></span>
                                                <span class="ml-1 text-xs {{ $request->status == 'completed' ? 'text-green-700 font-bold' : 'text-gray-500' }}">Completed</span>
                                            </div>
                                        </div>
                                        <!-- End Progress Tracker -->
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($request->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4">
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
                                <div class="text-sm text-gray-900">{{ $request->created_at->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $request->created_at->diffForHumans() }}
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        @foreach($documentRequests as $request)
                        <tr class="request-item hover:bg-gray-50 transition duration-150" data-type="document" data-status="{{ $request->status }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $request->document_type }}</div>
                                        <div class="text-sm text-gray-500">Document Request</div>
                                        <!-- Progress Tracker -->
                                        <div class="flex items-center space-x-2 mt-1">
                                            <div class="flex items-center">
                                                <span class="h-3 w-3 rounded-full {{ $request->status == 'pending' ? 'bg-yellow-400' : 'bg-gray-300' }}"></span>
                                                <span class="ml-1 text-xs {{ $request->status == 'pending' ? 'text-yellow-700 font-bold' : 'text-gray-500' }}">Pending</span>
                                            </div>
                                            <span class="h-0.5 w-4 bg-gray-300"></span>
                                            <div class="flex items-center">
                                                <span class="h-3 w-3 rounded-full {{ $request->status == 'approved' ? 'bg-blue-400' : ($request->status == 'completed' ? 'bg-blue-400' : 'bg-gray-300') }}"></span>
                                                <span class="ml-1 text-xs {{ $request->status == 'approved' ? 'text-blue-700 font-bold' : ($request->status == 'completed' ? 'text-blue-700' : 'text-gray-500') }}">Approved</span>
                                            </div>
                                            <span class="h-0.5 w-4 bg-gray-300"></span>
                                            <div class="flex items-center">
                                                <span class="h-3 w-3 rounded-full {{ $request->status == 'completed' ? 'bg-green-400' : 'bg-gray-300' }}"></span>
                                                <span class="ml-1 text-xs {{ $request->status == 'completed' ? 'text-green-700 font-bold' : 'text-gray-500' }}">Completed</span>
                                            </div>
                                        </div>
                                        <!-- End Progress Tracker -->
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($request->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4">
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
                                <div class="text-sm text-gray-900">{{ $request->created_at->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $request->created_at->diffForHumans() }}
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        @foreach($communityConcerns as $request)
                        <tr class="request-item hover:bg-gray-50 transition duration-150"
                            data-type="complaint"
                            data-status="{{ $request->status }}"
                            data-concern-id="{{ $request->id }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $request->title }}</div>
                                        <!-- Progress Tracker -->
                                        <div class="flex items-center space-x-1 mt-1">
                                            <div class="flex items-center">
                                                <span class="h-3 w-3 rounded-full {{ $request->status == 'pending' ? 'bg-yellow-400' : 'bg-gray-300' }}"></span>
                                                <span class="ml-1 text-xs {{ $request->status == 'pending' ? 'text-yellow-700 font-bold' : 'text-gray-500' }}">Pending</span>
                                            </div>
                                            <span class="h-0.5 w-3 bg-gray-300"></span>
                                            <div class="flex items-center">
                                                <span class="h-3 w-3 rounded-full {{ $request->status == 'under_review' ? 'bg-blue-400' : ($request->status == 'in_progress' ? 'bg-blue-400' : ($request->status == 'resolved' ? 'bg-blue-400' : ($request->status == 'closed' ? 'bg-blue-400' : 'bg-gray-300'))) }}"></span>
                                                <span class="ml-1 text-xs {{ $request->status == 'under_review' ? 'text-blue-700 font-bold' : ($request->status == 'in_progress' ? 'text-blue-700' : ($request->status == 'resolved' ? 'text-blue-700' : ($request->status == 'closed' ? 'text-blue-700' : 'text-gray-500'))) }}">Review</span>
                                            </div>
                                            <span class="h-0.5 w-3 bg-gray-300"></span>
                                            <div class="flex items-center">
                                                <span class="h-3 w-3 rounded-full {{ $request->status == 'in_progress' ? 'bg-orange-400' : ($request->status == 'resolved' ? 'bg-orange-400' : ($request->status == 'closed' ? 'bg-orange-400' : 'bg-gray-300')) }}"></span>
                                                <span class="ml-1 text-xs {{ $request->status == 'in_progress' ? 'text-orange-700 font-bold' : ($request->status == 'resolved' ? 'text-orange-700' : ($request->status == 'closed' ? 'text-orange-700' : 'text-gray-500')) }}">Progress</span>
                                            </div>
                                            <span class="h-0.5 w-3 bg-gray-300"></span>
                                            <div class="flex items-center">
                                                <span class="h-3 w-3 rounded-full {{ $request->status == 'resolved' ? 'bg-green-400' : ($request->status == 'closed' ? 'bg-green-400' : 'bg-gray-300') }}"></span>
                                                <span class="ml-1 text-xs {{ $request->status == 'resolved' ? 'text-green-700 font-bold' : ($request->status == 'closed' ? 'text-green-700' : 'text-gray-500') }}">Resolved</span>
                                            </div>
                                        </div>
                                        <!-- End Progress Tracker -->
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <button type="button"
                                        class="js-open-resident-concern-modal inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                                    <i class="fas fa-eye mr-1"></i>
                                    View Details
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                @if($request->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pending
                                    </span>
                                @elseif($request->status === 'under_review')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-eye mr-1"></i>
                                        Under Review
                                    </span>
                                @elseif($request->status === 'in_progress')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-spinner mr-1"></i>
                                        In Progress
                                    </span>
                                @elseif($request->status === 'resolved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Resolved
                                    </span>
                                @elseif($request->status === 'closed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Closed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $request->created_at->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $request->created_at->diffForHumans() }}</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                    </div>
                </div>
            </div>
                </div>

                <!-- Mobile Cards (hidden on desktop) -->
                <div class="md:hidden space-y-4" id="requestsMobileCards">
                    @foreach($blotterRequests as $request)
            <div class="request-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200" data-type="blotter" data-status="{{ $request->status }}">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-file-alt text-red-600"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">Blotter Report</h3>
                            <p class="text-sm text-gray-500">{{ $request->resident->name ?? 'N/A' }}</p>
                            <!-- Progress Tracker -->
                            <div class="flex items-center space-x-2 mt-1">
                                <div class="flex items-center">
                                    <span class="h-3 w-3 rounded-full {{ $request->status == 'pending' ? 'bg-yellow-400' : 'bg-gray-300' }}"></span>
                                    <span class="ml-1 text-xs {{ $request->status == 'pending' ? 'text-yellow-700 font-bold' : 'text-gray-500' }}">Pending</span>
                                </div>
                                <span class="h-0.5 w-4 bg-gray-300"></span>
                                <div class="flex items-center">
                                    <span class="h-3 w-3 rounded-full {{ $request->status == 'approved' ? 'bg-blue-400' : ($request->status == 'completed' ? 'bg-blue-400' : 'bg-gray-300') }}"></span>
                                    <span class="ml-1 text-xs {{ $request->status == 'approved' ? 'text-blue-700 font-bold' : ($request->status == 'completed' ? 'text-blue-700' : 'text-gray-500') }}">Approved</span>
                                </div>
                                <span class="h-0.5 w-4 bg-gray-300"></span>
                                <div class="flex items-center">
                                    <span class="h-3 w-3 rounded-full {{ $request->status == 'completed' ? 'bg-green-400' : 'bg-gray-300' }}"></span>
                                    <span class="ml-1 text-xs {{ $request->status == 'completed' ? 'text-green-700 font-bold' : 'text-gray-500' }}">Completed</span>
                                </div>
                            </div>
                            <!-- End Progress Tracker -->
                        </div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-align-left mr-1"></i>
                        {{ Str::limit($request->description, 80) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ $request->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            @endforeach

            @foreach($documentRequests as $request)
            <div class="request-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200" data-type="document" data-status="{{ $request->status }}">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-file-signature text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">{{ $request->document_type }}</h3>
                            <p class="text-sm text-gray-500">Document Request</p>
                            <!-- Progress Tracker -->
                            <div class="flex items-center space-x-2 mt-1">
                                <div class="flex items-center">
                                    <span class="h-3 w-3 rounded-full {{ $request->status == 'pending' ? 'bg-yellow-400' : 'bg-gray-300' }}"></span>
                                    <span class="ml-1 text-xs {{ $request->status == 'pending' ? 'text-yellow-700 font-bold' : 'text-gray-500' }}">Pending</span>
                                </div>
                                <span class="h-0.5 w-4 bg-gray-300"></span>
                                <div class="flex items-center">
                                    <span class="h-3 w-3 rounded-full {{ $request->status == 'approved' ? 'bg-blue-400' : ($request->status == 'completed' ? 'bg-blue-400' : 'bg-gray-300') }}"></span>
                                    <span class="ml-1 text-xs {{ $request->status == 'approved' ? 'text-blue-700 font-bold' : ($request->status == 'completed' ? 'text-blue-700' : 'text-gray-500') }}">Approved</span>
                                </div>
                                <span class="h-0.5 w-4 bg-gray-300"></span>
                                <div class="flex items-center">
                                    <span class="h-3 w-3 rounded-full {{ $request->status == 'completed' ? 'bg-green-400' : 'bg-gray-300' }}"></span>
                                    <span class="ml-1 text-xs {{ $request->status == 'completed' ? 'text-green-700 font-bold' : 'text-gray-500' }}">Completed</span>
                                </div>
                            </div>
                            <!-- End Progress Tracker -->
                        </div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-align-left mr-1"></i>
                        {{ Str::limit($request->description, 80) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ $request->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
                    @endforeach

                    @foreach($communityConcerns as $request)
            <div class="request-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200"
                 data-type="complaint"
                 data-status="{{ $request->status }}"
                 data-concern-id="{{ $request->id }}">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clipboard-list text-indigo-600"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">{{ $request->title }}</h3>
                            <!-- Progress Tracker -->
                            <div class="flex items-center space-x-1 mt-1">
                                <div class="flex items-center">
                                    <span class="h-3 w-3 rounded-full {{ $request->status == 'pending' ? 'bg-yellow-400' : 'bg-gray-300' }}"></span>
                                    <span class="ml-1 text-xs {{ $request->status == 'pending' ? 'text-yellow-700 font-bold' : 'text-gray-500' }}">Pending</span>
                                </div>
                                <span class="h-0.5 w-3 bg-gray-300"></span>
                                <div class="flex items-center">
                                    <span class="h-3 w-3 rounded-full {{ $request->status == 'under_review' ? 'bg-blue-400' : ($request->status == 'in_progress' ? 'bg-blue-400' : ($request->status == 'resolved' ? 'bg-blue-400' : ($request->status == 'closed' ? 'bg-blue-400' : 'bg-gray-300'))) }}"></span>
                                    <span class="ml-1 text-xs {{ $request->status == 'under_review' ? 'text-blue-700 font-bold' : ($request->status == 'in_progress' ? 'text-blue-700' : ($request->status == 'resolved' ? 'text-blue-700' : ($request->status == 'closed' ? 'text-blue-700' : 'text-gray-500'))) }}">Review</span>
                                </div>
                                <span class="h-0.5 w-3 bg-gray-300"></span>
                                <div class="flex items-center">
                                    <span class="h-3 w-3 rounded-full {{ $request->status == 'in_progress' ? 'bg-orange-400' : ($request->status == 'resolved' ? 'bg-orange-400' : ($request->status == 'closed' ? 'bg-orange-400' : 'bg-gray-300')) }}"></span>
                                    <span class="ml-1 text-xs {{ $request->status == 'in_progress' ? 'text-orange-700 font-bold' : ($request->status == 'resolved' ? 'text-orange-700' : ($request->status == 'closed' ? 'text-orange-700' : 'text-gray-500')) }}">Progress</span>
                                </div>
                                <span class="h-0.5 w-3 bg-gray-300"></span>
                                <div class="flex items-center">
                                    <span class="h-3 w-3 rounded-full {{ $request->status == 'resolved' ? 'bg-green-400' : ($request->status == 'closed' ? 'bg-green-400' : 'bg-gray-300') }}"></span>
                                    <span class="ml-1 text-xs {{ $request->status == 'resolved' ? 'text-green-700 font-bold' : ($request->status == 'closed' ? 'text-green-700' : 'text-gray-500') }}">Resolved</span>
                                </div>
                            </div>
                            <!-- End Progress Tracker -->
                        </div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-500 mb-2">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ $request->created_at->diffForHumans() }}
                    </p>
                    <button type="button"
                            class="w-full inline-flex items-center justify-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200 js-open-resident-concern-modal">
                        <i class="fas fa-eye mr-1"></i>
                        View Full Details
                    </button>
                </div>
            </div>
                    @endforeach
                </div>
            @endif

    <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden">No requests match your search criteria.</p>

    <!-- Unified Pagination for All Requests -->
    @if($paginatedRequests->hasPages())
        <div class="mt-6">
            <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
                <div class="-mt-px flex w-0 flex-1">
                    @if($paginatedRequests->onFirstPage())
                        <span class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500">
                            <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                            Previous
                        </span>
                    @else
                        <a href="{{ $paginatedRequests->appends(request()->except('page'))->previousPageUrl() }}" 
                           class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                            Previous
                        </a>
                    @endif
                </div>
                
                <div class="hidden md:-mt-px md:flex">
                    @php
                        $currentPage = $paginatedRequests->currentPage();
                        $lastPage = $paginatedRequests->lastPage();
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($lastPage, $currentPage + 2);
                    @endphp
                    
                    @if($startPage > 1)
                        <a href="{{ $paginatedRequests->appends(request()->except('page'))->url(1) }}" 
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
                            <a href="{{ $paginatedRequests->appends(request()->except('page'))->url($page) }}" 
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
                        <a href="{{ $paginatedRequests->appends(request()->except('page'))->url($lastPage) }}" 
                           class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            {{ $lastPage }}
                        </a>
                    @endif
                </div>
                
                <div class="-mt-px flex w-0 flex-1 justify-end">
                    @if($paginatedRequests->hasMorePages())
                        <a href="{{ $paginatedRequests->appends(request()->except('page'))->nextPageUrl() }}" 
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
                @if($paginatedRequests->onFirstPage())
                    <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500">
                        Previous
                    </span>
                @else
                    <a href="{{ $paginatedRequests->appends(request()->except('page'))->previousPageUrl() }}" 
                       class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Previous
                    </a>
                @endif
                
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">
                    Page {{ $paginatedRequests->currentPage() }} of {{ $paginatedRequests->lastPage() }}
                </span>
                
                @if($paginatedRequests->hasMorePages())
                    <a href="{{ $paginatedRequests->appends(request()->except('page'))->nextPageUrl() }}" 
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
                Showing {{ $paginatedRequests->firstItem() }} to {{ $paginatedRequests->lastItem() }} of {{ $paginatedRequests->total() }} results
            </div>
        </div>
    @endif
    </div>

    <!-- Resident Concern Details Modal -->
    <div id="residentConcernDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative mx-auto my-12 w-11/12 md:w-4/5 lg:w-3/5 xl:w-2/5">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-100">
                <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                    <div class="flex items-start space-x-3">
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <i class="fas fa-clipboard-list text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Community Concern</p>
                            <h3 class="text-xl font-semibold text-gray-900">Concern Details</h3>
                        </div>
                    </div>
                    <button type="button"
                            class="text-gray-400 hover:text-gray-600 transition"
                            onclick="window.residentConcernModal.close()">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-5">
                    <div id="residentConcernDetailsContent" class="max-h-[70vh] overflow-y-auto pr-1 custom-scrollbar">
                        <!-- Filled dynamically -->
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 pt-2">
                        <p class="text-xs text-gray-500 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            This information is based on your submitted concern and barangay updates.
                        </p>
                        <div class="flex items-center justify-end space-x-2 w-full md:w-auto">
                            <button type="button"
                                    onclick="window.residentConcernModal.close()"
                                    class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const wrapper = document.getElementById('residentRequestsSkeleton');
            if (wrapper) wrapper.style.display = 'none';
            const content = document.getElementById('residentRequestsContent');
            if (content) content.style.display = 'block';
        }, 1000);
    });
    </script>
</div>

<script>
// Resident Concern Modal - Global functions for better reliability
(function() {
    'use strict';
    
    // Modal state management
    let isModalOpen = false;
    let isLoading = false;
    
    // Get base URL for concern details
    function getConcernDetailsUrl(concernId) {
        return `/resident/community-concerns/${concernId}/details`;
    }
    
    // Escape HTML helper
    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
    
    // Show loading state
    function showLoadingState() {
        const modal = document.getElementById('residentConcernDetailsModal');
        const content = document.getElementById('residentConcernDetailsContent');
        if (modal && content) {
            content.innerHTML = `
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600 mb-4"></div>
                    <p class="text-sm text-gray-500">Loading concern details...</p>
                </div>
            `;
            modal.classList.remove('hidden');
            isModalOpen = true;
        }
    }
    
    // Show error state
    function showErrorState(message) {
        const content = document.getElementById('residentConcernDetailsContent');
        if (content) {
            content.innerHTML = `
                <div class="flex flex-col items-center justify-center py-12">
                    <i class="fas fa-exclamation-circle text-red-500 text-3xl mb-4"></i>
                    <p class="text-sm text-red-600 text-center">${escapeHtml(message || 'Failed to load concern details. Please try again.')}</p>
                    <button onclick="window.residentConcernModal.close()" 
                            class="mt-4 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        Close
                    </button>
                </div>
            `;
        }
    }
    
    // Open modal with data
    function openModalWithData(data) {
        const modal = document.getElementById('residentConcernDetailsModal');
        const content = document.getElementById('residentConcernDetailsContent');
        if (!modal || !content) {
            console.error('Modal elements not found');
            return;
        }

        const statusLabel = (data.status || 'pending').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        const media = Array.isArray(data.media_files) ? data.media_files : [];

        // Build media HTML
        let mediaHTML = '';
        if (media.length > 0) {
            const imageFiles = media.filter(f => f.type && f.type.startsWith('image/'));
            const otherFiles = media.filter(f => !f.type || !f.type.startsWith('image/'));
            
            let imageHTML = '';
            if (imageFiles.length > 0) {
                imageHTML = `
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-3">
                        ${imageFiles.map(file => {
                            const safeUrl = escapeHtml(file.url);
                            const safeName = escapeHtml(file.name || 'Image');
                            return `
                                <div class="relative group cursor-pointer" onclick="window.residentConcernModal.openImageLightbox('${safeUrl}', '${safeName}')">
                                    <img src="${safeUrl}" alt="${safeName}" 
                                         class="w-full h-24 object-cover rounded-lg border border-gray-200 hover:border-blue-400 transition"
                                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'100\\' height=\\'100\\'%3E%3Crect fill=\\'%23ddd\\' width=\\'100\\' height=\\'100\\'/%3E%3Ctext fill=\\'%23999\\' font-family=\\'sans-serif\\' font-size=\\'14\\' dy=\\'10.5\\' x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\'%3EImage%3C/text%3E%3C/svg%3E'">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-lg transition flex items-center justify-center">
                                        <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition"></i>
                                    </div>
                                </div>
                            `;
                        }).join('')}
                    </div>
                `;
            }
            
            let filesHTML = '';
            if (otherFiles.length > 0) {
                filesHTML = `
                    <div class="space-y-2">
                        ${otherFiles.map(file => {
                            const safeUrl = escapeHtml(file.url);
                            const safeName = escapeHtml(file.name || 'File');
                            return `
                                <a href="${safeUrl}" target="_blank" rel="noopener noreferrer"
                                   class="flex items-center p-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                    <i class="fas fa-file mr-2 text-gray-500"></i>
                                    <span class="text-sm text-gray-700 truncate">${safeName}</span>
                                    <i class="fas fa-external-link-alt ml-auto text-xs text-gray-400"></i>
                                </a>
                            `;
                        }).join('')}
                    </div>
                `;
            }
            
            if (imageHTML || filesHTML) {
                mediaHTML = `
                    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-paperclip"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">Attachments</div>
                                ${imageHTML}
                                ${filesHTML}
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        // Build status badge HTML
        const statusColors = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'under_review': 'bg-blue-100 text-blue-800',
            'in_progress': 'bg-orange-100 text-orange-800',
            'resolved': 'bg-green-100 text-green-800',
            'closed': 'bg-purple-100 text-purple-800'
        };
        const statusColor = statusColors[data.status] || 'bg-gray-100 text-gray-800';
        
        content.innerHTML = `
            <div class="space-y-4">
                <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900">
                                ${escapeHtml(data.title || 'Concern')}
                            </h4>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${statusColor}">
                            ${escapeHtml(statusLabel)}
                        </span>
                    </div>
                    <div class="text-xs text-gray-500">
                        <i class="fas fa-calendar mr-1"></i>
                        Filed on ${escapeHtml(data.created_at || 'N/A')}
                    </div>
                </div>
                
                <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                    <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">Description</div>
                    <div class="text-sm text-gray-700 whitespace-pre-wrap">
                        ${escapeHtml(data.description || 'No description provided.')}
                    </div>
                </div>
                
                ${data.admin_remarks ? `
                <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-comment-dots text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">Barangay Response</div>
                            <div class="text-sm text-gray-700 whitespace-pre-wrap">
                                ${escapeHtml(data.admin_remarks)}
                            </div>
                            ${data.remarks_timestamp ? `<div class="mt-2 text-xs text-gray-500"><i class="fas fa-clock mr-1"></i>${escapeHtml(data.remarks_timestamp)}</div>` : ''}
                        </div>
                    </div>
                </div>` : ''}
                
                ${mediaHTML}
            </div>
        `;

        modal.classList.remove('hidden');
        isModalOpen = true;
        isLoading = false;
    }
    
    // Fetch and display concern details
    function fetchAndDisplayConcern(concernId) {
        if (isLoading) {
            console.log('Request already in progress');
            return;
        }
        
        if (!concernId) {
            showErrorState('Invalid concern ID');
            return;
        }
        
        isLoading = true;
        showLoadingState();
        
        const url = getConcernDetailsUrl(concernId);
        
        fetch(url, {
            method: 'GET',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.error || `HTTP error! status: ${response.status}`);
                }).catch(() => {
                    throw new Error(`HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            openModalWithData(data);
        })
        .catch(error => {
            console.error('Error fetching concern details:', error);
            isLoading = false;
            showErrorState(error.message || 'Failed to load concern details. Please try again.');
        });
    }
    
    // Close modal
    function closeModal() {
        const modal = document.getElementById('residentConcernDetailsModal');
        if (modal) {
            modal.classList.add('hidden');
            isModalOpen = false;
            isLoading = false;
        }
    }
    
    // Image lightbox functions
    function openImageLightbox(url, caption) {
        const safeCaption = escapeHtml(caption || 'Image');
        const safeUrl = escapeHtml(url);
        
        // Remove existing lightbox if any
        const existing = document.getElementById('concernImageLightbox');
        if (existing) {
            existing.remove();
        }
        
        const lightboxHTML = `
            <div id="concernImageLightbox" class="fixed inset-0 bg-black bg-opacity-75 z-[60] flex items-center justify-center p-4" onclick="window.residentConcernModal.closeImageLightbox()">
                <div class="relative max-w-5xl max-h-full" onclick="event.stopPropagation()">
                    <button onclick="window.residentConcernModal.closeImageLightbox()" 
                            class="absolute -top-10 right-0 text-white hover:text-gray-300 transition z-10">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                    <div class="bg-white rounded-lg overflow-hidden shadow-2xl">
                        <div class="px-4 py-2 bg-gray-100 border-b border-gray-200">
                            <p class="text-sm font-medium text-gray-900 truncate">${safeCaption}</p>
                        </div>
                        <img src="${safeUrl}" alt="${safeCaption}" 
                             class="max-w-full max-h-[80vh] object-contain"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'400\\' height=\\'300\\'%3E%3Crect fill=\\'%23ddd\\' width=\\'400\\' height=\\'300\\'/%3E%3Ctext fill=\\'%23999\\' font-family=\\'sans-serif\\' font-size=\\'20\\' dy=\\'10.5\\' x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\'%3EImage not found%3C/text%3E%3C/svg%3E'">
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', lightboxHTML);
    }
    
    function closeImageLightbox() {
        const lightbox = document.getElementById('concernImageLightbox');
        if (lightbox) {
            lightbox.remove();
        }
    }
    
    // Expose functions globally
    window.residentConcernModal = {
        open: fetchAndDisplayConcern,
        close: closeModal,
        openImageLightbox: openImageLightbox,
        closeImageLightbox: closeImageLightbox
    };
    
    // Initialize event listeners when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Click handler for modal triggers
        document.addEventListener('click', function(e) {
            const trigger = e.target.closest('.js-open-resident-concern-modal');
            if (!trigger) return;
            
            e.preventDefault();
            e.stopPropagation();
            
            const row = trigger.closest('.request-item, .request-card');
            if (!row) {
                console.error('Could not find parent row/card');
                return;
            }
            
            const concernId = row.dataset.concernId;
            if (!concernId) {
                console.error('Concern ID not found in data attribute');
                showErrorState('Invalid concern ID');
                return;
            }
            
            fetchAndDisplayConcern(concernId);
        });
        
        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isModalOpen) {
                closeModal();
            }
        });
        
        // Close modal when clicking outside
        const modal = document.getElementById('residentConcernDetailsModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
        }
    });
})();

document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterBtns = document.querySelectorAll('.filter-btn');
    const requestItems = document.querySelectorAll('.request-item');
    const requestCards = document.querySelectorAll('.request-card');
    
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
            
            // Filter requests
            const allItems = [...requestItems, ...requestCards];
            allItems.forEach(item => {
                const type = item.dataset.type;
                const status = item.dataset.status;
                
                let show = false;
                if (filter === 'all') {
                    show = true;
                } else if (filter === 'blotter' && type === 'blotter') {
                    show = true;
                } else if (filter === 'document' && type === 'document') {
                    show = true;
                } else if (filter === 'pending' && status === 'pending') {
                    show = true;
                } else if (filter === 'completed' && status === 'completed') {
                    show = true;
                }
                
                item.style.display = show ? '' : 'none';
            });
            
            updateCounts();
        });
    });
    
    // Update counts
    function updateCounts() {
        let totalVisible = 0, blotter = 0, document = 0, pending = 0, completed = 0;
        
        if (window.innerWidth >= 768) { // Desktop
            const visibleItems = Array.from(requestItems).filter(item => item.style.display !== 'none');
            totalVisible = visibleItems.length;
            blotter = visibleItems.filter(item => item.dataset.type === 'blotter').length;
            document = visibleItems.filter(item => item.dataset.type === 'document').length;
            pending = visibleItems.filter(item => item.dataset.status === 'pending').length;
            completed = visibleItems.filter(item => item.dataset.status === 'completed').length;
        } else { // Mobile
            const visibleCards = Array.from(requestCards).filter(item => item.style.display !== 'none');
            totalVisible = visibleCards.length;
            blotter = visibleCards.filter(item => item.dataset.type === 'blotter').length;
            document = visibleCards.filter(item => item.dataset.type === 'document').length;
            pending = visibleCards.filter(item => item.dataset.status === 'pending').length;
            completed = visibleCards.filter(item => item.dataset.status === 'completed').length;
        }
        
        // Update statistics cards
        document.querySelectorAll('.bg-white.rounded-lg.shadow-sm.border.border-gray-200.p-4').forEach((card, index) => {
            const countElement = card.querySelector('.text-2xl.font-bold.text-gray-900');
            if (countElement) {
                if (index === 0) countElement.textContent = blotter;
                else if (index === 1) countElement.textContent = document;
                else if (index === 2) countElement.textContent = pending;
                else if (index === 3) countElement.textContent = completed;
            }
        });
    }
    
    // Initial count update
    updateCounts();
    // Update counts on window resize
    window.addEventListener('resize', updateCounts);

});
</script>
@endsection