@extends('resident.layout')

@section('title', 'Resident Dashboard')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Header Section -->
    <div class="mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Welcome, {{ $resident->name }}!</h1>
                <p class="text-sm md:text-base text-gray-600">Manage your requests and stay updated with barangay services</p>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-2">
                    <span class="text-green-800 text-sm font-medium">Last updated: {{ now()->format('M d, Y g:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-3">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-red-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Blotter Reports</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $blotterRequests->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-signature text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Document Requests</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $documentRequests->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $blotterRequests->where('status', 'pending')->count() + $documentRequests->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Completed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $blotterRequests->where('status', 'completed')->count() + $documentRequests->where('status', 'completed')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="grid grid-cols-2 md:grid-cols-2 gap-3 md:gap-4 mb-3">
        <!-- Request Blotter Card -->
        <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded-lg hover:shadow-md transition duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-500 rounded-lg p-3">
                        <i class="fas fa-file-alt text-white text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Blotter Report</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    New Report
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4">
                <div class="text-sm">
                    <a href="{{ route('resident.request_blotter_report') }}" class="font-medium text-red-600 hover:text-red-900 transition duration-200">
                        Submit incident report <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Community Complaint Card -->
        <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded-lg hover:shadow-md transition duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-lg p-3">
                        <i class="fas fa-clipboard-list text-white text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Community Complaint</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    New Complaint
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4">
                <div class="text-sm">
                    <a href="{{ route('resident.request_community_complaint') }}" class="font-medium text-indigo-600 hover:text-indigo-900 transition duration-200">
                        Report community issue <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Request Document Card -->
        <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded-lg hover:shadow-md transition duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-lg p-3">
                        <i class="fas fa-file-signature text-white text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Document Request</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    New Request
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4">
                <div class="text-sm">
                    <a href="{{ route('resident.request_document_request') }}" class="font-medium text-blue-600 hover:text-blue-900 transition duration-200">
                        Request official document <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Track Requests Card -->
        <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded-lg hover:shadow-md transition duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-lg p-3">
                        <i class="fas fa-clipboard-list text-white text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">My Requests</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    Track Status
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4">
                <div class="text-sm">
                    <a href="{{ route('resident.my-requests') }}" class="font-medium text-green-600 hover:text-green-900 transition duration-200">
                        View all your requests <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        

        <!-- Profile Management Card -->
        <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded-lg hover:shadow-md transition duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gray-500 rounded-lg p-3">
                        <i class="fas fa-user-circle text-white text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">My Profile</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    Manage Account
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4">
                <div class="text-sm">
                    <a href="{{ route('resident.profile') }}" class="font-medium text-gray-600 hover:text-gray-900 transition duration-200">
                        Update personal info <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    @if($blotterRequests->isNotEmpty() || $documentRequests->isNotEmpty())
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Activity</h2>
        <div class="space-y-4">
            @foreach($blotterRequests->take(3) as $request)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Blotter Report</p>
                        <p class="text-sm text-gray-500">vs {{ $request->recipient_name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($request->status === 'approved') bg-blue-100 text-blue-800
                        @elseif($request->status === 'completed') bg-green-100 text-green-800
                        @endif">
                        {{ ucfirst($request->status) }}
                    </span>
                    <span class="text-sm text-gray-500">{{ $request->created_at->diffForHumans() }}</span>
                </div>
            </div>
            @endforeach

            @foreach($documentRequests->take(3) as $request)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-signature text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">{{ $request->document_type }}</p>
                        <p class="text-sm text-gray-500">{{ Str::limit($request->purpose, 30) }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($request->status === 'approved') bg-blue-100 text-blue-800
                        @elseif($request->status === 'completed') bg-green-100 text-green-800
                        @endif">
                        {{ ucfirst($request->status) }}
                    </span>
                    <span class="text-sm text-gray-500">{{ $request->created_at->diffForHumans() }}</span>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4 text-center">
            <a href="{{ route('resident.my-requests') }}" class="text-sm font-medium text-green-600 hover:text-green-900">
                View all activity <span aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
    @endif
</div>
@endsection