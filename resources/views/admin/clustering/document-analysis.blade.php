@extends('admin.main.layout')

@section('title', 'Document Requests Analysis')

@section('content')
@php
    // Service instance for data processing
    $documentService = app(\App\Services\DocumentRequestClusteringService::class);
@endphp
<div class="max-w-7xl mx-auto pt-2">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-3">Document Requests Analysis</h1>
            <p class="text-gray-600 text-lg mb-4">Understanding document request patterns by purok and clustering similar requests</p>
            
            <!-- Controls -->
            <div class="flex flex-wrap justify-center gap-3 mb-4">
                @php
                    $baseUrl = route('clustering.document.analysis');
                    $isHier = !empty($useHierarchical);
                    $isAutoK = !empty($useOptimalK);
                    $kValue = $k ?? 3;
                @endphp
                
                <!-- Mode Toggle -->
                <a href="{{ $baseUrl }}?hierarchical={{ $isHier ? '' : '1' }}" 
                   class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium {{ $isHier ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }} hover:opacity-90">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    {{ $isHier ? 'By Purok' : 'Overall' }}
                </a>
                
                <!-- Auto-K Toggle -->
                <form method="GET" action="{{ $baseUrl }}" class="inline-flex items-center gap-2">
                    @foreach(request()->except(['use_optimal_k', 'k', 'hierarchical']) as $key => $val)
                        @if(!is_null($val) && $val !== '')
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endif
                    @endforeach
                    @if($isHier)
                        <input type="hidden" name="hierarchical" value="1">
                    @endif
                    <label class="inline-flex items-center text-sm">
                        <input type="checkbox" name="use_optimal_k" value="1" {{ $isAutoK ? 'checked' : '' }} onchange="this.form.submit()" class="mr-2">
                        <span>Auto-detect groups</span>
                    </label>
                </form>
                
                <!-- Manual K Selection -->
                @if(!$isAutoK)
                <form method="GET" action="{{ $baseUrl }}" class="inline-flex items-center gap-2">
                    @foreach(request()->except(['k', 'hierarchical']) as $key => $val)
                        @if(!is_null($val) && $val !== '')
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endif
                    @endforeach
                    @if($isHier)
                        <input type="hidden" name="hierarchical" value="1">
                    @endif
                    <label class="text-sm text-gray-700 font-medium">Number of Groups:</label>
                    <select name="k" class="px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                        @for($i = 2; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ $kValue == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </form>
                @endif
                
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
                <span class="inline-flex items-center px-3 py-1 rounded-full {{ $isHier ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    {{ $isHier ? 'Grouped by Purok' : 'Overall Groups' }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full {{ $isAutoK ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-700' }}">
                    <i class="fas fa-users mr-1"></i>
                    {{ $isAutoK ? 'Auto Groups' : $k . ' Groups' }}
                </span>
                @if(isset($silhouette))
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800" title="Quality score - higher is better">
                    <i class="fas fa-star mr-1"></i>
                    Quality: {{ number_format($silhouette ?? 0, 2) }}
                </span>
                @endif
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
                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ count($purokCounts) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-layer-group text-purple-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs lg:text-sm font-medium text-gray-500">Groups Found</p>
                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ count($clusters) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-line text-yellow-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs lg:text-sm font-medium text-gray-500">Highest Purok</p>
                                                 <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ !empty($purokCounts) ? array_key_first($purokCounts) : 'N/A' }}</p>
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

            <!-- Cluster Distribution Chart -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-sitemap text-purple-600 mr-2"></i>
                            Cluster Distribution (K={{ $k }})
                        </h3>
                        <div class="flex space-x-2">
                            <button onclick="downloadClusterChart()" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                                <i class="fas fa-download mr-1"></i>
                                Download
                            </button>
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height: 400px;">
                        <canvas id="clusterChart"></canvas>
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

                <!-- Cluster Analysis -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-layer-group text-purple-600 mr-2"></i>
                            Cluster Summary
                        </h3>
                        <div class="space-y-4">
                            @foreach($clusters as $clusterId => $cluster)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-900">Cluster {{ (int)$clusterId + 1 }}</h4>
                                    <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                        {{ count($cluster) }} requests
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">Contains {{ count($cluster) }} document requests</p>
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
                                @foreach(array_slice($purokCounts, 0, 3) as $purok => $count)
                                <li>{{ $purok }}: {{ $count }} requests ({{ $total > 0 ? round(($count / $total) * 100, 1) : 0 }}%)</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h4 class="font-semibold text-blue-900 mb-2">Distribution Analysis</h4>
                            <ul class="text-sm text-blue-800 space-y-1">
                                @php
                                    $top3Total = array_sum(array_slice($purokCounts, 0, 3));
                                    $top3Percentage = $total > 0 ? round(($top3Total / $total) * 100, 1) : 0;
                                @endphp
                                <li>Top 3 puroks account for {{ $top3Percentage }}% of all requests</li>
                                <li>Average requests per purok: {{ count($purokCounts) > 0 ? round($total / count($purokCounts), 1) : 0 }}</li>
                                <li>Most active purok: {{ !empty($purokCounts) ? array_key_first($purokCounts) : 'N/A' }}</li>
                            </ul>
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
// Purok Chart
const purokCtx = document.getElementById('purokChart').getContext('2d');
const purokChart = new Chart(purokCtx, {
    type: 'bar',
    data: {
        labels: @json(array_keys($purokCounts)),
        datasets: [{
            label: 'Document Requests',
            data: @json(array_values($purokCounts)),
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
                         const total = parseInt({{ array_sum($purokCounts) }});
                         const percentage = total > 0 ? ((parseInt(context.parsed.y) / total) * 100).toFixed(1) : 0;
                         return `${context.parsed.y} requests (${percentage}%)`;
                     }
                 }
             }
        }
    }
});

// Cluster Chart
const clusterCtx = document.getElementById('clusterChart').getContext('2d');
const clusterChart = new Chart(clusterCtx, {
    type: 'doughnut',
    data: {
        labels: @json(array_map(function($clusterId) { return 'Cluster ' . ((int)$clusterId + 1); }, array_keys($clusters))),
        datasets: [{
            data: @json(array_map('count', $clusters)),
            backgroundColor: [
                'rgba(139, 92, 246, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(239, 68, 68, 0.8)',
                'rgba(236, 72, 153, 0.8)'
            ],
            borderColor: [
                'rgba(139, 92, 246, 1)',
                'rgba(59, 130, 246, 1)',
                'rgba(16, 185, 129, 1)',
                'rgba(245, 158, 11, 1)',
                'rgba(239, 68, 68, 1)',
                'rgba(236, 72, 153, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: { size: 12 },
                    padding: 15
                }
            },
                         tooltip: {
                 callbacks: {
                     label: function(context) {
                         const total = context.dataset.data.reduce((a, b) => parseInt(a) + parseInt(b), 0);
                         const percentage = total > 0 ? ((parseInt(context.parsed) / total) * 100).toFixed(1) : 0;
                         return `${context.label}: ${context.parsed} requests (${percentage}%)`;
                     }
                 }
             }
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

function downloadClusterChart() {
    const canvas = document.getElementById('clusterChart');
    const link = document.createElement('a');
    link.download = 'document-cluster-distribution.png';
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
