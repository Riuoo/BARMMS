@extends('admin.main.layout')

@section('title', 'Decision Tree Analytics')

@section('content')
@php
    // Create lookup maps for predictions by resident ID
    $eligibilityMap = [];
    if (isset($serviceEligibility['predictions']) && !isset($serviceEligibility['error'])) {
        foreach ($serviceEligibility['predictions'] as $pred) {
            if (isset($pred['resident']->id)) {
                $eligibilityMap[$pred['resident']->id] = $pred['predicted'];
            }
        }
    }
    
    $riskMap = [];
    if (isset($healthRisk['testing_predictions']) && !isset($healthRisk['error'])) {
        foreach ($healthRisk['testing_predictions'] as $pred) {
            if (isset($pred['resident']->id)) {
                $riskMap[$pred['resident']->id] = $pred['predicted'];
            }
        }
    }
    
    $programMap = [];
    if (isset($programRecommendation['testing_predictions']) && !isset($programRecommendation['error'])) {
        foreach ($programRecommendation['testing_predictions'] as $pred) {
            if (isset($pred['resident']->id)) {
                $programMap[$pred['resident']->id] = $pred['predicted'];
            }
        }
    }
    
    // Get enhanced metrics from Python models
    $healthRiskMetrics = $healthRisk['model_info']['metrics'] ?? [];
    $eligibilityMetrics = $serviceEligibility['model_info']['metrics'] ?? [];
    $programMetrics = $programRecommendation['model_info']['metrics'] ?? [];
    
    // Feature importance (named if available)
    $healthRiskFeatures = $healthRisk['model_info']['feature_importance_named'] ?? $healthRisk['model_info']['feature_importance'] ?? [];
    $eligibilityFeatures = $serviceEligibility['model_info']['feature_importance_named'] ?? $serviceEligibility['model_info']['feature_importance'] ?? [];
    
    // Get model types
    $eligibilityModelType = $modelTypes['eligibility'] ?? 'decision_tree';
    $healthRiskModelType = $modelTypes['healthRisk'] ?? 'random_forest';
    $programModelType = $modelTypes['program'] ?? 'random_forest';
    
    // Get model display names
    $modelTypeNames = config('decision_tree.model_type_names', []);
    $eligibilityModelName = $modelTypeNames[$eligibilityModelType] ?? ucfirst(str_replace('_', ' ', $eligibilityModelType));
    $healthRiskModelName = $modelTypeNames[$healthRiskModelType] ?? ucfirst(str_replace('_', ' ', $healthRiskModelType));
    
    // Helper function to get status badge color
    $getStatusColor = function($type, $value) use ($statusColors) {
        $typeColors = $statusColors[$type] ?? [];
        return $typeColors[$value] ?? ($typeColors['default'] ?? 'bg-gray-100 text-gray-800');
    };
    
    // Helper function to get program description
    $getProgramDescription = function($program) use ($programDescriptions) {
        return $programDescriptions[$program] ?? 'Community support program';
    };
@endphp
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Decision Tree Skeleton -->
    <div id="decisionTreeSkeleton">
        @include('components.loading.decision-tree-skeleton')
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="decisionTreeContent" style="display: none;">
    <!-- Enhanced Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Decision Tree Analytics</h1>
                <p class="text-gray-600 text-lg">AI-powered classification for service eligibility and health risk assessment</p>
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

    @if(isset($errors) && !empty($errors))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Analysis Errors</h3>
                    <div class="mt-2 text-sm text-red-700">
                        @foreach($errors as $key => $error)
                            <p>{{ is_array($error) ? json_encode($error) : $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Performance Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Residents Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Residents</p>
                        <p class="text-white text-3xl font-bold">{{ $sampleSize ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Eligibility Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Service Eligibility</p>
                        <p class="text-white text-3xl font-bold">
                            @if(isset($serviceEligibility['accuracy']) && !isset($serviceEligibility['error']))
                                {{ round($serviceEligibility['accuracy'] * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-check-circle text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Health Risk Assessment Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Health Risk Assessment</p>
                        <p class="text-white text-3xl font-bold">
                            @if(isset($healthRisk['accuracy']) && !isset($healthRisk['error']))
                                {{ round($healthRisk['accuracy'] * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-heartbeat text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Program Recommendation Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Program Recommendation</p>
                        <p class="text-white text-3xl font-bold">
                            @if(isset($programRecommendation['accuracy']) && !isset($programRecommendation['error']))
                                {{ round($programRecommendation['accuracy'] * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </p>
                    </div>
                    <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-lightbulb text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Analysis Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Service Eligibility Analysis -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        Service Eligibility Analysis
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        @if(isset($serviceEligibility['accuracy']) && !isset($serviceEligibility['error']))
                            {{ round($serviceEligibility['accuracy'] * 100, 1) }}% Accuracy
                        @else
                            0% Accuracy
                        @endif
                    </span>
                </div>
                <div class="space-y-4">
                    <!-- Threshold control (binary eligibility) -->
                    @php
                        $hasEligibilityProb = isset($serviceEligibility['predictions']) && collect($serviceEligibility['predictions'])->contains(function($p){ return isset($p['probability']); });
                    @endphp
                    @if($hasEligibilityProb)
                    <div class="flex items-center gap-3">
                        <label class="text-sm text-gray-700 whitespace-nowrap"><i class="fas fa-sliders-h mr-1"></i>Decision Threshold</label>
                        <input id="eligibilityThreshold" type="range" min="0" max="100" step="1" value="50" class="w-56">
                        <span id="eligibilityThresholdValue" class="text-sm text-gray-700">50%</span>
                    </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <p id="eligibleCount" class="text-2xl font-bold text-green-600">{{ $serviceEligibility['eligible_count'] ?? 0 }}</p>
                            <p class="text-sm text-green-700">Eligible</p>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <p id="ineligibleCount" class="text-2xl font-bold text-red-600">{{ $serviceEligibility['ineligible_count'] ?? 0 }}</p>
                            <p class="text-sm text-red-700">Ineligible</p>
                        </div>
                    </div>
                    @if(isset($eligibilityFeatures) && !empty($eligibilityFeatures) && !isset($serviceEligibility['error']))
                        <div class="space-y-2">
                            <h4 class="text-sm font-semibold text-gray-900">Feature Importance ({{ $eligibilityModelName }} Model):</h4>
                            <div class="space-y-2">
                                @if(is_array($eligibilityFeatures))
                                    @php
                                        $featureDisplayNames = config('decision_tree.feature_display_names', []);
                                        $maxFeatures = min(5, count($eligibilityFeatures));
                                    @endphp
                                    @foreach(array_slice($eligibilityFeatures, 0, $maxFeatures, true) as $index => $importance)
                                        @php
                                            $featureName = is_numeric($index) 
                                                ? ($featureDisplayNames[$index] ?? 'Feature ' . ($index + 1))
                                                : ucfirst(str_replace('_', ' ', $index));
                                        @endphp
                                        <div class="p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-medium text-gray-700">{{ $featureName }}</span>
                                                <span class="text-xs text-gray-500">{{ round($importance * 100, 1) }}%</span>
                                            </div>
                                            <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ round($importance * 100, 1) }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endif
                    @if(isset($eligibilityMetrics['test_precision']) && !isset($serviceEligibility['error']))
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                @if(isset($eligibilityMetrics['test_precision']))
                                <div>
                                    <span class="text-gray-600">Precision:</span>
                                    <span class="font-semibold text-blue-700">{{ round($eligibilityMetrics['test_precision'] * 100, 1) }}%</span>
                                </div>
                                @endif
                                @if(isset($eligibilityMetrics['test_recall']))
                                <div>
                                    <span class="text-gray-600">Recall:</span>
                                    <span class="font-semibold text-blue-700">{{ round($eligibilityMetrics['test_recall'] * 100, 1) }}%</span>
                                </div>
                                @endif
                                @if(isset($eligibilityMetrics['test_f1_score']))
                                <div>
                                    <span class="text-gray-600">F1-Score:</span>
                                    <span class="font-semibold text-blue-700">{{ round($eligibilityMetrics['test_f1_score'] * 100, 1) }}%</span>
                                </div>
                                @endif
                                @if(isset($eligibilityMetrics['roc_auc_score']))
                                <div>
                                    <span class="text-gray-600">ROC-AUC:</span>
                                    <span class="font-semibold text-blue-700">{{ round($eligibilityMetrics['roc_auc_score'] * 100, 1) }}%</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Health Risk Assessment -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-heartbeat text-purple-600 mr-2"></i>
                        Health Risk Assessment
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        @if(isset($healthRisk['accuracy']) && !isset($healthRisk['error']))
                            {{ round($healthRisk['accuracy'] * 100, 1) }}% Accuracy
                        @else
                            0% Accuracy
                        @endif
                    </span>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <p class="text-lg font-bold text-green-600">{{ $healthRisk['low_count'] ?? 0 }}</p>
                            <p class="text-xs text-green-700">Low Risk</p>
                        </div>
                        <div class="text-center p-3 bg-yellow-50 rounded-lg">
                            <p class="text-lg font-bold text-yellow-600">{{ $healthRisk['medium_count'] ?? 0 }}</p>
                            <p class="text-xs text-yellow-700">Medium Risk</p>
                        </div>
                        <div class="text-center p-3 bg-red-50 rounded-lg">
                            <p class="text-lg font-bold text-red-600">{{ $healthRisk['high_count'] ?? 0 }}</p>
                            <p class="text-xs text-red-700">High Risk</p>
                        </div>
                    </div>
                    @if(isset($healthRiskFeatures) && !empty($healthRiskFeatures) && !isset($healthRisk['error']))
                        <div class="space-y-2">
                            <h4 class="text-sm font-semibold text-gray-900">Feature Importance ({{ $healthRiskModelName }} Model):</h4>
                            <div class="space-y-2">
                                @if(is_array($healthRiskFeatures))
                                    @php
                                        $featureDisplayNames = config('decision_tree.feature_display_names', []);
                                        $maxFeatures = min(5, count($healthRiskFeatures));
                                    @endphp
                                    @foreach(array_slice($healthRiskFeatures, 0, $maxFeatures, true) as $index => $importance)
                                        @php
                                            $featureName = is_numeric($index) 
                                                ? ($featureDisplayNames[$index] ?? 'Feature ' . ($index + 1))
                                                : ucfirst(str_replace('_', ' ', $index));
                                        @endphp
                                        <div class="p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-medium text-gray-700">{{ $featureName }}</span>
                                                <span class="text-xs text-gray-500">{{ round($importance * 100, 1) }}%</span>
                                            </div>
                                            <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ round($importance * 100, 1) }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endif
                    @if(isset($healthRiskMetrics['test_precision']) && !isset($healthRisk['error']))
                        <div class="mt-4 p-3 bg-purple-50 rounded-lg">
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                @if(isset($healthRiskMetrics['test_precision']))
                                <div>
                                    <span class="text-gray-600">Precision:</span>
                                    <span class="font-semibold text-purple-700">{{ round($healthRiskMetrics['test_precision'] * 100, 1) }}%</span>
                                </div>
                                @endif
                                @if(isset($healthRiskMetrics['test_recall']))
                                <div>
                                    <span class="text-gray-600">Recall:</span>
                                    <span class="font-semibold text-purple-700">{{ round($healthRiskMetrics['test_recall'] * 100, 1) }}%</span>
                                </div>
                                @endif
                                @if(isset($healthRiskMetrics['test_f1_score']))
                                <div>
                                    <span class="text-gray-600">F1-Score:</span>
                                    <span class="font-semibold text-purple-700">{{ round($healthRiskMetrics['test_f1_score'] * 100, 1) }}%</span>
                                </div>
                                @endif
                                @if(isset($healthRiskMetrics['roc_auc_score']))
                                <div>
                                    <span class="text-gray-600">ROC-AUC:</span>
                                    <span class="font-semibold text-purple-700">{{ round($healthRiskMetrics['roc_auc_score'] * 100, 1) }}%</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Program Recommendation Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-8">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-lightbulb text-orange-600 mr-2"></i>
                    Program Recommendations
                </h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                    @if(isset($programRecommendation['accuracy']) && !isset($programRecommendation['error']))
                        {{ round($programRecommendation['accuracy'] * 100, 1) }}% Accuracy
                    @else
                        0% Accuracy
                    @endif
                </span>
            </div>
            @if(isset($programRecommendation['recommendations']) && !isset($programRecommendation['error']) && count($programRecommendation['recommendations']) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($programRecommendation['recommendations'] as $program => $count)
                        <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-all duration-200">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-semibold text-gray-900">{{ $program }}</h4>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $count }} {{ $count == 1 ? 'resident' : 'residents' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-600">
                                {{ $getProgramDescription($program) }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-info-circle text-2xl mb-2"></i>
                    <p>No program recommendations available</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Toggle for Detailed Resident Analysis -->
    <div class="flex justify-start mb-2">
        <button id="toggleResidentsBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
            <i class="fas fa-chevron-down mr-2"></i>
            <span class="btn-text"> Show Details</span>
        </button>
    </div>

    <!-- Detailed Analysis Table -->
    @if(isset($residents) && $residents->count() > 0)
        <div id="detailedResidentSection" class="bg-white rounded-xl shadow-lg border border-gray-100 mb-8 hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-table text-blue-600 mr-2"></i>
                        Detailed Resident Analysis
                    </h3>
                    <div class="flex space-x-4">
                        <input type="text" id="searchTable" placeholder="Search residents..." class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <select id="filterCategory" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Categories</option>
                            <option value="eligible">Eligible for Services</option>
                            <option value="ineligible">Ineligible for Services</option>
                            <option value="low_risk">Low Health Risk</option>
                            <option value="medium_risk">Medium Health Risk</option>
                            <option value="high_risk">High Health Risk</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="residentsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resident Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Income</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PWD</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Eligibility</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Health Risk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recommended Program</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blotter Reports</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($residents as $resident)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $resident->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $resident->age ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $getStatusColor('income_level', $resident->income_level ?? '') }}">
                                            {{ $resident->income_level ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $getStatusColor('employment_status', $resident->employment_status ?? '') }}">
                                            {{ $resident->employment_status ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($resident->is_pwd ?? false) ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ($resident->is_pwd ?? false) ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $predictedEligibility = $eligibilityMap[$resident->id] ?? null;
                                            $eligibilityStatus = $predictedEligibility ?? 'N/A';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $getStatusColor('eligibility', $eligibilityStatus) }}">
                                            {{ $eligibilityStatus }}
                                            @if($predictedEligibility !== null)
                                                <i class="fas fa-robot ml-1 text-xs" title="AI Prediction"></i>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $predictedRisk = $riskMap[$resident->id] ?? null;
                                            $riskLevel = $predictedRisk ?? 'N/A';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $getStatusColor('risk_level', $riskLevel) }}">
                                            {{ $riskLevel !== 'N/A' ? $riskLevel . ' Risk' : 'N/A' }}
                                            @if($predictedRisk !== null)
                                                <i class="fas fa-robot ml-1 text-xs" title="AI Prediction"></i>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $predictedProgram = $programMap[$resident->id] ?? null;
                                            $program = $predictedProgram ?? 'N/A';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $program }}
                                            @if($predictedProgram !== null)
                                                <i class="fas fa-robot ml-1 text-xs" title="AI Recommendation"></i>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $resident->blotter_count ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button type="button" data-resident-id="{{ $resident->id }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200 js-view-resident">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Insights and Recommendations -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">
                <i class="fas fa-chart-line text-indigo-600 mr-2"></i>
                Analytics Insights
            </h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-md font-semibold text-blue-900 mb-4">
                        <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                        Key Performance Metrics
                    </h4>
                    <div class="space-y-4">
                        @if(isset($serviceEligibility['accuracy']) && !isset($serviceEligibility['error']))
                        <div class="p-4 border-l-4 border-green-500 bg-green-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h5 class="text-sm font-medium text-green-900">Service Eligibility Accuracy</h5>
                                    <p class="text-sm text-green-700">Model accuracy for service eligibility classification</p>
                                </div>
                                <span class="text-2xl font-bold text-green-600">
                                    {{ round($serviceEligibility['accuracy'] * 100, 1) }}%
                                </span>
                                @if(isset($eligibilityMetrics['cv_mean']))
                                    <p class="text-xs text-green-600 mt-1">CV Score: {{ round($eligibilityMetrics['cv_mean'] * 100, 1) }}% ± {{ round($eligibilityMetrics['cv_std'] * 100, 1) }}%</p>
                                @endif
                            </div>
                        </div>
                        @endif
                        @if(isset($healthRisk['accuracy']) && !isset($healthRisk['error']))
                        <div class="p-4 border-l-4 border-purple-500 bg-purple-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h5 class="text-sm font-medium text-purple-900">Health Risk Assessment</h5>
                                    <p class="text-sm text-purple-700">Model accuracy for health risk classification</p>
                                </div>
                                <span class="text-2xl font-bold text-purple-600">
                                    {{ round($healthRisk['accuracy'] * 100, 1) }}%
                                </span>
                                @if(isset($healthRiskMetrics['cv_mean']))
                                    <p class="text-xs text-purple-600 mt-1">CV Score: {{ round($healthRiskMetrics['cv_mean'] * 100, 1) }}% ± {{ round($healthRiskMetrics['cv_std'] * 100, 1) }}%</p>
                                @endif
                            </div>
                        </div>
                        @endif
                        @if(isset($programRecommendation['accuracy']) && !isset($programRecommendation['error']))
                        <div class="p-4 border-l-4 border-orange-500 bg-orange-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h5 class="text-sm font-medium text-orange-900">Program Recommendations</h5>
                                    <p class="text-sm text-orange-700">Model accuracy for program recommendation</p>
                                </div>
                                <span class="text-2xl font-bold text-orange-600">
                                    {{ round($programRecommendation['accuracy'] * 100, 1) }}%
                                </span>
                                @if(isset($programMetrics['cv_mean']))
                                    <p class="text-xs text-orange-600 mt-1">CV Score: {{ round($programMetrics['cv_mean'] * 100, 1) }}% ± {{ round($programMetrics['cv_std'] * 100, 1) }}%</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div>
                    <h4 class="text-md font-semibold text-green-900 mb-4">
                        <i class="fas fa-lightbulb text-green-600 mr-2"></i>
                        Strategic Recommendations
                    </h4>
                    <div class="space-y-4">
                        @if(isset($serviceEligibility['eligible_count']) && $serviceEligibility['eligible_count'] > 0)
                        <div class="p-4 border-l-4 border-blue-500 bg-blue-50 rounded-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-users text-white text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-blue-900">Service Optimization</h5>
                                    <p class="text-sm text-blue-700 mt-1">
                                        {{ $serviceEligibility['eligible_count'] }} residents are eligible for services. Focus on priority cases for immediate assistance.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if(isset($healthRisk['high_count']) && $healthRisk['high_count'] > 0)
                        <div class="p-4 border-l-4 border-green-500 bg-green-50 rounded-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-heartbeat text-white text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-green-900">Health Intervention</h5>
                                    <p class="text-sm text-green-700 mt-1">
                                        {{ $healthRisk['high_count'] }} residents identified as high-risk. Prioritize health programs for these individuals.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if(isset($programRecommendation['recommendations']) && count($programRecommendation['recommendations']) > 0)
                        <div class="p-4 border-l-4 border-purple-500 bg-purple-50 rounded-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-graduation-cap text-white text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-purple-900">Program Development</h5>
                                    <p class="text-sm text-purple-700 mt-1">
                                        {{ count($programRecommendation['recommendations']) }} different programs recommended. Develop targeted programs based on resident needs.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const skeleton = document.getElementById('decisionTreeSkeleton');
        if (skeleton) skeleton.style.display = 'none';

        const content = document.getElementById('decisionTreeContent');
        if (content) content.style.display = 'block';
    }, 1000);
    
    // Initialize table search and filter
    initializeTableFeatures();
    // Eligibility threshold tuning (client-side)
    try {
        const thresholdInput = document.getElementById('eligibilityThreshold');
        if (thresholdInput) {
            const thresholdValue = document.getElementById('eligibilityThresholdValue');
            const eligibleCountEl = document.getElementById('eligibleCount');
            const ineligibleCountEl = document.getElementById('ineligibleCount');
            // Read probability map from embedded JSON
            let probMap = {};
            const probEl = document.getElementById('eligibility-probabilities');
            if (probEl) {
                try { probMap = JSON.parse(probEl.textContent || '{}'); } catch(e) { probMap = {}; }
            }
            function applyThreshold(pct) {
                const rows = document.querySelectorAll('#residentsTable tbody tr');
                let eligible = 0, ineligible = 0;
                rows.forEach(row => {
                    const idCell = row.querySelector('[data-resident-id]');
                    const residentId = idCell ? idCell.getAttribute('data-resident-id') : (row.querySelector('button.js-view-resident')?.getAttribute('data-resident-id'));
                    const eligCell = row.querySelector('td:nth-child(6) span');
                    if (!residentId || !eligCell) return;
                    const p = probMap[residentId];
                    if (p == null) {
                        // No probability available; keep as-is
                        const isEligible = eligCell.textContent.includes('Eligible');
                        if (isEligible) eligible++; else ineligible++;
                        return;
                    }
                    const isEligibleNow = (p * 100) >= pct;
                    eligCell.textContent = isEligibleNow ? 'Eligible' : 'Ineligible';
                    eligCell.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' + (isEligibleNow ? '{{ $statusColors['eligibility']['Eligible'] ?? 'bg-green-100 text-green-800' }}' : '{{ $statusColors['eligibility']['Ineligible'] ?? 'bg-red-100 text-red-800' }}');
                    if (isEligibleNow) eligible++; else ineligible++;
                });
                if (eligibleCountEl) eligibleCountEl.textContent = eligible;
                if (ineligibleCountEl) ineligibleCountEl.textContent = ineligible;
            }
            thresholdInput.addEventListener('input', function() {
                const val = parseInt(this.value, 10) || 50;
                if (thresholdValue) thresholdValue.textContent = val + '%';
                applyThreshold(val);
            });
            // Initial apply
            applyThreshold(parseInt(thresholdInput.value, 10) || 50);
        }
    } catch (e) { console.warn('Threshold tuning init failed:', e); }
    
    // View resident functionality
    document.querySelectorAll('.js-view-resident').forEach(button => {
        button.addEventListener('click', function() {
            const residentId = this.getAttribute('data-resident-id');
            if (residentId) {
                window.open(`/admin/residents/${residentId}`, '_blank');
            }
        });
    });

    // Initialize residents details collapsed
    const details = document.getElementById('detailedResidentSection');
    const toggleBtn = document.getElementById('toggleResidentsBtn');
    if (details && toggleBtn) {
        details.classList.add('hidden');
        const icon = toggleBtn.querySelector('i');
        if (icon) { icon.classList.remove('fa-chevron-up'); icon.classList.add('fa-chevron-down'); }
        const textEl = toggleBtn.querySelector('.btn-text');
        if (textEl) { textEl.textContent = ' Show Details'; }
        toggleBtn.setAttribute('data-state', 'hidden');
        toggleBtn.addEventListener('click', function() {
            const isHidden = details.classList.contains('hidden');
            if (isHidden) {
                details.classList.remove('hidden');
                if (icon) { icon.classList.remove('fa-chevron-down'); icon.classList.add('fa-chevron-up'); }
                if (textEl) { textEl.textContent = ' Hide Details'; }
                this.setAttribute('data-state', 'shown');
            } else {
                details.classList.add('hidden');
                if (icon) { icon.classList.remove('fa-chevron-up'); icon.classList.add('fa-chevron-down'); }
                if (textEl) { textEl.textContent = ' Show Details'; }
                this.setAttribute('data-state', 'hidden');
            }
        });
    }
});

function initializeTableFeatures() {
    const searchInput = document.getElementById('searchTable');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#residentsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
    
    const filterSelect = document.getElementById('filterCategory');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const selectedCategory = this.value;
            const rows = document.querySelectorAll('#residentsTable tbody tr');
            
            rows.forEach(row => {
                const eligibilityCell = row.querySelector('td:nth-child(6)');
                const riskCell = row.querySelector('td:nth-child(7)');
                
                let show = true;
                if (selectedCategory) {
                    const eligibility = eligibilityCell ? eligibilityCell.textContent.trim() : '';
                    const risk = riskCell ? riskCell.textContent.trim() : '';
                    
                    if (selectedCategory === 'eligible' && !eligibility.includes('Eligible')) show = false;
                    if (selectedCategory === 'ineligible' && !eligibility.includes('Ineligible')) show = false;
                    if (selectedCategory === 'low_risk' && !risk.includes('Low')) show = false;
                    if (selectedCategory === 'medium_risk' && !risk.includes('Medium')) show = false;
                    if (selectedCategory === 'high_risk' && !risk.includes('High')) show = false;
                }
                
                row.style.display = show ? '' : 'none';
            });
        });
    }
}

function refreshAnalysis() {
    location.reload();
}

function exportData() {
    const link = document.createElement('a');
    link.href = '{{ route("admin.decision-tree.export") }}?format=csv';
    link.download = 'decision_tree_results.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
<script type="application/json" id="eligibility-probabilities">
{!! json_encode(collect($serviceEligibility['predictions'] ?? [])->mapWithKeys(function($p){
    if (isset($p['resident']->id)) {
        $val = (isset($p['probability']) && is_numeric($p['probability'])) ? (float)$p['probability'] : null;
        return [$p['resident']->id => $val];
    }
    return [];
}), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

<style>
@media print {
    .btn, .flex.space-x-4 {
        display: none !important;
    }
}

@media (max-width: 768px) {
    .flex.items-center.justify-between {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
}
</style>
@endsection
