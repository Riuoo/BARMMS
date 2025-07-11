@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Enhanced Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Welcome back, {{ $barangay_profile->name }}!</h1>
                <p class="text-gray-600 text-lg">Here's what's happening in your barangay today</p>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-2">
                    <span class="text-green-800 text-sm font-medium">Last updated: {{ now()->format('M d, Y g:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Residents Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Residents</p>
                        <p class="text-white text-3xl font-bold">{{ $totalResidents }}</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.residents') }}" class="text-blue-100 hover:text-white text-sm font-medium flex items-center">
                        View all <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Account Requests Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Account Requests</p>
                        <p class="text-white text-3xl font-bold">{{ $totalAccountRequests }}</p>
                    </div>
                    <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-user-plus text-white text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.new-account-requests') }}" class="text-orange-100 hover:text-white text-sm font-medium flex items-center">
                        Manage requests <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Blotter Reports Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Blotter Reports</p>
                        <p class="text-white text-3xl font-bold">{{ $totalBlotterReports }}</p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-file-alt text-white text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.blotter-reports') }}" class="text-purple-100 hover:text-white text-sm font-medium flex items-center">
                        View reports <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Document Requests Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Document Requests</p>
                        <p class="text-white text-3xl font-bold">{{ $totalDocumentRequests }}</p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-file-signature text-white text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.document-requests') }}" class="text-green-100 hover:text-white text-sm font-medium flex items-center">
                        Manage requests <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Total Accomplished Projects Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-yellow-100 rounded-lg p-3">
                        <i class="fas fa-check-circle text-yellow-600 text-2xl"></i>
                    </div>
                    <span class="text-yellow-600 text-sm font-medium bg-yellow-50 px-3 py-1 rounded-full">Projects</span>
                </div>
                <div>
                    <p class="text-gray-600 text-sm font-medium">Accomplished Projects</p>
                    <p class="text-gray-900 text-3xl font-bold">{{ $totalAccomplishedProjects }}</p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.accomplished-projects') }}" class="text-yellow-600 hover:text-yellow-700 text-sm font-medium flex items-center">
                        View projects <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Health Reports Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-red-100 rounded-lg p-3">
                        <i class="fas fa-heartbeat text-red-600 text-2xl"></i>
                    </div>
                    <span class="text-red-600 text-sm font-medium bg-red-50 px-3 py-1 rounded-full">Health</span>
                </div>
                <div>
                    <p class="text-gray-600 text-sm font-medium">Health Reports</p>
                    <p class="text-gray-900 text-3xl font-bold">{{ $totalHealthReports }}</p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.health-reports') }}" class="text-red-600 hover:text-red-700 text-sm font-medium flex items-center">
                        View reports <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="mt-8 bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('admin.barangay-profiles') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                    <i class="fas fa-users text-blue-600 text-xl mr-3"></i>
                    <div>
                        <p class="font-medium text-gray-900">Manage Officials</p>
                        <p class="text-sm text-gray-600">Add or edit barangay officials</p>
                    </div>
                </a>
                <a href="{{ route('admin.new-account-requests') }}" class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors duration-200">
                    <i class="fas fa-user-clock text-orange-600 text-xl mr-3"></i>
                    <div>
                        <p class="font-medium text-gray-900">Account Requests</p>
                        <p class="text-sm text-gray-600">Review pending applications</p>
                    </div>
                </a>
                <a href="{{ route('admin.blotter-reports') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors duration-200">
                    <i class="fas fa-file-alt text-purple-600 text-xl mr-3"></i>
                    <div>
                        <p class="font-medium text-gray-900">Blotter Reports</p>
                        <p class="text-sm text-gray-600">Review incident reports</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection