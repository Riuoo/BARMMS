@extends('admin.main.layout')

@section('title', 'Resident Clustering Analysis')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Enhanced Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Resident Demographic Clustering</h1>
                <p class="text-gray-600 text-lg">AI-powered demographic analysis to group residents for targeted services</p>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <button onclick="refreshAnalysis()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh Analysis
                </button>
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <i class="fas fa-print mr-2"></i>
                    Print Report
                </button>
                <button onclick="exportData()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <i class="fas fa-download mr-2"></i>
                    Export Data
                </button>
            </div>
        </div>
    </div>

    @if(isset($error))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Analysis Error</h3>
                    <div class="mt-2 text-sm text-red-700">
                        {{ $error }}
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Performance Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Residents Card -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Residents</p>
                            <p class="text-white text-3xl font-bold">{{ $sampleSize }}</p>
                        </div>
                        <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clusters Found Card -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Clusters Found</p>
                            <p class="text-white text-3xl font-bold">{{ count($clusters) }}</p>
                        </div>
                        <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                            <i class="fas fa-chart-pie text-white text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Processing Time Card -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Processing Time</p>
                            <p class="text-white text-3xl font-bold">{{ $processingTime }}ms</p>
                        </div>
                        <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                            <i class="fas fa-clock text-white text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Algorithm Status Card -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Algorithm Status</p>
                            <p class="text-white text-2xl font-bold">{{ $converged ? 'Converged' : 'Not Converged' }}</p>
                        </div>
                        <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                            <i class="fas fa-check-circle text-white text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Analysis Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Cluster Distribution Chart -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-chart-donut text-purple-600 mr-2"></i>
                            Cluster Distribution
                        </h3>
                        <div class="flex space-x-2">
                            <button onclick="downloadChart()" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                                <i class="fas fa-download mr-1"></i>
                                Download
                            </button>
                            <button onclick="fullscreenChart()" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                                <i class="fas fa-expand mr-1"></i>
                                Fullscreen
                            </button>
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height: 400px;">
                        <canvas id="clusterChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Cluster Characteristics -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Cluster Characteristics
                    </h3>
                    <div class="space-y-4">
                        @foreach($characteristics as $clusterId => $cluster)
                            @if($cluster['size'] > 0)
                                <div class="cluster-card p-4 border-l-4 border-blue-500 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all duration-200">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-lg font-semibold text-blue-900">
                                            <i class="fas fa-circle text-blue-500 mr-2"></i>
                                            Cluster {{ $clusterId + 1 }}
                                        </h4>
                                        <span class="bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                            {{ $cluster['size'] }} residents
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 mb-3">
                                        <div class="text-center">
                                            <p class="text-xs text-gray-600">Avg Age</p>
                                            <p class="text-lg font-bold text-gray-900">{{ $cluster['avg_age'] }}</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs text-gray-600">Family Size</p>
                                            <p class="text-lg font-bold text-gray-900">{{ $cluster['avg_family_size'] }}</p>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-600">Income:</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $cluster['most_common_income'] === 'High' ? 'bg-green-100 text-green-800' : ($cluster['most_common_income'] === 'Low' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $cluster['most_common_income'] }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-600">Employment:</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $cluster['most_common_employment'] === 'Full-time' ? 'bg-green-100 text-green-800' : ($cluster['most_common_employment'] === 'Unemployed' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                {{ $cluster['most_common_employment'] }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-600">Health:</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $cluster['most_common_health'] === 'Excellent' ? 'bg-green-100 text-green-800' : ($cluster['most_common_health'] === 'Critical' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $cluster['most_common_health'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analysis Section -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-8">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-table text-green-600 mr-2"></i>
                        Detailed Resident Analysis
                    </h3>
                    <div class="flex space-x-4">
                        <input type="text" id="searchTable" placeholder="Search residents..." class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <select id="filterCluster" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">All Clusters</option>
                            @foreach($characteristics as $clusterId => $cluster)
                                @if($cluster['size'] > 0)
                                    <option value="{{ $clusterId }}">Cluster {{ $clusterId + 1 }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="residentsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cluster</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resident Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Family Size</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Education</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Income</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Health Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($clusters as $clusterId => $cluster)
                                @foreach($cluster as $point)
                                    <tr class="cluster-row-{{ $clusterId }} hover:bg-gray-50 transition-colors duration-200" data-cluster="{{ $clusterId }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Cluster {{ $clusterId + 1 }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $point['resident']->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $point['resident']->age ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $point['resident']->family_size ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $point['resident']->education_level ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $point['resident']->income_level === 'High' ? 'bg-green-100 text-green-800' : ($point['resident']->income_level === 'Low' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $point['resident']->income_level ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $point['resident']->employment_status === 'Full-time' ? 'bg-green-100 text-green-800' : ($point['resident']->employment_status === 'Unemployed' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                {{ $point['resident']->employment_status ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $point['resident']->health_status === 'Excellent' ? 'bg-green-100 text-green-800' : ($point['resident']->health_status === 'Critical' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $point['resident']->health_status ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="viewResident({{ $point['resident']->id }})" class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Insights and Recommendations -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">
                    <i class="fas fa-lightbulb text-yellow-600 mr-2"></i>
                    Insights & Recommendations
                </h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-md font-semibold text-blue-900 mb-4">
                            <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                            Key Findings
                        </h4>
                        <div class="space-y-4">
                            @foreach($characteristics as $clusterId => $cluster)
                                @if($cluster['size'] > 0)
                                    <div class="p-4 border-l-4 border-blue-500 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all duration-200">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-users text-white text-sm"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <h5 class="text-sm font-medium text-blue-900">Cluster {{ $clusterId + 1 }}</h5>
                                                <p class="text-sm text-blue-700 mt-1">
                                                    {{ $cluster['size'] }} residents with average age {{ $cluster['avg_age'] }} years.
                                                    Most common income level is {{ $cluster['most_common_income'] }}.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <h4 class="text-md font-semibold text-green-900 mb-4">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            Actionable Recommendations
                        </h4>
                        <div class="space-y-4">
                            @foreach($characteristics as $clusterId => $cluster)
                                @if($cluster['size'] > 0)
                                    <div class="p-4 border-l-4 border-green-500 bg-green-50 rounded-lg hover:bg-green-100 transition-all duration-200">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-lightbulb text-white text-sm"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <h5 class="text-sm font-medium text-green-900">Cluster {{ $clusterId + 1 }}</h5>
                                                <p class="text-sm text-green-700 mt-1">
                                                    @if($cluster['most_common_income'] === 'Low')
                                                        Consider financial assistance programs and livelihood training.
                                                    @elseif($cluster['most_common_employment'] === 'Unemployed')
                                                        Focus on job placement and skills development programs.
                                                    @else
                                                        Provide general community services and health programs.
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cluster chart
    initializeClusterChart();
    
    // Initialize table search and filter
    initializeTableFeatures();
});

function initializeClusterChart() {
    const ctx = document.getElementById('clusterChart').getContext('2d');
    const clusterData = @json($characteristics);
    
    const labels = [];
    const data = [];
    const colors = ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444'];
    
    Object.keys(clusterData).forEach((clusterId, index) => {
        const cluster = clusterData[clusterId];
        if (cluster.size > 0) {
            labels.push(`Cluster ${parseInt(clusterId) + 1} (${cluster.size} residents)`);
            data.push(cluster.size);
        }
    });
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors.slice(0, labels.length),
                borderWidth: 3,
                borderColor: '#fff',
                hoverBorderWidth: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true
            }
        }
    });
}

function initializeTableFeatures() {
    // Search functionality
    const searchInput = document.getElementById('searchTable');
    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#residentsTable tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
    
    // Filter by cluster
    const filterSelect = document.getElementById('filterCluster');
    filterSelect.addEventListener('change', function() {
        const selectedCluster = this.value;
        const rows = document.querySelectorAll('#residentsTable tbody tr');
        
        rows.forEach(row => {
            const clusterId = row.getAttribute('data-cluster');
            row.style.display = selectedCluster === '' || clusterId === selectedCluster ? '' : 'none';
        });
    });
}

function refreshAnalysis() {
    location.reload();
}

function exportData() {
    // Create download link for CSV
    const link = document.createElement('a');
    link.href = '{{ route("admin.clustering.export") }}?format=csv';
    link.download = 'clustering_results.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function downloadChart() {
    const canvas = document.getElementById('clusterChart');
    const link = document.createElement('a');
    link.download = 'cluster_chart.png';
    link.href = canvas.toDataURL();
    link.click();
}

function fullscreenChart() {
    const chartContainer = document.querySelector('.chart-container');
    if (chartContainer.requestFullscreen) {
        chartContainer.requestFullscreen();
    }
}

function viewResident(residentId) {
    // Open resident details in modal or redirect
    window.open(`/admin/residents/${residentId}`, '_blank');
}
</script>

<style>
.cluster-card {
    transition: all 0.3s ease;
}

.cluster-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

@media print {
    .btn, .flex.space-x-4 {
        display: none !important;
    }
    
    .chart-container {
        height: 300px !important;
    }
}

@media (max-width: 768px) {
    .flex.items-center.justify-between {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .chart-container {
        height: 300px;
    }
}
</style>
@endsection 