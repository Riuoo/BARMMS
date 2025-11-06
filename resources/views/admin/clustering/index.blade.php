@extends('admin.main.layout')

@section('title', 'Resident Groups Analysis')

@section('content')
@php
    // Helper function to generate mini bar chart HTML
    function generateMiniBar($distribution, $colors = null) {
        if (empty($distribution)) return '';
        $total = array_sum($distribution);
        if ($total == 0) return '';
        
        // Default colors if not provided
        if (!$colors) {
            $colors = [
                'High' => '#10b981',
                'Medium' => '#f59e0b',
                'Low' => '#ef4444',
                'Full-time' => '#10b981',
                'Part-time' => '#3b82f6',
                'Unemployed' => '#ef4444',
                'Excellent' => '#10b981',
                'Good' => '#f59e0b',
                'Fair' => '#f97316',
                'Poor' => '#ef4444',
                'Critical' => '#dc2626'
            ];
        }
        
        $html = '<div class="flex flex-col gap-1">';
        foreach ($distribution as $label => $count) {
            $percentage = round(($count / $total) * 100, 1);
            $color = $colors[$label] ?? '#6b7280';
            $barWidth = min(100, $percentage);
            $html .= '<div class="flex items-center gap-2">';
            $html .= '<span class="text-xs text-gray-600 w-20 truncate">' . htmlspecialchars($label) . '</span>';
            $html .= '<div class="flex-1 bg-gray-200 rounded-full h-4 overflow-hidden">';
            $html .= '<div class="h-full rounded-full flex items-center justify-center" style="width: ' . $barWidth . '%; background-color: ' . $color . '; min-width: 2px;">';
            $html .= '<span class="text-xs text-white font-semibold px-1">' . ($barWidth > 10 ? $percentage . '%' : '') . '</span>';
            $html .= '</div></div>';
            $html .= '<span class="text-xs text-gray-700 w-12 text-right">' . $count . '</span>';
            $html .= '</div>';
        }
        $html .= '</div>';
        return $html;
    }
    
    // Helper function to format cluster traits
    function formatClusterTraits($cluster) {
        $traits = [];
        if (!empty($cluster['income_distribution'])) {
            $topIncome = array_search(max($cluster['income_distribution']), $cluster['income_distribution']);
            $incomeCount = $cluster['income_distribution'][$topIncome];
            $total = array_sum($cluster['income_distribution']);
            $percentage = $total > 0 ? round(($incomeCount / $total) * 100) : 0;
            $traits[] = ['label' => "Income: {$topIncome} ({$percentage}%)", 'type' => 'income'];
        }
        if (!empty($cluster['employment_distribution'])) {
            $topEmployment = array_search(max($cluster['employment_distribution']), $cluster['employment_distribution']);
            $employmentCount = $cluster['employment_distribution'][$topEmployment];
            $total = array_sum($cluster['employment_distribution']);
            $percentage = $total > 0 ? round(($employmentCount / $total) * 100) : 0;
            $traits[] = ['label' => "Employment: {$topEmployment} ({$percentage}%)", 'type' => 'employment'];
        }
        if (!empty($cluster['health_distribution'])) {
            $topHealth = array_search(max($cluster['health_distribution']), $cluster['health_distribution']);
            $healthCount = $cluster['health_distribution'][$topHealth];
            $total = array_sum($cluster['health_distribution']);
            $percentage = $total > 0 ? round(($healthCount / $total) * 100) : 0;
            $traits[] = ['label' => "Health: {$topHealth} ({$percentage}%)", 'type' => 'health'];
        }
        return $traits;
    }
    
    // Define color mappings
    $incomeColors = [
        'High' => '#10b981',
        'Medium' => '#f59e0b',
        'Low' => '#ef4444'
    ];
    $employmentColors = [
        'Full-time' => '#10b981',
        'Part-time' => '#3b82f6',
        'Unemployed' => '#ef4444'
    ];
    $healthColors = [
        'Excellent' => '#10b981',
        'Good' => '#f59e0b',
        'Fair' => '#f97316',
        'Poor' => '#ef4444',
        'Critical' => '#dc2626'
    ];
@endphp
<div class="max-w-7xl mx-auto pt-2">
    <!-- Analytics Dashboard Skeleton -->
    <div id="clusteringSkeleton">
        @include('components.loading.analytics-dashboard-skeleton')
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="clusteringContent" style="display: none;">
    <!-- Simple Header Section -->
    <div class="mb-6">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-3">Resident Groups Analysis</h1>
            <p class="text-gray-600 text-lg mb-4">Understanding our community by grouping residents with similar characteristics</p>
            
            <!-- Simple Controls -->
            <div class="flex flex-wrap justify-center gap-3 mb-4">
                @php
                    $baseUrl = route('admin.clustering');
                    $isHier = false;
                    $isAutoK = !empty($useOptimalK);
                    $kValue = $k ?? 3;
                @endphp
                
                <!-- Simple Mode Toggle -->
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700">
                    <i class="fas fa-layer-group mr-2"></i>
                    Clusters
                </span>
                
                <!-- Auto-K Toggle -->
                <form method="GET" action="{{ $baseUrl }}" class="inline-flex items-center gap-2">
                    @foreach(request()->except(['use_optimal_k', 'k']) as $key => $val)
                        @if(!is_null($val) && $val !== '')
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endif
                    @endforeach
                    <label class="inline-flex items-center text-sm">
                        <input type="checkbox" name="use_optimal_k" value="1" {{ $isAutoK ? 'checked' : '' }} onchange="this.form.submit()" class="mr-2">
                        <span>Auto-detect groups</span>
                    </label>
                </form>
                
                <!-- Manual K Selection (only when not using auto-K) -->
                @if(!$isAutoK)
                <form method="GET" action="{{ $baseUrl }}" class="inline-flex items-center gap-2">
                    @foreach(request()->except(['k']) as $key => $val)
                        @if(!is_null($val) && $val !== '')
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endif
                    @endforeach
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
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-700">
                    <i class="fas fa-tags mr-1"></i>
                    Overall Groups
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

    <!-- Removed hierarchical/purok mode badges. -->

    @if($sampleSize > 1000)
        <div class="mb-3 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded flex items-center">
            <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
            <span class="text-yellow-800 text-sm font-semibold">Warning: Large dataset detected ({{ $sampleSize }} residents). Clustering may take longer to process. Consider using filters or exporting data for offline analysis.</span>
        </div>
    @endif

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
        <!-- Performance Metrics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-3">
            <!-- Total Residents Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 text-sm md:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-500">Total Residents</p>
                        <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $sampleSize }}</p>
                    </div>
                </div>
            </div>
            <!-- Groups Found -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-layer-group text-purple-600 text-sm md:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-500">Groups Found</p>
                        <p class="text-lg md:text-2xl font-bold text-gray-900">{{ count($clusters) }}</p>
                    </div>
                </div>
            </div>
            <!-- Most Common Employment Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-briefcase text-green-600 text-sm md:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-500">Most Common Employment</p>
                        <p class="text-lg md:text-2xl font-bold text-gray-900">
                            {{ $mostCommonEmployment }}
                        </p>
                    </div>
                </div>
            </div>
            <!-- Most Common Health Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-heartbeat text-red-600 text-sm md:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-500">Most Common Health</p>
                        <p class="text-lg md:text-2xl font-bold text-gray-900">
                            {{ $mostCommonHealth }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Analysis Section -->
        <div class="space-y-6 mb-3">
            <!-- Cluster Distribution Chart -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-chart-donut text-purple-600 mr-2"></i>
                            Cluster Distribution
                            @if(!empty($useOptimalK))
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 ml-2">
                                    <i class="fas fa-magic mr-1"></i>Auto K={{ $k }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 ml-2">
                                    <i class="fas fa-cog mr-1"></i>Manual K={{ $k }}
                                </span>
                            @endif
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

            <!-- Toggle for Detailed Analysis -->
            <div class="flex justify-start mb-2">
                <button id="toggleDetailsBtn" onclick="toggleDetails()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <i class="fas fa-chevron-down mr-2"></i> Show Details
                </button>
            </div>

            <!-- Detailed Analysis Section -->
            <div id="detailedAnalysisSection" class="bg-white rounded-xl shadow-lg border border-gray-100 mb-3 hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-users text-green-600 mr-2"></i>
                            Resident List
                        </h3>
                        <div class="flex flex-wrap gap-2 items-center mb-2">
                            <input type="text" id="searchTable" placeholder="Search residents..." class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <label for="rowsPerPage" class="text-sm text-gray-700">Rows per page:</label>
                            <select id="rowsPerPage" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                                <option value="25">25 per page</option>
                                <option value="50">50 per page</option>
                                <option value="100">100 per page</option>
                            </select>
                        </div>
                        <div id="tableLoading" class="hidden flex items-center justify-center py-8"><span class="loader mr-2"></span> Loading...</div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 sticky-header-table" id="residentsTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resident Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Family Size</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Education</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Income</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employment</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Health Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Predicted Eligibility</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Predicted Risk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Predicted Program</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($residents as $resident)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                @if(!empty($resident->is_outlier))
                                                    <span class="inline-block w-2 h-2 mr-2 rounded-full bg-red-500" title="Outlier"></span>
                                                @endif
                                                {{ $resident->name ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $resident->age ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $resident->family_size ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $resident->education_level ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($resident->income_level ?? '') === 'High' ? 'bg-green-100 text-green-800' : (($resident->income_level ?? '') === 'Low' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $resident->income_level ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($resident->employment_status ?? '') === 'Full-time' ? 'bg-green-100 text-green-800' : (($resident->employment_status ?? '') === 'Unemployed' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                {{ $resident->employment_status ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($resident->health_status ?? '') === 'Excellent' ? 'bg-green-100 text-green-800' : (($resident->health_status ?? '') === 'Critical' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $resident->health_status ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php $elig = $resident->predicted_eligibility ?? null; @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $elig === 'Eligible' ? 'bg-green-100 text-green-800' : ($elig === 'Ineligible' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ $elig ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php $risk = $resident->predicted_risk ?? null; @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $risk === 'High' ? 'bg-red-100 text-red-800' : ($risk === 'Medium' ? 'bg-yellow-100 text-yellow-800' : ($risk === 'Low' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                                                {{ $risk ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $resident->predicted_program ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button data-action="view-resident" data-resident-id="{{ $resident->id ?? 0 }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                @if(count($residents) === 0)
                                    <tr>
                                        <td colspan="9" class="px-6 py-6 text-center text-gray-500">No residents to display</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        <div id="emptyState" class="hidden text-center text-gray-500 py-8">
                            <i class="fas fa-user-slash text-3xl mb-2"></i><br>No residents to display
                        </div>
                        <div class="flex flex-wrap justify-between items-center mt-4" id="paginationControls">
                            <div class="text-sm text-gray-700" id="paginationInfo"></div>
                            <div class="flex gap-2">
                                <button id="prevPage" class="px-3 py-1 border rounded bg-white hover:bg-gray-100">Prev</button>
                                <span id="currentPage" class="px-2"></span>
                                <button id="nextPage" class="px-3 py-1 border rounded bg-white hover:bg-gray-100">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Group Summary -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6">
                    <!-- Section Header and Description -->
                    <div class="mb-3">
                        <h2 class="text-2xl font-extrabold text-indigo-900 flex items-center gap-2 mb-2">
                            <i class="fas fa-info-circle text-indigo-600"></i>
                            Group Summary
                        </h2>
                        <p class="text-gray-700 text-base">
                            This section shows how residents are grouped based on similar characteristics. Each group highlights the most common traits of its members.
                        </p>
                    </div>
                    <!-- Simple Legend -->
                    <div class="mb-3 flex flex-wrap gap-4 items-center text-xs">
                        <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-800 font-semibold"><i class="fas fa-check mr-1"></i> Good</span>
                        <span class="inline-flex items-center px-2 py-1 rounded bg-yellow-100 text-yellow-800 font-semibold"><i class="fas fa-minus mr-1"></i> Average</span>
                        <span class="inline-flex items-center px-2 py-1 rounded bg-red-100 text-red-800 font-semibold"><i class="fas fa-exclamation-triangle mr-1"></i> Needs Attention</span>
                    </div>
                    
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($characteristics as $clusterId => $cluster)
                                @if($cluster['size'] > 0)
                                    @php
                                        // Get most common age and family size for this cluster
                                        $age = $cluster['most_common_age'] ?? $cluster['avg_age'];
                                        $familySize = $cluster['most_common_family_size'] ?? $cluster['avg_family_size'];
                                        
                                        // Get top traits for this cluster
                                        $traits = formatClusterTraits($cluster);
                                    @endphp
                                    <div class="cluster-card shadow-lg rounded-xl bg-white border border-purple-100 mb-3 p-4">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-base font-bold bg-purple-600 text-white shadow mr-2">
                                                <i class="fas fa-layer-group mr-1"></i> Cluster {{ $clusterId + 1 }}
                                            </span>
                                            <span class="bg-purple-200 text-purple-800 text-xs font-semibold px-2 py-1 rounded-full">{{ $cluster['size'] }} residents</span>
                                        </div>
                                        
                                        @if(isset($insightCounts[$clusterId]))
                                            @php $insights = $insightCounts[$clusterId]; @endphp
                                            <div class="mb-2 flex flex-wrap gap-2">
                                                @if(!empty($insights['program']))
                                                    @php $topProgram = array_search(max($insights['program']), $insights['program']); $programConfidence = round(($insights['program'][$topProgram] / $insights['total']) * 100); @endphp
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                        <i class="fas fa-magic mr-1"></i>
                                                        Model: {{ $topProgram }} ({{ $programConfidence }}%)
                                                    </span>
                                                @endif
                                                @if(!empty($insights['eligibility']))
                                                    @php $topEligibility = array_search(max($insights['eligibility']), $insights['eligibility']); $eligibilityConfidence = round(($insights['eligibility'][$topEligibility] / $insights['total']) * 100); @endphp
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check mr-1"></i>
                                                        Eligibility: {{ $topEligibility }} ({{ $eligibilityConfidence }}%)
                                                    </span>
                                                @endif
                                                @if(!empty($insights['risk']))
                                                    @php $topRisk = array_search(max($insights['risk']), $insights['risk']); $riskConfidence = round(($insights['risk'][$topRisk] / $insights['total']) * 100); @endphp
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i class="fas fa-heartbeat mr-1"></i>
                                                        Risk: {{ $topRisk }} ({{ $riskConfidence }}%)
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                        <div class="mb-2 text-xs text-gray-700"><i class="fas fa-tag mr-1 text-gray-500"></i>Label: <span class="font-semibold text-gray-900">{{ $cluster['label'] ?? 'N/A' }}</span></div>
                                        
                                        <!-- Compact View (Redesigned) -->
                                        <div class="space-y-3">
                                            <p class="text-sm text-gray-700">
                                                This group mostly includes <span class="font-semibold">{{ strtolower($cluster['most_common_employment'] ?? 'various') }}</span> households with
                                                <span class="font-semibold">{{ strtolower($cluster['most_common_income'] ?? 'various') }}</span> income, and overall health is
                                                <span class="font-semibold">{{ strtolower($cluster['most_common_health'] ?? 'mixed') }}</span>.
                                            </p>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div class="rounded-lg border border-gray-200 bg-white p-3">
                                                    <div class="text-xs text-gray-500">Residents</div>
                                                    <div class="text-lg font-bold text-purple-700 flex items-center gap-2">
                                                        <i class="fas fa-users"></i> {{ $cluster['size'] }}
                                                    </div>
                                                </div>
                                                <div class="rounded-lg border border-gray-200 bg-white p-3">
                                                    <div class="text-xs text-gray-500">Typical Age</div>
                                                    <div class="text-lg font-bold text-emerald-700 flex items-center gap-2">
                                                        <i class="fas fa-birthday-cake"></i> {{ is_numeric($age) ? number_format((float)$age, 1) : ($age ?? 'N/A') }}
                                                    </div>
                                                </div>
                                                <div class="rounded-lg border border-gray-200 bg-white p-3">
                                                    <div class="text-xs text-gray-500">Family Size</div>
                                                    <div class="text-lg font-bold text-indigo-700 flex items-center gap-2">
                                                        <i class="fas fa-people-roof"></i> {{ is_numeric($familySize) ? number_format((float)$familySize, 1) : ($familySize ?? 'N/A') }}
                                                    </div>
                                                </div>
                                                @php
                                                    $clusterHealth = $cluster['most_common_health'] ?? 'N/A';
                                                    $clusterHealthClass = 'bg-gray-100 text-gray-800';
                                                    if (in_array($clusterHealth, ['Excellent','Good'])) $clusterHealthClass = 'bg-green-100 text-green-800';
                                                    elseif ($clusterHealth === 'Fair') $clusterHealthClass = 'bg-yellow-100 text-yellow-800';
                                                    elseif (in_array($clusterHealth, ['Poor','Critical'])) $clusterHealthClass = 'bg-red-100 text-red-800';
                                                @endphp
                                                <div class="rounded-lg border border-gray-200 bg-white p-3">
                                                    <div class="text-xs text-gray-500">Health</div>
                                                    <div class="text-lg font-bold flex items-center gap-2 {{ $clusterHealthClass }} px-2 py-1 rounded">
                                                        <i class="fas fa-heartbeat"></i> {{ $clusterHealth }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex justify-between items-center mt-1">
                                                <button data-action="toggle-cluster-details" data-cluster-id="{{ $clusterId }}" class="text-purple-600 hover:text-purple-800 text-xs font-medium flex items-center gap-1">
                                                    <i class="fas fa-chevron-down" id="cluster-icon-{{ $clusterId }}"></i>
                                                    Show Details
                                                </button>
                                                @php
                                                    // Build a safe, serializable payload for the modal (exclude Eloquent models/objects)
                                                    $clusterForModal = [
                                                        'label' => $cluster['label'] ?? ('Cluster ' . ($clusterId + 1)),
                                                        'size' => $cluster['size'] ?? 0,
                                                        'avg_age' => $cluster['avg_age'] ?? null,
                                                        'avg_family_size' => $cluster['avg_family_size'] ?? null,
                                                        'income_distribution' => $cluster['income_distribution'] ?? [],
                                                        'employment_distribution' => $cluster['employment_distribution'] ?? [],
                                                        'health_distribution' => $cluster['health_distribution'] ?? [],
                                                        'education_distribution' => $cluster['education_distribution'] ?? [],
                                                        'most_common_purok' => $cluster['most_common_purok'] ?? 'N/A',
                                                        'most_common_employment' => $cluster['most_common_employment'] ?? 'N/A',
                                                        'most_common_health' => $cluster['most_common_health'] ?? 'N/A',
                                                    ];
                                                    $modalPayload = [
                                                        'cluster' => $clusterForModal,
                                                        'traits' => $traits,
                                                        'insights' => isset($insights) ? $insights : null,
                                                    ];
                                                @endphp
                                                <button data-action="show-cluster-modal" data-cluster-id="{{ $clusterId }}" data-payload='@json($modalPayload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'
                                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium flex items-center gap-1">
                                                    <i class="fas fa-external-link-alt"></i>
                                                    Full Analysis
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Expanded View (Hidden by default) -->
                                        <div class="expanded-view hidden" id="expanded-cluster-{{ $clusterId }}">
                                            <div class="border-t pt-3 mt-3">
                                                <!-- Top Traits -->
                                                <div class="mb-3">
                                                    <h6 class="text-xs font-semibold text-gray-700 mb-2">Top Traits:</h6>
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($traits as $trait)
                                                            <span class="inline-block px-2 py-1 rounded-full bg-red-100 text-red-800 text-xs font-bold">{{ $trait['label'] }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                
                                                <!-- Mini Bar Charts -->
                                                <div class="space-y-2">
                                                    @if(!empty($cluster['income_distribution']))
                                                        <div class="text-xs">
                                                            <span class="font-semibold text-gray-700">Income:</span>
                                                            {!! generateMiniBar($cluster['income_distribution'], $incomeColors) !!}
                                                        </div>
                                                    @endif
                                                    @if(!empty($cluster['employment_distribution']))
                                                        <div class="text-xs">
                                                            <span class="font-semibold text-gray-700">Employment:</span>
                                                            {!! generateMiniBar($cluster['employment_distribution'], $employmentColors) !!}
                                                        </div>
                                                    @endif
                                                    @if(!empty($cluster['health_distribution']))
                                                        <div class="text-xs">
                                                            <span class="font-semibold text-gray-700">Health:</span>
                                                            {!! generateMiniBar($cluster['health_distribution'], $healthColors) !!}
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                @if(!empty($cluster['health_percent']['Critical'] ?? null) && ($cluster['health_percent']['Critical'] ?? 0) > 40)
                                                    <span class="inline-block px-2 py-0.5 rounded-full bg-red-600 text-white text-xs font-bold mt-2"><i class="fas fa-exclamation-triangle mr-1"></i>High Health Risk!</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                </div>
            </div>
        </div>

        <!-- Charts and Graphs -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-3">
            <div class="p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                        Charts and Graphs
                    </h3>
                    <div class="flex space-x-2">
                        <button onclick="downloadAllCharts()" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <i class="fas fa-download mr-1"></i>
                            Download All
                        </button>
                    </div>
                </div>
                
                <!-- Legend -->
                <div class="mb-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-800">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle mt-0.5"></i>
                            <div>
                                <p class="font-medium">Legend</p>
                                <ul class="list-disc ml-5 mt-1 space-y-1">
                                    <li><span class="font-semibold">Clusters</span>: data-driven groups computed by Python from resident features (age, family size, education, income, employment, health).</li>
                                    <li><span class="font-semibold">k</span>: number of clusters used. <span class="font-semibold">Silhouette</span> indicates cluster separation quality (higher is better).</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Income Level Distribution -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">
                            <i class="fas fa-coins text-green-600 mr-2"></i>
                            Income Levels
                        </h4>
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="incomeChart"></canvas>
                        </div>
                    </div>

                    <!-- Employment Status Distribution -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">
                            <i class="fas fa-briefcase text-blue-600 mr-2"></i>
                            Employment Status
                        </h4>
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="employmentChart"></canvas>
                        </div>
                    </div>

                    <!-- Age Distribution -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">
                            <i class="fas fa-birthday-cake text-purple-600 mr-2"></i>
                            Age Groups
                        </h4>
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="ageChart"></canvas>
                        </div>
                    </div>

                    <!-- Health Status Distribution -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">
                            <i class="fas fa-heartbeat text-red-600 mr-2"></i>
                            Health Status
                        </h4>
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="healthChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Group Comparison -->
                <div class="mt-8">
                    <h4 class="text-md font-semibold text-gray-900 mb-3">
                        <i class="fas fa-chart-line text-indigo-600 mr-2"></i>
                        Group Comparison
                    </h4>
                    <div class="chart-container" style="position: relative; height: 400px;">
                        <canvas id="comparativeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        // Hide consolidated skeleton
        const skeleton = document.getElementById('clusteringSkeleton');
        if (skeleton) skeleton.style.display = 'none';

        // Show content
        const content = document.getElementById('clusteringContent');
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Clustering Config (colors, thresholds) -->
<script>
window.clusteringConfig = {
    colors: {
        clusters: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#F97316', '#EC4899', '#06B6D4'],
        employment: @json($employmentColors) ?? ['#EF4444', '#F59E0B', '#8B5CF6', '#10B981'],
        health: @json($healthColors) ?? ['#EF4444', '#F97316', '#F59E0B', '#3B82F6', '#10B981']
    },
    thresholds: {
        largeDataset: 500
    }
};
</script>

<!-- Clustering JavaScript -->
<script src="{{ asset('js/clustering.js') }}"></script>

<!-- Clustering Charts JavaScript -->
<script src="{{ asset('js/clustering-charts.js') }}"></script>

<!-- Pass PHP data to JavaScript safely -->
<script type="application/json" id="clustering-data">
{!! json_encode([
    'isHier' => false,
    'isAutoK' => $useOptimalK ?? false,
    'k' => $k ?? 3,
    'characteristics' => $characteristics ?? [],
    'silhouette' => $silhouette ?? null,
    'sampleSize' => $sampleSize ?? 0,
    'mostCommonEmployment' => $mostCommonEmployment ?? 'N/A',
    'mostCommonHealth' => $mostCommonHealth ?? 'N/A'
], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}
</script>
<script>
// Initialize global data from embedded JSON
(function(){
    try {
        var el = document.getElementById('clustering-data');
        if (el) { 
            window.clusteringData = JSON.parse(el.textContent || '{}');
            console.log('Clustering data loaded:', window.clusteringData);
        }
    } catch (e) { 
        console.error('Error parsing clustering data:', e);
        window.clusteringData = {};
    }
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait for clustering data to be loaded
    setTimeout(() => {
        // Initialize main cluster chart
        if (window.clusteringData && window.clusteringData.characteristics) {
            initializeClusterChart();
        }
        
        // Initialize analytics charts
        initializeAnalyticsCharts();
        
        // Initialize table features
        initializeTableFeatures();
        
        // Collapsible cluster groups: collapse all except first 2
        const clusterGroups = document.querySelectorAll('.cluster-card');
        clusterGroups.forEach((group, idx) => {
            const expandedView = group.querySelector('.expanded-view');
            const chevron = group.querySelector('.cluster-card i.fas');
            if (idx > 1) {
                expandedView.classList.add('hidden');
                chevron.classList.add('rotate-180');
            }
        });
        
        // Cluster search/filter
        const clusterSearch = document.getElementById('searchTable');
        if (clusterSearch) {
            clusterSearch.addEventListener('input', function() {
                const val = this.value.trim().toLowerCase();
                document.querySelectorAll('.cluster-card').forEach(group => {
                    const label = group.querySelector('.cluster-card .font-semibold').textContent.toLowerCase();
                    group.style.display = val === '' || label.includes(val) ? '' : 'none';
                });
            });
        }
        
        // Initialize details collapsed
        const details = document.getElementById('detailedAnalysisSection');
        const toggleBtn = document.getElementById('toggleDetailsBtn');
        if (details && toggleBtn) {
            details.classList.add('hidden');
            const icon = toggleBtn.querySelector('i');
            if (icon) { icon.classList.remove('fa-chevron-up'); icon.classList.add('fa-chevron-down'); }
            toggleBtn.setAttribute('data-state', 'hidden');
            toggleBtn.querySelector('span.btn-text')?.remove();
            const span = document.createElement('span');
            span.className = 'btn-text';
            span.textContent = ' Show Details';
            toggleBtn.appendChild(span);
        }
    }, 100);
});


</script>

<style>
.cluster-card {
    transition: all 0.3s ease;
}

.cluster-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.sticky-header-table thead th {
    position: sticky;
    top: 0;
    background: #f9fafb;
    z-index: 2;
}

#tableLoading .loader {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (max-width: 900px) {
    .sticky-header-table, .sticky-header-table thead, .sticky-header-table tbody, .sticky-header-table th, .sticky-header-table td, .sticky-header-table tr {
        display: block;
    }
    .sticky-header-table thead {
        float: left;
    }
    .sticky-header-table tbody {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .sticky-header-table th, .sticky-header-table td {
        width: 100%;
        box-sizing: border-box;
    }
    #paginationControls {
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-start;
    }
    .flex.flex-wrap.gap-2.items-center.mb-2 {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }
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

.cluster-card {
    transition: all 0.3s ease;
    background: #f8fafc;
    border-radius: 0.75rem;
    border: 1px solid #dbeafe;
}
.cluster-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59,130,246,0.08);
}
</style>

<!-- Modal for Detailed Analysis -->
<div id="analysisModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Detailed Analysis</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="modalContent" class="text-sm">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>


// Close modal when clicking outside
document.getElementById('analysisModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>

@endsection 