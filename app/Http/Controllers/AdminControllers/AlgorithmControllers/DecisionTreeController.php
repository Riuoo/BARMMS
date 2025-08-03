<?php

namespace App\Http\Controllers\AdminControllers\AlgorithmControllers;

use App\Services\ResidentClassificationPredictionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Residents;

class DecisionTreeController
{
    private $decisionTreeService;

    public function __construct(ResidentClassificationPredictionService $decisionTreeService)
    {
        $this->decisionTreeService = $decisionTreeService;
    }

    /**
     * Display decision tree analysis page
     */
    public function index()
    {
        // Run all four analyses for the comprehensive view
        $healthCondition = $this->decisionTreeService->buildHealthConditionTree();
        $serviceEligibility = $this->decisionTreeService->buildServiceEligibilityTree();
        $healthRisk = $this->decisionTreeService->buildHealthRiskTree();
        $programRecommendation = $this->decisionTreeService->buildProgramRecommendationTree();
        
        // Check if any analysis has errors
        $errors = [];
        if (isset($healthCondition['error'])) {
            $errors['healthCondition'] = $healthCondition['error'];
        }
        if (isset($serviceEligibility['error'])) {
            $errors['serviceEligibility'] = $serviceEligibility['error'];
        }
        if (isset($healthRisk['error'])) {
            $errors['healthRisk'] = $healthRisk['error'];
        }
        if (isset($programRecommendation['error'])) {
            $errors['programRecommendation'] = $programRecommendation['error'];
        }
        
        // Get sample size from any successful analysis
        $sampleSize = 0;
        if (!isset($healthCondition['error'])) {
            $sampleSize = $healthCondition['sample_size'] ?? 0;
        } elseif (!isset($serviceEligibility['error'])) {
            $sampleSize = $serviceEligibility['sample_size'] ?? 0;
        } elseif (!isset($healthRisk['error'])) {
            $sampleSize = $healthRisk['sample_size'] ?? 0;
        } elseif (!isset($programRecommendation['error'])) {
            $sampleSize = $programRecommendation['sample_size'] ?? 0;
        }
        
        // Calculate counts for health condition
        if (!isset($healthCondition['error'])) {
            $conditionCounts = [
                'Critical Condition' => 0,
                'Chronic Condition' => 0,
                'Minor Health Issues' => 0,
                'Healthy' => 0
            ];
            
            if (isset($healthCondition['training_predictions'])) {
                foreach ($healthCondition['training_predictions'] as $prediction) {
                    $conditionCounts[$prediction['predicted']]++;
                }
            }
            
            $healthCondition['condition_counts'] = $conditionCounts;
        }
        
        // Calculate counts for service eligibility
        if (!isset($serviceEligibility['error'])) {
            $eligibleCount = 0;
            $ineligibleCount = 0;
            
            if (isset($serviceEligibility['predictions'])) {
                foreach ($serviceEligibility['predictions'] as $prediction) {
                    if ($prediction['predicted'] === 'Eligible') {
                        $eligibleCount++;
                    } else {
                        $ineligibleCount++;
                    }
                }
            }
            
            $serviceEligibility['eligible_count'] = $eligibleCount;
            $serviceEligibility['ineligible_count'] = $ineligibleCount;
        }
        
        // Calculate counts for health risk
        if (!isset($healthRisk['error'])) {
            $lowCount = 0;
            $mediumCount = 0;
            $highCount = 0;
            
            if (isset($healthRisk['predictions'])) {
                foreach ($healthRisk['predictions'] as $prediction) {
                    switch ($prediction['predicted']) {
                        case 'Low':
                            $lowCount++;
                            break;
                        case 'Medium':
                            $mediumCount++;
                            break;
                        case 'High':
                            $highCount++;
                            break;
                    }
                }
            }
            
            $healthRisk['low_count'] = $lowCount;
            $healthRisk['medium_count'] = $mediumCount;
            $healthRisk['high_count'] = $highCount;
        }
        
        // Calculate recommendations for program recommendation
        if (!isset($programRecommendation['error'])) {
            $recommendations = [];
            
            if (isset($programRecommendation['predictions'])) {
                foreach ($programRecommendation['predictions'] as $prediction) {
                    $program = $prediction['predicted'];
                    if (!isset($recommendations[$program])) {
                        $recommendations[$program] = 0;
                    }
                    $recommendations[$program]++;
                }
            }
            
            $programRecommendation['recommendations'] = $recommendations;
        }
        
        // Get residents for the table
        $residents = Residents::all();
        
        return view('admin.decision-tree.index', [
            'healthCondition' => $healthCondition,
            'serviceEligibility' => $serviceEligibility,
            'healthRisk' => $healthRisk,
            'programRecommendation' => $programRecommendation,
            'errors' => $errors,
            'sampleSize' => $sampleSize,
            'residents' => $residents
        ]);
    }

    /**
     * Perform decision tree analysis with specific parameters
     */
    public function performAnalysis(Request $request)
    {
        $request->validate([
            'type' => 'required|in:service-eligibility,health-risk,program-recommendation',
            'max_depth' => 'integer|min:3|max:20',
            'min_samples_split' => 'integer|min:2|max:10'
        ]);

        $type = $request->input('type', 'service-eligibility');
        $maxDepth = $request->input('max_depth', 10);
        $minSamplesSplit = $request->input('min_samples_split', 2);
        
        $this->decisionTreeService = new ResidentClassificationPredictionService($maxDepth, $minSamplesSplit);
        
        switch ($type) {
            case 'health-risk':
                $result = $this->decisionTreeService->buildHealthRiskTree();
                break;
            case 'program-recommendation':
                $result = $this->decisionTreeService->buildProgramRecommendationTree();
                break;
            default:
                $result = $this->decisionTreeService->buildServiceEligibilityTree();
                break;
        }
        
        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ]);
        }
        
        // Cache the result for 1 hour
        Cache::put('decision_tree_result_' . $type, $result, 3600);
        
        return response()->json([
            'success' => true,
            'result' => $result,
            'type' => $type
        ]);
    }

    /**
     * Predict for a specific resident
     */
    public function predictForResident(Request $request)
    {
        $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'type' => 'required|in:service-eligibility,health-risk,program-recommendation'
        ]);

        $resident = Residents::find($request->resident_id);
        $type = $request->input('type');
        
        if (!$resident) {
            return response()->json([
                'success' => false,
                'error' => 'Resident not found'
            ]);
        }
        
        switch ($type) {
            case 'health-risk':
                $prediction = $this->decisionTreeService->predictHealthRisk($resident);
                break;
            case 'program-recommendation':
                $prediction = $this->decisionTreeService->predictRecommendedProgram($resident);
                break;
            default:
                $prediction = $this->decisionTreeService->predictServiceEligibility($resident);
                break;
        }
        
        return response()->json([
            'success' => true,
            'resident' => [
                'id' => $resident->id,
                'name' => $resident->name,
                'age' => $resident->age,
                'income_level' => $resident->income_level,
                'employment_status' => $resident->employment_status,
                'education_level' => $resident->education_level,
                'health_status' => $resident->health_status,
                'family_size' => $resident->family_size
            ],
            'prediction' => $prediction,
            'type' => $type
        ]);
    }

    /**
     * Get decision tree statistics
     */
    public function getStatistics(Request $request)
    {
        $type = $request->input('type', 'service-eligibility');
        
        switch ($type) {
            case 'health-risk':
                $result = $this->decisionTreeService->buildHealthRiskTree();
                break;
            case 'program-recommendation':
                $result = $this->decisionTreeService->buildProgramRecommendationTree();
                break;
            default:
                $result = $this->decisionTreeService->buildServiceEligibilityTree();
                break;
        }
        
        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ]);
        }
        
        $stats = [
            'accuracy' => round($result['accuracy'] * 100, 2),
            'sample_size' => $result['sample_size'],
            'feature_importance' => $result['feature_importance'] ?? [],
            'rules_count' => count($result['rules'] ?? [])
        ];
        
        if (isset($result['risk_levels'])) {
            $stats['distribution'] = $result['risk_levels'];
        } elseif (isset($result['programs'])) {
            $stats['distribution'] = $result['programs'];
        }
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
            'type' => $type
        ]);
    }

    /**
     * Export decision tree rules
     */
    public function exportRules(Request $request)
    {
        $type = $request->input('type', 'service-eligibility');
        $format = $request->input('format', 'json');
        
        switch ($type) {
            case 'health-risk':
                $result = $this->decisionTreeService->buildHealthRiskTree();
                break;
            case 'program-recommendation':
                $result = $this->decisionTreeService->buildProgramRecommendationTree();
                break;
            default:
                $result = $this->decisionTreeService->buildServiceEligibilityTree();
                break;
        }
        
        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ]);
        }
        
        if ($format === 'csv') {
            return $this->exportRulesToCSV($result, $type);
        }
        
        return response()->json([
            'success' => true,
            'rules' => $result['rules'] ?? [],
            'type' => $type
        ]);
    }

    /**
     * Export decision tree rules to CSV
     */
    private function exportRulesToCSV($result, $type)
    {
        $filename = 'decision_tree_rules_' . $type . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($result, $type) {
            $file = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($file, [
                'Rule Number',
                'Conditions',
                'Prediction',
                'Sample Count',
                'Analysis Type'
            ]);
            
            // Write rules
            $rules = $result['rules'] ?? [];
            foreach ($rules as $index => $rule) {
                fputcsv($file, [
                    $index + 1,
                    $rule['path'] ?? 'N/A',
                    $rule['prediction'] ?? 'N/A',
                    $rule['count'] ?? 0,
                    $type
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get feature importance analysis
     */
    public function getFeatureImportance(Request $request)
    {
        $type = $request->input('type', 'service-eligibility');
        
        switch ($type) {
            case 'health-risk':
                $result = $this->decisionTreeService->buildHealthRiskTree();
                break;
            case 'program-recommendation':
                $result = $this->decisionTreeService->buildProgramRecommendationTree();
                break;
            default:
                $result = $this->decisionTreeService->buildServiceEligibilityTree();
                break;
        }
        
        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ]);
        }
        
        $importance = $result['feature_importance'] ?? [];
        arsort($importance);
        
        return response()->json([
            'success' => true,
            'feature_importance' => $importance,
            'type' => $type
        ]);
    }

    /**
     * Get decision tree visualization data
     */
    public function getTreeVisualization(Request $request)
    {
        $type = $request->input('type', 'service-eligibility');
        
        switch ($type) {
            case 'health-risk':
                $result = $this->decisionTreeService->buildHealthRiskTree();
                break;
            case 'program-recommendation':
                $result = $this->decisionTreeService->buildProgramRecommendationTree();
                break;
            default:
                $result = $this->decisionTreeService->buildServiceEligibilityTree();
                break;
        }
        
        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ]);
        }
        
        $treeData = $this->convertTreeToVisualizationData($result['tree']);
        
        return response()->json([
            'success' => true,
            'tree_data' => $treeData,
            'type' => $type
        ]);
    }

    /**
     * Convert tree structure to visualization format
     */
    private function convertTreeToVisualizationData($node, $level = 0): array
    {
        if ($node['type'] === 'leaf') {
            return [
                'id' => uniqid(),
                'name' => $node['prediction'],
                'type' => 'leaf',
                'count' => $node['count'],
                'level' => $level
            ];
        }
        
        return [
            'id' => uniqid(),
            'name' => $node['feature'] . ' ≤ ' . $node['threshold'],
            'type' => 'node',
            'feature' => $node['feature'],
            'threshold' => $node['threshold'],
            'count' => $node['count'],
            'level' => $level,
            'children' => [
                $this->convertTreeToVisualizationData($node['left'], $level + 1),
                $this->convertTreeToVisualizationData($node['right'], $level + 1)
            ]
        ];
    }
}
