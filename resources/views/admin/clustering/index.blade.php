@extends('admin.main.layout')

@section('title', 'Resident Clustering Analysis')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Enhanced Header Section -->
    <div class="mb-3">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Resident Demographic Clustering</h1>
                <p class="text-gray-600 text-lg">AI-powered demographic analysis to group residents for targeted services</p>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                @php
                    $baseUrl = route('admin.clustering');
                    $qs = request()->query();
                    $isHier = !empty($useHierarchical);
                    $isAutoK = !empty($useOptimalK);
                    $toggleHierUrl = $isHier
                        ? $baseUrl . '?' . http_build_query(array_merge($qs, ['hierarchical' => null]))
                        : $baseUrl . '?' . http_build_query(array_merge($qs, ['hierarchical' => 1]));
                    $toggleAutoKUrl = $isAutoK
                        ? $baseUrl . '?' . http_build_query(array_merge($qs, ['use_optimal_k' => null]))
                        : $baseUrl . '?' . http_build_query(array_merge($qs, ['use_optimal_k' => 1]));
                @endphp
                <a href="{{ $toggleHierUrl }}" class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium {{ $isHier ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300' }} hover:opacity-90 {{ $isAutoK ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}" title="{{ $isAutoK ? 'Disable Auto-K to enable Hierarchical mode' : ($isHier ? 'Hierarchical clustering per purok is ON' : 'Enable hierarchical clustering per purok') }}">
                    <i class="fas fa-layer-group mr-2"></i>
                    {{ $isHier ? 'Hierarchical ON' : 'Hierarchical OFF' }}
                </a>
                <a href="{{ $toggleAutoKUrl }}" class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium {{ $isAutoK ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-gray-700 border-gray-300' }} hover:opacity-90 {{ $isHier ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}" title="{{ $isHier ? 'Disable Hierarchical to enable Auto-K mode' : ($isAutoK ? 'Auto-K is ON (optimal K will be chosen automatically)' : 'Enable Auto-K (optimal K)') }}">
                    <i class="fas fa-magic mr-2"></i>
                    {{ $isAutoK ? 'Auto-K ON' : 'Auto-K OFF' }}
                </a>
                <form method="GET" action="{{ route('admin.clustering') }}" class="flex items-center space-x-2">
                    @foreach(request()->except(['k']) as $key => $val)
                        @if(!is_null($val) && $val !== '')
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endif
                    @endforeach
                    <label class="text-sm text-gray-700">K</label>
                    <input type="number" name="k" min="2" max="10" value="{{ $k ?? 3 }}" class="w-16 px-2 py-1 border border-gray-300 rounded" />
                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Apply
                    </button>
                </form>
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
            <!-- Clusters Found or Puroks Found Card -->
            @if(!$isHier)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-layer-group text-purple-600 text-sm md:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-500">Clusters Found</p>
                        <p class="text-lg md:text-2xl font-bold text-gray-900">{{ count($clusters) }}</p>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-indigo-600 text-sm md:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-500">Puroks Found</p>
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

            <!-- Cluster Characteristics -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6">
                    <!-- Section Header and Description -->
                    <div class="mb-3">
                        <h2 class="text-2xl font-extrabold text-indigo-900 flex items-center gap-2 mb-2">
                            <i class="fas fa-info-circle text-indigo-600"></i>
                            @if($isHier)
                                Cluster Characteristics by Purok
                            @else
                                Cluster Characteristics
                            @endif
                        </h2>
                        <p class="text-gray-700 text-base">
                            @if($isHier)
                                This section summarizes the demographic clusters found within each purok. Click a purok to expand and see its clusters. Each cluster card highlights the most common traits and a quick insight for that group.
                            @else
                                This section summarizes the demographic clusters found in the dataset. Each cluster card highlights the most common traits and a quick insight for that group.
                            @endif
                        </p>
                    </div>
                    <!-- Legend -->
                    <div class="mb-3 flex flex-wrap gap-4 items-center text-xs">
                        <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-800 font-semibold"><i class="fas fa-arrow-up mr-1"></i> High/Good</span>
                        <span class="inline-flex items-center px-2 py-1 rounded bg-yellow-100 text-yellow-800 font-semibold"><i class="fas fa-minus mr-1"></i> Medium/Fair</span>
                        <span class="inline-flex items-center px-2 py-1 rounded bg-red-100 text-red-800 font-semibold"><i class="fas fa-arrow-down mr-1"></i> Low/Critical</span>
                        <span class="inline-flex items-center px-2 py-1 rounded bg-blue-100 text-blue-800 font-semibold"><i class="fas fa-briefcase mr-1"></i> Employed</span>
                        <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-800 font-semibold"><i class="fas fa-users mr-1"></i> Residents</span>
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
                                        
                                        $incomes[] = $c['most_common_income'];
                                        $employments[] = $c['most_common_employment'];
                                        $healths[] = $c['most_common_health'];
                                    }
                                    
                                    // Skip puroks with no residents or N/A purok
                                    if ($total === 0 || $purok === 'N/A') {
                                        continue;
                                    }
                                    
                                    $mostCommonIncome = array_count_values($incomes) ? array_search(max(array_count_values($incomes)), array_count_values($incomes)) : 'N/A';
                                    $mostCommonEmployment = array_count_values($employments) ? array_search(max(array_count_values($employments)), array_count_values($employments)) : 'N/A';
                                    $mostCommonHealth = array_count_values($healths) ? array_search(max(array_count_values($healths)), array_count_values($healths)) : 'N/A';
                                    $mostCommonAge = !empty($allAges) && array_count_values($allAges) ? array_search(max(array_count_values($allAges)), array_count_values($allAges)) : 'N/A';
                                    $mostCommonFamilySize = !empty($allFamilySizes) && array_count_values($allFamilySizes) ? array_search(max(array_count_values($allFamilySizes)), array_count_values($allFamilySizes)) : 'N/A';
                                @endphp
                                <div class="purok-group-card shadow-lg rounded-xl bg-white border border-indigo-100 mb-3 p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-base font-bold bg-indigo-600 text-white shadow mr-2">
                                            <i class="fas fa-map-marker-alt mr-1"></i> Purok {{ $purok }}
                                        </span>
                                        <span class="bg-indigo-200 text-indigo-800 text-xs font-semibold px-2 py-1 rounded-full">{{ $total }} residents</span>
                                    </div>
                                    <div class="mb-2 text-sm text-gray-600">Most common: <span class="font-semibold text-indigo-700">{{ $mostCommonIncome }}</span> income, <span class="font-semibold text-indigo-700">{{ $mostCommonEmployment }}</span> employment, <span class="font-semibold text-indigo-700">{{ $mostCommonHealth }}</span> health</div>
                                    <div class="grid grid-cols-2 gap-4 mb-3">
                                        <div class="text-center">
                                            <span class="text-xs text-gray-600 flex items-center justify-center"><i class="fas fa-birthday-cake mr-1"></i>Most Common Age</span>
                                            <span class="text-lg font-bold text-gray-900">{{ $mostCommonAge }}</span>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-xs text-gray-600 flex items-center justify-center"><i class="fas fa-home mr-1"></i>Most Common Family Size</span>
                                            <span class="text-lg font-bold text-gray-900">{{ $mostCommonFamilySize }}</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2 mb-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $mostCommonIncome === 'High' ? 'bg-green-100 text-green-800' : ($mostCommonIncome === 'Low' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            <i class="fas fa-coins mr-1"></i>{{ $mostCommonIncome }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $mostCommonEmployment === 'Full-time' ? 'bg-green-100 text-green-800' : ($mostCommonEmployment === 'Unemployed' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                            <i class="fas fa-briefcase mr-1"></i>{{ $mostCommonEmployment }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $mostCommonHealth === 'Excellent' ? 'bg-green-100 text-green-800' : ($mostCommonHealth === 'Critical' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            <i class="fas fa-heartbeat mr-1"></i>{{ $mostCommonHealth }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-700 mt-2"><i class="fas fa-lightbulb text-yellow-400 mr-1"></i>
                                        @if($mostCommonIncome === 'Low')
                                            This purok may benefit from financial assistance or livelihood programs.
                                        @elseif($mostCommonEmployment === 'Unemployed')
                                            Consider job placement and skills development for this purok.
                                        @elseif($mostCommonHealth === 'Critical')
                                            Health interventions may be needed for this purok.
                                        @else
                                            General community services and health programs are recommended.
                                        @endif
                                    </div>
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
                                        
                                        // Validate and format the values
                                        $mostCommonAge = (is_numeric($age) && $age > 0) ? (int)$age : 'N/A';
                                        $mostCommonFamilySize = (is_numeric($familySize) && $familySize > 0) ? (int)$familySize : 'N/A';
                                    @endphp
                                    <div class="purok-group-card shadow-lg rounded-xl bg-white border border-indigo-100 mb-3 p-4">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-base font-bold bg-blue-600 text-white shadow mr-2">
                                                <i class="fas fa-layer-group mr-1"></i> Cluster {{ $clusterId + 1 }}
                                            </span>
                                            <span class="bg-blue-200 text-blue-800 text-xs font-semibold px-2 py-1 rounded-full">{{ $cluster['size'] }} residents</span>
                                        </div>
                                        <div class="mb-2 text-sm text-gray-600">Most common: <span class="font-semibold text-blue-700">{{ $cluster['most_common_income'] }}</span> income, <span class="font-semibold text-blue-700">{{ $cluster['most_common_employment'] }}</span> employment, <span class="font-semibold text-blue-700">{{ $cluster['most_common_health'] }}</span> health</div>
                                        <div class="grid grid-cols-2 gap-4 mb-3">
                                            <div class="text-center">
                                                <span class="text-xs text-gray-600 flex items-center justify-center"><i class="fas fa-birthday-cake mr-1"></i>Most Common Age</span>
                                                <span class="text-lg font-bold text-gray-900">{{ $mostCommonAge }}</span>
                                            </div>
                                            <div class="text-center">
                                                <span class="text-xs text-gray-600 flex items-center justify-center"><i class="fas fa-home mr-1"></i>Most Common Family Size</span>
                                                <span class="text-lg font-bold text-gray-900">{{ $mostCommonFamilySize }}</span>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2 mb-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $cluster['most_common_income'] === 'High' ? 'bg-green-100 text-green-800' : ($cluster['most_common_income'] === 'Low' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                <i class="fas fa-coins mr-1"></i>{{ $cluster['most_common_income'] }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $cluster['most_common_employment'] === 'Full-time' ? 'bg-green-100 text-green-800' : ($cluster['most_common_employment'] === 'Unemployed' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                <i class="fas fa-briefcase mr-1"></i>{{ $cluster['most_common_employment'] }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $cluster['most_common_health'] === 'Excellent' ? 'bg-green-100 text-green-800' : ($cluster['most_common_health'] === 'Critical' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                <i class="fas fa-heartbeat mr-1"></i>{{ $cluster['most_common_health'] }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-700 mt-2"><i class="fas fa-lightbulb text-yellow-400 mr-1"></i>
                                            @if($cluster['most_common_income'] === 'Low')
                                                This cluster may benefit from financial assistance or livelihood programs.
                                            @elseif($cluster['most_common_employment'] === 'Unemployed')
                                                Consider job placement and skills development for this cluster.
                                            @elseif($cluster['most_common_health'] === 'Critical')
                                                Health interventions may be needed for this cluster.
                                            @else
                                                General community services and health programs are recommended.
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Analytics Dashboard Section -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-3">
            <div class="p-6">
                <div class="flex items-center justify-between mb-3">
                                            <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                            Analytics Dashboard
                            @if(!empty($useOptimalK))
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 ml-2">
                                    <i class="fas fa-magic mr-1"></i>Auto K={{ $k }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 ml-2">
                                    <i class="fas fa-cog mr-1"></i>Manual K={{ $k }}
                                </span>
                            @endif
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
                            Income Level Distribution
                        </h4>
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="incomeChart"></canvas>
                        </div>
                    </div>

                    <!-- Employment Status Distribution -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">
                            <i class="fas fa-briefcase text-blue-600 mr-2"></i>
                            Employment Status Distribution
                        </h4>
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="employmentChart"></canvas>
                        </div>
                    </div>

                    <!-- Age Distribution -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">
                            <i class="fas fa-birthday-cake text-purple-600 mr-2"></i>
                            Age Distribution
                        </h4>
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="ageChart"></canvas>
                        </div>
                    </div>

                    <!-- Health Status Distribution -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">
                            <i class="fas fa-heartbeat text-red-600 mr-2"></i>
                            Health Status Distribution
                        </h4>
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="healthChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Comparative Analysis -->
                <div class="mt-8">
                    <h4 class="text-md font-semibold text-gray-900 mb-3">
                        <i class="fas fa-chart-line text-indigo-600 mr-2"></i>
                        Comparative Analysis
                    </h4>
                    <div class="chart-container" style="position: relative; height: 400px;">
                        <canvas id="comparativeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analysis Section -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-3">
            <div class="p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-table text-green-600 mr-2"></i>
                        Detailed Resident Analysis
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($residents as $resident)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $resident->name ?? 'N/A' }}</div>
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="viewResident({{ $resident->id ?? 0 }})" class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
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

        <!-- Insights and Recommendations -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                    <i class="fas fa-lightbulb text-yellow-600 mr-2"></i>
                    Insights & Recommendations
                </h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-md font-semibold text-blue-900 mb-3">
                            <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                            Key Findings
                        </h4>
                        <div class="space-y-4">
                            @if($isHier)
                                @foreach($grouped as $purok => $items)
                                    @php
                                        $total = 0; $allAges = []; $incomes = []; $employments = []; $healths = [];
                                        foreach ($items as $it) {
                                            $c = $it['c'];
                                            $clusterSize = $c['size'];
                                            $total += $clusterSize;
                                            
                                            // Collect all individual values for mode calculation
                                            // Since we don't have individual resident data, we'll use the cluster's most common values
                                            for ($i = 0; $i < $clusterSize; $i++) {
                                                $age = $c['most_common_age'] ?? $c['avg_age'];
                                                if (is_numeric($age) && $age > 0) {
                                                    $allAges[] = (int)$age;
                                                }
                                            }
                                            
                                            $incomes[] = $c['most_common_income'];
                                            $employments[] = $c['most_common_employment'];
                                            $healths[] = $c['most_common_health'];
                                        }
                                        
                                        // Skip puroks with no residents or N/A purok
                                        if ($total === 0 || $purok === 'N/A') {
                                            continue;
                                        }
                                        
                                        $mostCommonIncome = array_count_values($incomes) ? array_search(max(array_count_values($incomes)), array_count_values($incomes)) : 'N/A';
                                        $mostCommonEmployment = array_count_values($employments) ? array_search(max(array_count_values($employments)), array_count_values($employments)) : 'N/A';
                                        $mostCommonHealth = array_count_values($healths) ? array_search(max(array_count_values($healths)), array_count_values($healths)) : 'N/A';
                                        $mostCommonAge = !empty($allAges) && array_count_values($allAges) ? array_search(max(array_count_values($allAges)), array_count_values($allAges)) : 'N/A';
                                    @endphp
                                    <div class="p-4 border-l-4 border-blue-500 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all duration-200">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-users text-white text-sm"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <h5 class="text-sm font-medium text-blue-900">Purok {{ $purok }}</h5>
                                                <p class="text-sm text-blue-700 mt-1">
                                                    {{ $total }} residents with most common age {{ $mostCommonAge }}.
                                                    Most common income level is {{ $mostCommonIncome }}.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                @foreach($characteristics as $clusterId => $cluster)
                                    @if($cluster['size'] > 0)
                                        @php
                                            $allAges = [];
                                            for ($i = 0; $i < $cluster['size']; $i++) {
                                                $age = $cluster['most_common_age'] ?? $cluster['avg_age'];
                                                if (is_numeric($age) && $age > 0) {
                                                    $allAges[] = (int)$age;
                                                }
                                            }
                                            $mostCommonAge = array_count_values($allAges) ? array_search(max(array_count_values($allAges)), array_count_values($allAges)) : 'N/A';
                                        @endphp
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
                                                        {{ $cluster['size'] }} residents with most common age {{ $mostCommonAge }}.
                                                        Most common income level is {{ $cluster['most_common_income'] }}.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div>
                        <h4 class="text-md font-semibold text-green-900 mb-3">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            Actionable Recommendations
                        </h4>
                        <div class="space-y-4">
                            @if($isHier)
                                @foreach($grouped as $purok => $items)
                                    @php
                                        // Skip N/A purok in recommendations
                                        if ($purok === 'N/A') {
                                            continue;
                                        }
                                        
                                        $incomes = []; $employments = []; $healths = [];
                                        foreach ($items as $it) {
                                            $c = $it['c'];
                                            $incomes[] = $c['most_common_income'];
                                            $employments[] = $c['most_common_employment'];
                                            $healths[] = $c['most_common_health'];
                                        }
                                        $mostCommonIncome = array_count_values($incomes) ? array_search(max(array_count_values($incomes)), array_count_values($incomes)) : 'N/A';
                                        $mostCommonEmployment = array_count_values($employments) ? array_search(max(array_count_values($employments)), array_count_values($employments)) : 'N/A';
                                        $mostCommonHealth = array_count_values($healths) ? array_search(max(array_count_values($healths)), array_count_values($healths)) : 'N/A';
                                    @endphp
                                    <div class="p-4 border-l-4 border-green-500 bg-green-50 rounded-lg hover:bg-green-100 transition-all duration-200">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-lightbulb text-white text-sm"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <h5 class="text-sm font-medium text-green-900">Purok {{ $purok }}</h5>
                                                <p class="text-sm text-green-700 mt-1">
                                                    @if($mostCommonIncome === 'Low')
                                                        Consider financial assistance programs and livelihood training.
                                                    @elseif($mostCommonEmployment === 'Unemployed')
                                                        Focus on job placement and skills development programs.
                                                    @elseif($mostCommonHealth === 'Critical')
                                                        Health interventions may be needed for this purok.
                                                    @else
                                                        Provide general community services and health programs.
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                @foreach($characteristics as $clusterId => $cluster)
                                    @if($cluster['size'] > 0)
                                        @php
                                            $allAges = [];
                                            for ($i = 0; $i < $cluster['size']; $i++) {
                                                $age = $cluster['most_common_age'] ?? $cluster['avg_age'];
                                                if (is_numeric($age) && $age > 0) {
                                                    $allAges[] = (int)$age;
                                                }
                                            }
                                            $mostCommonAge = array_count_values($allAges) ? array_search(max(array_count_values($allAges)), array_count_values($allAges)) : 'N/A';
                                        @endphp
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
                                                        @elseif($cluster['most_common_health'] === 'Critical')
                                                            Health interventions may be needed for this cluster.
                                                        @else
                                                            Provide general community services and health programs.
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
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
// Debounce helper
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function togglePurokGroup(purok) {
    const clustersDiv = document.getElementById('purok-clusters-' + purok);
    const chevron = document.getElementById('chevron-' + purok);
    if (clustersDiv.classList.contains('hidden')) {
        clustersDiv.classList.remove('hidden');
        chevron.classList.remove('rotate-180');
    } else {
        clustersDiv.classList.add('hidden');
        chevron.classList.add('rotate-180');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initializeClusterChart();
    initializeAnalyticsCharts(); // Initialize all new charts
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
});

function initializeClusterChart() {
    const isHier = @json($isHier ?? false);
    const isAutoK = @json($useOptimalK ?? false);
    const kValue = @json($k ?? 3);
    const kIndicator = isAutoK ? ` (Auto K=${kValue})` : ` (Manual K=${kValue})`;
    if (isHier) {
        // Purok Distribution Chart
        const ctx = document.getElementById('purokChart').getContext('2d');
        const grouped = @json($grouped);
        const labels = [];
        const data = [];
        const colors = ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444', '#6366F1', '#F472B6', '#FBBF24', '#34D399', '#60A5FA'];
        Object.keys(grouped).forEach((purok, idx) => {
            const total = grouped[purok].reduce((a, b) => a + b.c.size, 0);
            labels.push(`Purok ${purok} (${total} residents)`);
            data.push(total);
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
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return 'Purok Distribution' + kIndicator;
                            },
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            },
                            afterLabel: function() {
                                return isAutoK ? 'Auto-optimized clustering' : 'Manual clustering';
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
    } else {
        // Cluster Distribution Chart
        const ctx = document.getElementById('clusterChart').getContext('2d');
        const characteristics = @json($characteristics);
        const labels = [];
        const data = [];
        const colors = ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444', '#6366F1', '#F472B6', '#FBBF24', '#34D399', '#60A5FA'];
        characteristics.forEach((cluster, idx) => {
            if (cluster.size > 0) {
                labels.push(`Cluster ${idx + 1} (${cluster.size} residents)`);
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
                            font: { size: 12 }
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
}

function initializeAnalyticsCharts() {
    const isHier = @json($isHier ?? false);
    const characteristics = @json($characteristics);
    const grouped = @json($grouped ?? []);
    const isAutoK = @json($useOptimalK ?? false);
    const kValue = @json($k ?? 3);
    
    // Enhanced color palette - no duplicates
    const colorPalette = [
        '#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444', 
        '#6366F1', '#F472B6', '#FBBF24', '#34D399', '#60A5FA',
        '#84CC16', '#EC4899', '#06B6D4', '#F97316', '#A855F7'
    ];
    
    // Add K selection indicator to chart titles
    const kIndicator = isAutoK ? ` (Auto K=${kValue})` : ` (Manual K=${kValue})`;
    const kBadge = isAutoK ? 
        '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 ml-2">Auto K</span>' :
        '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 ml-2">Manual K</span>';
    
    // Prepare data for charts
    let chartData = [];
    if (isHier) {
        // Use purok-based data
        Object.keys(grouped).forEach((purok, idx) => {
            if (purok !== 'N/A') {
                const items = grouped[purok];
                let total = 0;
                let incomes = { 'Low': 0, 'Lower Middle': 0, 'Middle': 0, 'Upper Middle': 0, 'High': 0 };
                let employments = { 'Unemployed': 0, 'Part-time': 0, 'Self-employed': 0, 'Full-time': 0 };
                let healths = { 'Critical': 0, 'Poor': 0, 'Fair': 0, 'Good': 0, 'Excellent': 0 };
                let ages = [];
                let familySizes = [];
                
                items.forEach(item => {
                    const c = item.c;
                    total += c.size;
                    
                    // Count income levels using distribution data
                    const incomeDistribution = c.income_distribution || {};
                    let totalCountedIncome = 0;
                    Object.keys(incomeDistribution).forEach(incomeLevel => {
                        if (incomeDistribution[incomeLevel] && incomeDistribution[incomeLevel] > 0) {
                            incomes[incomeLevel] = (incomes[incomeLevel] || 0) + incomeDistribution[incomeLevel];
                            totalCountedIncome += incomeDistribution[incomeLevel];
                        }
                    });
                    
                    // Ensure all residents are counted - add remaining to most common or distribute evenly
                    const remainingResidents = c.size - totalCountedIncome;
                    if (remainingResidents > 0) {
                        if (c.most_common_income) {
                            incomes[c.most_common_income] = (incomes[c.most_common_income] || 0) + remainingResidents;
                        } else {
                            // Distribute remaining residents evenly across income levels
                            const incomeLevels = ['Low', 'Lower Middle', 'Middle', 'Upper Middle', 'High'];
                            const perLevel = Math.floor(remainingResidents / incomeLevels.length);
                            const extra = remainingResidents % incomeLevels.length;
                            incomeLevels.forEach((level, idx) => {
                                incomes[level] = (incomes[level] || 0) + perLevel + (idx < extra ? 1 : 0);
                            });
                        }
                    }
                    
                    // Count employment status using distribution data
                    const employmentDistribution = c.employment_distribution || {};
                    let totalCountedEmployment = 0;
                    Object.keys(employmentDistribution).forEach(employmentStatus => {
                        if (employmentDistribution[employmentStatus] && employmentDistribution[employmentStatus] > 0) {
                            employments[employmentStatus] = (employments[employmentStatus] || 0) + employmentDistribution[employmentStatus];
                            totalCountedEmployment += employmentDistribution[employmentStatus];
                        }
                    });
                    
                    // Ensure all residents are counted - add remaining to most common or distribute evenly
                    const remainingEmployment = c.size - totalCountedEmployment;
                    if (remainingEmployment > 0) {
                        if (c.most_common_employment) {
                            employments[c.most_common_employment] = (employments[c.most_common_employment] || 0) + remainingEmployment;
                        } else {
                            // Distribute remaining residents evenly across employment statuses
                            const employmentStatuses = ['Unemployed', 'Part-time', 'Self-employed', 'Full-time'];
                            const perStatus = Math.floor(remainingEmployment / employmentStatuses.length);
                            const extra = remainingEmployment % employmentStatuses.length;
                            employmentStatuses.forEach((status, idx) => {
                                employments[status] = (employments[status] || 0) + perStatus + (idx < extra ? 1 : 0);
                            });
                        }
                    }
                    
                    // Count health status using distribution data
                    const healthDistribution = c.health_distribution || {};
                    let totalCountedHealth = 0;
                    Object.keys(healthDistribution).forEach(healthStatus => {
                        if (healthDistribution[healthStatus] && healthDistribution[healthStatus] > 0) {
                            healths[healthStatus] = (healths[healthStatus] || 0) + healthDistribution[healthStatus];
                            totalCountedHealth += healthDistribution[healthStatus];
                        }
                    });
                    
                    // Ensure all residents are counted - add remaining to most common or distribute evenly
                    const remainingHealth = c.size - totalCountedHealth;
                    if (remainingHealth > 0) {
                        if (c.most_common_health) {
                            healths[c.most_common_health] = (healths[c.most_common_health] || 0) + remainingHealth;
                        } else {
                            // Distribute remaining residents evenly across health statuses
                            const healthStatuses = ['Critical', 'Poor', 'Fair', 'Good', 'Excellent'];
                            const perStatus = Math.floor(remainingHealth / healthStatuses.length);
                            const extra = remainingHealth % healthStatuses.length;
                            healthStatuses.forEach((status, idx) => {
                                healths[status] = (healths[status] || 0) + perStatus + (idx < extra ? 1 : 0);
                            });
                        }
                    }
                    
                    // Collect ages and family sizes
                    for (let i = 0; i < c.size; i++) {
                        const age = c.most_common_age || c.avg_age;
                        const familySize = c.most_common_family_size || c.avg_family_size;
                        if (is_numeric(age) && age > 0) {
                            ages.push(parseInt(age));
                        }
                        if (is_numeric(familySize) && familySize > 0) {
                            familySizes.push(parseInt(familySize));
                        }
                    }
                });
                
                if (total > 0) {
                    chartData.push({
                        label: `Purok ${purok}`,
                        total: total,
                        incomes: incomes,
                        employments: employments,
                        healths: healths,
                        ages: ages,
                        familySizes: familySizes,
                        color: colorPalette[idx % colorPalette.length]
                    });
                }
            }
        });
    } else {
        // Use cluster-based data
        characteristics.forEach((cluster, idx) => {
            if (cluster.size > 0) {
                let incomes = { 'Low': 0, 'Lower Middle': 0, 'Middle': 0, 'Upper Middle': 0, 'High': 0 };
                let employments = { 'Unemployed': 0, 'Part-time': 0, 'Self-employed': 0, 'Full-time': 0 };
                let healths = { 'Critical': 0, 'Poor': 0, 'Fair': 0, 'Good': 0, 'Excellent': 0 };
                let ages = [];
                let familySizes = [];
                
                // Count income levels using distribution data
                const incomeDistribution = cluster.income_distribution || {};
                let totalCountedIncome = 0;
                Object.keys(incomeDistribution).forEach(incomeLevel => {
                    if (incomeDistribution[incomeLevel] && incomeDistribution[incomeLevel] > 0) {
                        incomes[incomeLevel] = (incomes[incomeLevel] || 0) + incomeDistribution[incomeLevel];
                        totalCountedIncome += incomeDistribution[incomeLevel];
                    }
                });
                
                // Ensure all residents are counted - add remaining to most common or distribute evenly
                const remainingResidents = cluster.size - totalCountedIncome;
                if (remainingResidents > 0) {
                    if (cluster.most_common_income) {
                        incomes[cluster.most_common_income] = (incomes[cluster.most_common_income] || 0) + remainingResidents;
                    } else {
                        // Distribute remaining residents evenly across income levels
                        const incomeLevels = ['Low', 'Middle', 'Upper Middle', 'High'];
                        const perLevel = Math.floor(remainingResidents / incomeLevels.length);
                        const extra = remainingResidents % incomeLevels.length;
                        incomeLevels.forEach((level, idx) => {
                            incomes[level] = (incomes[level] || 0) + perLevel + (idx < extra ? 1 : 0);
                        });
                    }
                }
                
                // Count employment status using distribution data
                const employmentDistribution = cluster.employment_distribution || {};
                let totalCountedEmployment = 0;
                Object.keys(employmentDistribution).forEach(employmentStatus => {
                    if (employmentDistribution[employmentStatus] && employmentDistribution[employmentStatus] > 0) {
                        employments[employmentStatus] = (employments[employmentStatus] || 0) + employmentDistribution[employmentStatus];
                        totalCountedEmployment += employmentDistribution[employmentStatus];
                    }
                });
                
                // Ensure all residents are counted - add remaining to most common or distribute evenly
                const remainingEmployment = cluster.size - totalCountedEmployment;
                if (remainingEmployment > 0) {
                    if (cluster.most_common_employment) {
                        employments[cluster.most_common_employment] = (employments[cluster.most_common_employment] || 0) + remainingEmployment;
                    } else {
                        // Distribute remaining residents evenly across employment statuses
                        const employmentStatuses = ['Full-time', 'Part-time', 'Unemployed'];
                        const perStatus = Math.floor(remainingEmployment / employmentStatuses.length);
                        const extra = remainingEmployment % employmentStatuses.length;
                        employmentStatuses.forEach((status, idx) => {
                            employments[status] = (employments[status] || 0) + perStatus + (idx < extra ? 1 : 0);
                        });
                    }
                }
                
                // Count health status using distribution data
                const healthDistribution = cluster.health_distribution || {};
                let totalCountedHealth = 0;
                Object.keys(healthDistribution).forEach(healthStatus => {
                    if (healthDistribution[healthStatus] && healthDistribution[healthStatus] > 0) {
                        healths[healthStatus] = (healths[healthStatus] || 0) + healthDistribution[healthStatus];
                        totalCountedHealth += healthDistribution[healthStatus];
                    }
                });
                
                // Ensure all residents are counted - add remaining to most common or distribute evenly
                const remainingHealth = cluster.size - totalCountedHealth;
                if (remainingHealth > 0) {
                    if (cluster.most_common_health) {
                        healths[cluster.most_common_health] = (healths[cluster.most_common_health] || 0) + remainingHealth;
                    } else {
                        // Distribute remaining residents evenly across health statuses
                        const healthStatuses = ['Excellent', 'Good', 'Fair', 'Critical'];
                        const perStatus = Math.floor(remainingHealth / healthStatuses.length);
                        const extra = remainingHealth % healthStatuses.length;
                        healthStatuses.forEach((status, idx) => {
                            healths[status] = (healths[status] || 0) + perStatus + (idx < extra ? 1 : 0);
                        });
                    }
                }
                
                // Collect ages and family sizes
                for (let i = 0; i < cluster.size; i++) {
                    const age = cluster.most_common_age || cluster.avg_age;
                    const familySize = cluster.most_common_family_size || cluster.avg_family_size;
                    if (is_numeric(age) && age > 0) {
                        ages.push(parseInt(age));
                    }
                    if (is_numeric(familySize) && familySize > 0) {
                        familySizes.push(parseInt(familySize));
                    }
                }
                
                chartData.push({
                    label: `Cluster ${idx + 1}`,
                    total: cluster.size,
                    incomes: incomes,
                    employments: employments,
                    healths: healths,
                    ages: ages,
                    familySizes: familySizes,
                    color: colorPalette[idx % colorPalette.length]
                });
            }
        });
    }
    
    // Initialize all charts with enhanced features
    initializeIncomeChart(chartData);
    initializeEmploymentChart(chartData);
    initializeAgeChart(chartData);
    initializeHealthChart(chartData);
    initializeComparativeChart(chartData);
    initializeFamilySizeChart(chartData);
    initializeDemographicOverview(chartData);
}

function initializeIncomeChart(chartData) {
    const ctx = document.getElementById('incomeChart').getContext('2d');
    const incomeLabels = ['Low', 'Lower Middle', 'Middle', 'Upper Middle', 'High'];
    
    const datasets = chartData.map((data) => ({
        label: data.label,
        data: incomeLabels.map(label => data.incomes[label] || 0),
        backgroundColor: data.color + '80',
        borderColor: data.color,
        borderWidth: 2,
        borderRadius: 4,
        borderSkipped: false
    }));
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: incomeLabels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'nearest', intersect: true }, // ✅ Show one value at a time
            plugins: {
                legend: {
                    position: 'top',
                    labels: { 
                        font: { size: 11 },
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    enabled: true,
                    mode: 'nearest',
                    intersect: true,
                    callbacks: {
                        title: function(context) {
                            // Just return the label without Auto/Manual K
                            return context[0].label;
                        },
                        label: function(context) {
                            const value = context.parsed.y ?? context.parsed;
                            const datasetLabel = context.dataset.label;
                            return `${datasetLabel}: ${value}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Number of Residents' },
                    grid: { color: 'rgba(0,0,0,0.1)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
}

function initializeEmploymentChart(chartData) {
    const ctx = document.getElementById('employmentChart').getContext('2d');
    const employmentLabels = ['Unemployed', 'Part-time', 'Self-employed', 'Full-time'];
    const employmentColors = ['#EF4444', '#F59E0B', '#8B5CF6', '#10B981'];
    
    // Create pie chart for overall employment distribution
    const totalEmployments = { 'Unemployed': 0, 'Part-time': 0, 'Self-employed': 0, 'Full-time': 0 };
    chartData.forEach(data => {
        employmentLabels.forEach(label => {
            totalEmployments[label] += data.employments[label] || 0;
        });
    });
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: employmentLabels,
            datasets: [{
                data: employmentLabels.map(label => totalEmployments[label]),
                backgroundColor: employmentColors,
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        font: { size: 11 },
                        padding: 15
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function initializeAgeChart(chartData) {
    const ctx = document.getElementById('ageChart').getContext('2d');
    const ageRanges = ['0-20', '21-40', '41-60', '61-80', '80+'];
    const ageColors = ['#8B5CF6', '#3B82F6', '#10B981', '#F59E0B', '#EF4444'];
    
    const datasets = chartData.map((data, idx) => {
        const ageCounts = [0, 0, 0, 0, 0];
        data.ages.forEach(age => {
            if (age <= 20) ageCounts[0]++;
            else if (age <= 40) ageCounts[1]++;
            else if (age <= 60) ageCounts[2]++;
            else if (age <= 80) ageCounts[3]++;
            else ageCounts[4]++;
        });
        
        return {
            label: data.label,
            data: ageCounts,
            backgroundColor: data.color + '80',
            borderColor: data.color,
            borderWidth: 2,
            borderRadius: 4,
            borderSkipped: false
        };
    });
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ageRanges,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { 
                        font: { size: 11 },
                        usePointStyle: true,
                        padding: 15
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Number of Residents' },
                    grid: { color: 'rgba(0,0,0,0.1)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
}

function initializeHealthChart(chartData) {
    const ctx = document.getElementById('healthChart').getContext('2d');
    const healthLabels = ['Critical', 'Poor', 'Fair', 'Good', 'Excellent'];
    const healthColors = ['#EF4444', '#F97316', '#F59E0B', '#3B82F6', '#10B981'];
    
    // Create stacked bar chart for health distribution
    const datasets = healthLabels.map((label, idx) => ({
        label: label,
        data: chartData.map(data => data.healths[label] || 0),
        backgroundColor: healthColors[idx],
        borderColor: healthColors[idx],
        borderWidth: 1
    }));
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.map(data => data.label),
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { 
                        font: { size: 11 },
                        usePointStyle: true,
                        padding: 15
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    grid: { display: false }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    title: { display: true, text: 'Number of Residents' },
                    grid: { color: 'rgba(0,0,0,0.1)' }
                }
            }
        }
    });
}

function initializeComparativeChart(chartData) {
    const ctx = document.getElementById('comparativeChart').getContext('2d');
    const labels = chartData.map(data => data.label);
    
    // Calculate metrics for comparison
    const avgAges = chartData.map(data => {
        if (data.ages.length === 0) return 0;
        return Math.round(data.ages.reduce((a, b) => a + b, 0) / data.ages.length);
    });
    
    const avgFamilySizes = chartData.map(data => {
        if (data.familySizes.length === 0) return 0;
        return Math.round((data.familySizes.reduce((a, b) => a + b, 0) / data.familySizes.length) * 10) / 10;
    });
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Average Age',
                data: avgAges,
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                yAxisID: 'y'
            }, {
                label: 'Average Family Size',
                data: avgFamilySizes,
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                yAxisID: 'y1'
            }, {
                label: 'Total Residents',
                data: chartData.map(data => data.total),
                borderColor: '#F59E0B',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                yAxisID: 'y2'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { size: 11 } }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Average Age' },
                    grid: { color: 'rgba(0,0,0,0.1)' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'Average Family Size' },
                    grid: { drawOnChartArea: false }
                },
                y2: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'Total Residents' },
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });
}

function initializeFamilySizeChart(chartData) {
    // Add family size chart if you want to include it
    // This would be similar to the age chart but for family sizes
}

function initializeDemographicOverview(chartData) {
    // Add demographic overview chart if needed
    // This could be a radar chart showing multiple metrics
}

function downloadAllCharts() {
    // Download all charts as images
    const charts = ['incomeChart', 'employmentChart', 'ageChart', 'healthChart', 'comparativeChart'];
    charts.forEach(chartId => {
        const canvas = document.getElementById(chartId);
        if (canvas) {
            const link = document.createElement('a');
            link.download = `${chartId}.png`;
            link.href = canvas.toDataURL();
            link.click();
        }
    });
}

// Helper function to check if value is numeric
function is_numeric(value) {
    return !isNaN(parseFloat(value)) && isFinite(value);
}

function initializeTableFeatures() {
    const searchInput = document.getElementById('searchTable');
    const filterPurok = document.getElementById('filterPurok');
    const rowsPerPageSelect = document.getElementById('rowsPerPage');
    const table = document.getElementById('residentsTable');
    const tbody = table.querySelector('tbody');
    const allRows = Array.from(tbody.querySelectorAll('tr'));
    const emptyState = document.getElementById('emptyState');
    const loadingDiv = document.getElementById('tableLoading');
    const paginationControls = document.getElementById('paginationControls');
    const paginationInfo = document.getElementById('paginationInfo');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const currentPageSpan = document.getElementById('currentPage');
    let currentPage = 1;
    let rowsPerPage = parseInt(rowsPerPageSelect.value);
    let filteredRows = allRows;

    // Purok is always the 7th column (1-based index)
    const purokColIdx = 7;

    function showLoading(show) {
        loadingDiv.classList.toggle('hidden', !show);
        table.classList.toggle('opacity-50', show);
    }

    function updateTable() {
        showLoading(true);
        setTimeout(() => {
            // Hide all rows
            allRows.forEach(row => row.style.display = 'none');
            // Show filtered rows for current page
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            let anyVisible = false;
            filteredRows.slice(start, end).forEach(row => {
                row.style.display = '';
                anyVisible = true;
            });
            // Empty state
            emptyState.classList.toggle('hidden', anyVisible);
            // Pagination info
            const total = filteredRows.length;
            const totalPages = Math.ceil(total / rowsPerPage) || 1;
            paginationInfo.textContent = `Showing ${total ? start + 1 : 0} to ${Math.min(end, total)} of ${total} residents`;
            currentPageSpan.textContent = `${currentPage} / ${totalPages}`;
            prevPageBtn.disabled = currentPage === 1;
            nextPageBtn.disabled = currentPage === totalPages;
            showLoading(false);
        }, 200); // Simulate loading
    }

    function filterRows() {
        showLoading(true);
        setTimeout(() => {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedPurok = filterPurok.value.trim().toUpperCase();
            filteredRows = allRows.filter(row => {
                let match = true;
                if (searchTerm) {
                    match = row.textContent.toLowerCase().includes(searchTerm);
                }
                if (match && selectedPurok) {
                    // Use correct column index for purok
                    const purokCell = row.querySelector(`td:nth-child(${purokColIdx})`);
                    const val = (purokCell ? purokCell.textContent : '').trim().toUpperCase();
                    match = val.includes(selectedPurok);
                }
                return match;
            });
            currentPage = 1;
            updateTable();
        }, 200);
    }

    // Debounced search
    searchInput.addEventListener('input', debounce(filterRows, 300));
    filterPurok.addEventListener('change', filterRows);
    rowsPerPageSelect.addEventListener('change', function() {
        rowsPerPage = parseInt(this.value);
        currentPage = 1;
        updateTable();
    });
    prevPageBtn.addEventListener('click', function() {
        if (currentPage > 1) { currentPage--; updateTable(); }
    });
    nextPageBtn.addEventListener('click', function() {
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage) || 1;
        if (currentPage < totalPages) { currentPage++; updateTable(); }
    });

    // Initial render
    filterRows();
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
@endsection 