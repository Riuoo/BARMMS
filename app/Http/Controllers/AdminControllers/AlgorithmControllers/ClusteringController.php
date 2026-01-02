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
use App\Models\MedicineRequest;
use Illuminate\Support\Facades\DB;

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
            $baseUrl = config('services.python_analytics.url', 'http://localhost:5000');
            throw new \Exception('The Python analytics service (app.py) is not running. Please start the Python service at ' . $baseUrl . ' to use this feature.');
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
        
        // Format data for Python
        $residentsData = $residents->map(function ($r) {
            return [
                'id' => $r->id,
                'address' => $r->address,
                'age' => $r->age,
                'family_size' => $r->family_size,
            ];
        })->toArray();
        
        $blottersData = BlotterRequest::select('id', 'respondent_id', 'type')
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'respondent_id' => $b->respondent_id,
                    'type' => $b->type,
                ];
            })->toArray();
        
        $medicalRecordsData = MedicalRecord::select('id', 'resident_id', 'diagnosis')
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'resident_id' => $m->resident_id,
                    'diagnosis' => $m->diagnosis,
                ];
            })->toArray();
        
        $medicineTransactionsData = MedicineTransaction::where('transaction_type', 'OUT')
            ->whereHas('medicine')
            ->select('id', 'resident_id', 'medicine_id', 'quantity', 'transaction_type')
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'resident_id' => $t->resident_id,
                    'medicine_id' => $t->medicine_id,
                    'quantity' => $t->quantity,
                    'transaction_type' => $t->transaction_type,
                ];
            })->toArray();
        
        $medicineRequestsData = MedicineRequest::whereHas('medicine')
            ->select('id', 'resident_id', 'medicine_id', 'quantity_requested')
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'resident_id' => $r->resident_id,
                    'medicine_id' => $r->medicine_id,
                    'quantity_requested' => $r->quantity_requested ?? 1,
                ];
            })->toArray();
        
        // Get medicine names mapping (needed for analytics)
        $medicineNames = \App\Models\Medicine::pluck('name', 'id')->toArray();
        
        // Aggregate data by purok using Python
        try {
            $purokData = $this->pythonService->aggregatePurokData(
                $residentsData,
                $blottersData,
                $medicalRecordsData,
                $medicineTransactionsData,
                $medicineRequestsData,
                $medicineNames
            );
        } catch (\Exception $e) {
            Log::error('Error aggregating purok data: ' . $e->getMessage());
            return view('admin.clustering.index', [
                'error' => 'Failed to aggregate data: ' . $e->getMessage(),
                'clusters' => [],
                'purokData' => [],
                'k' => $k,
            ]);
        }

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

        // Build feature samples for clustering using Python
        try {
            $samples = $this->pythonService->buildPurokRiskFeatures($purokData);
        } catch (\Exception $e) {
            Log::error('Error building purok features: ' . $e->getMessage());
            return view('admin.clustering.index', [
                'error' => 'Failed to build features: ' . $e->getMessage(),
                'clusters' => [],
                'purokData' => [],
                'k' => $k,
            ]);
        }

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

            // Label clusters as Low/Moderate/High risk using Python
            try {
                $clusterLabels = $this->pythonService->labelClustersByRisk($clusters, $purokData);
            } catch (\Exception $e) {
                Log::error('Error labeling clusters: ' . $e->getMessage());
                // Fallback to simple labeling
                $clusterLabels = [];
                $clusterIds = array_keys($clusters);
                foreach ($clusterIds as $index => $clusterId) {
                    if (count($clusterIds) == 1) {
                        $clusterLabels[$clusterId] = 'Moderate Risk';
                    } elseif (count($clusterIds) == 2) {
                        $clusterLabels[$clusterId] = $index == 0 ? 'Low Risk' : 'High Risk';
                    } else {
                        if ($index == 0) {
                            $clusterLabels[$clusterId] = 'Low Risk';
                        } elseif ($index == count($clusterIds) - 1) {
                            $clusterLabels[$clusterId] = 'High Risk';
                        } else {
                            $clusterLabels[$clusterId] = 'Moderate Risk';
                        }
                    }
                }
            }

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

                // Collect all resident IDs for this cluster
                $clusterResidentIds = [];
                foreach ($clusterPuroks as $purok) {
                    $clusterResidentIds = array_merge($clusterResidentIds, $purok['resident_ids']);
                }
                $clusterResidentIds = array_unique($clusterResidentIds);

                // Compute analytics for this cluster using Python
                try {
                    // Format blotters for this cluster
                    $clusterBlotters = BlotterRequest::whereIn('respondent_id', $clusterResidentIds)
                        ->select('id', 'respondent_id', 'type')
                        ->get()
                        ->map(function ($b) {
                            return [
                                'id' => $b->id,
                                'respondent_id' => $b->respondent_id,
                                'type' => $b->type,
                            ];
                        })->toArray();
                    
                    $incidentAnalytics = $this->pythonService->computeIncidentAnalytics($clusterResidentIds, $clusterBlotters);
                } catch (\Exception $e) {
                    Log::error('Error computing incident analytics: ' . $e->getMessage());
                    $incidentAnalytics = ['case_types' => []];
                }
                
                try {
                    // Format medical records for this cluster
                    $clusterMedicalRecords = MedicalRecord::whereIn('resident_id', $clusterResidentIds)
                        ->select('id', 'resident_id', 'diagnosis')
                        ->get()
                        ->map(function ($m) {
                            return [
                                'id' => $m->id,
                                'resident_id' => $m->resident_id,
                                'diagnosis' => $m->diagnosis,
                            ];
                        })->toArray();
                    
                    $medicalAnalytics = $this->pythonService->computeMedicalAnalytics($clusterPuroks, $clusterResidentIds, $clusterMedicalRecords);
                } catch (\Exception $e) {
                    Log::error('Error computing medical analytics: ' . $e->getMessage());
                    $medicalAnalytics = ['visits_by_purok' => [], 'illnesses' => []];
                }
                
                // Aggregate medicine analytics from puroks (more reliable than recomputing)
                try {
                    $medicineAnalytics = $this->aggregateMedicineAnalyticsFromPuroks($clusterPuroks);
                    
                    // Fallback: if no medicines found, compute directly from database
                    if (empty($medicineAnalytics['medicines']) && $totalMedicine > 0) {
                        Log::warning('No medicines in purok analytics, computing directly from database for cluster ' . $clusterId);
                        $medicineAnalytics = $this->computeMedicineAnalyticsDirectly($clusterResidentIds);
                    }
                } catch (\Exception $e) {
                    Log::error('Error aggregating medicine analytics: ' . $e->getMessage());
                    // Fallback to direct computation
                    try {
                        $medicineAnalytics = $this->computeMedicineAnalyticsDirectly($clusterResidentIds);
                    } catch (\Exception $e2) {
                        Log::error('Error in fallback medicine analytics: ' . $e2->getMessage());
                        $medicineAnalytics = ['medicines' => []];
                    }
                }

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
                    'incident_analytics' => $incidentAnalytics,
                    'medical_analytics' => $medicalAnalytics,
                    'medicine_analytics' => $medicineAnalytics,
                ];
            }

            // Sort clusters by label (Low, Moderate, High)
            $labelOrder = ['Low Risk' => 1, 'Moderate Risk' => 2, 'High Risk' => 3];
            uasort($clusterData, function($a, $b) use ($labelOrder) {
                $orderA = $labelOrder[$a['label']] ?? 999;
                $orderB = $labelOrder[$b['label']] ?? 999;
                return $orderA <=> $orderB;
            });
            
            // Re-index array to ensure sequential keys for proper JSON encoding
            $clusterData = array_values($clusterData);

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

        // Calculate aggregate statistics
        $totalResidents = array_sum(array_column($clusterData, 'total_residents'));
        $highRiskPuroks = 0;
        foreach ($clusterData as $cluster) {
            if (str_contains($cluster['label'], 'High')) {
                $highRiskPuroks += $cluster['purok_count'];
            }
        }

        // Ensure puroks have medicine_analytics - compute if missing
        foreach ($clusterData as &$cluster) {
            foreach ($cluster['puroks'] as &$purok) {
                // If medicine_count > 0 but medicine_analytics is empty, compute it
                if (($purok['medicine_count'] ?? 0) > 0 && empty($purok['medicine_analytics']['medicines'] ?? [])) {
                    try {
                        $residentIds = $purok['resident_ids'] ?? [];
                        if (!empty($residentIds)) {
                            $purok['medicine_analytics'] = $this->computeMedicineAnalyticsDirectly($residentIds);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Failed to compute medicine analytics for purok: ' . $e->getMessage());
                    }
                }
            }
        }
        unset($cluster, $purok); // Unset references

        // Return view with cluster data
        return view('admin.clustering.index', [
            'clusters' => $clusterData,
            'purokData' => $purokData,
            'k' => $k,
            'silhouette' => $silhouette,
            'processingTime' => $processingTime,
            'totalPuroks' => count($purokData),
            'totalResidents' => $totalResidents,
            'highRiskPuroks' => $highRiskPuroks,
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
            // Only count transactions with valid medicine relationships to match analytics
            $medicineCount = MedicineTransaction::where('transaction_type', 'OUT')
                ->whereIn('resident_id', $residentIds)
                ->whereHas('medicine') // Only count transactions with valid medicine
                ->sum('quantity');

            // Calculate demographic score (average age and family size)
            $demographicScore = $this->calculatePurokDemographicScore($purokResidents);

            // Compute per-purok analytics
            $purokIncidentAnalytics = $this->computeIncidentAnalytics($residentIds);
            $purokMedicalAnalytics = $this->computeMedicalAnalyticsForPurok($residentIds, $purokDisplay);
            $purokMedicineAnalytics = $this->computeMedicineAnalytics($residentIds);

            $purokData[] = [
                'purok_token' => $purokToken,
                'purok_display' => $purokDisplay,
                'resident_ids' => $residentIds,
                'resident_count' => count($purokResidents),
                'blotter_count' => (int)$blotterCount,
                'demographic_score' => $demographicScore,
                'medical_count' => (int)$medicalCount,
                'medicine_count' => (int)$medicineCount,
                'incident_analytics' => $purokIncidentAnalytics,
                'medical_analytics' => $purokMedicalAnalytics,
                'medicine_analytics' => $purokMedicineAnalytics,
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

    /**
     * Compute incident analytics for a cluster
     * Returns case types ordered from most common to least common
     */
    private function computeIncidentAnalytics(array $residentIds): array
    {
        if (empty($residentIds)) {
            return ['case_types' => []];
        }

        $caseTypes = BlotterRequest::whereIn('respondent_id', $residentIds)
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->type,
                    'count' => $item->count,
                ];
            })
            ->toArray();

        return [
            'case_types' => $caseTypes,
        ];
    }

    /**
     * Compute medical analytics for a single purok
     */
    private function computeMedicalAnalyticsForPurok(array $residentIds, string $purokDisplay): array
    {
        if (empty($residentIds)) {
            return [
                'visits_by_purok' => [],
                'illnesses' => [],
            ];
        }

        $medicalRecords = MedicalRecord::whereIn('resident_id', $residentIds)
            ->select('diagnosis')
            ->get();

        $visitCount = $medicalRecords->count();
        $illnessCounts = [];

        foreach ($medicalRecords as $record) {
            if ($record->diagnosis) {
                $diagnosis = trim($record->diagnosis);
                if (!isset($illnessCounts[$diagnosis])) {
                    $illnessCounts[$diagnosis] = 0;
                }
                $illnessCounts[$diagnosis]++;
            }
        }

        arsort($illnessCounts);
        $illnessesArray = [];
        foreach ($illnessCounts as $illness => $count) {
            $illnessesArray[] = [
                'illness' => $illness,
                'count' => $count,
            ];
        }

        return [
            'visits_by_purok' => [['purok' => $purokDisplay, 'count' => $visitCount]],
            'illnesses' => $illnessesArray,
        ];
    }

    /**
     * Compute medical analytics for a cluster
     * Returns per-purok visit counts and illness frequencies
     */
    private function computeMedicalAnalytics(array $clusterPuroks, array $clusterResidentIds): array
    {
        if (empty($clusterResidentIds)) {
            return [
                'visits_by_purok' => [],
                'illnesses' => [],
            ];
        }

        // Get all medical records for cluster residents
        $medicalRecords = MedicalRecord::whereIn('resident_id', $clusterResidentIds)
            ->select('resident_id', 'diagnosis')
            ->get();

        // Map resident IDs to puroks
        $residentToPurok = [];
        foreach ($clusterPuroks as $purok) {
            foreach ($purok['resident_ids'] as $residentId) {
                $residentToPurok[$residentId] = $purok['purok_display'];
            }
        }

        // Count visits per purok
        $visitsByPurok = [];
        $illnessCounts = [];

        foreach ($medicalRecords as $record) {
            $purokDisplay = $residentToPurok[$record->resident_id] ?? 'Unknown';
            
            // Count visits per purok
            if (!isset($visitsByPurok[$purokDisplay])) {
                $visitsByPurok[$purokDisplay] = 0;
            }
            $visitsByPurok[$purokDisplay]++;

            // Count illnesses (diagnosis)
            if ($record->diagnosis) {
                $diagnosis = trim($record->diagnosis);
                if (!isset($illnessCounts[$diagnosis])) {
                    $illnessCounts[$diagnosis] = 0;
                }
                $illnessCounts[$diagnosis]++;
            }
        }

        // Convert to sorted arrays
        arsort($visitsByPurok);
        $visitsByPurokArray = [];
        foreach ($visitsByPurok as $purok => $count) {
            $visitsByPurokArray[] = [
                'purok' => $purok,
                'count' => $count,
            ];
        }

        arsort($illnessCounts);
        $illnessesArray = [];
        foreach ($illnessCounts as $illness => $count) {
            $illnessesArray[] = [
                'illness' => $illness,
                'count' => $count,
            ];
        }

        return [
            'visits_by_purok' => $visitsByPurokArray,
            'illnesses' => $illnessesArray,
        ];
    }

    /**
     * Aggregate medicine analytics from puroks
     * Combines medicine data from all puroks in a cluster
     * Only includes dispensed medicines (matching the card's medicine_count)
     */
    private function aggregateMedicineAnalyticsFromPuroks(array $clusterPuroks): array
    {
        $medicineCounts = [];
        
        foreach ($clusterPuroks as $purok) {
            $purokMedicineAnalytics = $purok['medicine_analytics'] ?? [];
            $medicines = $purokMedicineAnalytics['medicines'] ?? [];
            
            if (empty($medicines)) {
                continue;
            }
            
            foreach ($medicines as $medicine) {
                // Only include medicines that were actually dispensed
                $dispensed = $medicine['dispensed'] ?? 0;
                if ($dispensed <= 0) {
                    continue;
                }
                
                $name = $medicine['name'] ?? 'Unknown Medicine';
                
                if (!isset($medicineCounts[$name])) {
                    $medicineCounts[$name] = [
                        'name' => $name,
                        'dispensed' => 0,
                        'total' => 0,
                    ];
                }
                
                $medicineCounts[$name]['dispensed'] += $dispensed;
                $medicineCounts[$name]['total'] += $medicine['total'] ?? $dispensed;
            }
        }
        
        // Convert to array and sort by total (most common first)
        $medicinesArray = array_values($medicineCounts);
        usort($medicinesArray, function($a, $b) {
            return ($b['total'] ?? 0) <=> ($a['total'] ?? 0);
        });
        
        return ['medicines' => $medicinesArray];
    }

    /**
     * Compute medicine analytics directly from database (fallback method)
     * Only includes dispensed medicines (OUT transactions)
     */
    private function computeMedicineAnalyticsDirectly(array $clusterResidentIds): array
    {
        if (empty($clusterResidentIds)) {
            return ['medicines' => []];
        }

        // Get medicine transactions directly (only OUT = dispensed)
        $medicineTransactions = MedicineTransaction::whereIn('resident_id', $clusterResidentIds)
            ->where('transaction_type', 'OUT')
            ->with('medicine:id,name')
            ->get();

        $medicineCounts = [];
        
        foreach ($medicineTransactions as $transaction) {
            if ($transaction->medicine) {
                $name = $transaction->medicine->name;
                
                if (!isset($medicineCounts[$name])) {
                    $medicineCounts[$name] = [
                        'name' => $name,
                        'dispensed' => 0,
                        'total' => 0,
                    ];
                }
                
                $quantity = $transaction->quantity ?? 0;
                $medicineCounts[$name]['dispensed'] += $quantity;
                $medicineCounts[$name]['total'] += $quantity;
            }
        }

        // Convert to array and sort by total (most common first)
        // Only include medicines that were actually dispensed
        $medicinesArray = [];
        foreach ($medicineCounts as $medicine) {
            if ($medicine['dispensed'] > 0) {
                $medicinesArray[] = $medicine;
            }
        }
        
        usort($medicinesArray, function($a, $b) {
            return ($b['total'] ?? 0) <=> ($a['total'] ?? 0);
        });

        return ['medicines' => $medicinesArray];
    }

    /**
     * Compute medicine analytics for a cluster
     * Returns medicines requested/dispensed ordered from most common to least common
     * @deprecated Use aggregateMedicineAnalyticsFromPuroks instead
     */
    private function computeMedicineAnalytics(array $clusterResidentIds): array
    {
        if (empty($clusterResidentIds)) {
            return ['medicines' => []];
        }

        // Get medicine requests
        $medicineRequests = MedicineRequest::whereIn('resident_id', $clusterResidentIds)
            ->with('medicine:id,name')
            ->get();

        // Get medicine transactions (OUT type for dispensed)
        $medicineTransactions = MedicineTransaction::whereIn('resident_id', $clusterResidentIds)
            ->where('transaction_type', 'OUT')
            ->with('medicine:id,name')
            ->get();

        // Combine and count medicines
        $medicineCounts = [];

        // Count from requests
        foreach ($medicineRequests as $request) {
            if ($request->medicine) {
                $medicineName = $request->medicine->name;
                if (!isset($medicineCounts[$medicineName])) {
                    $medicineCounts[$medicineName] = [
                        'name' => $medicineName,
                        'requested' => 0,
                        'dispensed' => 0,
                    ];
                }
                $medicineCounts[$medicineName]['requested'] += $request->quantity_requested ?? 1;
            }
        }

        // Count from transactions
        foreach ($medicineTransactions as $transaction) {
            // Only count transactions with valid medicine relationship
            // This matches the medicine_count calculation which also requires valid medicine
            if ($transaction->medicine) {
                $medicineName = $transaction->medicine->name;
                if (!isset($medicineCounts[$medicineName])) {
                    $medicineCounts[$medicineName] = [
                        'name' => $medicineName,
                        'requested' => 0,
                        'dispensed' => 0,
                    ];
                }
                $medicineCounts[$medicineName]['dispensed'] += $transaction->quantity ?? 0;
            }
        }
        
        // Verify sum matches: sum of all dispensed should equal medicine_count
        // If there are transactions without medicines, they won't appear in analytics
        // but will be counted in medicine_count - this is expected behavior

        // Calculate total and sort
        // Total represents actual dispensed quantity to match the card's total_medicine
        // The card shows sum of all dispensed quantities, so modal totals should match
        // Only include medicines that were actually dispensed (dispensed > 0)
        $medicinesArray = [];
        foreach ($medicineCounts as $medicine) {
            // Skip medicines with 0 dispensed (only requested, never dispensed)
            if ($medicine['dispensed'] <= 0) {
                continue;
            }
            
            // Total is the dispensed amount (actual medicine activity)
            // This ensures sum of all modal totals equals the card's total_medicine
            $medicine['total'] = $medicine['dispensed'];
            $medicinesArray[] = $medicine;
        }

        // Sort by total (most common first)
        usort($medicinesArray, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        return [
            'medicines' => $medicinesArray,
        ];
    }
}
