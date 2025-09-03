@extends('admin.main.layout')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Header Skeleton -->
    <div id="dashboardHeaderSkeleton" class="mb-3 animate-pulse">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <div class="h-10 w-80 bg-gray-200 rounded mb-2"></div>
                <div class="h-5 w-96 bg-gray-100 rounded"></div>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="bg-green-100 border border-green-200 rounded-lg px-8 py-4 w-56 h-10"></div>
            </div>
        </div>
    </div>
    <!-- Header Content (hidden initially) -->
    <div id="dashboardHeaderContent" class="mb-3 hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Welcome back, {{ $barangay_profile->name ?? 'Admin' }}</h1>
                <p class="text-sm md:text-base text-gray-600">Here's what's happening in your barangay today</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-2">
                    <span class="text-green-800 text-sm font-medium">Last updated: {{ now()->format('M d, Y g:i A') }}</span>
                </div>
            </div>
        </div>
    </div>
    <!-- Stats Cards -->
    <div id="dashboardStatsContainer">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-4 mb-3 animate-pulse">
            @for ($i = 0; $i < 4; $i++)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4 flex flex-col justify-between h-32">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                            <div class="h-8 w-16 bg-gray-300 rounded"></div>
                        </div>
                        <div class="bg-gray-200 rounded-full w-10 h-10"></div>
                    </div>
                    <div class="h-4 w-20 bg-gray-100 rounded"></div>
                </div>
            @endfor
        </div>
        <div id="dashboardStatsContent" class="hidden">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-4 mb-3">
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
        </div>
    </div>
    <!-- Charts Section -->
    <div id="chartsContainer">
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6 animate-pulse">
            @for ($i = 0; $i < 2; $i++)
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 flex flex-col justify-between h-80">
                    <div class="h-6 w-2/5 bg-gray-200 rounded mb-6"></div>
                    <div class="flex items-center justify-center flex-1">
                        <div class="bg-gray-200 rounded-full w-40 h-40"></div>
                    </div>
                </div>
            @endfor
        </div>
        <div id="chartsContent" class="hidden">
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Resident Demographics Chart -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Resident Demographics</h3>
                        <div class="chart-container" style="position: relative; height:200px; width:100%">
                            <canvas id="residentDemographicsChart"></canvas>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Trends Section -->
    <div id="trendsContainer">
        <div class="mt-8 bg-white rounded-xl shadow-lg border border-gray-100 p-4 animate-pulse flex flex-col h-56">
            <div class="h-6 w-2/5 bg-gray-200 rounded mb-6"></div>
            <div class="h-20 w-full bg-gray-200 rounded flex-1"></div>
        </div>
        <div id="trendsContent" class="hidden">
            <div class="mt-8 bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Resident Registration Trends</h3>
                    <div class="chart-container" style="position: relative; height:80px; width:100%">
                        <canvas id="residentTrendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Floating Action Button -->
    <div id="fabContainer">
        <div class="fixed bottom-6 right-6 z-50 animate-pulse flex items-center justify-center">
            <div class="bg-gray-300 rounded-full w-16 h-16 shadow-lg flex items-center justify-center">
                <div class="relative w-7 h-7">
                    <div class="absolute bg-gray-400 rounded w-7 h-1 top-3 left-0"></div>
                    <div class="absolute bg-gray-400 rounded w-1 h-7 top-0 left-3"></div>
                </div>
            </div>
        </div>
        <div id="fabContent" class="hidden">
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
                            <span class="text-sm font-medium text-gray-700">Create Blotter Report</span>
                        </a>
                        <a href="{{ route('admin.document-requests.create') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                            <i class="fas fa-file-alt text-purple-600 mr-2"></i>
                            <span class="text-sm font-medium text-gray-700">Create Document Request</span>
                        </a>
                        <a href="{{ route('admin.clustering') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                            <i class="fas fa-chart-pie text-purple-600 mr-2"></i>
                            <span class="text-sm font-medium text-gray-700">Resident Demographic Analysis</span>
                        </a>
                        <a href="{{ route('admin.decision-tree') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                            <i class="fas fa-sitemap text-indigo-600 mr-2"></i>
                            <span class="text-sm font-medium text-gray-700">Resident Classification & Prediction</span>
                        </a>
                    </div>
                </div>
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
            const residentDemographicsData = JSON.parse(`@json($residentDemographics)`);
            // Validate data
            if (!residentDemographicsData || !Array.isArray(residentDemographicsData) || residentDemographicsData.length === 0) {
                console.warn('No resident demographics data available');
                // Create empty chart with placeholder data
                const residentCtx = document.getElementById('residentDemographicsChart').getContext('2d');
                window.residentChartInstance = new Chart(residentCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['No Data Available'],
                        datasets: [{
                            data: [1],
                            backgroundColor: ['#E5E7EB'],
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
                return;
            }
            
            const residentLabels = residentDemographicsData.map(item => item.age_bracket || 'Unknown');
            const residentCounts = residentDemographicsData.map(item => item.count);

            // Validate that we have data to display
            if (residentLabels.length === 0 || residentCounts.length === 0) {
                console.warn('No resident demographics data to display');
                // Create empty chart with placeholder data
                const residentCtx = document.getElementById('residentDemographicsChart').getContext('2d');
                window.residentChartInstance = new Chart(residentCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['No Data Available'],
                        datasets: [{
                            data: [1],
                            backgroundColor: ['#E5E7EB'],
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
            const documentRequestsData = JSON.parse(`@json($documentRequestTypes)`);
            // Validate data
            if (!documentRequestsData || !Array.isArray(documentRequestsData) || documentRequestsData.length === 0) {
                console.warn('No document requests data available');
                // Create empty chart with placeholder data
                const documentCtx = document.getElementById('documentRequestsChart').getContext('2d');
                window.documentChartInstance = new Chart(documentCtx, {
                    type: 'pie',
                    data: {
                        labels: ['No Data Available'],
                        datasets: [{
                            data: [1],
                            backgroundColor: ['#E5E7EB'],
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
                return;
            }
            
            const documentLabels = documentRequestsData.map(item => item.document_type || 'Unknown');
            const documentCounts = documentRequestsData.map(item => item.count);

            // Validate that we have data to display
            if (documentLabels.length === 0 || documentCounts.length === 0) {
                console.warn('No document requests data to display');
                // Create empty chart with placeholder data
                const documentCtx = document.getElementById('documentRequestsChart').getContext('2d');
                window.documentChartInstance = new Chart(documentCtx, {
                    type: 'pie',
                    data: {
                        labels: ['No Data Available'],
                        datasets: [{
                            data: [1],
                            backgroundColor: ['#E5E7EB'],
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
            const residentTrendsData = JSON.parse(`@json($residentTrends)`);
            // Validate data
            if (!residentTrendsData || !Array.isArray(residentTrendsData) || residentTrendsData.length === 0) {
                console.warn('No resident trends data available');
                // Create empty chart with placeholder data
                const trendsCtx = document.getElementById('residentTrendsChart').getContext('2d');
                window.trendsChartInstance = new Chart(trendsCtx, {
                    type: 'line',
                    data: {
                        labels: ['No Data'],
                        datasets: [{
                            label: 'New Residents',
                            data: [0],
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
                return;
            }
            
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const trendLabels = residentTrendsData.map(item => monthNames[item.month - 1]);
            const trendCounts = residentTrendsData.map(item => item.count);

            // Validate that we have data to display
            if (trendLabels.length === 0 || trendCounts.length === 0) {
                console.warn('No resident trends data to display');
                // Create empty chart with placeholder data
                const trendsCtx = document.getElementById('residentTrendsChart').getContext('2d');
                window.trendsChartInstance = new Chart(trendsCtx, {
                    type: 'line',
                    data: {
                        labels: ['No Data'],
                        datasets: [{
                            label: 'New Residents',
                            data: [0],
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

</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add 1 second delay to show skeleton effect
    setTimeout(() => {
        // Header
        const headerSkeleton = document.getElementById('dashboardHeaderSkeleton');
        const headerContent = document.getElementById('dashboardHeaderContent');
        if (headerSkeleton && headerContent) {
            headerSkeleton.style.display = 'none';
            headerContent.classList.remove('hidden');
        }
        // Stats
        const statsContainer = document.getElementById('dashboardStatsContainer');
        const statsContent = document.getElementById('dashboardStatsContent');
        if (statsContainer && statsContent) {
            statsContainer.innerHTML = '';
            statsContainer.appendChild(statsContent);
            statsContent.classList.remove('hidden');
        }
        // Charts
        const chartsContainer = document.getElementById('chartsContainer');
        const chartsContent = document.getElementById('chartsContent');
        if (chartsContainer && chartsContent) {
            chartsContainer.innerHTML = '';
            chartsContainer.appendChild(chartsContent);
            chartsContent.classList.remove('hidden');
        }
        // Trends
        const trendsContainer = document.getElementById('trendsContainer');
        const trendsContent = document.getElementById('trendsContent');
        if (trendsContainer && trendsContent) {
            trendsContainer.innerHTML = '';
            trendsContainer.appendChild(trendsContent);
            trendsContent.classList.remove('hidden');
        }
        // FAB
        const fabContainer = document.getElementById('fabContainer');
        const fabContent = document.getElementById('fabContent');
        if (fabContainer && fabContent) {
            fabContainer.innerHTML = '';
            fabContainer.appendChild(fabContent);
            fabContent.classList.remove('hidden');
        }
    }, 1000); // 1 second delay to show skeleton effect
});
</script>
@endsection