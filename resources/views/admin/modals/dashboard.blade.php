@extends('admin.modals.layout')

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

    <!-- Enhanced Data Visualization Section -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity Chart -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-600">New Residents</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-16 h-2 bg-gray-200 rounded-full mr-2">
                                <div class="w-12 h-2 bg-green-500 rounded-full"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900">75%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-600">Account Requests</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-16 h-2 bg-gray-200 rounded-full mr-2">
                                <div class="w-8 h-2 bg-blue-500 rounded-full"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900">50%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-600">Blotter Reports</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-16 h-2 bg-gray-200 rounded-full mr-2">
                                <div class="w-10 h-2 bg-purple-500 rounded-full"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900">62%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-orange-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-600">Document Requests</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-16 h-2 bg-gray-200 rounded-full mr-2">
                                <div class="w-14 h-2 bg-orange-500 rounded-full"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900">87%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Health Status -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">System Health</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <span class="text-sm font-medium text-green-800">Database</span>
                        </div>
                        <span class="text-sm text-green-600">Healthy</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <span class="text-sm font-medium text-green-800">Email Service</span>
                        </div>
                        <span class="text-sm text-green-600">Active</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                            <span class="text-sm font-medium text-yellow-800">Storage</span>
                        </div>
                        <span class="text-sm text-yellow-600">75% Used</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <span class="text-sm font-medium text-green-800">Security</span>
                        </div>
                        <span class="text-sm text-green-600">Protected</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Summary -->
    <div class="mt-8 bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($totalResidents / max($totalResidents, 1) * 100, 1) }}%</div>
                    <div class="text-sm text-gray-600">Resident Coverage</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($totalAccountRequests / max($totalAccountRequests, 1) * 100, 1) }}%</div>
                    <div class="text-sm text-gray-600">Request Processing</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ number_format($totalBlotterReports / max($totalBlotterReports, 1) * 100, 1) }}%</div>
                    <div class="text-sm text-gray-600">Report Completion</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ number_format($totalDocumentRequests / max($totalDocumentRequests, 1) * 100, 1) }}%</div>
                    <div class="text-sm text-gray-600">Document Processing</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="mt-8 bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('admin.barangay-profiles.create') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-user-plus text-blue-600 text-xl mr-3"></i>
                    <div>
                        <p class="font-medium text-gray-900">Add Official</p>
                        <p class="text-sm text-gray-600">Register new barangay official</p>
                    </div>
                </a>
                <a href="{{ route('admin.residents.create') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-home text-green-600 text-xl mr-3"></i>
                    <div>
                        <p class="font-medium text-gray-900">Add Resident</p>
                        <p class="text-sm text-gray-600">Register new resident</p>
                    </div>
                </a>
                <a href="{{ route('admin.blotter-reports.create') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-file-alt text-purple-600 text-xl mr-3"></i>
                    <div>
                        <p class="font-medium text-gray-900">Create Report</p>
                        <p class="text-sm text-gray-600">Add new blotter report</p>
                    </div>
                </a>
                <a href="{{ route('admin.document-requests.create') }}" class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-file-signature text-orange-600 text-xl mr-3"></i>
                    <div>
                        <p class="font-medium text-gray-900">Document Request</p>
                        <p class="text-sm text-gray-600">Process document request</p>
                    </div>
                </a>
                <a href="{{ route('admin.clustering') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-chart-pie text-purple-600 text-xl mr-3"></i>
                    <div>
                        <p class="font-medium text-gray-900">Clustering Analysis</p>
                        <p class="text-sm text-gray-600">Analyze resident demographics</p>
                    </div>
                </a>
                <a href="{{ route('admin.decision-tree') }}" class="flex items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-sitemap text-indigo-600 text-xl mr-3"></i>
                    <div>
                        <p class="font-medium text-gray-900">Decision Trees</p>
                        <p class="text-sm text-gray-600">Classification & prediction</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="fixed bottom-6 right-6 z-50">
        <div class="relative" x-data="{ open: false }">
            <!-- Main FAB -->
            <button @click="open = !open" class="bg-green-600 hover:bg-green-700 text-white rounded-full p-4 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-110">
                <i class="fas fa-plus text-xl"></i>
            </button>
            
            <!-- FAB Menu -->
            <div x-show="open" @click.away="open = false" x-transition class="absolute bottom-16 right-0 space-y-2">
                <a href="{{ route('admin.barangay-profiles.create') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-user-plus text-blue-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700">Add Official</span>
                </a>
                <a href="{{ route('admin.residents.create') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-home text-green-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700">Add Resident</span>
                </a>
                <a href="{{ route('admin.blotter-reports.create') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-file-alt text-purple-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700">New Report</span>
                </a>
                <a href="{{ route('admin.clustering') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-chart-pie text-purple-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700">Clustering</span>
                </a>
                <a href="{{ route('admin.decision-tree') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-sitemap text-indigo-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700">Decision Trees</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection