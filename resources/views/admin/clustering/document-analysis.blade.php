@extends('admin.main.layout')

@section('title', 'Document Requests Analysis')

@section('content')
@php
    // Data validation
    $purokCounts = $purokCounts ?? [];
    $purokTypeBreakdown = $purokTypeBreakdown ?? [];
    $totalRequests = $totalRequests ?? 0;
    $totalPuroks = $totalPuroks ?? 0;
    $analysis = $analysis ?? [];
@endphp
<div class="max-w-7xl mx-auto pt-2">
    <!-- Document Analysis Dashboard Skeleton -->
    <div id="documentAnalysisSkeleton">
        @include('components.loading.analytics-dashboard-skeleton')
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="documentAnalysisContent" style="display: none;">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-3">Document Requests Analysis</h1>
                <p class="text-gray-600 text-lg mb-4">Understanding document request patterns by purok</p>
            
            <!-- Controls -->
            <div class="flex flex-wrap justify-center gap-3 mb-4">
                <!-- Action Buttons -->
                <button onclick="refreshAnalysis()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
                
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                    <i class="fas fa-print mr-2"></i>
                    Print
                </button>
            </div>
            
            <!-- Current Settings -->
            <div class="flex flex-wrap justify-center gap-2 text-sm">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    Purok-based Analysis
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                    <i class="fas fa-chart-bar mr-1"></i>
                    {{ $totalPuroks }} Puroks Analyzed
                </span>
            </div>
        </div>
    </div>

    @if(isset($error))
        <div class="mb-3 bg-red-50 border border-red-200 rounded-lg p-4">
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
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-green-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs lg:text-sm font-medium text-gray-500">Total Puroks</p>
                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $totalPuroks }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-line text-purple-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs lg:text-sm font-medium text-gray-500">Average per Purok</p>
                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $analysis['averagePerPurok'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-file-alt text-blue-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs lg:text-sm font-medium text-gray-500">Most Active Purok</p>
                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $analysis['mostActivePurok'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Analysis Section -->
        <div class="space-y-6 mb-3">
            <!-- Purok Distribution Chart -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-chart-bar text-green-600 mr-2"></i>
                            Purok Distribution
                        </h3>
                        <div class="flex space-x-2">
                            <button onclick="downloadChart()" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                <i class="fas fa-download mr-1"></i>
                                Download
                            </button>
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height: 400px;">
                        <canvas id="purokChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Pie Chart Modal for Document Types Breakdown -->
            <div id="pieChartModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Background overlay -->
                    <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closePieChart()"></div>

                    <!-- Modal panel -->
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                        <div class="bg-white px-6 pt-5 pb-4 sm:p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xl font-semibold text-gray-900" id="pieChartTitle">
                                    <i class="fas fa-chart-pie text-green-600 mr-2"></i>
                                    Document Types Breakdown
                                </h3>
                                <button onclick="closePieChart()" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition-colors" title="Close modal">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                            <div class="chart-container" style="position: relative; height: 450px;">
                                <canvas id="pieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Analysis -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Purok Analysis -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-map-marker-alt text-green-600 mr-2"></i>
                            Purok Breakdown
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purok</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $total = array_sum($purokCounts);
                                    @endphp
                                    @foreach(array_slice($purokCounts, 0, 10) as $purok => $count)
                                    <tr class="{{ $loop->index < 3 ? 'bg-green-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $purok }}
                                            @if($loop->index < 3)
                                                <i class="fas fa-file-alt text-blue-500 ml-1"></i>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $count }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $total > 0 ? round(($count / $total) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Top Puroks Summary -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-file-alt text-blue-600 mr-2"></i>
                            Top Performing Puroks
                        </h3>
                        <div class="space-y-4">
                            @foreach(array_slice($purokCounts, 0, 5) as $purok => $count)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-900">{{ $purok }}</h4>
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                        {{ $count }} requests
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $total > 0 ? ($count / $total) * 100 : 0 }}%"></div>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">{{ $total > 0 ? round(($count / $total) * 100, 1) : 0 }}% of total requests</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Insights Section -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-lightbulb text-yellow-600 mr-2"></i>
                        Key Insights
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-green-50 rounded-lg p-4">
                            <h4 class="font-semibold text-green-900 mb-2">Top 3 Puroks</h4>
                            <ul class="text-sm text-green-800 space-y-1">
                                @foreach($analysis['top3Puroks'] ?? [] as $purok => $count)
                                <li>{{ $purok }}: {{ $count }} requests ({{ $analysis['distribution'][$purok]['percentage'] ?? 0 }}%)</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h4 class="font-semibold text-blue-900 mb-2">Distribution Analysis</h4>
                            <ul class="text-sm text-blue-800 space-y-1">
                                <li>Top 3 puroks account for {{ $analysis['top3Percentage'] ?? 0 }}% of all requests</li>
                                <li>Average requests per purok: {{ $analysis['averagePerPurok'] ?? 0 }}</li>
                                <li>Most active purok: {{ $analysis['mostActivePurok'] ?? 'N/A' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Skeleton loading control
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const analysisSkeleton = document.getElementById('documentAnalysisSkeleton');
        const content = document.getElementById('documentAnalysisContent');
        
        if (analysisSkeleton) analysisSkeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});

// Purok Chart
const purokCtx = document.getElementById('purokChart');
let purokChart = null;
let pieChart = null;
const purokTypeBreakdown = JSON.parse(`@json($purokTypeBreakdown ?? [])`);

if (purokCtx) {
    const purokLabels = JSON.parse(`@json(array_keys($purokCounts))`);
    const purokValues = JSON.parse(`@json(array_values($purokCounts))`);
    const purokTotal = JSON.parse(`@json(array_sum($purokCounts))`);

    purokChart = new Chart(purokCtx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: purokLabels,
            datasets: [{
                label: 'Document Requests',
                data: purokValues,
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(14, 165, 233, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(251, 146, 60, 0.8)',
                    'rgba(168, 85, 247, 0.8)'
                ],
                borderColor: [
                    'rgba(34, 197, 94, 1)',
                    'rgba(59, 130, 246, 1)',
                    'rgba(245, 158, 11, 1)',
                    'rgba(239, 68, 68, 1)',
                    'rgba(139, 92, 246, 1)',
                    'rgba(236, 72, 153, 1)',
                    'rgba(14, 165, 233, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(251, 146, 60, 1)',
                    'rgba(168, 85, 247, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const percentage = purokTotal > 0 ? ((parseInt(context.parsed.y) / purokTotal) * 100).toFixed(1) : 0;
                            return `${context.parsed.y} requests (${percentage}%)`;
                        }
                    }
                }
            },
            onClick: function(event, elements) {
                if (elements.length > 0) {
                    const elementIndex = elements[0].index;
                    const clickedPurok = purokLabels[elementIndex];
                    showPieChart(clickedPurok);
                }
            }
        }
    });
}

function showPieChart(purok) {
    const breakdown = purokTypeBreakdown[purok] || {};
    const typeLabels = Object.keys(breakdown);
    const typeValues = Object.values(breakdown);
    
    if (typeLabels.length === 0) {
        alert('No document type data available for ' + purok);
        return;
    }
    
    // Show the modal
    const modal = document.getElementById('pieChartModal');
    const title = document.getElementById('pieChartTitle');
    title.innerHTML = `<i class="fas fa-chart-pie text-green-600 mr-2"></i>Document Types Breakdown - ${purok}`;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
    
    // Destroy existing pie chart if it exists
    const pieCtx = document.getElementById('pieChart');
    if (pieChart) {
        pieChart.destroy();
    }
    
    // Generate colors for pie chart
    const pieColors = [
        'rgba(34, 197, 94, 0.8)',   // Green
        'rgba(59, 130, 246, 0.8)',  // Blue
        'rgba(245, 158, 11, 0.8)',  // Yellow
        'rgba(239, 68, 68, 0.8)',   // Red
        'rgba(139, 92, 246, 0.8)',  // Purple
        'rgba(236, 72, 153, 0.8)', // Pink
        'rgba(14, 165, 233, 0.8)', // Sky
        'rgba(16, 185, 129, 0.8)',  // Emerald
        'rgba(251, 146, 60, 0.8)', // Orange
        'rgba(168, 85, 247, 0.8)'   // Violet
    ];
    
    const borderColors = pieColors.map(color => color.replace('0.8', '1'));
    
    // Create new pie chart
    pieChart = new Chart(pieCtx.getContext('2d'), {
        type: 'pie',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeValues,
                backgroundColor: pieColors.slice(0, typeLabels.length),
                borderColor: borderColors.slice(0, typeLabels.length),
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function closePieChart() {
    const modal = document.getElementById('pieChartModal');
    modal.classList.add('hidden');
    document.body.style.overflow = ''; // Restore scrolling
    if (pieChart) {
        pieChart.destroy();
        pieChart = null;
    }
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('pieChartModal');
        if (!modal.classList.contains('hidden')) {
            closePieChart();
        }
    }
});

function refreshAnalysis() {
    location.reload();
}

function downloadChart() {
    const canvas = document.getElementById('purokChart');
    const link = document.createElement('a');
    link.download = 'document-purok-distribution.png';
    link.href = canvas.toDataURL();
    link.click();
}
</script>

<style>
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
