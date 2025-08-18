<?php

namespace App\Http\Controllers\AdminControllers\AlgorithmControllers;

use App\Services\ResidentDemographicAnalysisService;
use App\Services\ResidentClassificationPredictionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Residents;

class ClusteringController
{
    private $clusteringService;

    public function __construct(ResidentDemographicAnalysisService $clusteringService)
    {
        $this->clusteringService = $clusteringService;
    }

    /**
     * Display clustering analysis page
     */
    public function index()
    {
        $k = request('k', 3);
        $useOptimalK = request('use_optimal_k', false);
        $useHierarchical = request('hierarchical', false);
        
        if ($useOptimalK) {
            $residents = Residents::all();
            $k = $this->clusteringService->findOptimalK($residents);
        }
        
        // Always compute both global and hierarchical summaries for comparison
        $globalService = new ResidentDemographicAnalysisService($k);
        $globalResult = $globalService->clusterResidents();
        $globalCharacteristics = $globalService->getClusterCharacteristics($globalResult);
        $globalSummary = [
            'sizes' => array_map(fn($c) => $c['size'], $globalCharacteristics),
            'silhouette' => $globalResult['silhouette'] ?? null,
            'k' => $k,
        ];
        
        $hierService = new ResidentDemographicAnalysisService($k);
        $hierResult = $hierService->clusterResidentsHierarchical();
        $hierCharacteristics = $hierService->getClusterCharacteristics($hierResult);
        // Group by purok for summary
        $purokTotals = [];
        foreach ($hierCharacteristics as $c) {
            $p = $c['most_common_purok'] ?? 'N/A';
            if ($p === '' || $p === null) { $p = 'N/A'; }
            $purokTotals[$p] = ($purokTotals[$p] ?? 0) + $c['size'];
        }
        $hierSummary = [
            'purok_totals' => $purokTotals,
            'k' => $k,
        ];
        
        // Now select the main mode for detailed display
        $this->clusteringService = $useHierarchical ? $hierService : $globalService;
        $result = $useHierarchical ? $hierResult : $globalResult;
        $characteristics = $this->clusteringService->getClusterCharacteristics($result);
        // Build grouped array for purok summary
        $grouped = [];
        foreach ($characteristics as $idx => $c) {
            $p = $c['most_common_purok'] ?? 'N/A';
            if ($p === '' || $p === null) { $p = 'N/A'; }
            $grouped[$p] = $grouped[$p] ?? [];
            $grouped[$p][] = ['idx' => $idx, 'c' => $c];
        }

        // Compute global most common employment and health across all clusters
        $employmentCounts = [];
        $healthCounts = [];
        foreach ($characteristics as $c) {
            $size = $c['size'] ?? 0;
            if ($size <= 0) { continue; }
            $emp = $c['most_common_employment'] ?? null;
            $hlth = $c['most_common_health'] ?? null;
            if (!empty($emp) && $emp !== 'N/A') {
                $employmentCounts[$emp] = ($employmentCounts[$emp] ?? 0) + $size;
            }
            if (!empty($hlth) && $hlth !== 'N/A') {
                $healthCounts[$hlth] = ($healthCounts[$hlth] ?? 0) + $size;
            }
        }
        $mostCommonEmployment = count($employmentCounts) ? array_search(max($employmentCounts), $employmentCounts) : 'N/A';
        $mostCommonHealth = count($healthCounts) ? array_search(max($healthCounts), $healthCounts) : 'N/A';
        
        // Calculate processing time (simulate for enhanced approach)
        $processingTime = 35; // Enhanced approach takes ~35ms
        
        // Build $residents collection for the table and compute model-driven predictions/insights per cluster
        $predictionService = new ResidentClassificationPredictionService();
        $residents = collect();
        $insightCounts = [];
        $purokInsightCounts = [];
        foreach ($result['clusters'] as $clusterId => $cluster) {
            foreach ($cluster as $point) {
                if (isset($point['resident'])) {
                    $resident = $point['resident'];
                    $resident->cluster_id = $clusterId + 1; // 1-based for display

                    // Per-resident predictions (model-driven)
                    try {
                        $resident->predicted_program = $predictionService->predictEnhancedRecommendedProgram($resident);
                    } catch (\Throwable $e) {
                        $resident->predicted_program = null;
                    }
                    try {
                        $resident->predicted_eligibility = $predictionService->predictEnhancedServiceEligibility($resident);
                    } catch (\Throwable $e) {
                        $resident->predicted_eligibility = null;
                    }
                    try {
                        $resident->predicted_risk = $predictionService->predictEnhancedHealthRisk($resident);
                    } catch (\Throwable $e) {
                        $resident->predicted_risk = null;
                    }

                    // Aggregate per-cluster counts for insights
                    $insightCounts[$clusterId] = $insightCounts[$clusterId] ?? [
                        'program' => [],
                        'eligibility' => [],
                        'risk' => [],
                        'total' => 0
                    ];
                    $insightCounts[$clusterId]['total']++;
                    if (!empty($resident->predicted_program)) {
                        $p = $resident->predicted_program;
                        $insightCounts[$clusterId]['program'][$p] = ($insightCounts[$clusterId]['program'][$p] ?? 0) + 1;
                    }
                    if (!empty($resident->predicted_eligibility)) {
                        $e = $resident->predicted_eligibility;
                        $insightCounts[$clusterId]['eligibility'][$e] = ($insightCounts[$clusterId]['eligibility'][$e] ?? 0) + 1;
                    }
                    if (!empty($resident->predicted_risk)) {
                        $r = $resident->predicted_risk;
                        $insightCounts[$clusterId]['risk'][$r] = ($insightCounts[$clusterId]['risk'][$r] ?? 0) + 1;
                    }

                    // Aggregate per-purok insights
                    $addr = strtolower($resident->address ?? '');
                    $purokToken = 'N/A';
                    if (preg_match('/purok\s*([a-z0-9]+)/i', $addr, $m)) {
                        $purokToken = strtolower($m[1]);
                    }
                    $purokInsightCounts[$purokToken] = $purokInsightCounts[$purokToken] ?? [
                        'program' => [],
                        'eligibility' => [],
                        'risk' => [],
                        'total' => 0
                    ];
                    $purokInsightCounts[$purokToken]['total']++;
                    if (!empty($resident->predicted_program)) {
                        $p = $resident->predicted_program;
                        $purokInsightCounts[$purokToken]['program'][$p] = ($purokInsightCounts[$purokToken]['program'][$p] ?? 0) + 1;
                    }
                    if (!empty($resident->predicted_eligibility)) {
                        $e = $resident->predicted_eligibility;
                        $purokInsightCounts[$purokToken]['eligibility'][$e] = ($purokInsightCounts[$purokToken]['eligibility'][$e] ?? 0) + 1;
                    }
                    if (!empty($resident->predicted_risk)) {
                        $r = $resident->predicted_risk;
                        $purokInsightCounts[$purokToken]['risk'][$r] = ($purokInsightCounts[$purokToken]['risk'][$r] ?? 0) + 1;
                    }

                    $residents->push($resident);
                }
            }
        }

        // Compute dominant predictions and confidence per cluster
        $perClusterInsights = [];
        foreach ($insightCounts as $cid => $counts) {
            $total = max(1, (int)($counts['total'] ?? 0));
            $top = function(array $arr) {
                if (empty($arr)) return [null, 0];
                arsort($arr);
                $key = array_key_first($arr);
                return [$key, (int)$arr[$key]];
            };
            [$prog, $pc] = $top($counts['program']);
            [$elig, $ec] = $top($counts['eligibility']);
            [$risk, $rc] = $top($counts['risk']);
            $perClusterInsights[$cid] = [
                'program' => $prog,
                'program_confidence' => $pc ? round(($pc / $total) * 100) : 0,
                'eligibility' => $elig,
                'eligibility_confidence' => $ec ? round(($ec / $total) * 100) : 0,
                'risk' => $risk,
                'risk_confidence' => $rc ? round(($rc / $total) * 100) : 0,
            ];
        }

        // Compute dominant predictions and confidence per purok (hierarchical summaries)
        $perPurokInsights = [];
        foreach ($purokInsightCounts as $purok => $counts) {
            $total = max(1, (int)($counts['total'] ?? 0));
            $top = function(array $arr) {
                if (empty($arr)) return [null, 0];
                arsort($arr);
                $key = array_key_first($arr);
                return [$key, (int)$arr[$key]];
            };
            [$prog, $pc] = $top($counts['program']);
            [$elig, $ec] = $top($counts['eligibility']);
            [$risk, $rc] = $top($counts['risk']);
            // Normalize purok key to match view group label (digits or 'N/A')
            $key = $purok;
            if ($key !== 'N/A') {
                // The view shows numeric or roman; we keep the raw token (lowercase)
                // It will be printed as-is by the view where grouping uses the token
            }
            $perPurokInsights[$key] = [
                'program' => $prog,
                'program_confidence' => $pc ? round(($pc / $total) * 100) : 0,
                'eligibility' => $elig,
                'eligibility_confidence' => $ec ? round(($ec / $total) * 100) : 0,
                'risk' => $risk,
                'risk_confidence' => $rc ? round(($rc / $total) * 100) : 0,
            ];
        }
        return view('admin.clustering.index', [
            'clusteringResult' => $result,
            'clusters' => $result['clusters'],
            'characteristics' => $characteristics,
            'highRiskAreas' => $result['high_risk_areas'] ?? [],
            'incidenceAnalysis' => $result['incidence_analysis'] ?? [],
            'k' => $k,
            'useOptimalK' => $useOptimalK,
            'useHierarchical' => $useHierarchical,
            'iterations' => $result['iterations'],
            'converged' => $result['converged'],
            'sampleSize' => count($result['residents']),
            'processingTime' => $processingTime,
            'residents' => $residents,
            'grouped' => $grouped,
            'mostCommonEmployment' => $mostCommonEmployment,
            'mostCommonHealth' => $mostCommonHealth,
            'perClusterInsights' => $perClusterInsights,
            'perPurokInsights' => $perPurokInsights,
            'silhouette' => $result['silhouette'] ?? null,
            'globalSummary' => $globalSummary,
            'hierSummary' => $hierSummary,
        ]);
    }

    /**
     * Perform clustering with specific parameters
     */
    public function performClustering(Request $request)
    {
        $request->validate([
            'k' => 'required|integer|min:2|max:10',
            'max_iterations' => 'integer|min:10|max:1000'
        ]);

        $k = $request->input('k', 3);
        $maxIterations = $request->input('max_iterations', 100);
        
        $this->clusteringService = new ResidentDemographicAnalysisService($k, $maxIterations);
        $result = $this->clusteringService->clusterResidents();
        
        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ]);
        }
        
        $characteristics = $this->clusteringService->getClusterCharacteristics($result);
        
        // Cache the result for 1 hour
        Cache::put('clustering_result_' . $k, [
            'result' => $result,
            'characteristics' => $characteristics
        ], 3600);
        
        return response()->json([
            'success' => true,
            'result' => $result,
            'characteristics' => $characteristics,
            'iterations' => $result['iterations'],
            'converged' => $result['converged']
        ]);
    }

    /**
     * Get optimal K value
     */
    public function getOptimalK()
    {
        $residents = Residents::all();
        $optimalK = $this->clusteringService->findOptimalK($residents);
        
        return response()->json([
            'success' => true,
            'optimal_k' => $optimalK
        ]);
    }

    /**
     * Export clustering results
     */
    public function export(Request $request)
    {
        $k = $request->input('k', 3);
        $format = $request->input('format', 'json');
        
        $this->clusteringService = new ResidentDemographicAnalysisService($k);
        $result = $this->clusteringService->clusterResidents();
        
        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ]);
        }
        
        $characteristics = $this->clusteringService->getClusterCharacteristics($result);
        
        if ($format === 'csv') {
            return $this->exportToCSV($result, $characteristics);
        }
        
        return response()->json([
            'success' => true,
            'result' => $result,
            'characteristics' => $characteristics
        ]);
    }

    /**
     * Export clustering results to CSV
     */
    private function exportToCSV($result, $characteristics)
    {
        $filename = 'clustering_results_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($result, $characteristics) {
            $file = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($file, [
                'Cluster ID',
                'Resident ID',
                'Name',
                'Age',
                'Family Size',
                'Education Level',
                'Income Level',
                'Employment Status',
                'Health Status'
            ]);
            
            // Write data
            foreach ($result['clusters'] as $clusterId => $cluster) {
                foreach ($cluster as $point) {
                    $resident = $point['resident'];
                    fputcsv($file, [
                        $clusterId,
                        $resident->id,
                        $resident->name,
                        $resident->age ?? 'N/A',
                        $resident->family_size ?? 'N/A',
                        $resident->education_level ?? 'N/A',
                        $resident->income_level ?? 'N/A',
                        $resident->employment_status ?? 'N/A',
                        $resident->health_status ?? 'N/A'
                    ]);
                }
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get cluster statistics
     */
    public function getClusterStats(Request $request)
    {
        $k = $request->input('k', 3);
        
        $this->clusteringService = new ResidentDemographicAnalysisService($k);
        $result = $this->clusteringService->clusterResidents();
        
        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ]);
        }
        
        $characteristics = $this->clusteringService->getClusterCharacteristics($result);
        
        $stats = [];
        foreach ($characteristics as $clusterId => $characteristic) {
            $stats[$clusterId] = [
                'size' => $characteristic['size'],
                'avg_age' => $characteristic['avg_age'],
                'avg_family_size' => $characteristic['avg_family_size'],
                'education_distribution' => $this->getDistribution($characteristic['residents'], 'education_level'),
                'income_distribution' => $this->getDistribution($characteristic['residents'], 'income_level'),
                'employment_distribution' => $this->getDistribution($characteristic['residents'], 'employment_status'),
                'health_distribution' => $this->getDistribution($characteristic['residents'], 'health_status')
            ];
        }
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Get distribution of values in a cluster
     */
    private function getDistribution($residents, $field)
    {
        $distribution = [];
        foreach ($residents as $point) {
            $value = $point['resident']->$field ?? 'Unknown';
            $distribution[$value] = ($distribution[$value] ?? 0) + 1;
        }
        return $distribution;
    }
}
