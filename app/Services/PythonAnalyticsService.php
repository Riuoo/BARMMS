<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PythonAnalyticsService
{
    private $baseUrl;
    private $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.python_analytics.url', 'http://localhost:5000');
        $this->timeout = config('services.python_analytics.timeout', 30);
    }

    /**
     * Check if Python service is available
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/health');
            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('Python analytics service not available: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Perform K-Means clustering
     */
    public function kmeansClustering(array $samples, int $k = 3, int $maxIterations = 100, int $numRuns = 3): array
    {
        $cacheKey = 'python_kmeans_' . md5(json_encode($samples) . $k . $maxIterations . $numRuns);
        
        return Cache::remember($cacheKey, 3600, function () use ($samples, $k, $maxIterations, $numRuns) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/clustering/kmeans', [
                        'samples' => $samples,
                        'k' => $k,
                        'max_iterations' => $maxIterations,
                        'num_runs' => $numRuns
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }

                throw new \Exception('Python service error: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('Python clustering error: ' . $e->getMessage());
                return ['error' => 'Analytics service unavailable: ' . $e->getMessage()];
            }
        });
    }

    /**
     * Find optimal K value
     */
    public function findOptimalK(array $samples, int $maxK = 10, string $method = 'elbow'): array
    {
        $cacheKey = 'python_optimal_k_' . md5(json_encode($samples) . $maxK . $method);
        
        return Cache::remember($cacheKey, 3600, function () use ($samples, $maxK, $method) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/clustering/optimal-k', [
                        'samples' => $samples,
                        'max_k' => $maxK,
                        'method' => $method
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }

                throw new \Exception('Python service error: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('Python optimal K error: ' . $e->getMessage());
                return ['error' => 'Analytics service unavailable: ' . $e->getMessage()];
            }
        });
    }

    /**
     * Perform hierarchical clustering
     */
    public function hierarchicalClustering(array $samples, int $nClusters = 3, string $linkage = 'ward'): array
    {
        $cacheKey = 'python_hierarchical_' . md5(json_encode($samples) . $nClusters . $linkage);
        
        return Cache::remember($cacheKey, 3600, function () use ($samples, $nClusters, $linkage) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/clustering/hierarchical', [
                        'samples' => $samples,
                        'n_clusters' => $nClusters,
                        'linkage' => $linkage
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }

                throw new \Exception('Python service error: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('Python hierarchical clustering error: ' . $e->getMessage());
                return ['error' => 'Analytics service unavailable: ' . $e->getMessage()];
            }
        });
    }

    /**
     * Train decision tree model
     */
    public function trainDecisionTree(array $samples, array $labels, string $modelType = 'decision_tree', float $testSize = 0.3, array $params = []): array
    {
        $cacheKey = 'python_dt_train_' . md5(json_encode($samples) . json_encode($labels) . $modelType . json_encode($params));
        
        return Cache::remember($cacheKey, 1800, function () use ($samples, $labels, $modelType, $testSize, $params) {
            try {
                $requestData = [
                    'samples' => $samples,
                    'labels' => $labels,
                    'model_type' => $modelType,
                    'test_size' => $testSize,
                ];
                
                // Add optional parameters
                if (isset($params['max_depth'])) {
                    $requestData['max_depth'] = $params['max_depth'];
                }
                if (isset($params['min_samples_split'])) {
                    $requestData['min_samples_split'] = $params['min_samples_split'];
                }
                if (isset($params['min_samples_leaf'])) {
                    $requestData['min_samples_leaf'] = $params['min_samples_leaf'];
                }
                if (isset($params['random_state'])) {
                    $requestData['random_state'] = $params['random_state'];
                }
                
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/decision-tree/train', $requestData);

                if ($response->successful()) {
                    return $response->json();
                }

                throw new \Exception('Python service error: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('Python decision tree training error: ' . $e->getMessage());
                return ['error' => 'Analytics service unavailable: ' . $e->getMessage()];
            }
        });
    }

    /**
     * Make predictions using trained model
     */
    public function predict(string $modelId, array $samples): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->baseUrl . '/api/decision-tree/predict', [
                    'model_id' => $modelId,
                    'samples' => $samples
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Python service error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Python prediction error: ' . $e->getMessage());
            return ['error' => 'Analytics service unavailable: ' . $e->getMessage()];
        }
    }

    /**
     * Get feature importance for a trained model
     */
    public function getFeatureImportance(string $modelId): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->baseUrl . '/api/decision-tree/feature-importance', [
                    'model_id' => $modelId
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Python service error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Python feature importance error: ' . $e->getMessage());
            return ['error' => 'Analytics service unavailable: ' . $e->getMessage()];
        }
    }

    /**
     * Analyze health risk
     */
    public function analyzeHealthRisk(array $residents, string $modelType = 'random_forest'): array
    {
        $cacheKey = 'python_health_risk_' . md5(json_encode($residents) . $modelType);
        
        return Cache::remember($cacheKey, 1800, function () use ($residents, $modelType) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/analytics/health-risk', [
                        'residents' => $residents,
                        'model_type' => $modelType
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }

                throw new \Exception('Python service error: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('Python health risk analysis error: ' . $e->getMessage());
                return ['error' => 'Analytics service unavailable: ' . $e->getMessage()];
            }
        });
    }

    /**
     * Analyze service eligibility
     */
    public function analyzeServiceEligibility(array $residents, string $modelType = 'xgboost'): array
    {
        $cacheKey = 'python_service_eligibility_' . md5(json_encode($residents) . $modelType);
        
        return Cache::remember($cacheKey, 1800, function () use ($residents, $modelType) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/analytics/service-eligibility', [
                        'residents' => $residents,
                        'model_type' => $modelType
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }

                throw new \Exception('Python service error: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('Python service eligibility error: ' . $e->getMessage());
                return ['error' => 'Analytics service unavailable: ' . $e->getMessage()];
            }
        });
    }

    /**
     * Analyze demographics
     */
    public function analyzeDemographics(array $residents): array
    {
        $cacheKey = 'python_demographics_' . md5(json_encode($residents));
        
        return Cache::remember($cacheKey, 3600, function () use ($residents) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/analytics/demographic', [
                        'residents' => $residents
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }

                throw new \Exception('Python service error: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('Python demographics analysis error: ' . $e->getMessage());
                return ['error' => 'Analytics service unavailable: ' . $e->getMessage()];
            }
        });
    }

    /**
     * Analyze health condition
     */
    public function analyzeHealthCondition(array $residents, string $modelType = 'decision_tree'): array
    {
        $cacheKey = 'python_health_condition_' . md5(json_encode($residents) . $modelType);
        
        return Cache::remember($cacheKey, 1800, function () use ($residents, $modelType) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/analytics/health-condition', [
                        'residents' => $residents,
                        'model_type' => $modelType
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }

                throw new \Exception('Python service error: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('Python health condition analysis error: ' . $e->getMessage());
                return ['error' => 'Analytics service unavailable: ' . $e->getMessage()];
            }
        });
    }

    /**
     * Analyze program recommendation
     */
    public function analyzeProgramRecommendation(array $residents, string $modelType = 'random_forest'): array
    {
        $cacheKey = 'python_program_recommendation_' . md5(json_encode($residents) . $modelType);
        
        return Cache::remember($cacheKey, 1800, function () use ($residents, $modelType) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/analytics/program-recommendation', [
                        'residents' => $residents,
                        'model_type' => $modelType
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }

                throw new \Exception('Python service error: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('Python program recommendation analysis error: ' . $e->getMessage());
                return ['error' => 'Analytics service unavailable: ' . $e->getMessage()];
            }
        });
    }

    /**
     * Convert residents to format expected by Python service
     */
    public function formatResidentsForPython($residents): array
    {
        $formatted = [];
        
        foreach ($residents as $resident) {
            $formatted[] = [
                'id' => $resident->id ?? null,
                'name' => $resident->full_name ?? '',
                'age' => $resident->age ?? 0,
                'family_size' => $resident->family_size ?? 0,
                'education_level' => $resident->education_level ?? '',
                'income_level' => $resident->income_level ?? '',
                'employment_status' => $resident->employment_status ?? '',
                'is_pwd' => $resident->is_pwd ?? false,
                'address' => $resident->address ?? '',
                'cluster_id' => $resident->cluster_id ?? null, // NEW: Include cluster ID for decision tree
            ];
        }
        
        return $formatted;
    }

    /**
     * Build samples from residents for clustering
     */
    public function buildSamplesFromResidents($residents): array
    {
        $samples = [];
        
        foreach ($residents as $resident) {
            $samples[] = [
                floatval($resident->age ?? 0),
                floatval($resident->family_size ?? 0),
                $this->encodeEducation($resident->education_level ?? ''),
                $this->encodeIncome($resident->income_level ?? ''),
                $this->encodeEmployment($resident->employment_status ?? ''),
                $this->encodePWD($resident->is_pwd ?? false)
            ];
        }
        
        return $samples;
    }

    private function encodeEducation(string $educationLevel): float
    {
        $mapping = [
            'Elementary' => 1,
            'High School' => 2,
            'College' => 3,
            'Graduate' => 4,
        ];
        
        return $mapping[$educationLevel] ?? 0;
    }

    private function encodeIncome(string $incomeLevel): float
    {
        $mapping = [
            'Low' => 1,
            'Lower Middle' => 2,
            'Middle' => 3,
            'Upper Middle' => 4,
            'High' => 5,
        ];
        
        return $mapping[$incomeLevel] ?? 0;
    }

    private function encodeEmployment(string $employmentStatus): float
    {
        $mapping = [
            'Unemployed' => 0,
            'Part-time' => 1,
            'Self-employed' => 2,
            'Full-time' => 3,
        ];
        
        return $mapping[$employmentStatus] ?? 0;
    }

    private function encodePWD(bool $isPWD): float
    {
        return $isPWD ? 1.0 : 0.0;
    }

    /**
     * Build samples from blotter data for clustering
     * Features: total count, type distribution, status distribution
     */
    public function buildSamplesFromBlotterData($residents, $blotterData): array
    {
        $samples = [];
        
        foreach ($residents as $resident) {
            $residentId = $resident->id;
            $blotterInfo = $blotterData[$residentId] ?? [
                'total_count' => 0,
                'type_distribution' => [],
                'status_distribution' => []
            ];
            
            // Encode blotter type (common types)
            $typeEncoding = [
                'Theft' => 1,
                'Assault' => 2,
                'Domestic Violence' => 3,
                'Trespassing' => 4,
                'Noise Complaint' => 5,
                'Property Damage' => 6,
                'Other' => 7,
            ];
            
            // Get most common type or default
            $mostCommonType = 'Other';
            if (!empty($blotterInfo['type_distribution'])) {
                arsort($blotterInfo['type_distribution']);
                $mostCommonType = array_key_first($blotterInfo['type_distribution']) ?? 'Other';
            }
            
            // Encode status distribution
            $statusEncoding = [
                'pending' => 0,
                'approved' => 1,
                'completed' => 2,
            ];
            
            $pendingCount = $blotterInfo['status_distribution']['pending'] ?? 0;
            $approvedCount = $blotterInfo['status_distribution']['approved'] ?? 0;
            $completedCount = $blotterInfo['status_distribution']['completed'] ?? 0;
            
            $samples[] = [
                floatval($blotterInfo['total_count']),
                floatval($typeEncoding[$mostCommonType] ?? 7),
                floatval($pendingCount),
                floatval($approvedCount),
                floatval($completedCount),
            ];
        }
        
        return $samples;
    }

    /**
     * Build samples from document requests data for clustering
     * Features: total count, document type distribution, status distribution
     */
    public function buildSamplesFromDocumentRequestsData($residents, $documentData): array
    {
        $samples = [];
        
        foreach ($residents as $resident) {
            $residentId = $resident->id;
            $docInfo = $documentData[$residentId] ?? [
                'total_count' => 0,
                'type_distribution' => [],
                'status_distribution' => []
            ];
            
            // Encode document type (common types)
            $typeEncoding = [
                'Barangay Clearance' => 1,
                'Certificate of Indigency' => 2,
                'Certificate of Residency' => 3,
                'Business Permit' => 4,
                'Barangay ID' => 5,
                'Other' => 6,
            ];
            
            // Get most common type or default
            $mostCommonType = 'Other';
            if (!empty($docInfo['type_distribution'])) {
                arsort($docInfo['type_distribution']);
                $mostCommonType = array_key_first($docInfo['type_distribution']) ?? 'Other';
            }
            
            // Encode status distribution
            $pendingCount = $docInfo['status_distribution']['pending'] ?? 0;
            $approvedCount = $docInfo['status_distribution']['approved'] ?? 0;
            $completedCount = $docInfo['status_distribution']['completed'] ?? 0;
            
            $samples[] = [
                floatval($docInfo['total_count']),
                floatval($typeEncoding[$mostCommonType] ?? 6),
                floatval($pendingCount),
                floatval($approvedCount),
                floatval($completedCount),
            ];
        }
        
        return $samples;
    }

    /**
     * Build samples from medical records data for clustering
     * Features: total count, consultation type distribution, recent activity
     */
    public function buildSamplesFromMedicalRecordsData($residents, $medicalData): array
    {
        $samples = [];
        
        foreach ($residents as $resident) {
            $residentId = $resident->id;
            $medicalInfo = $medicalData[$residentId] ?? [
                'total_count' => 0,
                'type_distribution' => [],
                'recent_count' => 0, // Last 30 days
                'has_follow_up' => 0,
            ];
            
            // Encode consultation type (common types)
            $typeEncoding = [
                'General Check-up' => 1,
                'Emergency' => 2,
                'Follow-up' => 3,
                'Vaccination' => 4,
                'Chronic Disease Management' => 5,
                'Prenatal' => 6,
                'Other' => 7,
            ];
            
            // Get most common type or default
            $mostCommonType = 'Other';
            if (!empty($medicalInfo['type_distribution'])) {
                arsort($medicalInfo['type_distribution']);
                $mostCommonType = array_key_first($medicalInfo['type_distribution']) ?? 'Other';
            }
            
            $samples[] = [
                floatval($medicalInfo['total_count']),
                floatval($typeEncoding[$mostCommonType] ?? 7),
                floatval($medicalInfo['recent_count']),
                floatval($medicalInfo['has_follow_up']),
            ];
        }
        
        return $samples;
    }
}


