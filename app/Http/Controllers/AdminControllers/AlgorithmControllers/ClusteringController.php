<?php

namespace App\Http\Controllers\AdminControllers\AlgorithmControllers;

use App\Services\PythonAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Residents;
use App\Models\BlotterRequest;
use App\Models\MedicalRecord;
use App\Models\MedicineTransaction;

class ClusteringController
{
    private $pythonService;

    public function __construct(PythonAnalyticsService $pythonService)
    {
        $this->pythonService = $pythonService;
    }
    
    /**
     * Ensure Python analytics service is available
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
     * Main index method for purok risk clustering
     */
    public function index()
    {
        // Fixed k=3 for Low/Moderate/High risk clustering
        $k = 3;
        
        $residents = Residents::select('id', 'first_name', 'middle_name', 'last_name', 'suffix', 'age', 'family_size', 'education_level', 'income_level', 'employment_status', 'is_pwd', 'address')->get();
        
        if ($residents->count() < 3) {
            return view('admin.clustering.index', [
                'error' => 'Not enough residents for clustering (minimum 3 required)',
                'clusters' => [],
                'purokData' => [],
                'k' => $k,
            ]);
        }
        
        // Measure processing time
        $t0 = microtime(true);

        // Python service is required
        try {
            $this->ensurePythonAvailable();
        } catch (\Throwable $e) {
            return view('admin.clustering.index', [
                'error' => $e->getMessage(),
                'clusters' => [],
                'purokData' => [],
                'k' => $k,
            ]);
        }
        
        // Aggregate data by purok
        $purokData = $this->aggregateDataByPurok($residents);

        if (empty($purokData)) {
            return view('admin.clustering.index', [
                'error' => 'No purok data available for clustering',
                'clusters' => [],
                'purokData' => [],
                'k' => $k,
            ]);
        }

        if (count($purokData) < $k) {
            // Adjust k if there are fewer puroks than requested clusters
            $k = count($purokData);
        }

        // Log purok data for debugging
        Log::info('Purok Risk Clustering: Aggregated data', [
            'total_puroks' => count($purokData),
            'k' => $k,
            'features' => ['blotter_count', 'demographic_score', 'medical_count', 'medicine_count']
        ]);

        // Build feature samples for clustering
        $samples = $this->pythonService->buildPurokRiskFeatures($purokData);

        if (empty($samples)) {
            return view('admin.clustering.index', [
                'error' => 'Failed to build feature samples for clustering',
                'clusters' => [],
                'purokData' => [],
                'k' => $k,
            ]);
        }

        // Perform K-means clustering with k=3
        try {
            $pythonResult = $this->pythonService->kmeansClustering($samples, $k, 100, 3);
            
            if (isset($pythonResult['error'])) {
                return view('admin.clustering.index', [
                    'error' => 'Clustering failed: ' . $pythonResult['error'],
                    'clusters' => [],
                    'purokData' => [],
                    'k' => $k,
                ]);
            }

            $labels = $pythonResult['labels'] ?? [];
            $centroids = $pythonResult['centroids'] ?? [];
            $silhouette = $pythonResult['metrics']['silhouette_score'] ?? 0;

            if (!is_array($labels) || empty($labels)) {
                return view('admin.clustering.index', [
                    'error' => 'No cluster labels returned from clustering algorithm',
                    'clusters' => [],
                    'purokData' => [],
                    'k' => $k,
                ]);
            }

            // Group puroks by their cluster assignment
            $clusters = [];
            foreach ($labels as $purokIndex => $clusterId) {
                if (!isset($clusters[$clusterId])) {
                    $clusters[$clusterId] = [];
                }
                $clusters[$clusterId][] = $purokIndex;
            }

            // Label clusters as Low/Moderate/High risk
            $clusterLabels = $this->labelClustersByRisk($clusters, $centroids, $purokData);

            Log::info('Purok Risk Clustering: Completed successfully', [
                'total_puroks' => count($purokData),
                'clusters_formed' => count($clusters),
                'silhouette_score' => $silhouette,
                'cluster_labels' => $clusterLabels
            ]);

            // Prepare cluster data with aggregated metrics for each cluster
            $clusterData = [];
            foreach ($clusters as $clusterId => $purokIndices) {
                $clusterPuroks = [];
                $totalBlotter = 0;
                $totalMedical = 0;
                $totalMedicine = 0;
                $totalResidents = 0;
                $avgDemographic = 0;

                foreach ($purokIndices as $purokIndex) {
                    if (isset($purokData[$purokIndex])) {
                        $purok = $purokData[$purokIndex];
                        $clusterPuroks[] = $purok;
                        $totalBlotter += $purok['blotter_count'];
                        $totalMedical += $purok['medical_count'];
                        $totalMedicine += $purok['medicine_count'];
                        $totalResidents += $purok['resident_count'];
                        $avgDemographic += $purok['demographic_score'];
                    }
                }

                $avgDemographic = count($clusterPuroks) > 0 ? $avgDemographic / count($clusterPuroks) : 0;

                $clusterData[$clusterId] = [
                    'id' => $clusterId,
                    'label' => $clusterLabels[$clusterId] ?? 'Cluster ' . ($clusterId + 1),
                    'puroks' => $clusterPuroks,
                    'purok_count' => count($clusterPuroks),
                    'total_blotter' => $totalBlotter,
                    'total_medical' => $totalMedical,
                    'total_medicine' => $totalMedicine,
                    'total_residents' => $totalResidents,
                    'avg_demographic' => $avgDemographic,
                ];
            }

            // Sort clusters by label (Low, Moderate, High)
            $labelOrder = ['Low Risk' => 1, 'Moderate Risk' => 2, 'High Risk' => 3];
            uasort($clusterData, function($a, $b) use ($labelOrder) {
                $orderA = $labelOrder[$a['label']] ?? 999;
                $orderB = $labelOrder[$b['label']] ?? 999;
                return $orderA <=> $orderB;
            });

            $processingTime = (int)round((microtime(true) - $t0) * 1000);

        } catch (\Throwable $e) {
            Log::error('Purok Risk Clustering: Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return view('admin.clustering.index', [
                'error' => 'Clustering failed: ' . $e->getMessage(),
                'clusters' => [],
                'purokData' => [],
                'k' => $k,
            ]);
        }

        // Return view with cluster data
        return view('admin.clustering.index', [
            'clusters' => $clusterData,
            'purokData' => $purokData,
            'k' => $k,
            'silhouette' => $silhouette,
            'processingTime' => $processingTime,
            'totalPuroks' => count($purokData),
        ]);
    }

    /**
     * Aggregate all data by purok for combined risk clustering
     */
    private function aggregateDataByPurok($residents): array
    {
        $purokData = [];
        
        // Group residents by purok
        $groupedResidents = [];
        foreach ($residents as $resident) {
            $purokToken = $this->extractPurokToken($resident->address ?? null);
            if (!isset($groupedResidents[$purokToken])) {
                $groupedResidents[$purokToken] = [];
            }
            $groupedResidents[$purokToken][] = $resident;
        }

        // Aggregate data for each purok
        foreach ($groupedResidents as $purokToken => $purokResidents) {
            if (count($purokResidents) == 0) {
                        continue;
            }

            $residentIds = array_map(function($r) { return $r->id; }, $purokResidents);
            $purokDisplay = $purokToken === 'n/a' ? 'N/A' : 'Purok ' . strtoupper($purokToken);

            // Count blotter incidents (using respondent_id as that's the column name in blotter_requests)
            $blotterCount = BlotterRequest::whereIn('respondent_id', $residentIds)->count();

            // Count medical visits
            $medicalCount = MedicalRecord::whereIn('resident_id', $residentIds)->count();

            // Count medicine dispenses (OUT transactions only)
            $medicineCount = MedicineTransaction::where('transaction_type', 'OUT')
                ->whereIn('resident_id', $residentIds)
                ->sum('quantity');

            // Calculate demographic score (average age and family size)
            $demographicScore = $this->calculatePurokDemographicScore($purokResidents);

            $purokData[] = [
                'purok_token' => $purokToken,
                'purok_display' => $purokDisplay,
                'resident_ids' => $residentIds,
                'resident_count' => count($purokResidents),
                'blotter_count' => (int)$blotterCount,
                'demographic_score' => $demographicScore,
                'medical_count' => (int)$medicalCount,
                'medicine_count' => (int)$medicineCount,
            ];
        }

        return $purokData;
    }

    /**
     * Calculate demographic risk score for a purok
     * Higher score = higher need (younger population + larger families)
     */
    private function calculatePurokDemographicScore(array $residents): float
    {
        if (empty($residents)) {
            return 0.0;
        }

        $totalAge = 0;
        $totalFamilySize = 0;
        $count = 0;

        foreach ($residents as $resident) {
            $age = $resident->age ?? 0;
            $familySize = $resident->family_size ?? 1;
            
            $totalAge += $age;
            $totalFamilySize += $familySize;
            $count++;
        }

        if ($count == 0) {
            return 0.0;
        }

        $avgAge = $totalAge / $count;
        $avgFamilySize = $totalFamilySize / $count;

        // Normalize: Younger age (0-100) and larger family size (1-20) indicate higher need
        // Invert age: younger = higher score
        $ageScore = (100 - min(100, $avgAge)) / 100; // 0-1 scale, inverted
        $familySizeScore = min(20, $avgFamilySize) / 20; // 0-1 scale

        // Average the two scores
        return ($ageScore + $familySizeScore) / 2;
    }

    /**
     * Label clusters as Low/Moderate/High risk based on centroid analysis
     * Returns mapping: [originalClusterId => 'Low Risk'|'Moderate Risk'|'High Risk']
     */
    private function labelClustersByRisk(array $clusters, array $centroids, array $purokData): array
    {
        // Calculate average risk score for each cluster
        $clusterRiskScores = [];
        
        foreach ($clusters as $clusterId => $purokIndices) {
            $totalRisk = 0.0;
            $count = 0;
            
            foreach ($purokIndices as $index) {
                if (isset($purokData[$index])) {
                    $data = $purokData[$index];
                    // Average all 4 normalized features as overall risk
                    $risk = ($data['blotter_count'] + $data['demographic_score'] + 
                            $data['medical_count'] + $data['medicine_count']) / 4;
                    $totalRisk += $risk;
                    $count++;
                }
            }
            
            $avgRisk = $count > 0 ? $totalRisk / $count : 0;
            $clusterRiskScores[$clusterId] = $avgRisk;
        }

        // Sort clusters by risk score
        asort($clusterRiskScores);
        $sortedClusterIds = array_keys($clusterRiskScores);

        // Assign labels based on ranking
        $labels = [];
        $numClusters = count($sortedClusterIds);
        
        foreach ($sortedClusterIds as $index => $clusterId) {
            if ($numClusters == 1) {
                $labels[$clusterId] = 'Moderate Risk';
            } elseif ($numClusters == 2) {
                $labels[$clusterId] = $index == 0 ? 'Low Risk' : 'High Risk';
            } else {
                // 3 or more clusters
                if ($index == 0) {
                    $labels[$clusterId] = 'Low Risk';
                } elseif ($index == $numClusters - 1) {
                    $labels[$clusterId] = 'High Risk';
                    } else {
                    $labels[$clusterId] = 'Moderate Risk';
                }
            }
        }

        return $labels;
    }

    /**
     * Extract purok token from address
     */
    private function extractPurokToken(?string $address): string
    {
        $addr = trim($address ?? '');
        if ($addr === '') return 'n/a';
        
        // Normalize to lowercase for consistent matching
        $addrLower = strtolower($addr);
        
        // Common patterns
        $patterns = [
            '/\bpurok\s*([0-9]+[a-z]?)/i',
            '/\bpurok\s*([ivxlcdm]+)/i',
            '/\bprk\s*[-]?\s*([0-9]+[a-z]?)/i',
            '/\bprk\.\s*([0-9]+[a-z]?)/i',
            '/\bzone\s*([0-9]+[a-z]?)/i',
            '/\bbrgy\s*([0-9]+[a-z]?)/i',
        ];
        
        foreach ($patterns as $pat) {
            if (preg_match($pat, $addrLower, $m)) {
                $token = strtolower(trim($m[1]));
                if (preg_match('/^(\d+)([a-z]?)$/', $token, $numMatch)) {
                    $num = (int)$numMatch[1];
                    $letter = $numMatch[2] ?? '';
                    $token = str_pad($num, 2, '0', STR_PAD_LEFT) . $letter;
                }
                return $token;
            }
        }
        
        return 'n/a';
    }
}
