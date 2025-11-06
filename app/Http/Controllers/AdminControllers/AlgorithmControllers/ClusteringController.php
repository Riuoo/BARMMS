<?php

namespace App\Http\Controllers\AdminControllers\AlgorithmControllers;

use App\Services\PythonAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Residents;

class ClusteringController
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

    private function extractPurokToken(?string $address): string
    {
        $addr = strtolower($address ?? '');
        if ($addr === '') return 'N/A';
        // Common patterns: "purok 1", "purok i", "prk-2", "zone 3"
        $patterns = [
            '/\bpurok\s*([0-9]+)/i',                 // Purok 1
            '/\bpurok\s*([ivxlcdm]+)/i',            // Purok II (roman)
            '/\bprk\s*[-]?\s*([0-9]+)/i',          // Prk-2 or Prk 2
            '/\bzone\s*([0-9]+)/i',                 // Zone 3
        ];
        foreach ($patterns as $pat) {
            if (preg_match($pat, $addr, $m)) {
                $token = strtoupper($m[1]);
                // Normalize roman numerals to roman (keep as-is), digits stay digits
                return $token;
            }
        }
        return 'N/A';
    }

    /**
     * Perform clustering via Python by type, with normalized response shape
     */
    private function performClusteringByType(array $samples, string $type = 'kmeans', array $params = []): array
    {
        // Normalize and clamp params
        $k = max(2, min((int)($params['k'] ?? 3), 50));
        $maxIterations = max(10, min((int)($params['max_iterations'] ?? 100), 10000));
        $numRuns = max(1, min((int)($params['num_runs'] ?? 3), 50));
        $linkage = $params['linkage'] ?? 'ward';

        if ($type === 'hierarchical') {
            $resp = $this->pythonService->hierarchicalClustering($samples, $k, $linkage);
        } else {
            // default to kmeans
            $resp = $this->pythonService->kmeansClustering($samples, $k, $maxIterations, $numRuns);
        }

        if (isset($resp['error'])) {
            return ['error' => $resp['error']];
        }

        // Ensure normalized structure
        $labels = $resp['labels'] ?? [];
        $centers = $resp['centroids'] ?? ($resp['centers'] ?? []);
        $metrics = $resp['metrics'] ?? [];

        return [
            'type' => $type,
            'labels' => $labels,
            'centers' => $centers,
            'metrics' => [
                'silhouette' => $metrics['silhouette_score'] ?? null,
                'inertia' => $metrics['inertia'] ?? null,
                'iterations' => $metrics['iterations'] ?? null,
                'converged' => $metrics['converged'] ?? true,
            ],
            'characteristics' => $resp['characteristics'] ?? [],
        ];
    }

    /**
     * Display clustering analysis page
     */
    public function index()
    {
        $k = request('k', 3);
        $useOptimalK = request('use_optimal_k', false);
        
        $residents = Residents::select('id', 'name', 'age', 'family_size', 'education_level', 'income_level', 'employment_status', 'health_status', 'address')->get();
        $allResidents = $residents; // keep original list for purok stats
        
        // Python service is required
        $this->ensurePythonAvailable();
        
        // Find optimal K if requested
        if ($useOptimalK) {
            $samples = $this->pythonService->buildSamplesFromResidents($residents);
            $optimalKResult = $this->pythonService->findOptimalK($samples, 10, 'silhouette');
            if (!isset($optimalKResult['error'])) {
                $k = $optimalKResult['optimal_k'];
            } else {
                throw new \Exception('Failed to find optimal K: ' . ($optimalKResult['error'] ?? 'Unknown error'));
            }
        }
        
        // Perform K-Means clustering using Python
        $samples = $this->pythonService->buildSamplesFromResidents($residents);
        $pythonResult = $this->pythonService->kmeansClustering($samples, $k, 100, 3);
        
        if (isset($pythonResult['error'])) {
            throw new \Exception('Python clustering failed: ' . $pythonResult['error']);
        }
        
        // Convert Python result to PHP format
        $globalResult = $this->convertPythonResultToPhpFormat($pythonResult, $residents);
        $globalCharacteristics = $this->extractCharacteristicsFromPythonResult($pythonResult, $residents);
        $globalSummary = [
            'sizes' => array_map(fn($c) => $c['size'], $globalCharacteristics),
            'silhouette' => $globalResult['silhouette'] ?? null,
            'k' => $k,
        ];
        
        // Clusters-only view (no hierarchical/purok mode)
        $result = $globalResult;
        $characteristics = $globalCharacteristics;

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
        
        // Build $residents collection for the table
        // Note: Predictions are now handled by Python service in DecisionTreeController
        $residents = collect();
        $insightCounts = [];
        $purokInsightCounts = [];
        foreach ($result['clusters'] as $clusterId => $cluster) {
            foreach ($cluster as $point) {
                if (isset($point['resident'])) {
                    $resident = $point['resident'];
                    $resident->cluster_id = $clusterId + 1; // 1-based for display

                    // Note: Predictions are handled by Python service in DecisionTreeController
                    // These can be added later if needed via Python service

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
                    $purokToken = strtolower($this->extractPurokToken($resident->address ?? null));
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

        // Clusters-only: drop per-purok summaries
        return view('admin.clustering.index', [
            'clusteringResult' => $result,
            'clusters' => $result['clusters'],
            'characteristics' => $characteristics,
            'highRiskAreas' => $result['high_risk_areas'] ?? [],
            'incidenceAnalysis' => $result['incidence_analysis'] ?? [],
            'k' => $k,
            'useOptimalK' => $useOptimalK,
            'iterations' => $result['iterations'],
            'converged' => $result['converged'],
            'sampleSize' => count($result['residents']),
            'processingTime' => $processingTime,
            'residents' => $residents,
            'grouped' => $grouped,
            'mostCommonEmployment' => $mostCommonEmployment,
            'mostCommonHealth' => $mostCommonHealth,
            'perClusterInsights' => $perClusterInsights,
            'silhouette' => $result['silhouette'] ?? null,
            'globalSummary' => $globalSummary,
            'hierSummary' => [],
            'incomeColors' => ['#EF4444', '#F59E0B', '#8B5CF6', '#10B981', '#3B82F6'],
            'employmentColors' => ['#EF4444', '#F59E0B', '#8B5CF6', '#10B981'],
            'healthColors' => ['#EF4444', '#F97316', '#F59E0B', '#3B82F6', '#10B981'],
        ]);
    }

    /**
     * Perform clustering with specific parameters
     */
    public function performClustering(Request $request)
    {
        $request->validate([
            'k' => 'required|integer|min:2|max:50',
            'max_iterations' => 'integer|min:10|max:10000',
            'num_runs' => 'integer|min:1|max:50',
            'type' => 'in:kmeans,hierarchical',
            'linkage' => 'in:ward,complete,average,single'
        ]);

        $type = $request->input('type', 'kmeans');
        $k = (int)$request->input('k', 3);
        $maxIterations = (int)$request->input('max_iterations', 100);
        $numRuns = (int)$request->input('num_runs', 3);
        $linkage = $request->input('linkage', 'ward');
        
        $this->ensurePythonAvailable();
        $residents = Residents::select('id', 'name', 'age', 'family_size', 'education_level', 'income_level', 'employment_status', 'health_status', 'address')->get();
        $samples = $this->pythonService->buildSamplesFromResidents($residents);

        $normalized = $this->performClusteringByType($samples, $type, [
            'k' => $k,
            'max_iterations' => $maxIterations,
            'num_runs' => $numRuns,
            'linkage' => $linkage,
        ]);

        if (isset($normalized['error'])) {
            return response()->json(['success' => false, 'error' => $normalized['error']]);
        }

        // Convert to current view structures
        $pythonResult = [
            'labels' => $normalized['labels'],
            'centroids' => $normalized['centers'],
            'metrics' => [
                'silhouette_score' => $normalized['metrics']['silhouette'],
                'iterations' => $normalized['metrics']['iterations'],
            ],
            'characteristics' => $normalized['characteristics'],
        ];

        $result = $this->convertPythonResultToPhpFormat($pythonResult, $residents);
        $characteristics = $this->extractCharacteristicsFromPythonResult($pythonResult, $residents);
        
        Cache::put('clustering_result_' . $k . '_' . $type, [
            'result' => $result,
            'characteristics' => $characteristics
        ], 3600);
        
        return response()->json([
            'success' => true,
            'type' => $type,
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
        // Python service is required
        $this->ensurePythonAvailable();
        
        $residents = Residents::select('id', 'name', 'age', 'family_size', 'education_level', 'income_level', 'employment_status', 'health_status', 'address')->get();
        $samples = $this->pythonService->buildSamplesFromResidents($residents);
        $optimalKResult = $this->pythonService->findOptimalK($samples, 10, 'silhouette');
        
        if (isset($optimalKResult['error'])) {
            return response()->json([
                'success' => false,
                'error' => $optimalKResult['error']
            ]);
        }
        
        return response()->json([
            'success' => true,
            'optimal_k' => $optimalKResult['optimal_k'],
            'method' => $optimalKResult['method'],
            'scores' => $optimalKResult['scores'] ?? []
        ]);
    }

    /**
     * Export clustering results
     */
    public function export(Request $request)
    {
        $k = (int)$request->input('k', 3);
        $format = $request->input('format', 'json');
        $type = $request->input('type', 'kmeans');

        $this->ensurePythonAvailable();
        $residents = Residents::select('id', 'name', 'age', 'family_size', 'education_level', 'income_level', 'employment_status', 'health_status', 'address')->get();
        $samples = $this->pythonService->buildSamplesFromResidents($residents);

        $normalized = $this->performClusteringByType($samples, $type, ['k' => $k]);
        if (isset($normalized['error'])) {
            return response()->json(['success' => false, 'error' => $normalized['error']]);
        }
        $pythonResult = [
            'labels' => $normalized['labels'],
            'centroids' => $normalized['centers'],
            'metrics' => [
                'silhouette_score' => $normalized['metrics']['silhouette'],
                'iterations' => $normalized['metrics']['iterations'],
            ],
            'characteristics' => $normalized['characteristics'],
        ];

        $result = $this->convertPythonResultToPhpFormat($pythonResult, $residents);
        $characteristics = $this->extractCharacteristicsFromPythonResult($pythonResult, $residents);
        
        if ($format === 'csv') {
            return $this->exportToCSV($result, $characteristics);
        }
        
        return response()->json([
            'success' => true,
            'type' => $type,
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
        $k = (int)$request->input('k', 3);
        $type = $request->input('type', 'kmeans');

        $this->ensurePythonAvailable();
        $residents = Residents::select('id', 'name', 'age', 'family_size', 'education_level', 'income_level', 'employment_status', 'health_status', 'address')->get();
        $samples = $this->pythonService->buildSamplesFromResidents($residents);
        $normalized = $this->performClusteringByType($samples, $type, ['k' => $k]);
        if (isset($normalized['error'])) {
            return response()->json(['success' => false, 'error' => $normalized['error']]);
        }
        $pythonResult = [
            'labels' => $normalized['labels'],
            'centroids' => $normalized['centers'],
            'metrics' => [
                'silhouette_score' => $normalized['metrics']['silhouette'],
                'iterations' => $normalized['metrics']['iterations'],
            ],
            'characteristics' => $normalized['characteristics'],
        ];

        $result = $this->convertPythonResultToPhpFormat($pythonResult, $residents);
        $characteristics = $this->extractCharacteristicsFromPythonResult($pythonResult, $residents);
        
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

    /**
     * Convert Python clustering result to PHP format
     */
    private function convertPythonResultToPhpFormat(array $pythonResult, $residents): array
    {
        $clusters = [];
        $labels = $pythonResult['labels'] ?? [];
        $residentsArray = $residents->toArray();
        
        // Group residents by cluster
        foreach ($labels as $index => $clusterId) {
            if (!isset($clusters[$clusterId])) {
                $clusters[$clusterId] = [];
            }
            
            if (isset($residentsArray[$index])) {
                $resident = $residents->get($index);
                $clusters[$clusterId][] = [
                    'id' => $resident->id,
                    'features' => [],
                    'resident' => $resident
                ];
            }
        }
        
        // Extract centroids
        $centroids = [];
        if (isset($pythonResult['centroids'])) {
            foreach ($pythonResult['centroids'] as $idx => $centroid) {
                $centroids[$idx] = $centroid;
            }
        }
        
        // Extract metrics
        $silhouette = $pythonResult['metrics']['silhouette_score'] ?? 0;
        $iterations = $pythonResult['metrics']['iterations'] ?? 100;
        
        return [
            'clusters' => $clusters,
            'centroids' => $centroids,
            'iterations' => $iterations,
            'converged' => true,
            'silhouette' => round($silhouette, 3),
            'residents' => $residents,
            'python_metrics' => $pythonResult['metrics'] ?? []
        ];
    }

    /**
     * Extract characteristics from Python result
     */
    private function extractCharacteristicsFromPythonResult(array $pythonResult, $residents): array
    {
        $characteristics = [];
        $labels = $pythonResult['labels'] ?? [];
        $pythonCharacteristics = $pythonResult['characteristics'] ?? [];
        
        // Group residents by cluster
        $clusterGroups = [];
        foreach ($labels as $index => $clusterId) {
            if (!isset($clusterGroups[$clusterId])) {
                $clusterGroups[$clusterId] = [];
            }
            $clusterGroups[$clusterId][] = $residents->get($index);
        }
        
        // Build characteristics for each cluster
        foreach ($clusterGroups as $clusterId => $clusterResidents) {
            $pythonChar = $pythonCharacteristics[$clusterId] ?? [];
            
            // Calculate distributions
            $incomeDistribution = [];
            $employmentDistribution = [];
            $healthDistribution = [];
            $educationDistribution = [];
            
            foreach ($clusterResidents as $resident) {
                $income = $resident->income_level ?? 'Unknown';
                $employment = $resident->employment_status ?? 'Unknown';
                $health = $resident->health_status ?? 'Unknown';
                $education = $resident->education_level ?? 'Unknown';
                
                $incomeDistribution[$income] = ($incomeDistribution[$income] ?? 0) + 1;
                $employmentDistribution[$employment] = ($employmentDistribution[$employment] ?? 0) + 1;
                $healthDistribution[$health] = ($healthDistribution[$health] ?? 0) + 1;
                $educationDistribution[$education] = ($educationDistribution[$education] ?? 0) + 1;
            }
            
            // Find most common purok
            $purokCounts = [];
            foreach ($clusterResidents as $resident) {
                $address = strtolower($resident->address ?? '');
                if (preg_match('/purok\s*([a-z0-9]+)/i', $address, $m)) {
                    $purok = strtoupper($m[1]);
                    $purokCounts[$purok] = ($purokCounts[$purok] ?? 0) + 1;
                }
            }
            $mostCommonPurok = count($purokCounts) > 0 ? array_search(max($purokCounts), $purokCounts) : 'N/A';
            
            $characteristics[$clusterId] = [
                'size' => count($clusterResidents),
                'avg_age' => $pythonChar['avg_age'] ?? 0,
                'avg_family_size' => $pythonChar['avg_family_size'] ?? 0,
                'income_distribution' => $incomeDistribution,
                'employment_distribution' => $employmentDistribution,
                'health_distribution' => $healthDistribution,
                'education_distribution' => $educationDistribution,
                'most_common_purok' => $mostCommonPurok,
                'most_common_employment' => count($employmentDistribution) > 0 ? array_search(max($employmentDistribution), $employmentDistribution) : 'N/A',
                'most_common_health' => count($healthDistribution) > 0 ? array_search(max($healthDistribution), $healthDistribution) : 'N/A',
                'residents' => array_map(function($r) {
                    return ['resident' => $r];
                }, is_array($clusterResidents) ? $clusterResidents : $clusterResidents->toArray())
            ];
        }
        
        return array_values($characteristics);
    }

}
