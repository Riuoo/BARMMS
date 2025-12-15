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
        if (!empty($cluster['pwd_distribution'])) {
            $topPWD = array_search(max($cluster['pwd_distribution']), $cluster['pwd_distribution']);
            $pwdCount = $cluster['pwd_distribution'][$topPWD];
            $total = array_sum($cluster['pwd_distribution']);
            $percentage = $total > 0 ? round(($pwdCount / $total) * 100) : 0;
            $traits[] = ['label' => "PWD: {$topPWD} ({$percentage}%)", 'type' => 'pwd'];
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
                <p class="text-gray-600 text-lg mb-4">Data-driven grouping of residents based on similar demographic and socioeconomic characteristics</p>
            
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
                    <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-red-800 mb-2">Analysis Error</h3>
                    <div class="mt-2 text-sm text-red-700 mb-3">
                        {{ $error }}
                    </div>
                    @php
                        $errorLower = strtolower($error ?? '');
                        $suggestions = [];
                        
                        if (strpos($errorLower, 'insufficient') !== false || strpos($errorLower, 'not enough') !== false) {
                            $suggestions[] = 'Try reducing the number of clusters (k) to 2 or 3';
                            $suggestions[] = 'Check if you have enough residents in your database (minimum 3 required)';
                            $suggestions[] = 'Remove any filters that might be limiting the dataset';
                        }
                        if (strpos($errorLower, 'python') !== false || strpos($errorLower, 'service') !== false) {
                            $suggestions[] = 'Ensure the Python analytics service is running';
                            $suggestions[] = 'Check the service configuration in your .env file';
                            $suggestions[] = 'Try refreshing the page and running the analysis again';
                        }
                        if (strpos($errorLower, 'timeout') !== false || strpos($errorLower, 'time') !== false) {
                            $suggestions[] = 'Try reducing the number of clusters or sample size';
                            $suggestions[] = 'Apply filters to reduce the dataset size';
                            $suggestions[] = 'Contact support if the issue persists';
                        }
                        if (strpos($errorLower, 'memory') !== false) {
                            $suggestions[] = 'Reduce the number of clusters or apply filters';
                            $suggestions[] = 'Try processing a smaller subset of residents';
                        }
                        if (empty($suggestions)) {
                            $suggestions[] = 'Try refreshing the page and running the analysis again';
                            $suggestions[] = 'Check if all required data fields are filled';
                            $suggestions[] = 'Contact support if the problem continues';
                        }
                    @endphp
                    @if(!empty($suggestions))
                    <div class="mt-3 pt-3 border-t border-red-200">
                        <p class="text-xs font-semibold text-red-800 mb-2">
                            <i class="fas fa-lightbulb mr-1"></i> Suggestions:
                        </p>
                        <ul class="list-disc list-inside space-y-1 text-xs text-red-700">
                            @foreach($suggestions as $suggestion)
                                <li>{{ $suggestion }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="mt-3">
                        <button onclick="window.location.reload()" class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-xs font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-sync-alt mr-2"></i> Retry Analysis
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Performance Metrics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
            <!-- Total Residents Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs font-medium text-gray-500">Total Residents</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $sampleSize }}</p>
                    </div>
                </div>
            </div>
            <!-- Groups Found -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-layer-group text-purple-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs font-medium text-gray-500">Groups Identified</p>
                        <p class="text-2xl font-bold text-gray-900">{{ count($clusters) }}</p>
                        @if(isset($silhouette))
                        <p class="text-xs text-gray-500 mt-1">Quality: {{ number_format($silhouette, 2) }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Most Common Employment Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-briefcase text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs font-medium text-gray-500">Most Common Employment</p>
                        <p class="text-lg font-bold text-gray-900 truncate">
                            {{ $mostCommonEmployment }}
                        </p>
                    </div>
                </div>
            </div>
            <!-- Blotter Reports Summary Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-file-alt text-red-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs font-medium text-gray-500">With Blotter Reports</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $globalBlotterStats['residents_with_reports'] ?? 0 }}
                        </p>
                        @php
                            $blotterPct = $sampleSize > 0 ? round((($globalBlotterStats['residents_with_reports'] ?? 0) / $sampleSize) * 100, 1) : 0;
                        @endphp
                        <p class="text-xs text-gray-500 mt-1">{{ $blotterPct }}% of residents</p>
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

            <!-- Button to Open Detailed Analysis Modal -->
            <div class="flex justify-start mb-2">
                <button id="showDetailsModalBtn" onclick="showResidentDetailsModal()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <i class="fas fa-table mr-2"></i> Show Details
                </button>
            </div>

            <!-- Detailed Analysis Section (Hidden, content moved to modal) -->
            <div id="detailedAnalysisSection" class="hidden">
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PWD</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blotter Reports</th>
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
                                                {{ $resident->full_name }}
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
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($resident->is_pwd ?? false) ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                {{ ($resident->is_pwd ?? false) ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $resident->blotter_count ?? 0 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @php
                                                $residentId = 0;
                                                if (is_object($resident)) {
                                                    // Convert to array to safely access properties
                                                    $residentArray = (array)$resident;
                                                    $residentId = $residentArray['id'] ?? $residentArray['resident_id'] ?? 0;
                                                } elseif (is_array($resident)) {
                                                    $residentId = $resident['id'] ?? $resident['resident_id'] ?? 0;
                                                }
                                            @endphp
                                            <button data-action="view-resident" data-resident-id="{{ $residentId }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                @if(count($residents) === 0)
                                    <tr>
                                        <td colspan="12" class="px-6 py-6 text-center text-gray-500">No residents to display</td>
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

            <!-- Understanding Clustering -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                        Understanding Resident Groups
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">How It Works</h4>
                            <p class="text-xs text-blue-800 mb-2">
                                Clustering groups residents based on similar characteristics like age, income, employment, and family size. This helps identify patterns and common traits within the community.
                            </p>
                            <p class="text-xs text-blue-800">
                                <strong>Quality Score:</strong> The silhouette score (shown above) indicates how well-separated the groups are. Higher scores mean clearer distinctions between groups.
                            </p>
                        </div>
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                            <h4 class="text-sm font-semibold text-green-900 mb-2">Interpreting Results</h4>
                            <ul class="text-xs text-green-800 space-y-1">
                                <li><strong>Group Size:</strong> Shows how many residents share similar characteristics.</li>
                                <li><strong>Common Traits:</strong> Highlights the most frequent income, employment, and other attributes in each group.</li>
                                <li><strong>Additional Data:</strong> Includes patterns from blotter reports, document requests, and medical records when available.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                        <p class="text-xs text-yellow-800">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <strong>Important:</strong> These groups are based on data patterns and should be used as a tool for understanding community structure. All groups are equally valid - no group is inherently "better" or "worse" than others.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Group Summary -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6">
                    <!-- Section Header and Description -->
                    <div class="mb-3">
                        <h2 class="text-2xl font-extrabold text-indigo-900 flex items-center gap-2 mb-2">
                            <i class="fas fa-layer-group text-indigo-600"></i>
                            Group Summary
                        </h2>
                        <p class="text-gray-700 text-base">
                            This section shows how residents are grouped based on similar characteristics. Each group highlights the most common traits of its members.
                        </p>
                    </div>
                    
                    <!-- Filter and Search Section -->
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <!-- Search by Label -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    <i class="fas fa-search mr-1"></i> Search Clusters
                                </label>
                                <input type="text" id="clusterSearch" placeholder="Search clusters..." 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            
                            <!-- Filter by Size -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    <i class="fas fa-users mr-1"></i> Filter by Size
                                </label>
                                <select id="sizeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                    <option value="all">All Sizes</option>
                                    <option value="small">Small (1-10)</option>
                                    <option value="medium">Medium (11-30)</option>
                                    <option value="large">Large (31+)</option>
                                </select>
                            </div>
                            
                            <!-- Filter by Employment -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    <i class="fas fa-filter mr-1"></i> Filter by Employment
                                </label>
                                <select id="employmentFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                    <option value="all">All Types</option>
                                    <option value="full-time">Full-time</option>
                                    <option value="part-time">Part-time</option>
                                    <option value="unemployed">Unemployed</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <button id="clearFilters" class="text-xs text-gray-600 hover:text-gray-800 underline">
                                <i class="fas fa-times mr-1"></i> Clear Filters
                            </button>
                            <span id="filterResults" class="text-xs text-gray-500"></span>
                        </div>
                    </div>
                    
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="clustersGrid">
                            @foreach($characteristics as $clusterId => $cluster)
                                @if($cluster['size'] > 0)
                                    @php
                                        // Get most common age and family size for this cluster
                                        $age = $cluster['most_common_age'] ?? $cluster['avg_age'];
                                        $familySize = $cluster['most_common_family_size'] ?? $cluster['avg_family_size'];
                                        
                                        // Get top traits for this cluster
                                        $traits = formatClusterTraits($cluster);
                                        
                                        // Merge data from additional clustering sources
                                        $blotterCluster = isset($blotterClustering['characteristics'][$clusterId]) ? $blotterClustering['characteristics'][$clusterId] : null;
                                        $documentCluster = isset($documentClustering['characteristics'][$clusterId]) ? $documentClustering['characteristics'][$clusterId] : null;
                                        $medicalCluster = isset($medicalClustering['characteristics'][$clusterId]) ? $medicalClustering['characteristics'][$clusterId] : null;
                                    @endphp
                                    <div class="cluster-card shadow-lg rounded-xl bg-white border border-purple-100 mb-3 p-4">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-base font-bold bg-purple-600 text-white shadow mr-2">
                                                <i class="fas fa-layer-group mr-1"></i> Cluster {{ $clusterId + 1 }}
                                            </span>
                                            <span class="bg-purple-200 text-purple-800 text-xs font-semibold px-2 py-1 rounded-full">{{ $cluster['size'] }} residents</span>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="text-xs text-gray-600 mb-1">
                                                <i class="fas fa-tag mr-1 text-gray-500"></i>
                                                <span class="font-medium">Group Label:</span>
                                            </div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $cluster['label'] ?? 'N/A' }}</div>
                                        </div>
                                        
                                        <!-- Additional Data Sources Indicators -->
                                        @if($blotterCluster || $documentCluster || $medicalCluster)
                                        <div class="mb-3 flex flex-wrap gap-2">
                                            <span class="text-xs text-gray-600 font-medium">Additional Data Sources:</span>
                                            @if($blotterCluster)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-file-alt mr-1"></i> Blotter
                                            </span>
                                            @endif
                                            @if($documentCluster)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-file-signature mr-1"></i> Documents
                                            </span>
                                            @endif
                                            @if($medicalCluster)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-heartbeat mr-1"></i> Medical
                                            </span>
                                            @endif
                                        </div>
                                        @endif
                                        
                                        <!-- Compact View (Redesigned) -->
                                        <div class="space-y-3">
                                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                                <p class="text-sm text-gray-700">
                                                    This group primarily consists of <span class="font-semibold text-gray-900">{{ strtolower($cluster['most_common_employment'] ?? 'various') }}</span> households with
                                                    <span class="font-semibold text-gray-900">{{ strtolower($cluster['most_common_income'] ?? 'various') }}</span> income levels.
                                                </p>
                                            </div>
                                            <div class="grid grid-cols-3 gap-3">
                                                <div class="rounded-lg border border-gray-200 bg-gradient-to-br from-purple-50 to-purple-100 p-3">
                                                    <div class="text-xs text-gray-600 mb-1">Group Size</div>
                                                    <div class="text-xl font-bold text-purple-700 flex items-center gap-2">
                                                        <i class="fas fa-users text-sm"></i> {{ $cluster['size'] }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        @php
                                                            $sizePct = $sampleSize > 0 ? round(($cluster['size'] / $sampleSize) * 100, 1) : 0;
                                                        @endphp
                                                        {{ $sizePct }}% of total
                                                    </div>
                                                </div>
                                                <div class="rounded-lg border border-gray-200 bg-gradient-to-br from-emerald-50 to-emerald-100 p-3">
                                                    <div class="text-xs text-gray-600 mb-1">Average Age</div>
                                                    <div class="text-xl font-bold text-emerald-700 flex items-center gap-2">
                                                        <i class="fas fa-birthday-cake text-sm"></i> {{ is_numeric($age) ? round((float)$age) : ($age ?? 'N/A') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">years old</div>
                                                </div>
                                                <div class="rounded-lg border border-gray-200 bg-gradient-to-br from-indigo-50 to-indigo-100 p-3">
                                                    <div class="text-xs text-gray-600 mb-1">Family Size</div>
                                                    <div class="text-xl font-bold text-indigo-700 flex items-center gap-2">
                                                        <i class="fas fa-people-roof text-sm"></i> {{ is_numeric($familySize) ? round((float)$familySize) : ($familySize ?? 'N/A') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">members</div>
                                                </div>
                                            </div>
                                            @php
                                                // Build a safe, serializable payload for the modal (exclude Eloquent models/objects)
                                                $clusterForModal = [
                                                    'label' => $cluster['label'] ?? ('Cluster ' . ($clusterId + 1)),
                                                    'size' => $cluster['size'] ?? 0,
                                                    'avg_age' => $cluster['avg_age'] ?? null,
                                                    'avg_family_size' => $cluster['avg_family_size'] ?? null,
                                                    'income_distribution' => $cluster['income_distribution'] ?? [],
                                                    'employment_distribution' => $cluster['employment_distribution'] ?? [],
                                                    'pwd_distribution' => $cluster['pwd_distribution'] ?? [],
                                                    'education_distribution' => $cluster['education_distribution'] ?? [],
                                                    'most_common_purok' => $cluster['most_common_purok'] ?? 'N/A',
                                                    'most_common_employment' => $cluster['most_common_employment'] ?? 'N/A',
                                                ];
                                                $modalPayload = [
                                                    'cluster' => $clusterForModal,
                                                    'traits' => $traits,
                                                    'insights' => isset($insights) ? $insights : null,
                                                ];
                                            @endphp
                                            <div class="flex justify-between items-center mt-1">
                                                <button data-action="show-cluster-details-modal" data-cluster-id="{{ $clusterId }}" class="text-purple-600 hover:text-purple-800 text-xs font-medium flex items-center gap-1">
                                                    <i class="fas fa-external-link-alt mr-1"></i>
                                                    Show Details
                                                </button>
                                                <div class="flex gap-2">
                                                    <button onclick="exportCluster({{ $clusterId }})" class="text-green-600 hover:text-green-800 text-xs font-medium flex items-center gap-1" title="Export cluster data">
                                                        <i class="fas fa-download"></i>
                                                        Export
                                                    </button>
                                                    <button data-action="show-cluster-modal" data-cluster-id="{{ $clusterId }}" data-payload='@json($modalPayload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'
                                                        class="text-blue-600 hover:text-blue-800 text-xs font-medium flex items-center gap-1">
                                                        <i class="fas fa-external-link-alt"></i>
                                                        Full Analysis
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Expanded View (Hidden by default) -->
                                        <div class="expanded-view hidden" id="expanded-cluster-{{ $clusterId }}">
                                            <div class="border-t pt-3 mt-3">
                                                <!-- Top Traits -->
                                                <div class="mb-3">
                                                    <h6 class="text-xs font-semibold text-gray-700 mb-2">Key Characteristics:</h6>
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($traits as $trait)
                                                            <span class="inline-block px-3 py-1 rounded-full bg-gray-100 text-gray-800 text-xs font-medium border border-gray-300">{{ $trait['label'] }}</span>
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
                                                    @if(!empty($cluster['pwd_distribution']))
                                                        <div class="text-xs">
                                                            <span class="font-semibold text-gray-700">PWD:</span>
                                                            {!! generateMiniBar($cluster['pwd_distribution'], ['Yes' => '#ef4444', 'No' => '#10b981']) !!}
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <!-- Additional Clustering Data -->
                                                @if($blotterCluster || $documentCluster || $medicalCluster)
                                                <div class="mt-4 pt-3 border-t border-gray-200">
                                                    <h6 class="text-xs font-semibold text-gray-700 mb-2">Additional Data Sources:</h6>
                                                    
                                                    <!-- Blotter Data -->
                                                    @if($blotterCluster)
                                                    <div class="mb-3 p-2 bg-red-50 rounded border border-red-100">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <i class="fas fa-file-alt text-red-600"></i>
                                                            <span class="text-xs font-semibold text-red-800">Blotter Reports Clustering</span>
                                                            @if(isset($blotterClustering['result']['silhouette']))
                                                            <span class="text-xs text-gray-600">(Quality: {{ number_format($blotterClustering['result']['silhouette'], 2) }})</span>
                                                            @endif
                                                        </div>
                                                        <p class="text-xs text-gray-700 mb-2">Residents with similar blotter report patterns: {{ $blotterCluster['size'] ?? 0 }}</p>
                                                        <div class="space-y-2 mt-2">
                                                            @if(!empty($blotterCluster['income_distribution']))
                                                            <div class="text-xs">
                                                                <span class="font-semibold text-gray-700">Income Distribution:</span>
                                                                {!! generateMiniBar($blotterCluster['income_distribution'], $incomeColors) !!}
                                                            </div>
                                                            @endif
                                                            @if(!empty($blotterCluster['employment_distribution']))
                                                            <div class="text-xs">
                                                                <span class="font-semibold text-gray-700">Employment Distribution:</span>
                                                                {!! generateMiniBar($blotterCluster['employment_distribution'], $employmentColors) !!}
                                                            </div>
                                                            @endif
                                                            @if(!empty($blotterCluster['pwd_distribution']))
                                                            <div class="text-xs">
                                                                <span class="font-semibold text-gray-700">PWD Distribution:</span>
                                                                {!! generateMiniBar($blotterCluster['pwd_distribution'], ['Yes' => '#ef4444', 'No' => '#10b981']) !!}
                                                            </div>
                                                            @endif
                                                            @if(!empty($blotterCluster['education_distribution']))
                                                            <div class="text-xs">
                                                                <span class="font-semibold text-gray-700">Education Distribution:</span>
                                                                {!! generateMiniBar($blotterCluster['education_distribution']) !!}
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @endif
                                                    
                                                    <!-- Document Data -->
                                                    @if($documentCluster)
                                                    <div class="mb-3 p-2 bg-blue-50 rounded border border-blue-100">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <i class="fas fa-file-signature text-blue-600"></i>
                                                            <span class="text-xs font-semibold text-blue-800">Document Requests Clustering</span>
                                                            @if(isset($documentClustering['result']['silhouette']))
                                                            <span class="text-xs text-gray-600">(Quality: {{ number_format($documentClustering['result']['silhouette'], 2) }})</span>
                                                            @endif
                                                        </div>
                                                        <p class="text-xs text-gray-700 mb-2">Residents with similar document request patterns: {{ $documentCluster['size'] ?? 0 }}</p>
                                                        <div class="space-y-2 mt-2">
                                                            @if(!empty($documentCluster['income_distribution']))
                                                            <div class="text-xs">
                                                                <span class="font-semibold text-gray-700">Income Distribution:</span>
                                                                {!! generateMiniBar($documentCluster['income_distribution'], $incomeColors) !!}
                                                            </div>
                                                            @endif
                                                            @if(!empty($documentCluster['employment_distribution']))
                                                            <div class="text-xs">
                                                                <span class="font-semibold text-gray-700">Employment Distribution:</span>
                                                                {!! generateMiniBar($documentCluster['employment_distribution'], $employmentColors) !!}
                                                            </div>
                                                            @endif
                                                            @if(!empty($documentCluster['pwd_distribution']))
                                                            <div class="text-xs">
                                                                <span class="font-semibold text-gray-700">PWD Distribution:</span>
                                                                {!! generateMiniBar($documentCluster['pwd_distribution'], ['Yes' => '#ef4444', 'No' => '#10b981']) !!}
                                                            </div>
                                                            @endif
                                                            @if(!empty($documentCluster['education_distribution']))
                                                            <div class="text-xs">
                                                                <span class="font-semibold text-gray-700">Education Distribution:</span>
                                                                {!! generateMiniBar($documentCluster['education_distribution']) !!}
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @endif
                                                    
                                                    <!-- Medical Data -->
                                                    @if($medicalCluster)
                                                    <div class="mb-3 p-2 bg-green-50 rounded border border-green-100">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <i class="fas fa-heartbeat text-green-600"></i>
                                                            <span class="text-xs font-semibold text-green-800">Medical Records Clustering</span>
                                                            @if(isset($medicalClustering['result']['silhouette']))
                                                            <span class="text-xs text-gray-600">(Quality: {{ number_format($medicalClustering['result']['silhouette'], 2) }})</span>
                                                            @endif
                                                        </div>
                                                        <p class="text-xs text-gray-700 mb-2">Residents with similar medical consultation patterns: {{ $medicalCluster['size'] ?? 0 }}</p>
                                                        <div class="space-y-2 mt-2">
                                                            @if(!empty($medicalCluster['income_distribution']))
                                                            <div class="text-xs">
                                                                <span class="font-semibold text-gray-700">Income Distribution:</span>
                                                                {!! generateMiniBar($medicalCluster['income_distribution'], $incomeColors) !!}
                                                            </div>
                                                            @endif
                                                            @if(!empty($medicalCluster['employment_distribution']))
                                                            <div class="text-xs">
                                                                <span class="font-semibold text-gray-700">Employment Distribution:</span>
                                                                {!! generateMiniBar($medicalCluster['employment_distribution'], $employmentColors) !!}
                                                            </div>
                                                            @endif
                                                            @if(!empty($medicalCluster['pwd_distribution']))
                                                            <div class="text-xs">
                                                                <span class="font-semibold text-gray-700">PWD Distribution:</span>
                                                                {!! generateMiniBar($medicalCluster['pwd_distribution'], ['Yes' => '#ef4444', 'No' => '#10b981']) !!}
                                                            </div>
                                                            @endif
                                                            @if(!empty($medicalCluster['education_distribution']))
                                                            <div class="text-xs">
                                                                <span class="font-semibold text-gray-700">Education Distribution:</span>
                                                                {!! generateMiniBar($medicalCluster['education_distribution']) !!}
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
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
                                    <li><span class="font-semibold">Clusters</span>: data-driven groups computed by Python from resident features (age, family size, education, income, employment).</li>
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

                    <!-- PWD Distribution -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">
                            <i class="fas fa-wheelchair text-red-600 mr-2"></i>
                            Person with Disability (PWD)
                        </h4>
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="pwdChart"></canvas>
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
        
        // Ensure details section is hidden (content will be shown in modal)
        const details = document.getElementById('detailedAnalysisSection');
        if (details) {
            details.classList.add('hidden');
        }
        
        // Initialize cluster filtering
        initializeClusterFiltering();
    }, 100);
});

// Cluster Filtering Functionality
function initializeClusterFiltering() {
    const clusterSearch = document.getElementById('clusterSearch');
    const sizeFilter = document.getElementById('sizeFilter');
    const employmentFilter = document.getElementById('employmentFilter');
    const clearFilters = document.getElementById('clearFilters');
    const filterResults = document.getElementById('filterResults');
    const clustersGrid = document.getElementById('clustersGrid');
    
    if (!clustersGrid) return;
    
    const clusterCards = Array.from(clustersGrid.querySelectorAll('.cluster-card'));
    
    function applyFilters() {
        const searchTerm = (clusterSearch?.value || '').toLowerCase().trim();
        const sizeValue = sizeFilter?.value || 'all';
        const employmentValue = employmentFilter?.value || 'all';
        
        let visibleCount = 0;
        let totalCount = clusterCards.length;
        
        clusterCards.forEach(card => {
            let visible = true;
            
            // Search filter
            if (searchTerm) {
                const cardText = card.textContent.toLowerCase();
                if (!cardText.includes(searchTerm)) {
                    visible = false;
                }
            }
            
            // Size filter
            if (visible && sizeValue !== 'all') {
                const sizeText = card.querySelector('.text-lg.font-bold.text-purple-700')?.textContent || '';
                const size = parseInt(sizeText) || 0;
                if (sizeValue === 'small' && (size < 1 || size > 10)) visible = false;
                if (sizeValue === 'medium' && (size < 11 || size > 30)) visible = false;
                if (sizeValue === 'large' && size < 31) visible = false;
            }
            
            // Employment filter
            if (visible && employmentValue !== 'all') {
                const cardText = card.textContent.toLowerCase();
                const employmentMatch = cardText.match(/(full-time|part-time|unemployed)/i);
                if (employmentMatch) {
                    const employment = employmentMatch[1].toLowerCase();
                    if (employmentValue === 'full-time' && employment !== 'full-time') visible = false;
                    if (employmentValue === 'part-time' && employment !== 'part-time') visible = false;
                    if (employmentValue === 'unemployed' && employment !== 'unemployed') visible = false;
                } else if (employmentValue !== 'all') {
                    // If no employment data found and filter is set, hide
                    visible = false;
                }
            }
            
            card.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });
        
        // Update results counter
        if (filterResults) {
            if (visibleCount === totalCount) {
                filterResults.textContent = '';
            } else {
                filterResults.textContent = `Showing ${visibleCount} of ${totalCount} clusters`;
            }
        }
    }
    
    // Event listeners
    if (clusterSearch) {
        clusterSearch.addEventListener('input', applyFilters);
    }
    if (sizeFilter) {
        sizeFilter.addEventListener('change', applyFilters);
    }
    if (employmentFilter) {
        employmentFilter.addEventListener('change', applyFilters);
    }
    if (clearFilters) {
        clearFilters.addEventListener('click', () => {
            if (clusterSearch) clusterSearch.value = '';
            if (sizeFilter) sizeFilter.value = 'all';
            if (employmentFilter) employmentFilter.value = 'all';
            applyFilters();
        });
    }
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

<!-- Modal for Cluster Detailed Analysis -->
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

<!-- Modal for Resident Details -->
<div id="residentDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-5/6 shadow-lg rounded-md bg-white max-h-[90vh]">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-users text-green-600 mr-2"></i>
                    Resident List
                </h3>
                <button onclick="closeResidentDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="residentDetailsModalContent" class="overflow-y-auto max-h-[calc(90vh-120px)]">
                <!-- Content will be populated from detailedAnalysisSection -->
            </div>
        </div>
    </div>
</div>

<!-- Modal for Cluster Details -->
<div id="clusterDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white max-h-[90vh]">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900" id="clusterDetailsModalTitle">
                    <i class="fas fa-layer-group text-purple-600 mr-2"></i>
                    Cluster Details
                </h3>
                <button onclick="closeClusterDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="clusterDetailsModalContent" class="overflow-y-auto max-h-[calc(90vh-120px)]">
                <!-- Content will be populated from expanded view -->
            </div>
        </div>
    </div>
</div>

<script>


// Close modals when clicking outside
document.getElementById('analysisModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

document.getElementById('residentDetailsModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeResidentDetailsModal();
    }
});

document.getElementById('clusterDetailsModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeClusterDetailsModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
        closeResidentDetailsModal();
        closeClusterDetailsModal();
    }
});

// Export Cluster Function
function exportCluster(clusterId) {
    try {
        // Find the cluster card by looking for the cluster ID in various ways
        let clusterCard = null;
        const allCards = document.querySelectorAll('.cluster-card');
        
        for (let card of allCards) {
            const clusterHeader = card.querySelector('.bg-purple-600');
            if (clusterHeader && clusterHeader.textContent.includes(`Cluster ${clusterId + 1}`)) {
                clusterCard = card;
                break;
            }
        }
        
        if (!clusterCard) {
            alert('Cluster data not found');
            return;
        }
        
        // Extract cluster data
        const clusterNumber = clusterCard.querySelector('.bg-purple-600')?.textContent?.trim() || `Cluster ${clusterId + 1}`;
        const size = clusterCard.querySelector('.bg-purple-200')?.textContent?.match(/\d+/)?.[0] || '0';
        const labelEl = Array.from(clusterCard.querySelectorAll('.font-semibold.text-gray-900')).find(el => 
            el.textContent.includes('Label:') || el.previousElementSibling?.textContent?.includes('Label:')
        );
        const label = labelEl?.textContent?.replace('Label:', '').trim() || 
                     clusterCard.querySelector('.font-semibold.text-gray-900')?.textContent?.trim() || 'N/A';
        
        // Get all text content
        const content = clusterCard.innerText || clusterCard.textContent;
        
        // Create CSV content
        let csvContent = `Cluster Analysis Export\n`;
        csvContent += `Cluster: ${clusterNumber}\n`;
        csvContent += `Label: ${label}\n`;
        csvContent += `Size: ${size} residents\n`;
        csvContent += `\n--- Cluster Details ---\n`;
        csvContent += `${content.replace(/\n{3,}/g, '\n\n')}\n`;
        csvContent += `\nExported on: ${new Date().toLocaleString()}\n`;
        
        // Create blob and download
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `cluster-${clusterId + 1}-${new Date().toISOString().split('T')[0]}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Show success message
        const event = window.event || arguments[0];
        if (event && event.target) {
            const btn = event.target.closest('button');
            if (btn) {
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Exported!';
                btn.classList.add('text-green-600');
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('text-green-600');
                }, 2000);
            }
        }
    } catch (error) {
        console.error('Export error:', error);
        alert('Error exporting cluster data. Please try again.');
    }
}
</script>

@endsection 