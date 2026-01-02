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
     * Get standardized error message for Python service errors
     * Detects connection errors and provides clear, user-friendly messages
     */
    private function getStandardizedErrorMessage(\Exception $e, string $operation = 'operation'): string
    {
        $errorMessage = $e->getMessage();
        $errorMessageLower = strtolower($errorMessage);
        
        // Check for connection-related errors (when Python app.py is not running)
        $connectionErrors = [
            'connection refused',
            'connection timed out',
            'failed to connect',
            'could not resolve host',
            'name or service not known',
            'no connection could be made',
            'errno 111',
            'errno 110',
            'curl error',
            'guzzlehttp',
        ];
        
        $isConnectionError = false;
        foreach ($connectionErrors as $connectionError) {
            if (str_contains($errorMessageLower, $connectionError)) {
                $isConnectionError = true;
                break;
            }
        }
        
        if ($isConnectionError) {
            return sprintf(
                'The Python analytics service (app.py) is not running. Please start the Python service at %s to use this feature.',
                $this->baseUrl
            );
        }
        
        // For other errors, provide a clear message with context
        return sprintf(
            'Python analytics service error during %s: %s',
            $operation,
            $errorMessage
        );
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
                return ['error' => $this->getStandardizedErrorMessage($e, 'K-Means clustering')];
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
                return ['error' => $this->getStandardizedErrorMessage($e, 'optimal K calculation')];
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
                return ['error' => $this->getStandardizedErrorMessage($e, 'hierarchical clustering')];
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
     * CATEGORY-SPECIFIC: Uses ONLY demographic features
     * Features: age, family_size, education_level, income_level, employment_status, is_pwd
     * Does NOT include blotter or medical data
     */
    public function buildSamplesFromResidents($residents): array
    {
        $samples = [];
        
        foreach ($residents as $resident) {
            // Feature vector: ONLY demographic data (6 features)
            $samples[] = [
                floatval($resident->age ?? 0),                                    // Feature 1: Age
                floatval($resident->family_size ?? 0),                           // Feature 2: Family size
                $this->encodeEducation($resident->education_level ?? ''),        // Feature 3: Education level
                $this->encodeIncome($resident->income_level ?? ''),              // Feature 4: Income level
                $this->encodeEmployment($resident->employment_status ?? ''),      // Feature 5: Employment status
                $this->encodePWD($resident->is_pwd ?? false)                     // Feature 6: PWD status
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
     * CATEGORY-SPECIFIC: Uses ONLY blotter-related features
     * Features: total_count, most_common_type, pending_count, approved_count, completed_count
     * Does NOT include demographic data (age, income, employment, etc.)
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
            
            // Encode blotter type (common types) - BLOTTER-SPECIFIC ONLY
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
            
            // Encode status distribution - BLOTTER-SPECIFIC ONLY
            $pendingCount = $blotterInfo['status_distribution']['pending'] ?? 0;
            $approvedCount = $blotterInfo['status_distribution']['approved'] ?? 0;
            $completedCount = $blotterInfo['status_distribution']['completed'] ?? 0;
            
            // Feature vector: ONLY blotter data (5 features)
            $samples[] = [
                floatval($blotterInfo['total_count']),           // Feature 1: Total blotter reports
                floatval($typeEncoding[$mostCommonType] ?? 7),   // Feature 2: Most common type
                floatval($pendingCount),                         // Feature 3: Pending count
                floatval($approvedCount),                        // Feature 4: Approved count
                floatval($completedCount),                      // Feature 5: Completed count
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
     * CATEGORY-SPECIFIC: Uses ONLY medical-related features
     * Features: total_count, most_common_consultation_type, recent_count, has_follow_up
     * Does NOT include demographic data (age, income, employment, etc.) or blotter data
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
            
            // Encode consultation type (common types) - MEDICAL-SPECIFIC ONLY
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
            
            // Feature vector: ONLY medical data (4 features)
            $samples[] = [
                floatval($medicalInfo['total_count']),                    // Feature 1: Total medical records
                floatval($typeEncoding[$mostCommonType] ?? 7),            // Feature 2: Most common consultation type
                floatval($medicalInfo['recent_count']),                   // Feature 3: Recent consultations (last 30 days)
                floatval($medicalInfo['has_follow_up']),                 // Feature 4: Has follow-up scheduled
            ];
        }
        
        return $samples;
    }


    /**
     * Analyze blotter data using Python service
     * Pure Python implementation - throws exception if service unavailable
     */
    public function analyzeBlotters(array $blotters): array
    {
        $cacheKey = 'python_blotter_analysis_' . md5(json_encode($blotters));
        
        return Cache::remember($cacheKey, 1800, function () use ($blotters) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/analytics/blotter', [
                        'blotters' => $blotters
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::error('Python blotter analysis error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python blotter analysis error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'blotter analysis'));
            }
        });
    }

    /**
     * Analyze document requests using Python service
     * Pure Python implementation - throws exception if service unavailable
     */
    public function analyzeDocuments(array $requests): array
    {
        $cacheKey = 'python_document_analysis_' . md5(json_encode($requests));
        
        return Cache::remember($cacheKey, 1800, function () use ($requests) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/analytics/documents', [
                        'requests' => $requests
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::error('Python document analysis error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python document analysis error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'document analysis'));
            }
        });
    }

    /**
     * Analyze health report using Python service
     * Pure Python implementation - throws exception if service unavailable
     */
    public function analyzeHealthReport(array $data): array
    {
        $cacheKey = 'python_health_report_' . md5(json_encode($data));
        
        return Cache::remember($cacheKey, 1800, function () use ($data) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/analytics/health-report', $data);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::error('Python health report analysis error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python health report analysis error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'health report analysis'));
            }
        });
    }

    /**
     * Analyze medicine report using Python service
     * Pure Python implementation - throws exception if service unavailable
     */
    public function analyzeMedicineReport(array $data, string $startDate, string $endDate): array
    {
        $cacheKey = 'python_medicine_report_' . md5(json_encode($data) . $startDate . $endDate);
        
        return Cache::remember($cacheKey, 1800, function () use ($data, $startDate, $endDate) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/analytics/medicine-report', [
                        ...$data,
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::error('Python medicine report error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python medicine report error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'medicine report analysis'));
            }
        });
    }

    /**
     * Analyze dashboard data using Python service
     * Pure Python implementation - throws exception if service unavailable
     */
    public function analyzeDashboard(array $data): array
    {
        $cacheKey = 'python_dashboard_' . md5(json_encode($data));
        
        return Cache::remember($cacheKey, 1800, function () use ($data) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/analytics/dashboard', $data);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::error('Python dashboard analysis error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python dashboard analysis error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'dashboard analysis'));
            }
        });
    }

    /**
     * Format residents data for Python program evaluation
     */
    public function formatResidentsForPrograms($residents): array
    {
        $formatted = [];
        
        foreach ($residents as $resident) {
            $residentData = [
                'id' => $resident->id,
                'first_name' => $resident->first_name,
                'middle_name' => $resident->middle_name,
                'last_name' => $resident->last_name,
                'suffix' => $resident->suffix,
                'address' => $resident->address,
                'age' => $resident->age,
                'gender' => $resident->gender,
                'marital_status' => $resident->marital_status,
                'employment_status' => $resident->employment_status,
                'income_level' => $resident->income_level,
                'education_level' => $resident->education_level,
                'family_size' => $resident->family_size,
                'is_pwd' => $resident->is_pwd,
                'occupation' => $resident->occupation,
            ];
            
            // Get blotters
            $blotters = \App\Models\BlotterRequest::where('respondent_id', $resident->id)
                ->get()
                ->map(function ($blotter) {
                    return [
                        'id' => $blotter->id,
                        'type' => $blotter->type,
                        'created_at' => $blotter->created_at ? $blotter->created_at->toIso8601String() : null,
                    ];
                })
                ->toArray();
            
            // Get medical records
            $medicalRecords = \App\Models\MedicalRecord::where('resident_id', $resident->id)
                ->get()
                ->map(function ($record) {
                    return [
                        'id' => $record->id,
                        'diagnosis' => $record->diagnosis,
                        'consultation_datetime' => $record->consultation_datetime ? $record->consultation_datetime->toIso8601String() : null,
                    ];
                })
                ->toArray();
            
            // Aggregate profile using Python
            try {
                $profile = $this->aggregateResidentData($residentData, $blotters, $medicalRecords);
            } catch (\Exception $e) {
                Log::warning('Error aggregating resident data for resident ' . $resident->id . ': ' . $e->getMessage());
                // Fallback to basic profile structure
                $purok = 'n/a';
                if (preg_match('/Purok\s*(\d+)/i', $resident->address, $matches)) {
                    $purok = strtolower($matches[1]);
                }
                
                $profile = [
                    'resident' => $residentData,
                    'demographics' => [
                        'age' => $resident->age,
                        'gender' => $resident->gender,
                        'marital_status' => $resident->marital_status,
                        'employment_status' => $resident->employment_status,
                        'income_level' => $resident->income_level,
                        'education_level' => $resident->education_level,
                        'family_size' => $resident->family_size,
                        'is_pwd' => $resident->is_pwd ? 'Yes' : 'No',
                        'occupation' => $resident->occupation,
                        'purok' => $purok,
                    ],
                    'blotter' => [
                        'total_count' => count($blotters),
                        'recent_count' => 0,
                        'types' => array_column($blotters, 'type'),
                        'has_recent_incidents' => false,
                        'last_incident_date' => null,
                    ],
                    'medical' => [
                        'total_visits' => count($medicalRecords),
                        'recent_visits' => 0,
                        'has_recent_visits' => false,
                        'diagnoses' => array_filter(array_column($medicalRecords, 'diagnosis')),
                        'chronic_conditions' => [],
                        'has_chronic_conditions' => false,
                        'last_visit_date' => null,
                    ],
                ];
            }
            
            $formatted[] = [
                'resident' => $residentData,
                'profile' => $profile,
            ];
        }
        
        return $formatted;
    }

    /**
     * Format program data for Python evaluation
     */
    public function formatProgramForEvaluation($program): array
    {
        return [
            'id' => $program->id,
            'name' => $program->name,
            'type' => $program->type,
            'description' => $program->description,
            'criteria' => $program->criteria,
            'target_puroks' => $program->target_puroks,
            'is_active' => $program->is_active,
            'priority' => $program->priority,
        ];
    }

    /**
     * Aggregate resident data using Python service
     */
    public function aggregateResidentData($resident, $blotters, $medicalRecords): array
    {
        $cacheKey = 'python_aggregate_resident_' . md5(json_encode([
            'resident_id' => is_array($resident) ? $resident['id'] : $resident->id,
            'blotters_count' => count($blotters),
            'medical_count' => count($medicalRecords),
        ]));
        
        return Cache::remember($cacheKey, 1800, function () use ($resident, $blotters, $medicalRecords) {
            $residentData = is_array($resident) ? $resident : [
                'id' => $resident->id,
                'address' => $resident->address,
                'age' => $resident->age,
                'gender' => $resident->gender,
                'marital_status' => $resident->marital_status,
                'employment_status' => $resident->employment_status,
                'income_level' => $resident->income_level,
                'education_level' => $resident->education_level,
                'family_size' => $resident->family_size,
                'is_pwd' => is_array($resident) ? ($resident['is_pwd'] ?? false) : $resident->is_pwd,
                'occupation' => $resident->occupation ?? null,
            ];
            
            $blottersData = is_array($blotters) ? $blotters : $blotters->map(function ($b) {
                return [
                    'id' => $b->id,
                    'type' => $b->type,
                    'created_at' => $b->created_at ? $b->created_at->toIso8601String() : null,
                ];
            })->toArray();
            
            $medicalData = is_array($medicalRecords) ? $medicalRecords : $medicalRecords->map(function ($m) {
                return [
                    'id' => $m->id,
                    'diagnosis' => $m->diagnosis,
                    'consultation_datetime' => $m->consultation_datetime ? $m->consultation_datetime->toIso8601String() : null,
                ];
            })->toArray();
            
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/programs/aggregate-resident-data', [
                        'resident' => $residentData,
                        'blotters' => $blottersData,
                        'medical_records' => $medicalData,
                    ]);

                if ($response->successful()) {
                    return $response->json()['profile'] ?? [];
                }

                Log::error('Python aggregate resident data error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python aggregate resident data error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'resident data aggregation'));
            }
        });
    }

    /**
     * Evaluate resident eligibility using Python service
     */
    public function evaluateResident(array $residentData, array $programData): bool
    {
        $cacheKey = 'python_evaluate_resident_' . md5(json_encode($residentData) . json_encode($programData));
        
        return Cache::remember($cacheKey, 1800, function () use ($residentData, $programData) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/programs/evaluate-resident', [
                        'profile' => $residentData,
                        'program' => $programData,
                    ]);

                if ($response->successful()) {
                    $result = $response->json();
                    return $result['eligible'] ?? false;
                }

                Log::error('Python evaluate resident error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python evaluate resident error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'resident eligibility evaluation'));
            }
        });
    }

    /**
     * Get eligible residents for a program using Python service
     */
    public function getEligibleResidents(array $residentsData, array $programData, ?string $purok = null): array
    {
        $cacheKey = 'python_eligible_residents_' . md5(json_encode($residentsData) . json_encode($programData) . $purok);
        
        return Cache::remember($cacheKey, 1800, function () use ($residentsData, $programData, $purok) {
            $payload = [
                'residents' => $residentsData,
                'program' => $programData,
            ];
            
            if ($purok !== null) {
                $payload['purok'] = $purok;
            }
            
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/programs/eligible-residents', $payload);

                if ($response->successful()) {
                    return $response->json()['eligible_residents'] ?? [];
                }

                Log::error('Python eligible residents error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python eligible residents error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'eligible residents retrieval'));
            }
        });
    }

    /**
     * Get programs a resident is eligible for using Python service
     */
    public function getResidentPrograms(array $residentData, array $programsData): array
    {
        $cacheKey = 'python_resident_programs_' . md5(json_encode($residentData) . json_encode($programsData));
        
        return Cache::remember($cacheKey, 1800, function () use ($residentData, $programsData) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/programs/resident-programs', [
                        'resident' => $residentData,
                        'programs' => $programsData,
                    ]);

                if ($response->successful()) {
                    return $response->json()['eligible_programs'] ?? [];
                }

                Log::error('Python resident programs error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python resident programs error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'resident programs retrieval'));
            }
        });
    }

    /**
     * Get program recommendations by purok using Python service
     */
    public function getProgramRecommendationsByPurok(array $residentsData, array $programData): array
    {
        $cacheKey = 'python_recommendations_by_purok_' . md5(json_encode($residentsData) . json_encode($programData));
        
        return Cache::remember($cacheKey, 1800, function () use ($residentsData, $programData) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/programs/recommendations-by-purok', [
                        'residents' => $residentsData,
                        'program' => $programData,
                    ]);

                if ($response->successful()) {
                    return $response->json()['recommendations'] ?? [];
                }

                Log::error('Python recommendations by purok error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python recommendations by purok error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'program recommendations by purok'));
            }
        });
    }

    /**
     * Get purok eligibility statistics using Python service
     */
    public function getPurokEligibilityStats(array $residentsData, array $programData, ?string $purok = null): array
    {
        $cacheKey = 'python_purok_eligibility_stats_' . md5(json_encode($residentsData) . json_encode($programData) . $purok);
        
        return Cache::remember($cacheKey, 1800, function () use ($residentsData, $programData, $purok) {
            $payload = [
                'residents' => $residentsData,
                'program' => $programData,
            ];
            
            if ($purok !== null) {
                $payload['purok'] = $purok;
            }
            
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/programs/purok-eligibility-stats', $payload);

                if ($response->successful()) {
                    return $response->json()['stats'] ?? [];
                }

                Log::error('Python purok eligibility stats error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python purok eligibility stats error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'purok eligibility statistics'));
            }
        });
    }

    /**
     * Identify target puroks using Python service
     */
    public function identifyTargetPuroks(array $stats, float $threshold = 0.5): array
    {
        $targetPuroks = [];
        
        foreach ($stats as $stat) {
            if (($stat['eligibility_percentage'] ?? 0) >= ($threshold * 100)) {
                $targetPuroks[] = $stat['purok'] ?? '';
            }
        }
        
        return array_filter($targetPuroks);
    }

    /**
     * Get all puroks using Python service
     */
    public function getAllPuroks(array $residentsData): array
    {
        $cacheKey = 'python_all_puroks_' . md5(json_encode($residentsData));
        
        return Cache::remember($cacheKey, 3600, function () use ($residentsData) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/programs/all-puroks', [
                        'residents' => $residentsData,
                    ]);

                if ($response->successful()) {
                    return $response->json()['puroks'] ?? [];
                }

                Log::error('Python all puroks error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python all puroks error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'purok list retrieval'));
            }
        });
    }

    /**
     * Aggregate data by purok using Python service
     */
    public function aggregatePurokData(
        array $residents,
        array $blotters,
        array $medicalRecords,
        array $medicineTransactions,
        array $medicineRequests,
        array $medicineNames = []
    ): array {
        $cacheKey = 'python_aggregate_purok_' . md5(json_encode([
            'residents_count' => count($residents),
            'blotters_count' => count($blotters),
            'medical_count' => count($medicalRecords),
        ]));
        
        return Cache::remember($cacheKey, 1800, function () use ($residents, $blotters, $medicalRecords, $medicineTransactions, $medicineRequests, $medicineNames) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/clustering/aggregate-purok-data', [
                        'residents' => $residents,
                        'blotters' => $blotters,
                        'medical_records' => $medicalRecords,
                        'medicine_transactions' => $medicineTransactions,
                        'medicine_requests' => $medicineRequests,
                        'medicine_names' => $medicineNames,
                    ]);

                if ($response->successful()) {
                    return $response->json()['purok_data'] ?? [];
                }

                Log::error('Python aggregate purok data error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python aggregate purok data error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'purok data aggregation'));
            }
        });
    }

    /**
     * Build purok risk features using Python service
     */
    public function buildPurokRiskFeatures(array $purokData): array
    {
        $cacheKey = 'python_purok_features_' . md5(json_encode($purokData));
        
        return Cache::remember($cacheKey, 1800, function () use ($purokData) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/clustering/build-purok-features', [
                        'purok_data' => $purokData,
                    ]);

                if ($response->successful()) {
                    return $response->json()['samples'] ?? [];
                }

                Log::error('Python build purok features error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python build purok features error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'purok risk features building'));
            }
        });
    }

    /**
     * Compute incident analytics using Python service
     */
    public function computeIncidentAnalytics(array $residentIds, array $blotters): array
    {
        $cacheKey = 'python_incident_analytics_' . md5(json_encode($residentIds) . json_encode($blotters));
        
        return Cache::remember($cacheKey, 1800, function () use ($residentIds, $blotters) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/clustering/compute-incident-analytics', [
                        'resident_ids' => $residentIds,
                        'blotters' => $blotters,
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::error('Python incident analytics error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python incident analytics error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'incident analytics computation'));
            }
        });
    }

    /**
     * Compute medical analytics using Python service
     */
    public function computeMedicalAnalytics(array $clusterPuroks, array $clusterResidentIds, array $medicalRecords): array
    {
        $cacheKey = 'python_medical_analytics_' . md5(json_encode($clusterResidentIds) . json_encode($medicalRecords));
        
        return Cache::remember($cacheKey, 1800, function () use ($clusterPuroks, $clusterResidentIds, $medicalRecords) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/clustering/compute-medical-analytics', [
                        'cluster_puroks' => $clusterPuroks,
                        'cluster_resident_ids' => $clusterResidentIds,
                        'medical_records' => $medicalRecords,
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::error('Python medical analytics error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python medical analytics error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'medical analytics computation'));
            }
        });
    }

    /**
     * Compute medicine analytics using Python service
     */
    public function computeMedicineAnalytics(
        array $clusterResidentIds,
        array $medicineRequests,
        array $medicineTransactions,
        array $medicineNames
    ): array {
        $cacheKey = 'python_medicine_analytics_' . md5(json_encode($clusterResidentIds) . json_encode($medicineTransactions));
        
        return Cache::remember($cacheKey, 1800, function () use ($clusterResidentIds, $medicineRequests, $medicineTransactions, $medicineNames) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/clustering/compute-medicine-analytics', [
                        'cluster_resident_ids' => $clusterResidentIds,
                        'medicine_requests' => $medicineRequests,
                        'medicine_transactions' => $medicineTransactions,
                        'medicine_names' => $medicineNames,
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::error('Python medicine analytics error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python medicine analytics error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'medicine analytics computation'));
            }
        });
    }

    /**
     * Label clusters by risk using Python service
     */
    public function labelClustersByRisk(array $clusters, array $purokData): array
    {
        $cacheKey = 'python_label_clusters_' . md5(json_encode($clusters) . json_encode($purokData));
        
        return Cache::remember($cacheKey, 1800, function () use ($clusters, $purokData) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($this->baseUrl . '/api/clustering/label-clusters-by-risk', [
                        'clusters' => $clusters,
                        'purok_data' => $purokData,
                    ]);

                if ($response->successful()) {
                    $result = $response->json();
                    return $result['labels'] ?? [];
                }

                Log::error('Python label clusters error: ' . $response->body());
                throw new \Exception($response->body());
            } catch (\Exception $e) {
                Log::error('Python label clusters error: ' . $e->getMessage());
                throw new \Exception($this->getStandardizedErrorMessage($e, 'cluster risk labeling'));
            }
        });
    }

}


