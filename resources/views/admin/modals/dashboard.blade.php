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
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <!-- Total Residents Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-xs font-medium">Total Residents</p>
                        <p class="text-white text-2xl font-bold">{{ $totalResidents }}</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-2">
                        <i class="fas fa-users text-white text-lg"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('admin.residents') }}" class="text-blue-100 hover:text-white text-xs font-medium flex items-center">
                        View all <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Account Requests Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-xs font-medium">Account Requests</p>
                        <p class="text-white text-2xl font-bold">{{ $totalAccountRequests }}</p>
                    </div>
                    <div class="bg-orange-400 bg-opacity-30 rounded-full p-2">
                        <i class="fas fa-user-plus text-white text-lg"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('admin.requests.new-account-requests') }}" class="text-orange-100 hover:text-white text-xs font-medium flex items-center">
                        Manage <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Blotter Reports Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-xs font-medium">Blotter Reports</p>
                        <p class="text-white text-2xl font-bold">{{ $totalBlotterReports }}</p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-2">
                        <i class="fas fa-file-alt text-white text-lg"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('admin.blotter-reports') }}" class="text-purple-100 hover:text-white text-xs font-medium flex items-center">
                        View <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Document Requests Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-xs font-medium">Document Requests</p>
                        <p class="text-white text-2xl font-bold">{{ $totalDocumentRequests }}</p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-2">
                        <i class="fas fa-file-signature text-white text-lg"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('admin.document-requests') }}" class="text-green-100 hover:text-white text-xs font-medium flex items-center">
                        Manage <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Data Visualization Section -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Resident Demographics Chart -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Resident Demographics</h3>
                <div class="chart-container" style="position: relative; height:200px; width:100%">
                    <canvas id="residentDemographicsChart"></canvas>
                </div>
                <!-- Fallback table for resident demographics -->
                <div id="residentDemographicsTable" class="hidden mt-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age Group</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($residentDemographics as $demographic)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $demographic->age_bracket ?? 'Unknown' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $demographic->count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @php
                                        $total = collect($residentDemographics)->sum('count');
                                        $percentage = $total > 0 ? round(($demographic->count / $total) * 100, 1) : 0;
                                    @endphp
                                    {{ $percentage }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Document Requests Distribution -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Document Requests Distribution</h3>
                <div class="chart-container" style="position: relative; height:200px; width:100%">
                    <canvas id="documentRequestsChart"></canvas>
                </div>
                <!-- Fallback table for document requests -->
                <div id="documentRequestsTable" class="hidden mt-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($documentRequestTypes as $requestType)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $requestType->document_type ?? 'Unknown' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $requestType->count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @php
                                        $total = collect($documentRequestTypes)->sum('count');
                                        $percentage = $total > 0 ? round(($requestType->count / $total) * 100, 1) : 0;
                                    @endphp
                                    {{ $percentage }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Metrics Row -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Vaccination Records Card -->
        <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-teal-100 text-xs font-medium">Vaccination Records</p>
                        <p class="text-white text-2xl font-bold">{{ $totalVaccinationRecords }}</p>
                    </div>
                    <div class="bg-teal-400 bg-opacity-30 rounded-full p-2">
                        <i class="fas fa-syringe text-white text-lg"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('admin.vaccination-records.index') }}" class="text-teal-100 hover:text-white text-xs font-medium flex items-center">
                        View <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Upcoming Health Activities Card -->
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-xs font-medium">Upcoming Activities</p>
                        <p class="text-white text-2xl font-bold">{{ $upcomingHealthActivities }}</p>
                    </div>
                    <div class="bg-amber-400 bg-opacity-30 rounded-full p-2">
                        <i class="fas fa-calendar-alt text-white text-lg"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('admin.health-center-activities.index') }}" class="text-amber-100 hover:text-white text-xs font-medium flex items-center">
                        View <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Health Reports Card -->
        <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-pink-100 text-xs font-medium">Health Reports</p>
                        <p class="text-white text-2xl font-bold">{{ $totalHealthReports }}</p>
                    </div>
                    <div class="bg-pink-400 bg-opacity-30 rounded-full p-2">
                        <i class="fas fa-heartbeat text-white text-lg"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('admin.health-reports') }}" class="text-pink-100 hover:text-white text-xs font-medium flex items-center">
                        View <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Trends Section -->
    <div class="mt-8 bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Resident Registration Trends</h3>
            <div class="chart-container" style="position: relative; height:80px; width:100%">
                <canvas id="residentTrendsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- System Health Status -->
    <div class="mt-8 bg-white rounded-xl shadow-lg border border-gray-100">
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
                        <p class="font-medium text-gray-900">Resident Demographic Analysis</p>
                        <p class="text-sm text-gray-600">Analyze resident demographics</p>
                    </div>
                </a>
                <a href="{{ route('admin.decision-tree') }}" class="flex items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-sitemap text-indigo-600 text-xl mr-3"></i>
                    <div>
                        <p class="font-medium text-gray-900">Resident Classification & Prediction</p>
                        <p class="text-sm text-gray-600">Classification & prediction</p>
                    </div>
                </a>
                <a href="{{ route('admin.vaccination-records.create') }}" class="flex items-center p-4 bg-teal-50 rounded-lg hover:bg-teal-100 transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-syringe text-teal-600 text-xl mr-3"></i>
                    <div>
                        <p class="font-medium text-gray-900">Add Vaccination Record</p>
                        <p class="text-sm text-gray-600">Record new vaccination</p>
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
                    <span class="text-sm font-medium text-gray-700">Resident Demographics</span>
                </a>
                <a href="{{ route('admin.decision-tree') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-sitemap text-indigo-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700">Resident Classification</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Ensure charts don't extend infinitely */
    .chart-container {
        max-height: 200px;
        overflow: hidden;
    }
    
    #residentTrendsChart {
        max-height: 80px;
    }
    
    /* Ensure tables don't extend infinitely */
    #residentDemographicsTable, #documentRequestsTable {
        max-height: 200px;
        overflow-y: auto;
    }
</style>

<!-- Chart initialization scripts -->
<script>
    // Initialize charts with error handling and cleanup
    function initializeCharts() {
        // Destroy existing charts if they exist
        if (window.residentChartInstance) {
            window.residentChartInstance.destroy();
            window.residentChartInstance = null;
        }
        if (window.documentChartInstance) {
            window.documentChartInstance.destroy();
            window.documentChartInstance = null;
        }
        if (window.trendsChartInstance) {
            window.trendsChartInstance.destroy();
            window.trendsChartInstance = null;
        }

        // Prepare data for resident demographics chart
        try {
            const residentDemographicsData = @json($residentDemographics);
            // Validate data
            if (!residentDemographicsData || !Array.isArray(residentDemographicsData) || residentDemographicsData.length === 0) {
                console.warn('No resident demographics data available');
                return;
            }
            
            const residentLabels = residentDemographicsData.map(item => item.age_bracket || 'Unknown');
            const residentCounts = residentDemographicsData.map(item => item.count);

            // Validate that we have data to display
            if (residentLabels.length === 0 || residentCounts.length === 0) {
                console.warn('No resident demographics data to display');
                return;
            }

            // Resident Demographics Chart
            const residentCtx = document.getElementById('residentDemographicsChart').getContext('2d');
            window.residentChartInstance = new Chart(residentCtx, {
                type: 'doughnut',
                data: {
                    labels: residentLabels,
                    datasets: [{
                        data: residentCounts,
                        backgroundColor: [
                            '#3B82F6', // blue-500
                            '#10B981', // green-500
                            '#8B5CF6', // purple-500
                            '#F59E0B', // amber-500
                            '#EF4444', // red-500
                            '#EC4899', // pink-500
                            '#6366F1', // indigo-500
                            '#14B8A6'  // teal-500
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 15
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error initializing resident demographics chart:', error);
        }

        // Prepare data for document requests chart
        try {
            const documentRequestsData = @json($documentRequestTypes);
            // Validate data
            if (!documentRequestsData || !Array.isArray(documentRequestsData) || documentRequestsData.length === 0) {
                console.warn('No document requests data available');
                return;
            }
            
            const documentLabels = documentRequestsData.map(item => item.document_type || 'Unknown');
            const documentCounts = documentRequestsData.map(item => item.count);

            // Validate that we have data to display
            if (documentLabels.length === 0 || documentCounts.length === 0) {
                console.warn('No document requests data to display');
                return;
            }

            // Document Requests Chart
            const documentCtx = document.getElementById('documentRequestsChart').getContext('2d');
            window.documentChartInstance = new Chart(documentCtx, {
                type: 'pie',
                data: {
                    labels: documentLabels,
                    datasets: [{
                        data: documentCounts,
                        backgroundColor: [
                            '#3B82F6', // blue-500
                            '#10B981', // green-500
                            '#8B5CF6', // purple-500
                            '#F59E0B', // amber-500
                            '#EF4444', // red-500
                            '#EC4899', // pink-500
                            '#6366F1', // indigo-500
                            '#14B8A6'  // teal-500
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 15
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error initializing document requests chart:', error);
        }

        // Prepare data for resident trends chart
        try {
            const residentTrendsData = @json($residentTrends);
            // Validate data
            if (!residentTrendsData || !Array.isArray(residentTrendsData) || residentTrendsData.length === 0) {
                console.warn('No resident trends data available');
                return;
            }
            
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const trendLabels = residentTrendsData.map(item => monthNames[item.month - 1]);
            const trendCounts = residentTrendsData.map(item => item.count);

            // Validate that we have data to display
            if (trendLabels.length === 0 || trendCounts.length === 0) {
                console.warn('No resident trends data to display');
                return;
            }

            // Resident Trends Chart
            const trendsCtx = document.getElementById('residentTrendsChart').getContext('2d');
            window.trendsChartInstance = new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'New Residents',
                        data: trendCounts,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#10B981',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error initializing resident trends chart:', error);
        }
    }

    // Initialize charts when the page is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeCharts);
    } else {
        // DOM is already loaded
        initializeCharts();
    }

    // Re-initialize charts if the window is resized
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            initializeCharts();
        }, 100);
    });

    // Fallback to tables if charts fail to initialize
    function showChartFallback() {
        // Show fallback tables if charts fail
        const residentChartContainer = document.querySelector('#residentDemographicsChart').closest('.chart-container');
        const documentChartContainer = document.querySelector('#documentRequestsChart').closest('.chart-container');
        const residentTable = document.getElementById('residentDemographicsTable');
        const documentTable = document.getElementById('documentRequestsTable');

        if (residentChartContainer && residentTable) {
            residentChartContainer.classList.add('hidden');
            residentTable.classList.remove('hidden');
        }

        if (documentChartContainer && documentTable) {
            documentChartContainer.classList.add('hidden');
            documentTable.classList.remove('hidden');
        }
    }

    // Add a timeout to show fallback if charts don't initialize in time
    setTimeout(function() {
        // Check if charts are initialized
        if (!window.residentChartInstance || !window.documentChartInstance) {
            console.warn('Charts failed to initialize, showing fallback tables');
            showChartFallback();
        }
    }, 5000); // 5 second timeout
</script>
@endsection
