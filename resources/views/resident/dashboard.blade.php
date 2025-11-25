@extends('resident.layout')

@section('title', 'Resident Dashboard')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Consolidated Skeleton -->
    <div id="residentSkeleton" data-skeleton>
        @include('components.loading.resident-dashboard-skeleton')
    </div>

    <!-- Enhanced Header Section -->
    <div id="residentHeaderContent" class="mb-2" style="display: none;">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Welcome back, {{ $resident->name }}</h1>
                <p class="text-sm md:text-base text-gray-600">Here's what's happening with your requests today</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-2">
                    <span class="text-green-800 text-sm font-medium">Last updated: {{ now()->format('M d, Y g:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div id="residentStatsContainer">
        <div id="residentStatsContent" class="hidden">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-4 mb-2">
        <!-- Total Blotter Reports Card -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-xs font-medium">Blotter Reports</p>
                        <p class="text-white text-2xl font-bold">{{ $totalBlotterRequests }}</p>
                    </div>
                    <div class="bg-red-400 bg-opacity-30 rounded-full p-2">
                        <i class="fas fa-file-alt text-white text-lg"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('resident.my-requests') }}" class="text-red-100 hover:text-white text-xs font-medium flex items-center">
                        View all <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Document Requests Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-xs font-medium">Document Requests</p>
                        <p class="text-white text-2xl font-bold">{{ $totalDocumentRequests }}</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-2">
                        <i class="fas fa-file-signature text-white text-lg"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('resident.my-requests') }}" class="text-blue-100 hover:text-white text-xs font-medium flex items-center">
                        View all <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Pending Requests Card -->
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-xs font-medium">Pending</p>
                        <p class="text-white text-2xl font-bold">{{ $pendingCounts['blotter'] + $pendingCounts['document'] + $pendingCounts['concern'] }}</p>
                    </div>
                    <div class="bg-yellow-400 bg-opacity-30 rounded-full p-2">
                        <i class="fas fa-clock text-white text-lg"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('resident.my-requests') }}" class="text-yellow-100 hover:text-white text-xs font-medium flex items-center">
                        Track status <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Completed Requests Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-xs font-medium">Completed</p>
                        <p class="text-white text-2xl font-bold">{{ $completedCounts['blotter'] + $completedCounts['document'] + $completedCounts['concern'] }}</p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-2">
                        <i class="fas fa-check-circle text-white text-lg"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('resident.my-requests') }}" class="text-green-100 hover:text-white text-xs font-medium flex items-center">
                        View completed <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    @if($recentBlotterRequests->isNotEmpty() || $recentDocumentRequests->isNotEmpty() || $recentCommunityConcerns->isNotEmpty())
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Recent Activity</h2>
        <div class="space-y-4">
            @foreach($recentBlotterRequests as $request)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Blotter Report</p>
                        <p class="text-sm text-gray-500">vs {{ $request->resident->name ?? 'N/A' }}</p>
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

            @foreach($recentDocumentRequests as $request)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200">
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

            @foreach($recentCommunityConcerns as $concern)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-indigo-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Community Concern</p>
                        <p class="text-sm text-gray-500">{{ Str::limit($concern->concern, 30) }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($concern->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($concern->status === 'approved') bg-blue-100 text-blue-800
                        @elseif($concern->status === 'completed') bg-green-100 text-green-800
                        @endif">
                        {{ ucfirst($concern->status) }}
                    </span>
                    <span class="text-sm text-gray-500">{{ $concern->created_at->diffForHumans() }}</span>
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
    
    <!-- Floating Action Button -->
    <!-- FAB Skeleton is part of consolidated skeleton -->
    <div class="fixed bottom-6 right-6 z-50">
        <div class="relative" x-data="{ open: false }">
            <!-- Main FAB -->
            <button @click="open = !open" class="bg-green-600 hover:bg-green-700 text-white rounded-full p-4 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-110">
                <i class="fas fa-plus text-xl"></i>
            </button>
            
            <!-- FAB Menu -->
            <div x-show="open" @click.away="open = false" x-transition class="absolute bottom-16 right-0 space-y-2">
                <a href="{{ route('resident.request_blotter_report') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-file-alt text-red-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700">Blotter Report</span>
                </a>
                <a href="{{ route('resident.request_document_request') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-file-signature text-blue-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700">Document Request</span>
                </a>
                <a href="{{ route('resident.request_community_concern') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-clipboard-list text-indigo-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700">Community Concern</span>
                </a>
                <a href="{{ route('resident.my-requests') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-clipboard-list text-green-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700">My Requests</span>
                </a>
                <a href="{{ route('resident.announcements') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-bullhorn text-purple-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700">Bulletin Board</span>
                </a>
                <a href="{{ route('resident.profile') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-user-circle text-gray-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700">My Profile</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Skeleton loading control for resident dashboard
    document.addEventListener('DOMContentLoaded', function() {
        const residentStatsContainer = document.getElementById('residentStatsContainer');
        const residentStatsContent = document.getElementById('residentStatsContent');
        const residentSkel = document.getElementById('residentDashboardSkeleton') || document.getElementById('residentSkeleton');
        const header = document.getElementById('residentHeaderContent');

        function reveal() {
            if (residentStatsContainer && residentStatsContent) {
                residentStatsContainer.innerHTML = '';
                residentStatsContainer.appendChild(residentStatsContent);
                residentStatsContent.classList.remove('hidden');
            }
            if (header) header.style.display = 'block';
            if (residentSkel) residentSkel.style.display = 'none';
        }

        // Instant reveal on repeat visit; keep 1s delay only on first visit
        var path = window.location && window.location.pathname ? window.location.pathname : 'root';
        var key = 'skeletonSeen:' + path;
        var seen = false;
        try { seen = sessionStorage.getItem(key) === '1'; } catch(e) { seen = false; }

        if (seen) {
            reveal();
        } else {
            setTimeout(reveal, 1000);
        }
    });
    </script>
@endsection