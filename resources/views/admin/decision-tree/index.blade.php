@extends('admin.main.layout')

@section('title', 'Decision Tree Analytics')

@section('content')
@php
    // Helper function to safely get resident ID from various structures
    $getResidentId = function($pred) {
        // Try direct resident_id first
        if (isset($pred['resident_id'])) {
            return $pred['resident_id'];
        }
        // Try nested resident object/array
        if (isset($pred['resident'])) {
            $resident = $pred['resident'];
            if (is_object($resident)) {
                // Try direct property access first
                if (isset($resident->id)) {
                    return $resident->id;
                }
                if (isset($resident->resident_id)) {
                    return $resident->resident_id;
                }
                // Convert to array as fallback
                $residentArray = (array)$resident;
                return $residentArray['id'] ?? $residentArray['resident_id'] ?? null;
            } elseif (is_array($resident)) {
                return $resident['id'] ?? $resident['resident_id'] ?? null;
            }
        }
        return null;
    };
    
    // Create lookup maps for predictions by resident ID
    // Handle multiple possible data structures from Python service
    $eligibilityMap = [];
    if (isset($serviceEligibility['predictions']) && !isset($serviceEligibility['error'])) {
        foreach ($serviceEligibility['predictions'] as $pred) {
            // Try to get resident ID from various possible structures
            $residentId = $pred['resident_id'] ?? null;
            if (!$residentId && isset($pred['resident'])) {
                $residentId = $getResidentId($pred);
            }
            if ($residentId !== null) {
                $predicted = $pred['predicted'] ?? $pred['predicted_eligibility'] ?? null;
                if ($predicted && $predicted !== 'N/A' && $predicted !== '') {
                    $eligibilityMap[$residentId] = $predicted;
                }
            }
        }
    }
    
    $riskMap = [];
    // Check testing_predictions first
    if (isset($healthRisk['testing_predictions']) && !isset($healthRisk['error'])) {
        foreach ($healthRisk['testing_predictions'] as $pred) {
            // Try multiple ways to get resident ID
            $residentId = $pred['resident_id'] ?? null;
            if (!$residentId && isset($pred['resident'])) {
                $residentId = $getResidentId($pred);
            }
            // Also try to get from nested structure
            if (!$residentId && isset($pred['resident']) && is_object($pred['resident'])) {
                $residentId = $pred['resident']->id ?? null;
            }
            // Try as string too
            if ($residentId !== null) {
                $predicted = $pred['predicted'] ?? $pred['predicted_risk'] ?? null;
                if ($predicted && $predicted !== 'N/A' && $predicted !== '' && $predicted !== null) {
                    // Store for both integer and string keys to handle type mismatches
                    $riskMap[$residentId] = $predicted;
                    $riskMap[(string)$residentId] = $predicted;
                }
            }
        }
    }
    // Fallback to predictions if testing_predictions is empty
    if (empty($riskMap) && isset($healthRisk['predictions']) && !isset($healthRisk['error'])) {
        foreach ($healthRisk['predictions'] as $pred) {
            $residentId = $pred['resident_id'] ?? null;
            if (!$residentId && isset($pred['resident'])) {
                $residentId = $getResidentId($pred);
            }
            if (!$residentId && isset($pred['resident']) && is_object($pred['resident'])) {
                $residentId = $pred['resident']->id ?? null;
            }
            if ($residentId !== null) {
                $predicted = $pred['predicted'] ?? $pred['predicted_risk'] ?? null;
                if ($predicted && $predicted !== 'N/A' && $predicted !== '' && $predicted !== null) {
                    // Store for both integer and string keys
                    $riskMap[$residentId] = $predicted;
                    $riskMap[(string)$residentId] = $predicted;
                }
            }
        }
    }
    
    $programMap = [];
    // Check testing_predictions first
    if (isset($programRecommendation['testing_predictions']) && !isset($programRecommendation['error'])) {
        foreach ($programRecommendation['testing_predictions'] as $pred) {
            $residentId = $pred['resident_id'] ?? null;
            if (!$residentId && isset($pred['resident'])) {
                $residentId = $getResidentId($pred);
            }
            if ($residentId !== null) {
                $predicted = $pred['predicted'] ?? null;
                if ($predicted && $predicted !== 'N/A' && $predicted !== 'Unknown' && $predicted !== '') {
                    $programMap[$residentId] = $predicted;
                }
            }
        }
    }
    // Fallback to predictions if testing_predictions is empty
    if (empty($programMap) && isset($programRecommendation['predictions']) && !isset($programRecommendation['error'])) {
        foreach ($programRecommendation['predictions'] as $pred) {
            $residentId = $pred['resident_id'] ?? null;
            if (!$residentId && isset($pred['resident'])) {
                $residentId = $getResidentId($pred);
            }
            if ($residentId !== null) {
                $predicted = $pred['predicted'] ?? null;
                if ($predicted && $predicted !== 'N/A' && $predicted !== 'Unknown' && $predicted !== '') {
                    $programMap[$residentId] = $predicted;
                }
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
                <p class="text-gray-600 text-lg">Data-driven predictions for service eligibility, health risk assessment, and program recommendations</p>
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

    <!-- Model Performance Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Service Eligibility Model -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    Service Eligibility
                </h3>
                <span class="text-xs text-gray-500">{{ $eligibilityModelName }}</span>
            </div>
            @if(isset($serviceEligibility['accuracy']) && !isset($serviceEligibility['error']))
                <div class="mb-4">
                    <div class="flex items-baseline justify-between mb-2">
                        <span class="text-sm text-gray-600">Model Accuracy</span>
                        <span class="text-2xl font-bold text-green-600">{{ round($serviceEligibility['accuracy'] * 100, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-green-600 h-3 rounded-full" style="width: {{ round($serviceEligibility['accuracy'] * 100, 1) }}%"></div>
                    </div>
                </div>
                @if(isset($eligibilityMetrics['test_precision']) || isset($eligibilityMetrics['test_recall']))
                <div class="space-y-2 text-xs">
                    @if(isset($eligibilityMetrics['test_precision']))
                    <div class="flex justify-between">
                        <span class="text-gray-600">Precision:</span>
                        <span class="font-semibold">{{ round($eligibilityMetrics['test_precision'] * 100, 1) }}%</span>
                    </div>
                    @endif
                    @if(isset($eligibilityMetrics['test_recall']))
                    <div class="flex justify-between">
                        <span class="text-gray-600">Recall:</span>
                        <span class="font-semibold">{{ round($eligibilityMetrics['test_recall'] * 100, 1) }}%</span>
                    </div>
                    @endif
                </div>
                @endif
                <div class="mt-4 pt-4 border-t">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="text-gray-600">Eligible:</span>
                            <span class="font-bold text-green-600 ml-1">{{ $serviceEligibility['eligible_count'] ?? 0 }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Ineligible:</span>
                            <span class="font-bold text-red-600 ml-1">{{ $serviceEligibility['ineligible_count'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500">Model not available</p>
            @endif
        </div>

        <!-- Health Risk Model -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-heartbeat text-purple-600 mr-2"></i>
                    Health Risk
                </h3>
                <span class="text-xs text-gray-500">{{ $healthRiskModelName }}</span>
            </div>
            @if(isset($healthRisk['accuracy']) && !isset($healthRisk['error']))
                <div class="mb-4">
                    <div class="flex items-baseline justify-between mb-2">
                        <span class="text-sm text-gray-600">Model Accuracy</span>
                        <span class="text-2xl font-bold text-purple-600">{{ round($healthRisk['accuracy'] * 100, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-purple-600 h-3 rounded-full" style="width: {{ round($healthRisk['accuracy'] * 100, 1) }}%"></div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t">
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <div class="text-center">
                            <div class="font-bold text-green-600">{{ $healthRisk['low_count'] ?? 0 }}</div>
                            <div class="text-gray-600">Low</div>
                        </div>
                        <div class="text-center">
                            <div class="font-bold text-yellow-600">{{ $healthRisk['medium_count'] ?? 0 }}</div>
                            <div class="text-gray-600">Medium</div>
                        </div>
                        <div class="text-center">
                            <div class="font-bold text-red-600">{{ $healthRisk['high_count'] ?? 0 }}</div>
                            <div class="text-gray-600">High</div>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500">Model not available</p>
            @endif
        </div>

        <!-- Program Recommendation Model -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-lightbulb text-orange-600 mr-2"></i>
                    Program Recommendation
                </h3>
                <span class="text-xs text-gray-500">{{ $programModelType }}</span>
            </div>
            @if(isset($programRecommendation['accuracy']) && !isset($programRecommendation['error']))
                <div class="mb-4">
                    <div class="flex items-baseline justify-between mb-2">
                        <span class="text-sm text-gray-600">Model Accuracy</span>
                        <span class="text-2xl font-bold text-orange-600">{{ round($programRecommendation['accuracy'] * 100, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-orange-600 h-3 rounded-full" style="width: {{ round($programRecommendation['accuracy'] * 100, 1) }}%"></div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t">
                    <div class="text-xs text-gray-600 mb-1">Programs Recommended:</div>
                    <div class="font-bold text-orange-600">{{ count($programRecommendation['recommendations'] ?? []) }}</div>
                </div>
            @else
                <p class="text-sm text-gray-500">Model not available</p>
            @endif
        </div>
    </div>

    <!-- Feature Importance Section -->
    @if((isset($eligibilityFeatures) && !empty($eligibilityFeatures)) || (isset($healthRiskFeatures) && !empty($healthRiskFeatures)))
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-8">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                What Drives These Predictions?
            </h3>
            <p class="text-sm text-gray-600 mb-6">Feature importance shows which factors the models consider most when making predictions. Higher values indicate stronger influence.</p>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @if(isset($eligibilityFeatures) && !empty($eligibilityFeatures) && !isset($serviceEligibility['error']))
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Service Eligibility Features</h4>
                    <div class="space-y-3">
                        @php
                            $featureDisplayNames = config('decision_tree.feature_display_names', [
                                0 => 'Age',
                                1 => 'Family Size',
                                2 => 'Education Level',
                                3 => 'Income Level',
                                4 => 'Employment Status',
                                5 => 'PWD Status',
                                6 => 'Cluster ID'
                            ]);
                            $maxFeatures = min(5, count($eligibilityFeatures));
                            $sortedFeatures = is_array($eligibilityFeatures) ? array_slice($eligibilityFeatures, 0, $maxFeatures, true) : [];
                            if (is_array($sortedFeatures)) {
                                arsort($sortedFeatures);
                            }
                        @endphp
                        @if(!empty($sortedFeatures))
                            @foreach($sortedFeatures as $index => $importance)
                                @php
                                    $featureName = is_numeric($index) 
                                        ? ($featureDisplayNames[$index] ?? 'Feature ' . ($index + 1))
                                        : ucfirst(str_replace('_', ' ', $index));
                                    $percentage = round($importance * 100, 1);
                                @endphp
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-gray-700">{{ $featureName }}</span>
                                        <span class="font-semibold text-green-600">{{ $percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-xs text-gray-500">Feature importance data not available</p>
                        @endif
                    </div>
                </div>
                @endif

                @if(isset($healthRiskFeatures) && !empty($healthRiskFeatures) && !isset($healthRisk['error']))
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Health Risk Features</h4>
                    <div class="space-y-3">
                        @php
                            $featureDisplayNames = config('decision_tree.feature_display_names', [
                                0 => 'Age',
                                1 => 'Family Size',
                                2 => 'Education Level',
                                3 => 'Income Level',
                                4 => 'Employment Status',
                                5 => 'Cluster ID'
                            ]);
                            $maxFeatures = min(5, count($healthRiskFeatures));
                            $sortedFeatures = is_array($healthRiskFeatures) ? array_slice($healthRiskFeatures, 0, $maxFeatures, true) : [];
                            if (is_array($sortedFeatures)) {
                                arsort($sortedFeatures);
                            }
                        @endphp
                        @if(!empty($sortedFeatures))
                            @foreach($sortedFeatures as $index => $importance)
                                @php
                                    $featureName = is_numeric($index) 
                                        ? ($featureDisplayNames[$index] ?? 'Feature ' . ($index + 1))
                                        : ucfirst(str_replace('_', ' ', $index));
                                    $percentage = round($importance * 100, 1);
                                @endphp
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-gray-700">{{ $featureName }}</span>
                                        <span class="font-semibold text-purple-600">{{ $percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-xs text-gray-500">Feature importance data not available</p>
                        @endif
                    </div>
                </div>
                @endif

                @if(isset($clusteringData) && !empty($clusteringData['cluster_labels']))
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Cluster Integration</h4>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-xs text-blue-800 mb-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Cluster membership</strong> is used as an additional feature to improve prediction accuracy.
                        </p>
                        <p class="text-xs text-blue-800">
                            This helps the models consider group characteristics when making individual predictions, leading to more context-aware results.
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Cluster-Based Analysis (if available) -->
    @if(isset($clusteringData) && !empty($clusteringData['cluster_labels']))
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-8">
        <div class="p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    <i class="fas fa-project-diagram text-purple-600 mr-2"></i>
                    Predictions by Cluster Group
                </h3>
                <p class="text-sm text-gray-600">See how predictions are distributed across different resident groups. This helps identify patterns and target interventions.</p>
            </div>
            
            <!-- Cluster Summary Cards -->
            @php
                $clusterStats = [];
                foreach ($residents as $resident) {
                    // Extract resident ID properly
                    $residentId = null;
                    if (is_object($resident)) {
                        $residentId = $resident->id ?? null;
                        if (!$residentId) {
                            $residentArray = (array)$resident;
                            $residentId = $residentArray['id'] ?? $residentArray['resident_id'] ?? null;
                        }
                    } elseif (is_array($resident)) {
                        $residentId = $resident['id'] ?? $resident['resident_id'] ?? null;
                    }
                    
                    if (!$residentId) continue;
                    
                    // Get cluster info
                    $clusterId = null;
                    $clusterLabel = null;
                    if (is_object($resident)) {
                        $clusterId = $resident->cluster_id ?? null;
                        $clusterLabel = $resident->cluster_label ?? null;
                    } else {
                        $clusterId = $resident['cluster_id'] ?? null;
                        $clusterLabel = $resident['cluster_label'] ?? null;
                    }
                    
                    // If no cluster assigned, skip or assign to "Unassigned"
                    if (!$clusterId && !$clusterLabel) {
                        $clusterId = 'unassigned';
                        $clusterLabel = 'Unassigned';
                    }
                    
                    if (!isset($clusterStats[$clusterId])) {
                        $clusterStats[$clusterId] = [
                            'label' => $clusterLabel ?? 'Cluster ' . $clusterId,
                            'total' => 0,
                            'eligible' => 0,
                            'ineligible' => 0,
                            'low_risk' => 0,
                            'medium_risk' => 0,
                            'high_risk' => 0,
                            'programs' => []
                        ];
                    }
                    
                    $clusterStats[$clusterId]['total']++;
                    
                    // Match predictions using resident ID (try multiple ID formats)
                    $matchedEligibility = false;
                    $matchedRisk = false;
                    
                    // Try exact match first
                    if (isset($eligibilityMap[$residentId])) {
                        $eligibility = $eligibilityMap[$residentId];
                        if ($eligibility === 'Eligible' || $eligibility === 'Eligible for Services') {
                            $clusterStats[$clusterId]['eligible']++;
                            $matchedEligibility = true;
                        } elseif ($eligibility === 'Ineligible' || $eligibility === 'Not Eligible' || $eligibility === 'Ineligible for Services') {
                            $clusterStats[$clusterId]['ineligible']++;
                            $matchedEligibility = true;
                        }
                    }
                    
                    // Try exact match for risk
                    if (isset($riskMap[$residentId])) {
                        $risk = strtolower(trim($riskMap[$residentId]));
                        // Handle various formats: "low", "low risk", "Low", "Low Risk", "0", "1", etc.
                        if (strpos($risk, 'low') === 0 || $risk === '0' || $risk === '0.0') {
                            $clusterStats[$clusterId]['low_risk']++;
                            $matchedRisk = true;
                        } elseif (strpos($risk, 'medium') === 0 || $risk === '1' || $risk === '1.0') {
                            $clusterStats[$clusterId]['medium_risk']++;
                            $matchedRisk = true;
                        } elseif (strpos($risk, 'high') === 0 || $risk === '2' || $risk === '2.0') {
                            $clusterStats[$clusterId]['high_risk']++;
                            $matchedRisk = true;
                        }
                    }
                    
                    // Try string ID match (in case IDs are stored as strings)
                    if (!$matchedEligibility && isset($eligibilityMap[(string)$residentId])) {
                        $eligibility = $eligibilityMap[(string)$residentId];
                        if ($eligibility === 'Eligible' || $eligibility === 'Eligible for Services') {
                            $clusterStats[$clusterId]['eligible']++;
                        } elseif ($eligibility === 'Ineligible' || $eligibility === 'Not Eligible' || $eligibility === 'Ineligible for Services') {
                            $clusterStats[$clusterId]['ineligible']++;
                        }
                    }
                    
                    if (!$matchedRisk && isset($riskMap[(string)$residentId])) {
                        $risk = strtolower(trim($riskMap[(string)$residentId]));
                        if (strpos($risk, 'low') === 0 || $risk === '0' || $risk === '0.0') {
                            $clusterStats[$clusterId]['low_risk']++;
                        } elseif (strpos($risk, 'medium') === 0 || $risk === '1' || $risk === '1.0') {
                            $clusterStats[$clusterId]['medium_risk']++;
                        } elseif (strpos($risk, 'high') === 0 || $risk === '2' || $risk === '2.0') {
                            $clusterStats[$clusterId]['high_risk']++;
                        }
                    }
                    
                    if (isset($programMap[$residentId]) && !empty($programMap[$residentId])) {
                        $program = $programMap[$residentId];
                        $clusterStats[$clusterId]['programs'][$program] = ($clusterStats[$clusterId]['programs'][$program] ?? 0) + 1;
                    }
                }
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($clusterStats as $clusterId => $stats)
                <div class="border border-gray-200 rounded-lg p-4 bg-white hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-bold text-gray-900">
                            <i class="fas fa-layer-group mr-1 text-purple-600"></i>
                            {{ $stats['label'] }}
                        </h4>
                        <span class="text-xs text-gray-600 font-medium">{{ $stats['total'] }} residents</span>
                    </div>
                    
                    <!-- Service Eligibility -->
                    <div class="mb-3">
                        <div class="text-xs text-gray-600 mb-2">Service Eligibility</div>
                        <div class="flex items-center gap-2 mb-1">
                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                @php
                                    $eligiblePct = $stats['total'] > 0 ? round(($stats['eligible'] / $stats['total']) * 100) : 0;
                                @endphp
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $eligiblePct }}%"></div>
                            </div>
                            <span class="text-xs font-semibold text-gray-700 w-12 text-right">{{ $eligiblePct }}%</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-green-600">{{ $stats['eligible'] }} eligible</span>
                            <span class="text-red-600">{{ $stats['ineligible'] }} ineligible</span>
                        </div>
                    </div>
                    
                    <!-- Health Risk -->
                    <div class="mb-3">
                        <div class="text-xs text-gray-600 mb-2">Health Risk Distribution</div>
                        <div class="space-y-1">
                            @php
                                $lowPct = $stats['total'] > 0 ? round(($stats['low_risk'] / $stats['total']) * 100) : 0;
                                $medPct = $stats['total'] > 0 ? round(($stats['medium_risk'] / $stats['total']) * 100) : 0;
                                $highPct = $stats['total'] > 0 ? round(($stats['high_risk'] / $stats['total']) * 100) : 0;
                            @endphp
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-600 w-16">Low:</span>
                                <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-green-600 h-1.5 rounded-full" style="width: {{ $lowPct }}%"></div>
                                </div>
                                <span class="text-xs font-semibold text-gray-700 w-8 text-right">{{ $lowPct }}%</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-600 w-16">Medium:</span>
                                <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-yellow-600 h-1.5 rounded-full" style="width: {{ $medPct }}%"></div>
                                </div>
                                <span class="text-xs font-semibold text-gray-700 w-8 text-right">{{ $medPct }}%</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-600 w-16">High:</span>
                                <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-red-600 h-1.5 rounded-full" style="width: {{ $highPct }}%"></div>
                                </div>
                                <span class="text-xs font-semibold text-gray-700 w-8 text-right">{{ $highPct }}%</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Top Programs -->
                    @if(!empty($stats['programs']))
                    <div class="pt-3 border-t border-gray-200">
                        <div class="text-xs text-gray-600 mb-2">Recommended Programs</div>
                        @php
                            arsort($stats['programs']);
                            $topPrograms = array_slice($stats['programs'], 0, 2, true);
                        @endphp
                        @foreach($topPrograms as $program => $count)
                            @php
                                $programPct = $stats['total'] > 0 ? round(($count / $stats['total']) * 100) : 0;
                            @endphp
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-gray-700 truncate flex-1">{{ $program }}</span>
                                <span class="text-xs font-semibold text-blue-600 ml-2">{{ $programPct }}%</span>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Button to Open Detailed Resident Analysis Modal -->
    <div class="flex justify-start mb-2">
        <button id="showResidentDetailsModalBtn" onclick="showResidentDetailsModal()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
            <i class="fas fa-table mr-2"></i>
            Show Details
        </button>
    </div>

    <!-- Detailed Analysis Table (Hidden, content moved to modal) -->
    @if(isset($residents) && $residents->count() > 0)
        <div id="detailedResidentSection" class="hidden">
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
                        @if(isset($clusteringData) && !empty($clusteringData['cluster_labels']))
                        <select id="filterCluster" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">All Clusters</option>
                            @foreach($clusteringData['cluster_labels'] as $clusterId => $label)
                            <option value="{{ $clusterId }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @endif
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cluster</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Eligibility</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Health Risk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recommended Program</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blotter Reports</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($residents as $resident)
                                @php
                                    // Safely get resident ID once per row
                                    $residentId = null;
                                    if (is_object($resident)) {
                                        $residentArray = (array)$resident;
                                        $residentId = $residentArray['id'] ?? $residentArray['resident_id'] ?? null;
                                    } elseif (is_array($resident)) {
                                        $residentId = $resident['id'] ?? $resident['resident_id'] ?? null;
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $resident->full_name ?? 'N/A' }}</div>
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
                                        @if(isset($resident->cluster_label) && $resident->cluster_label)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                <i class="fas fa-layer-group mr-1"></i>
                                                {{ $resident->cluster_label }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                N/A
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $predictedEligibility = $eligibilityMap[$residentId] ?? null;
                                            $eligibilityStatus = $predictedEligibility ?? 'N/A';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $getStatusColor('eligibility', $eligibilityStatus) }}">
                                            {{ $eligibilityStatus }}
                                            @if($predictedEligibility !== null)
                                                <i class="fas fa-chart-line ml-1 text-xs" title="Model Prediction"></i>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $predictedRisk = $riskMap[$residentId] ?? null;
                                            $riskLevel = $predictedRisk ?? 'N/A';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $getStatusColor('risk_level', $riskLevel) }}">
                                            {{ $riskLevel !== 'N/A' ? $riskLevel . ' Risk' : 'N/A' }}
                                            @if($predictedRisk !== null)
                                                <i class="fas fa-chart-line ml-1 text-xs" title="Model Prediction"></i>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $predictedProgram = $programMap[$residentId] ?? null;
                                            $program = $predictedProgram ?? 'N/A';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $program }}
                                            @if($predictedProgram !== null)
                                                <i class="fas fa-chart-line ml-1 text-xs" title="Model Recommendation"></i>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $resident->blotter_count ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button type="button" data-resident-id="{{ $residentId ?? 0 }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200 js-view-resident">
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

    <!-- Understanding the Predictions -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-8">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                Understanding the Predictions
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">How It Works</h4>
                    <p class="text-xs text-blue-800 mb-2">
                        Decision tree models analyze resident characteristics (age, income, employment, etc.) to make predictions. Each model is trained on historical patterns to identify eligibility, assess health risks, and recommend programs.
                    </p>
                    <p class="text-xs text-blue-800">
                        <strong>Cluster Enhancement:</strong> When cluster information is available, it's used as an additional feature to provide context-aware predictions based on group characteristics.
                    </p>
                </div>
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                    <h4 class="text-sm font-semibold text-green-900 mb-2">Interpreting Results</h4>
                    <ul class="text-xs text-green-800 space-y-1">
                        <li><strong>Service Eligibility:</strong> Predicts if a resident qualifies for services based on income, age, and other factors.</li>
                        <li><strong>Health Risk:</strong> Assesses potential health concerns (Low/Medium/High) based on demographic and health indicators.</li>
                        <li><strong>Program Recommendation:</strong> Suggests the most suitable support program for each resident's needs.</li>
                    </ul>
                </div>
            </div>
            <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                    <p class="text-xs text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Important:</strong> These are model-generated predictions based on patterns in the data. They should be used as decision support tools alongside human judgment and verification.
                    </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Resident Details -->
<div id="residentDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-4/5 shadow-lg rounded-md bg-white max-h-[90vh]">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-table text-blue-600 mr-2"></i>
                    Detailed Resident Analysis
                </h3>
                <button onclick="closeResidentDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="residentDetailsModalContent" class="overflow-y-auto max-h-[calc(90vh-120px)]">
                <!-- Content will be populated from detailedResidentSection -->
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
    
    // View resident functionality
    document.querySelectorAll('.js-view-resident').forEach(button => {
        button.addEventListener('click', function() {
            const residentId = this.getAttribute('data-resident-id');
            if (residentId) {
                window.open(`/admin/residents/${residentId}`, '_blank');
            }
        });
    });

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
    const filterCluster = document.getElementById('filterCluster');
    
    function applyFilters() {
        const selectedCategory = filterSelect ? filterSelect.value : '';
        const selectedCluster = filterCluster ? filterCluster.value : '';
        const rows = document.querySelectorAll('#residentsTable tbody tr');
        
        rows.forEach(row => {
            const eligibilityCell = row.querySelector('td:nth-child(7)');
            const riskCell = row.querySelector('td:nth-child(8)');
            const clusterCell = row.querySelector('td:nth-child(6)');
            
            let show = true;
            
            // Category filter
            if (selectedCategory) {
                const eligibility = eligibilityCell ? eligibilityCell.textContent.trim() : '';
                const risk = riskCell ? riskCell.textContent.trim() : '';
                
                if (selectedCategory === 'eligible' && !eligibility.includes('Eligible')) show = false;
                if (selectedCategory === 'ineligible' && !eligibility.includes('Ineligible')) show = false;
                if (selectedCategory === 'low_risk' && !risk.includes('Low')) show = false;
                if (selectedCategory === 'medium_risk' && !risk.includes('Medium')) show = false;
                if (selectedCategory === 'high_risk' && !risk.includes('High')) show = false;
            }
            
            // Cluster filter
            if (show && selectedCluster) {
                const clusterText = clusterCell ? clusterCell.textContent.trim() : '';
                if (!clusterText.includes(selectedCluster) && clusterText !== 'N/A') {
                    show = false;
                }
            }
            
            row.style.display = show ? '' : 'none';
        });
    }
    
    if (filterSelect) {
        filterSelect.addEventListener('change', applyFilters);
    }
    if (filterCluster) {
        filterCluster.addEventListener('change', applyFilters);
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

function showResidentDetailsModal() {
    const modal = document.getElementById('residentDetailsModal');
    const modalContent = document.getElementById('residentDetailsModalContent');
    const detailsSection = document.getElementById('detailedResidentSection');

    if (modal && modalContent && detailsSection) {
        const contentClone = detailsSection.cloneNode(true);
        contentClone.classList.remove('hidden');
        contentClone.id = 'residentDetailsModalContentInner';

        modalContent.innerHTML = '';
        modalContent.appendChild(contentClone);

        modal.classList.remove('hidden');

        // Reinitialize table features after modal opens
        setTimeout(() => {
            initializeTableFeatures();
            
            // Reattach view resident functionality
            document.querySelectorAll('#residentDetailsModalContentInner .js-view-resident').forEach(button => {
                button.addEventListener('click', function() {
                    const residentId = this.getAttribute('data-resident-id');
                    if (residentId) {
                        window.open(`/admin/residents/${residentId}`, '_blank');
                    }
                });
            });
        }, 100);
    }
}

function closeResidentDetailsModal() {
    const modal = document.getElementById('residentDetailsModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeResidentDetailsModal();
    }
});

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('residentDetailsModal');
    if (modal && event.target === modal) {
        closeResidentDetailsModal();
    }
});
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
