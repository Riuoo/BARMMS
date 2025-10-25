@extends('admin.main.layout')

@section('title', 'Resident Groups Analysis')

@section('content')
@php
    // Service instance for data processing
    $demographicService = app(\App\Services\ResidentDemographicAnalysisService::class);
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
                    $isHier = !empty($useHierarchical);
                    $isAutoK = !empty($useOptimalK);
                    $kValue = $k ?? 3;
                @endphp
                
                <!-- Simple Mode Toggle -->
                <a href="{{ $baseUrl }}?hierarchical={{ $isHier ? '' : '1' }}" 
                   class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium {{ $isHier ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} hover:opacity-90">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    {{ $isHier ? 'By Area' : 'Overall' }}
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
                
                <!-- Manual K Selection (only when not using auto-K) -->
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
                <span class="inline-flex items-center px-3 py-1 rounded-full {{ $isHier ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700' }}">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    {{ $isHier ? 'Grouped by Area' : 'Overall Groups' }}
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

    <!-- Current Mode Badges -->
    <div class="mb-3 flex flex-wrap items-center gap-2">
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ !empty($useHierarchical) ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-700' }}">
            <i class="fas fa-layer-group mr-1"></i>
            {{ !empty($useHierarchical) ? 'Hierarchical per Purok' : 'Standard K-Means' }}
        </span>
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ !empty($useOptimalK) ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-700' }}">
            <i class="fas fa-magic mr-1"></i>
            {{ !empty($useOptimalK) ? 'Global Auto-K' : 'Manual K='.$k }}
        </span>
        @if(isset($silhouette))
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800" title="Silhouette score indicates cluster separation quality (higher is better)">
            <i class="fas fa-chart-line mr-1"></i>
            Silhouette: {{ number_format($silhouette ?? 0, 3) }}
        </span>
        @endif
    </div>

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
            @if(!$isHier)
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
            @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-purple-600 text-sm md:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-3 md:ml-4">  
                        <p class="text-xs md:text-sm font-medium text-gray-500">Areas Found</p>
                        <p class="text-lg md:text-2xl font-bold text-gray-900">{{ count(array_filter(array_keys($grouped), fn($p) => $p !== 'N/A')) }}</p>
                    </div>
                </div>
            </div>
            @endif
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
                            {{ $isHier ? 'Purok Distribution' : 'Cluster Distribution' }}
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
                    @if($isHier ?? false)
                        <div class="chart-container" style="position: relative; height: 400px;">
                            <canvas id="purokChart"></canvas>
                        </div>
                    @else
                        <div class="chart-container" style="position: relative; height: 400px;">
                            <canvas id="clusterChart"></canvas>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Toggle for Detailed Analysis -->
            <div class="flex justify-start mb-2">
                <button id="toggleDetailsBtn" onclick="toggleDetails()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <i class="fas fa-chevron-down mr-2"></i>
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
                        @php
                            // Build flat rows once for table and filters
                            $rows = [];
                            $purokSet = [];
                            foreach (($clusters ?? []) as $cid => $cl) {
                                if (!is_array($cl)) continue;
                                foreach ($cl as $pt) {
                                    if (is_array($pt) && isset($pt['resident'])) {
                                        $rows[] = ['clusterId' => $cid, 'point' => $pt];
                                        $addr = strtolower($pt['resident']->address ?? '');
                                        $purok = 'N/A';
                                        if (preg_match('/purok\s*([a-z0-9]+)/i', $addr, $m)) { $purok = strtoupper($m[1]); }
                                        $purokSet[$purok] = true;
                                    }
                                }
                            }
                            ksort($purokSet);
                        @endphp
                        <div class="flex flex-wrap gap-2 items-center mb-2">
                            <input type="text" id="searchTable" placeholder="Search residents..." class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <select id="filterPurok" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">All Puroks</option>
                                @foreach(array_keys($purokSet) as $p)
                                    <option value="{{ $p }}">Purok {{ $p }}</option>
                                @endforeach
                            </select>
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purok</th>
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @php
                                                $addr = strtolower($resident->address ?? '');
                                                $purok = 'N/A';
                                                if (preg_match('/purok\s*([a-z0-9]+)/i', $addr, $m)) { $purok = strtoupper($m[1]); }
                                            @endphp
                                            {{ $purok }}
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
                            @if($isHier)
                                Group Summary by Area
                            @else
                                Group Summary
                            @endif
                        </h2>
                        <p class="text-gray-700 text-base">
                            @if($isHier)
                                This section shows how residents are grouped within each area. Each group highlights the most common characteristics of its members.
                            @else
                                This section shows how residents are grouped based on similar characteristics. Each group highlights the most common traits of its members.
                            @endif
                        </p>
                    </div>
                    <!-- Simple Legend -->
                    <div class="mb-3 flex flex-wrap gap-4 items-center text-xs">
                        <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-800 font-semibold"><i class="fas fa-check mr-1"></i> Good</span>
                        <span class="inline-flex items-center px-2 py-1 rounded bg-yellow-100 text-yellow-800 font-semibold"><i class="fas fa-minus mr-1"></i> Average</span>
                        <span class="inline-flex items-center px-2 py-1 rounded bg-red-100 text-red-800 font-semibold"><i class="fas fa-exclamation-triangle mr-1"></i> Needs Attention</span>
                    </div>
                    @php
                        $grouped = [];
                        foreach ($characteristics as $idx => $c) {
                            $p = $c['most_common_purok'] ?? 'N/A';
                            if ($p === '' || $p === null) { $p = 'N/A'; }
                            $grouped[$p] = $grouped[$p] ?? [];
                            $grouped[$p][] = ['idx' => $idx, 'c' => $c];
                        }
                    @endphp
                    @if($isHier)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($grouped as $purok => $items)
                                @php
                                    // Calculate most common values and weighted averages
                                    $total = 0; $allAges = []; $allFamilySizes = []; $incomes = []; $employments = []; $healths = [];
                                    foreach ($items as $it) {
                                        $c = $it['c'];
                                        $clusterSize = $c['size'];
                                        $total += $clusterSize;
                                        
                                        // Collect all individual values for mode calculation
                                        // Since we don't have individual resident data, we'll use the cluster's most common values
                                        for ($i = 0; $i < $clusterSize; $i++) {
                                            $age = $c['most_common_age'] ?? $c['avg_age'];
                                            $familySize = $c['most_common_family_size'] ?? $c['avg_family_size'];
                                            
                                            // Only add valid numeric values
                                            if (is_numeric($age) && $age > 0) {
                                                $allAges[] = (int)$age;
                                            }
                                            if (is_numeric($familySize) && $familySize > 0) {
                                                $allFamilySizes[] = (int)$familySize;
                                            }
                                        }
                                        
                                        $incomes[] = $c['most_common_income'] ?? 'N/A';
                                        $employments[] = $c['most_common_employment'] ?? 'N/A';
                                        $healths[] = $c['most_common_health'] ?? 'N/A';
                                    }
                                    
                                    // Skip N/A purok
                                    if ($purok === 'N/A') {
                                        continue;
                                    }
                                    
                                    $mostCommonIncome = array_count_values($incomes) ? array_search(max(array_count_values($incomes)), array_count_values($incomes)) : 'N/A';
                                    $mostCommonEmployment = array_count_values($employments) ? array_search(max(array_count_values($employments)), array_count_values($employments)) : 'N/A';
                                    $mostCommonHealth = array_count_values($healths) ? array_search(max(array_count_values($healths)), array_count_values($healths)) : 'N/A';
                                    $mostCommonAge = !empty($allAges) && array_count_values($allAges) ? array_search(max(array_count_values($allAges)), array_count_values($allAges)) : 'N/A';
                                    $mostCommonFamilySize = !empty($allFamilySizes) && array_count_values($allFamilySizes) ? array_search(max(array_count_values($allFamilySizes)), array_count_values($allFamilySizes)) : 'N/A';
                                    
                                    // Calculate detailed statistics for expanded view
                                    $avgAge = !empty($allAges) ? round(array_sum($allAges) / count($allAges), 1) : 'N/A';
                                    $stdAge = !empty($allAges) && count($allAges) > 1 ? round(sqrt(array_sum(array_map(function($x) use ($avgAge) { return pow($x - $avgAge, 2); }, $allAges)) / (count($allAges) - 1)), 1) : 'N/A';
                                    $avgFamilySize = !empty($allFamilySizes) ? round(array_sum($allFamilySizes) / count($allFamilySizes), 1) : 'N/A';
                                    $stdFamilySize = !empty($allFamilySizes) && count($allFamilySizes) > 1 ? round(sqrt(array_sum(array_map(function($x) use ($avgFamilySize) { return pow($x - $avgFamilySize, 2); }, $allFamilySizes)) / (count($allFamilySizes) - 1)), 1) : 'N/A';
                                    
                                    // Calculate distributions for bar charts
                                    $incomeDistribution = array_count_values($incomes);
                                    $employmentDistribution = array_count_values($employments);
                                    $healthDistribution = array_count_values($healths);
                                @endphp
                                <div class="purok-group-card shadow-lg rounded-xl bg-white border border-indigo-100 mb-3 p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-base font-bold bg-indigo-600 text-white shadow mr-2">
                                            <i class="fas fa-map-marker-alt mr-1"></i> Purok {{ $purok }}
                                        </span>
                                        <span class="bg-indigo-200 text-indigo-800 text-xs font-semibold px-2 py-1 rounded-full">{{ $total }} residents</span>
                                    </div>
                                    
                                    @if($total > 0)
                                        @php $purokKey = strtolower($purok); @endphp
                                        @if(isset($perPurokInsights[$purokKey]))
                                            @php $pins = $perPurokInsights[$purokKey]; @endphp
                                            <div class="mb-2 flex flex-wrap gap-2">
                                                @if(!empty($pins['program']))
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        <i class="fas fa-magic mr-1"></i>
                                                        Model: {{ $pins['program'] }} ({{ $pins['program_confidence'] }}%)
                                                    </span>
                                                @endif
                                                @if(!empty($pins['eligibility']))
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check mr-1"></i>
                                                        Eligibility: {{ $pins['eligibility'] }} ({{ $pins['eligibility_confidence'] }}%)
                                                    </span>
                                                @endif
                                                @if(!empty($pins['risk']))
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i class="fas fa-heartbeat mr-1"></i>
                                                        Risk: {{ $pins['risk'] }} ({{ $pins['risk_confidence'] }}%)
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                        <div class="mb-2 text-xs text-gray-700"><i class="fas fa-tag mr-1 text-gray-500"></i>Label: <span class="font-semibold text-gray-900">{{ $items[0]['c']['label'] ?? 'N/A' }}</span></div>
                                        
                                        <!-- Compact View (Default) -->
                                        <div class="compact-view">
                                            <div class="mb-2 text-xs text-gray-700">
                                                <span class="font-semibold">Most Common:</span>
                                                <span class="ml-2">Age: {{ $mostCommonAge }}, Family: {{ $mostCommonFamilySize }}, Income: {{ $mostCommonIncome }}, Employment: {{ $mostCommonEmployment }}, Health: {{ $mostCommonHealth }}</span>
                                            </div>
                                            
                                            <!-- Expand Button -->
                                            <div class="flex justify-between items-center mt-3">
                                                <button data-action="toggle-purok-details" data-purok="{{ $purok }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium flex items-center gap-1">
                                                    <i class="fas fa-chevron-down" id="icon-{{ $purok }}"></i>
                                                    Show Details
                                                </button>
                                                <button data-action="show-purok-modal" data-purok="{{ $purok }}" data-payload="{{ e(json_encode([
                                                    'total' => $total,
                                                    'avgAge' => $avgAge,
                                                    'stdAge' => $stdAge,
                                                    'avgFamilySize' => $avgFamilySize,
                                                    'stdFamilySize' => $stdFamilySize,
                                                    'mostCommonAge' => $mostCommonAge,
                                                    'mostCommonFamilySize' => $mostCommonFamilySize,
                                                    'mostCommonIncome' => $mostCommonIncome,
                                                    'mostCommonEmployment' => $mostCommonEmployment,
                                                    'mostCommonHealth' => $mostCommonHealth,
                                                    'incomeDistribution' => $incomeDistribution,
                                                    'employmentDistribution' => $employmentDistribution,
                                                    'healthDistribution' => $healthDistribution,
                                                    'label' => $items[0]["c"]["label"] ?? "N/A",
                                                    'pins' => isset($pins) ? [
                                                        'program' => $pins['program'] ?? null,
                                                        'program_confidence' => $pins['program_confidence'] ?? null,
                                                        'eligibility' => $pins['eligibility'] ?? null,
                                                        'eligibility_confidence' => $pins['eligibility_confidence'] ?? null,
                                                        'risk' => $pins['risk'] ?? null,
                                                        'risk_confidence' => $pins['risk_confidence'] ?? null
                                                    ] : null
                                                ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium flex items-center gap-1">
                                                    <i class="fas fa-external-link-alt"></i>
                                                    Full Analysis
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Expanded View (Hidden by default) -->
                                        <div class="expanded-view hidden" id="expanded-{{ $purok }}">
                                            <div class="border-t pt-3 mt-3">
                                                <div class="mb-2 text-xs text-gray-700">
                                                    <span class="font-semibold">Size:</span> {{ $total }} ({{ round(($total / array_sum(array_column($characteristics, 'size'))) * 100, 1) }}%)
                                                </div>
                                                <div class="mb-2 text-xs text-gray-700">
                                                    <span class="font-semibold">Age:</span> {{ $avgAge }} ± {{ $stdAge }}
                                                    <span class="ml-2 font-semibold">Family size:</span> {{ $avgFamilySize }} ± {{ $stdFamilySize }}
                                                </div>
                                                
                                                <!-- Top Traits -->
                                                <div class="mb-3">
                                                    <h6 class="text-xs font-semibold text-gray-700 mb-2">Top Traits:</h6>
                                                    <div class="flex flex-wrap gap-1">
                                                        @php
                                                            $traits = [];
                                                            if (!empty($incomeDistribution)) {
                                                                $topIncome = array_search(max($incomeDistribution), $incomeDistribution);
                                                                $traits[] = "Income: {$topIncome} " . round(($incomeDistribution[$topIncome] / $total) * 100) . "%";
                                                            }
                                                            if (!empty($employmentDistribution)) {
                                                                $topEmployment = array_search(max($employmentDistribution), $employmentDistribution);
                                                                $traits[] = "Employment: {$topEmployment} " . round(($employmentDistribution[$topEmployment] / $total) * 100) . "%";
                                                            }
                                                            if (!empty($healthDistribution)) {
                                                                $topHealth = array_search(max($healthDistribution), $healthDistribution);
                                                                $traits[] = "Health: {$topHealth} " . round(($healthDistribution[$topHealth] / $total) * 100) . "%";
                                                            }
                                                            $traits = array_slice($traits, 0, 3);
                                                        @endphp
                                                        @foreach($traits as $trait)
                                                            <span class="inline-block px-2 py-1 rounded-full bg-red-100 text-red-800 text-xs font-bold">{{ $trait }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                
                                                <!-- Mini Bar Charts -->
                                                <div class="space-y-2">
                                                    @if(!empty($incomeDistribution))
                                                        <div class="text-xs">
                                                            <span class="font-semibold text-gray-700">Income:</span>
                                                            {!! $demographicService->generateMiniBar($incomeDistribution, $incomeColors) !!}
                                                        </div>
                                                    @endif
                                                    @if(!empty($employmentDistribution))
                                                        <div class="text-xs">
                                                            <span class="font-semibold text-gray-700">Employment:</span>
                                                            {!! $demographicService->generateMiniBar($employmentDistribution, $employmentColors) !!}
                                                        </div>
                                                    @endif
                                                    @if(!empty($healthDistribution))
                                                        <div class="text-xs">
                                                            <span class="font-semibold text-gray-700">Health:</span>
                                                            {!! $demographicService->generateMiniBar($healthDistribution, $healthColors) !!}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Show empty purok message -->
                                        <div class="text-center py-4 text-gray-500">
                                            <i class="fas fa-map-marker-alt text-2xl mb-2"></i>
                                            <p class="text-sm">No residents assigned to this area yet</p>
                                            <p class="text-xs mt-1">Residents will appear here once they are added to the system</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($characteristics as $clusterId => $cluster)
                                @if($cluster['size'] > 0)
                                    @php
                                        // Get most common age and family size for this cluster
                                        $age = $cluster['most_common_age'] ?? $cluster['avg_age'];
                                        $familySize = $cluster['most_common_family_size'] ?? $cluster['avg_family_size'];
                                        
                                        // Get top traits for this cluster
                                        $traits = $demographicService->formatClusterTraits($cluster);
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
                                        
                                        <!-- Compact View (Default) -->
                                        <div class="compact-view">
                                            <div class="mb-2 text-xs text-gray-700">
                                                <span class="font-semibold">Size:</span> {{ $cluster['size'] }} ({{ $cluster['percent_of_total'] ?? '?' }}%)
                                                @if(!empty($cluster['outlier_count']))
                                                    <span class="ml-2 inline-block px-2 py-0.5 rounded-full bg-red-100 text-red-800">{{ $cluster['outlier_count'] }} outlier{{ $cluster['outlier_count']>1?'s':'' }}</span>
                                                @endif
                                            </div>
                                            <div class="mb-2 text-xs text-gray-700">
                                                <span class="font-semibold">Age:</span> {{ $cluster['avg_age'] ?? 'N/A' }} ± {{ $cluster['std_age'] ?? 'N/A' }}
                                                <span class="ml-2 font-semibold">Family size:</span> {{ $cluster['avg_family_size'] ?? 'N/A' }} ± {{ $cluster['std_family_size'] ?? 'N/A' }}
                                            </div>
                                            
                                            <!-- Expand Button -->
                                            <div class="flex justify-between items-center mt-3">
                                                <button data-action="toggle-cluster-details" data-cluster-id="{{ $clusterId }}" class="text-purple-600 hover:text-purple-800 text-xs font-medium flex items-center gap-1">
                                                    <i class="fas fa-chevron-down" id="cluster-icon-{{ $clusterId }}"></i>
                                                    Show Details
                                                </button>
                                                <button data-action="show-cluster-modal" data-cluster-id="{{ $clusterId }}" data-payload="{{ e(json_encode([
                                                    'cluster' => $cluster,
                                                    'traits' => $traits,
                                                    'insights' => isset($insights) ? $insights : null
                                                ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium flex items-center gap-1">
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
                                                            {!! $demographicService->generateMiniBar($cluster['income_distribution'], $incomeColors) !!}
                                                        </div>
                                                    @endif
                                                    @if(!empty($cluster['employment_distribution']))
                                                        <div class="text-xs">
                                                            <span class="font-semibold text-gray-700">Employment:</span>
                                                            {!! $demographicService->generateMiniBar($cluster['employment_distribution'], $employmentColors) !!}
                                                        </div>
                                                    @endif
                                                    @if(!empty($cluster['health_distribution']))
                                                        <div class="text-xs">
                                                            <span class="font-semibold text-gray-700">Health:</span>
                                                            {!! $demographicService->generateMiniBar($cluster['health_distribution'], $healthColors) !!}
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
                    @endif
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

<!-- Clustering JavaScript -->
<script src="{{ asset('js/clustering.js') }}"></script>

<!-- Clustering Charts JavaScript -->
<script src="{{ asset('js/clustering-charts.js') }}"></script>

<!-- Pass PHP data to JavaScript safely -->
<script type="application/json" id="clustering-data">
{!! json_encode([
    'isHier' => $isHier ?? false,
    'isAutoK' => $useOptimalK ?? false,
    'k' => $k ?? 3,
    'characteristics' => $characteristics ?? [],
    'grouped' => $grouped ?? [],
    'silhouette' => $silhouette ?? null,
    'sampleSize' => $sampleSize ?? 0,
    'mostCommonEmployment' => $mostCommonEmployment ?? 'N/A',
    'mostCommonHealth' => $mostCommonHealth ?? 'N/A',
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
            if (window.clusteringData.isHier) {
                initializePurokChart();
            } else {
                initializeClusterChart();
            }
        }
        
        // Initialize analytics charts
        initializeAnalyticsCharts();
        
        // Initialize table features
        initializeTableFeatures();
        
        // Collapsible purok groups: collapse all except first 2
        const purokGroups = document.querySelectorAll('.purok-group');
        purokGroups.forEach((group, idx) => {
            const clustersDiv = group.querySelector('.purok-clusters');
            const chevron = group.querySelector('.purok-header i.fas');
            if (idx > 1) {
                clustersDiv.classList.add('hidden');
                chevron.classList.add('rotate-180');
            }
        });
        
        // Purok search/filter
        const purokSearch = document.getElementById('purokSearch');
        if (purokSearch) {
            purokSearch.addEventListener('input', function() {
                const val = this.value.trim().toLowerCase();
                document.querySelectorAll('.purok-group').forEach(group => {
                    group.style.display = val === '' || group.getAttribute('data-purok').includes(val) ? '' : 'none';
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

.purok-header { user-select: none; }
.purok-header:hover { background: #e0e7ff; }
.rotate-180 { transform: rotate(180deg); }
.purok-group-card {
    box-shadow: 0 2px 8px rgba(99,102,241,0.08);
    border: 1px solid #e0e7ff;
    background: #fff;
    margin-bottom: 1.5rem;
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