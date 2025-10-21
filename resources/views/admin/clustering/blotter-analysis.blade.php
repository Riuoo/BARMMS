@extends('admin.main.layout')

@section('title', 'Blotter Reports Analysis')

@section('content')
@php
    // Data validation
    $purokCounts = $purokCounts ?? [];
    $totalReports = $totalReports ?? 0;
    $totalPuroks = $totalPuroks ?? 0;
    $analysis = $analysis ?? [];
@endphp
<div class="max-w-7xl mx-auto pt-2">
    <!-- Analysis Dashboard Skeleton -->
    <div id="analysisSkeleton">
        @include('components.loading.analytics-dashboard-skeleton')
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="analysisContent" style="display: none;">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-3">Blotter Reports Analysis</h1>
                <p class="text-gray-600 text-lg mb-4">Understanding incident patterns by purok</p>
                
                <!-- Controls -->
                <div class="flex flex-wrap justify-center gap-3 mb-4">
                    <!-- Action Buttons -->
                    <button onclick="refreshAnalysis()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors" id="refreshBtn" title="Refresh analysis with current data">
                        <i class="fas fa-sync-alt mr-2" id="refreshIcon"></i>
                        <span id="refreshText">Refresh</span>
                    </button>
                    
                    <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors" title="Print this analysis">
                        <i class="fas fa-print mr-2"></i>
                        Print
                    </button>
                </div>
                
                <!-- Current Settings -->
                <div class="flex flex-wrap justify-center gap-2 text-sm">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        Purok-based Analysis
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800">
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
        @elseif(empty($purokCounts))
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-chart-bar text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No data available for analysis</h3>
                <p class="text-gray-500">No blotter reports found. Please add some reports first.</p>
            </div>
        @else
            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-3">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-file-alt text-red-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs lg:text-sm font-medium text-gray-500">Total Reports</p>
                            <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $totalReports }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-blue-600 text-sm"></i>
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
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-chart-line text-green-600 text-sm"></i>
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
                                <i class="fas fa-trophy text-yellow-600 text-sm"></i>
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
                                <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                                Purok Distribution
                            </h3>
                            <div class="flex space-x-2">
                                <button onclick="downloadChart()" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200" title="Download chart as image">
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

                <!-- Detailed Analysis -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Purok Analysis -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
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
                                        <tr class="{{ $loop->index < 3 ? 'bg-blue-50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $purok }}
                                                @if($loop->index < 3)
                                                    <i class="fas fa-trophy text-yellow-500 ml-1"></i>
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
                                <i class="fas fa-trophy text-yellow-600 mr-2"></i>
                                Top Performing Puroks
                            </h3>
                            <div class="space-y-4">
                                @foreach(array_slice($purokCounts, 0, 5) as $purok => $count)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-semibold text-gray-900">{{ $purok }}</h4>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                            {{ $count }} reports
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $total > 0 ? ($count / $total) * 100 : 0 }}%"></div>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">{{ $total > 0 ? round(($count / $total) * 100, 1) : 0 }}% of total reports</p>
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
                            <div class="bg-blue-50 rounded-lg p-4">
                                <h4 class="font-semibold text-blue-900 mb-2">Top 3 Puroks</h4>
                                <ul class="text-sm text-blue-800 space-y-1">
                                    @foreach($analysis['top3Puroks'] ?? [] as $purok => $count)
                                    <li>{{ $purok }}: {{ $count }} reports ({{ $analysis['distribution'][$purok]['percentage'] ?? 0 }}%)</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <h4 class="font-semibold text-green-900 mb-2">Distribution Analysis</h4>
                                <ul class="text-sm text-green-800 space-y-1">
                                    <li>Top 3 puroks account for {{ $analysis['top3Percentage'] ?? 0 }}% of all reports</li>
                                    <li>Average reports per purok: {{ $analysis['averagePerPurok'] ?? 0 }}</li>
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
        const analysisSkeleton = document.getElementById('analysisSkeleton');
        const content = document.getElementById('analysisContent');
        
        if (analysisSkeleton) analysisSkeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});

// Purok Chart
const purokCtx = document.getElementById('purokChart');
if (purokCtx) {
    const purokLabels = JSON.parse(`@json(array_keys($purokCounts))`);
    const purokValues = JSON.parse(`@json(array_values($purokCounts))`);
    const purokTotal = JSON.parse(`@json(array_sum($purokCounts))`);

    const purokChart = new Chart(purokCtx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: purokLabels,
            datasets: [{
                label: 'Blotter Reports',
                data: purokValues,
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(14, 165, 233, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(251, 146, 60, 0.8)',
                    'rgba(168, 85, 247, 0.8)'
                ],
                borderColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(245, 158, 11, 1)',
                    'rgba(239, 68, 68, 1)',
                    'rgba(139, 92, 246, 1)',
                    'rgba(236, 72, 153, 1)',
                    'rgba(14, 165, 233, 1)',
                    'rgba(34, 197, 94, 1)',
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
                            return `${context.parsed.y} reports (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function refreshAnalysis() {
    const btn = document.getElementById('refreshBtn');
    const icon = document.getElementById('refreshIcon');
    const text = document.getElementById('refreshText');
    
    // Add loading state
    btn.disabled = true;
    icon.classList.add('fa-spin');
    text.textContent = 'Refreshing...';
    
    // Reload page
    setTimeout(() => {
        location.reload();
    }, 500);
}

function downloadChart() {
    const canvas = document.getElementById('purokChart');
    if (canvas) {
        const link = document.createElement('a');
        link.download = 'blotter-purok-distribution.png';
        link.href = canvas.toDataURL();
        link.click();
    }
}
</script>

<style>
@media print {
    .btn, .flex.space-x-4, button {
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
    
    .flex.flex-wrap.justify-center {
        flex-direction: column;
        align-items: center;
    }
    
    .inline-flex.items-center.gap-2 {
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 640px) {
    .grid.grid-cols-2.lg\\:grid-cols-4 {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .grid.grid-cols-1.lg\\:grid-cols-2 {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
