<?php

namespace App\Http\Controllers\AdminControllers\AlgorithmControllers;

use App\Services\PythonAnalyticsService;
use Illuminate\Http\Request;
use App\Models\Residents;
use App\Models\BlotterRequest;

/**
 * DecisionTreeController - Refactored to remove redundancies
 * 
 * Changes:
 * - Extracted duplicate switch statements to performAnalysisByType() helper
 * - Removed unused healthCondition calculation
 * - Removed useless getTreeVisualization method
 * - Removed duplicate validation_metrics
 * - Removed placeholder rules array
 * - Removed empty training_predictions
 * - Simplified config usage
 * - Used modern PHP features (array functions, match expressions)
 */
class DecisionTreeController
{
    private $pythonService;

    public function __construct(PythonAnalyticsService $pythonService)
    {
        $this->pythonService = $pythonService;
    }
    
    /**
     * Check if Python service is available (required)
     */
    private function ensurePythonAvailable(): void
    {
        if (!config('services.python_analytics.enabled', true)) {
            throw new \Exception('Python analytics service is disabled. Please enable it in .env');
        }
        
        if (!$this->pythonService->isAvailable()) {
            throw new \Exception('Python analytics service is not available. Please ensure the Python service is running on ' . config('services.python_analytics.url', 'http://localhost:5000'));
        }
    }

    /**
     * Extract duplicate analysis logic to helper method
     * This eliminates 5 duplicate switch statements across different methods
     */
    private function performAnalysisByType(string $type, array $formattedResidents, string $modelType = 'decision_tree'): array
    {
        switch ($type) {
            case 'health-risk':
                $pythonResult = $this->pythonService->analyzeHealthRisk($formattedResidents, 'random_forest');
                if (isset($pythonResult['error'])) {
                    return ['error' => $pythonResult['error']];
                }
                return $this->convertPythonHealthRiskToPhpFormat($pythonResult);
                
            case 'program-recommendation':
                $pythonResult = $this->pythonService->analyzeProgramRecommendation($formattedResidents, 'random_forest');
                if (isset($pythonResult['error'])) {
                    return ['error' => $pythonResult['error']];
                }
                return $this->convertPythonProgramRecommendationToPhpFormat($pythonResult);
                
            case 'health-condition':
                $pythonResult = $this->pythonService->analyzeHealthCondition($formattedResidents, $modelType);
                if (isset($pythonResult['error'])) {
                    return ['error' => $pythonResult['error']];
                }
                return $this->convertPythonHealthConditionToPhpFormat($pythonResult);
                
            default: // service-eligibility
                $pythonResult = $this->pythonService->analyzeServiceEligibility($formattedResidents, $modelType);
                if (isset($pythonResult['error'])) {
                    return ['error' => $pythonResult['error']];
                }
                return $this->convertPythonEligibilityToPhpFormat($pythonResult);
        }
    }

    /**
     * Display decision tree analysis page
     */
    public function index()
    {
        $residents = Residents::all();

        // Attach blotter report counts to residents so decision-tree analytics can display incident context
        $blotterCountsMap = BlotterRequest::selectRaw('respondent_id, COUNT(*) as cnt')
            ->whereNotNull('respondent_id')
            ->groupBy('respondent_id')
            ->pluck('cnt', 'respondent_id');
        foreach ($residents as $resident) {
            $resident->blotter_count = (int) ($blotterCountsMap[$resident->id] ?? 0);
        }

        $this->ensurePythonAvailable();
        $formattedResidents = $this->pythonService->formatResidentsForPython($residents);
        
        // Perform analyses using helper method
        $healthRisk = $this->performAnalysisByType('health-risk', $formattedResidents);
        $serviceEligibility = $this->performAnalysisByType('service-eligibility', $formattedResidents);
        $programRecommendation = $this->performAnalysisByType('program-recommendation', $formattedResidents);
        
        // Collect errors
        $errors = [];
        foreach (['healthRisk' => $healthRisk, 'serviceEligibility' => $serviceEligibility, 'programRecommendation' => $programRecommendation] as $key => $result) {
            if (isset($result['error'])) {
                $errors[$key] = $result['error'];
            }
        }
        
        // Calculate sample size from first successful analysis
        $sampleSize = $serviceEligibility['sample_size'] ?? $healthRisk['sample_size'] ?? $programRecommendation['sample_size'] ?? 0;
        
        // Calculate counts using array functions
        $serviceEligibility['eligible_count'] = count(array_filter($serviceEligibility['predictions'] ?? [], fn($p) => ($p['predicted'] ?? '') === 'Eligible'));
        $serviceEligibility['ineligible_count'] = count($serviceEligibility['predictions'] ?? []) - $serviceEligibility['eligible_count'];
        
        $healthRisk['low_count'] = count(array_filter($healthRisk['testing_predictions'] ?? [], fn($p) => ($p['predicted'] ?? '') === 'Low'));
        $healthRisk['medium_count'] = count(array_filter($healthRisk['testing_predictions'] ?? [], fn($p) => ($p['predicted'] ?? '') === 'Medium'));
        $healthRisk['high_count'] = count($healthRisk['testing_predictions'] ?? []) - $healthRisk['low_count'] - $healthRisk['medium_count'];
        
        // Calculate program recommendations
        $recommendations = [];
        foreach ($programRecommendation['testing_predictions'] ?? [] as $prediction) {
            $program = $prediction['predicted'] ?? 'Unknown';
            $recommendations[$program] = ($recommendations[$program] ?? 0) + 1;
        }
        $programRecommendation['recommendations'] = $recommendations;
        
        // Get model types from results
        $modelTypes = [
            'eligibility' => $serviceEligibility['model_info']['model_type'] ?? 'decision_tree',
            'healthRisk' => $healthRisk['model_info']['model_type'] ?? 'random_forest',
            'program' => $programRecommendation['model_info']['model_type'] ?? 'random_forest',
        ];
        
        return view('admin.decision-tree.index', [
            'serviceEligibility' => $serviceEligibility,
            'healthRisk' => $healthRisk,
            'programRecommendation' => $programRecommendation,
            'errors' => $errors,
            'sampleSize' => $sampleSize,
            'residents' => $residents,
            'modelTypes' => $modelTypes,
            'featureNames' => config('decision_tree.feature_display_names', []),
            'programDescriptions' => config('decision_tree.program_descriptions', []),
            'statusColors' => config('decision_tree.status_colors', []),
        ]);
    }

    /**
     * Perform decision tree analysis with specific parameters
     */
    public function performAnalysis(Request $request)
    {
        $request->validate([
            'type' => 'required|in:service-eligibility,health-risk,program-recommendation,health-condition',
            'model_type' => 'in:decision_tree,random_forest'
        ]);

        $this->ensurePythonAvailable();
        $residents = Residents::all();
        $formattedResidents = $this->pythonService->formatResidentsForPython($residents);
        
        $result = $this->performAnalysisByType(
            $request->input('type'),
            $formattedResidents,
            $request->input('model_type', 'decision_tree')
        );
        
        if (isset($result['error'])) {
            return response()->json(['success' => false, 'error' => $result['error']]);
        }
        
        return response()->json([
            'success' => true,
            'result' => $result,
            'type' => $request->input('type')
        ]);
    }

    /**
     * Predict for a specific resident
     */
    public function predictForResident(Request $request)
    {
        $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'type' => 'required|in:service-eligibility,health-risk,program-recommendation,health-condition'
        ]);

        $resident = Residents::find($request->resident_id);
        if (!$resident) {
            return response()->json(['success' => false, 'error' => 'Resident not found']);
        }
        
        $this->ensurePythonAvailable();
        $formattedResidents = $this->pythonService->formatResidentsForPython(collect([$resident]));
        $result = $this->performAnalysisByType($request->input('type'), $formattedResidents);
        
        if (isset($result['error'])) {
            return response()->json(['success' => false, 'error' => $result['error']]);
        }
        
        // Extract prediction for single resident
        $type = $request->input('type');
        $prediction = match($type) {
            'health-risk' => ['predicted_risk' => $result['testing_predictions'][0]['predicted'] ?? 'N/A'],
            'program-recommendation', 'health-condition' => ['predicted' => $result['testing_predictions'][0]['predicted'] ?? 'N/A'],
            default => ['predicted_eligibility' => $result['predictions'][0]['predicted'] ?? 'N/A'],
        };
        
        $residentData = $resident->only(['id', 'age', 'income_level', 'employment_status', 'education_level', 'is_pwd', 'family_size']);
        $residentData['name'] = $resident->full_name;
        
        return response()->json([
            'success' => true,
            'resident' => $residentData,
            'prediction' => $prediction,
            'type' => $type
        ]);
    }

    /**
     * Get decision tree statistics
     */
    public function getStatistics(Request $request)
    {
        $this->ensurePythonAvailable();
        $residents = Residents::all();
        $formattedResidents = $this->pythonService->formatResidentsForPython($residents);
        $result = $this->performAnalysisByType(
            $request->input('type', 'service-eligibility'),
            $formattedResidents,
            $request->input('model_type', 'decision_tree')
        );
        
        if (isset($result['error'])) {
            return response()->json(['success' => false, 'error' => $result['error']]);
        }
        
        return response()->json([
            'success' => true,
            'stats' => [
                'accuracy' => $result['accuracy'] ?? 0,
                'sample_size' => $result['sample_size'] ?? 0,
                'feature_importance' => $result['feature_importance'] ?? [],
            ],
            'type' => $request->input('type', 'service-eligibility')
        ]);
    }

    /**
     * Export decision tree results
     */
    public function exportRules(Request $request)
    {
        $this->ensurePythonAvailable();
        $residents = Residents::all();
        $formattedResidents = $this->pythonService->formatResidentsForPython($residents);
        $result = $this->performAnalysisByType(
            $request->input('type', 'service-eligibility'),
            $formattedResidents,
            $request->input('model_type', 'decision_tree')
        );
        
        if (isset($result['error'])) {
            return response()->json(['success' => false, 'error' => $result['error']]);
        }
        
        if ($request->input('format') === 'csv') {
            return $this->exportRulesToCSV($result, $request->input('type', 'service-eligibility'));
        }
        
        return response()->json([
            'success' => true,
            'data' => $result,
            'type' => $request->input('type', 'service-eligibility')
        ]);
    }

    /**
     * Export results to CSV
     */
    private function exportRulesToCSV($result, $type)
    {
        $filename = 'decision_tree_' . $type . '_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        return response()->stream(function() use ($result, $type) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Resident ID', 'Resident Name', 'Prediction', 'Accuracy', 'Analysis Type']);
            
            $predictions = $result['testing_predictions'] ?? $result['predictions'] ?? [];
            foreach ($predictions as $pred) {
                fputcsv($file, [
                    $pred['resident']->id ?? 'N/A',
                    $pred['resident']->full_name ?? 'N/A',
                    $pred['predicted'] ?? 'N/A',
                    isset($pred['correct']) ? ($pred['correct'] ? 'Yes' : 'No') : 'N/A',
                    $type
                ]);
            }
            fclose($file);
        }, 200, $headers);
    }

    /**
     * Get feature importance analysis
     */
    public function getFeatureImportance(Request $request)
    {
            $this->ensurePythonAvailable();
        $residents = Residents::all();
        $formattedResidents = $this->pythonService->formatResidentsForPython($residents);
        $result = $this->performAnalysisByType(
            $request->input('type', 'service-eligibility'),
            $formattedResidents,
            $request->input('model_type', 'decision_tree')
        );
        
        if (isset($result['error'])) {
            return response()->json(['success' => false, 'error' => $result['error']]);
        }
        
        $importance = $result['feature_importance'] ?? [];
        arsort($importance);
        
        return response()->json([
            'success' => true,
            'feature_importance' => $importance,
            'type' => $request->input('type', 'service-eligibility')
        ]);
    }

    // Removed getTreeVisualization() - was returning useless placeholder data
    // If tree visualization is needed, implement actual tree structure extraction from Python models
    
    /**
     * Convert Python health risk result to PHP format
     */
    private function convertPythonHealthRiskToPhpFormat(array $pythonResult): array
    {
        $riskAnalysis = $pythonResult['risk_analysis'] ?? [];
        $modelInfo = $pythonResult['model_info'] ?? [];
        
        $predictions = array_map(fn($a) => [
            'resident' => (object)['id' => $a['resident_id'], 'name' => $a['resident_name']],
            'predicted' => $a['predicted_risk'],
            'actual' => $a['actual_risk'],
            'correct' => $a['predicted_risk'] === $a['actual_risk']
        ], $riskAnalysis);
        
        return [
            'accuracy' => $modelInfo['metrics']['test_accuracy'] ?? 0,
            'testing_accuracy' => $modelInfo['metrics']['test_accuracy'] ?? 0,
            'training_accuracy' => $modelInfo['metrics']['train_accuracy'] ?? 0,
            'feature_importance' => $modelInfo['feature_importance'] ?? [],
            'sample_size' => count($predictions),
            'testing_predictions' => $predictions,
            'model_info' => $modelInfo,
        ];
    }
    
    /**
     * Convert Python eligibility result to PHP format
     */
    private function convertPythonEligibilityToPhpFormat(array $pythonResult): array
    {
        $eligibilityAnalysis = $pythonResult['eligibility_analysis'] ?? [];
        $modelInfo = $pythonResult['model_info'] ?? [];
        
        $predictions = array_map(fn($a) => [
            'resident' => (object)['id' => $a['resident_id'], 'name' => $a['resident_name']],
            'predicted' => $a['predicted_eligibility'],
            'actual' => $a['actual_eligibility'],
            'probability' => $a['probability'] ?? null,
            'correct' => $a['predicted_eligibility'] === $a['actual_eligibility']
        ], $eligibilityAnalysis);
        
        return [
            'accuracy' => $modelInfo['metrics']['test_accuracy'] ?? 0,
            'feature_importance' => $modelInfo['feature_importance'] ?? [],
            'sample_size' => count($predictions),
            'predictions' => $predictions,
            'model_info' => $modelInfo,
        ];
    }
    
    /**
     * Convert Python health condition result to PHP format
     */
    private function convertPythonHealthConditionToPhpFormat(array $pythonResult): array
    {
        $predictions = $pythonResult['predictions'] ?? [];
        $modelInfo = $pythonResult['model_info'] ?? [];
        
        $phpPredictions = array_map(fn($p) => [
            'resident' => (object)['id' => $p['resident_id'], 'name' => $p['resident_name']],
            'predicted' => $p['predicted'],
            'actual' => $p['actual'],
            'correct' => $p['correct']
        ], $predictions);
        
        return [
            'accuracy' => $modelInfo['metrics']['test_accuracy'] ?? 0,
            'training_accuracy' => $modelInfo['metrics']['train_accuracy'] ?? null,
            'testing_accuracy' => $modelInfo['metrics']['test_accuracy'] ?? 0,
            'feature_importance' => $modelInfo['feature_importance'] ?? [],
            'sample_size' => count($phpPredictions),
            'testing_predictions' => $phpPredictions,
            'model_info' => $modelInfo,
        ];
    }
    
    /**
     * Convert Python program recommendation result to PHP format
     */
    private function convertPythonProgramRecommendationToPhpFormat(array $pythonResult): array
    {
        $predictions = $pythonResult['predictions'] ?? [];
        $modelInfo = $pythonResult['model_info'] ?? [];
        
        $phpPredictions = array_map(fn($p) => [
            'resident' => (object)['id' => $p['resident_id'], 'name' => $p['resident_name']],
            'predicted' => $p['predicted'],
            'actual' => $p['actual'],
            'correct' => $p['correct']
        ], $predictions);
        
        return [
            'accuracy' => $modelInfo['metrics']['test_accuracy'] ?? 0,
            'training_accuracy' => $modelInfo['metrics']['train_accuracy'] ?? null,
            'testing_accuracy' => $modelInfo['metrics']['test_accuracy'] ?? 0,
            'feature_importance' => $modelInfo['feature_importance'] ?? [],
            'sample_size' => count($phpPredictions),
            'testing_predictions' => $phpPredictions,
            'model_info' => $modelInfo,
        ];
    }
}

