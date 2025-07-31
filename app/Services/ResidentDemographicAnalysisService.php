<?php

namespace App\Services;

use App\Models\Residents;
use App\Models\HealthStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ResidentDemographicAnalysisService
{
    private $k;

    public function __construct(int $k = 3)
    {
        $this->k = $k;
    }

    /**
     * Enhanced clustering with health status, demographic profile, and incidence rates
     */
    public function clusterResidents(): array
    {
        // Check cache first
        $cacheKey = "enhanced_clustering_k{$this->k}_" . Residents::count();
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        $residents = Residents::select('id', 'name', 'age', 'family_size', 'education_level', 'income_level', 'employment_status', 'health_status', 'address')->get();
        
        if ($residents->count() < $this->k) {
            return [
                'clusters' => [],
                'centroids' => [],
                'iterations' => 1,
                'converged' => true,
                'error' => 'Not enough data points for clustering',
                'residents' => $residents
            ];
        }

        // Enhanced clustering with health status and incidence rates
        $clusters = $this->enhancedHealthBasedClustering($residents);
        
        $result = [
            'clusters' => $clusters,
            'centroids' => $this->calculateEnhancedCentroids($clusters),
            'iterations' => 1,
            'converged' => true,
            'residents' => $residents,
            'high_risk_areas' => $this->identifyHighRiskAreas($clusters),
            'incidence_analysis' => $this->analyzeIncidenceRates($clusters)
        ];

        // Cache the result for 30 minutes
        Cache::put($cacheKey, $result, 1800);

        return $result;
    }

    /**
     * Enhanced clustering considering health status, demographics, and incidence rates
     */
    private function enhancedHealthBasedClustering(Collection $residents): array
    {
        $clusters = array_fill(0, $this->k, []);
        
        // Get health status data for incidence analysis
        $healthStatuses = HealthStatus::select('user_id', 'concern_type', 'severity', 'status')->get();
        $healthIncidenceByUser = $healthStatuses->groupBy('user_id');
        
        foreach ($residents as $resident) {
            $clusterId = $this->assignToHealthBasedCluster($resident, $healthIncidenceByUser);
            $clusters[$clusterId][] = [
                'id' => $resident->id,
                'features' => [
                    $this->normalizeAge($resident->age ?? 30),
                    $this->normalizeFamilySize($resident->family_size ?? 1),
                    $this->normalizeEducation($resident->education_level ?? 'Elementary'),
                    $this->normalizeIncome($resident->income_level ?? 'Low'),
                    $this->normalizeEmployment($resident->employment_status ?? 'Unemployed'),
                    $this->normalizeHealth($resident->health_status ?? 'Healthy'),
                    $this->normalizeHealthIncidence($healthIncidenceByUser->get($resident->id, collect())),
                    $this->normalizeSeverityLevel($healthIncidenceByUser->get($resident->id, collect()))
                ],
                'resident' => $resident,
                'health_incidence' => $healthIncidenceByUser->get($resident->id, collect())
            ];
        }
        
        return $clusters;
    }

    /**
     * Assign resident to cluster based on health status and risk factors
     */
    private function assignToHealthBasedCluster($resident, $healthIncidenceByUser): int
    {
        $age = $resident->age ?? 30;
        $income = $resident->income_level ?? 'Low';
        $employment = $resident->employment_status ?? 'Unemployed';
        $health = $resident->health_status ?? 'Healthy';
        $healthIncidence = $healthIncidenceByUser->get($resident->id, collect());
        
        // Calculate health risk score
        $healthRiskScore = $this->calculateHealthRiskScore($resident, $healthIncidence);
        
        // Cluster 0: High-risk vulnerable population
        if ($healthRiskScore >= 7 || $health === 'Critical' || 
            ($income === 'Low' && $healthIncidence->count() > 2)) {
            return 0;
        }
        
        // Cluster 1: Moderate-risk population
        if ($healthRiskScore >= 4 || $health === 'Poor' || 
            ($age > 60 && $healthIncidence->count() > 0)) {
            return 1;
        }
        
        // Cluster 2: Low-risk healthy population
        return 2;
    }

    /**
     * Calculate health risk score based on multiple factors
     */
    private function calculateHealthRiskScore($resident, $healthIncidence): int
    {
        $score = 0;
        
        // Age factor
        $age = $resident->age ?? 30;
        if ($age > 60) $score += 2;
        elseif ($age > 40) $score += 1;
        
        // Health status factor
        $health = $resident->health_status ?? 'Healthy';
        switch ($health) {
            case 'Critical': $score += 4; break;
            case 'Poor': $score += 3; break;
            case 'Fair': $score += 2; break;
            case 'Good': $score += 1; break;
        }
        
        // Income factor
        $income = $resident->income_level ?? 'Low';
        if ($income === 'Low') $score += 2;
        elseif ($income === 'Lower Middle') $score += 1;
        
        // Employment factor
        $employment = $resident->employment_status ?? 'Unemployed';
        if ($employment === 'Unemployed') $score += 2;
        
        // Health incidence factor
        $incidenceCount = $healthIncidence->count();
        $score += min($incidenceCount * 2, 4); // Max 4 points for incidence
        
        // Severity factor
        $highSeverityCount = $healthIncidence->where('severity', 'Severe')->count() + 
                           $healthIncidence->where('severity', 'Emergency')->count();
        $score += $highSeverityCount * 2;
        
        return $score;
    }

    /**
     * Identify high-risk areas based on cluster analysis
     */
    private function identifyHighRiskAreas(array $clusters): array
    {
        $highRiskAreas = [];
        
        foreach ($clusters as $clusterId => $cluster) {
            if (empty($cluster)) continue;
            
            $healthRiskScore = 0;
            $totalResidents = count($cluster);
            $criticalHealthCount = 0;
            $lowIncomeCount = 0;
            $unemployedCount = 0;
            $highIncidenceCount = 0;
            
            foreach ($cluster as $point) {
                $resident = $point['resident'];
                $healthIncidence = $point['health_incidence'];
                
                if (($resident->health_status ?? 'Healthy') === 'Critical') $criticalHealthCount++;
                if (($resident->income_level ?? 'Low') === 'Low') $lowIncomeCount++;
                if (($resident->employment_status ?? 'Unemployed') === 'Unemployed') $unemployedCount++;
                if ($healthIncidence->count() > 2) $highIncidenceCount++;
                
                $healthRiskScore += $this->calculateHealthRiskScore($resident, $healthIncidence);
            }
            
            $avgRiskScore = $healthRiskScore / $totalResidents;
            
            // Determine if this cluster represents a high-risk area
            if ($avgRiskScore >= 6 || 
                ($criticalHealthCount / $totalResidents) > 0.3 ||
                ($lowIncomeCount / $totalResidents) > 0.5) {
                
                $highRiskAreas[] = [
                    'cluster_id' => $clusterId,
                    'risk_level' => $this->determineRiskLevel($avgRiskScore),
                    'total_residents' => $totalResidents,
                    'avg_risk_score' => round($avgRiskScore, 2),
                    'critical_health_percentage' => round(($criticalHealthCount / $totalResidents) * 100, 1),
                    'low_income_percentage' => round(($lowIncomeCount / $totalResidents) * 100, 1),
                    'unemployed_percentage' => round(($unemployedCount / $totalResidents) * 100, 1),
                    'high_incidence_percentage' => round(($highIncidenceCount / $totalResidents) * 100, 1),
                    'intervention_priority' => $this->determineInterventionPriority($avgRiskScore, $criticalHealthCount, $totalResidents)
                ];
            }
        }
        
        return $highRiskAreas;
    }

    /**
     * Analyze incidence rates by cluster
     */
    private function analyzeIncidenceRates(array $clusters): array
    {
        $incidenceAnalysis = [];
        
        foreach ($clusters as $clusterId => $cluster) {
            if (empty($cluster)) continue;
            
            $totalResidents = count($cluster);
            $totalIncidents = 0;
            $incidentTypes = [];
            $severityLevels = [];
            
            foreach ($cluster as $point) {
                $healthIncidence = $point['health_incidence'];
                $totalIncidents += $healthIncidence->count();
                
                foreach ($healthIncidence as $incident) {
                    // Count by concern type
                    $type = $incident->concern_type;
                    if (!isset($incidentTypes[$type])) {
                        $incidentTypes[$type] = 0;
                    }
                    $incidentTypes[$type]++;
                    
                    // Count by severity
                    $severity = $incident->severity;
                    if (!isset($severityLevels[$severity])) {
                        $severityLevels[$severity] = 0;
                    }
                    $severityLevels[$severity]++;
                }
            }
            
            $incidenceAnalysis[] = [
                'cluster_id' => $clusterId,
                'total_residents' => $totalResidents,
                'total_incidents' => $totalIncidents,
                'incidence_rate' => $totalResidents > 0 ? round(($totalIncidents / $totalResidents) * 100, 2) : 0,
                'incident_types' => $incidentTypes,
                'severity_distribution' => $severityLevels,
                'most_common_concern' => !empty($incidentTypes) ? array_keys($incidentTypes, max($incidentTypes))[0] : 'None',
                'most_common_severity' => !empty($severityLevels) ? array_keys($severityLevels, max($severityLevels))[0] : 'None'
            ];
        }
        
        return $incidenceAnalysis;
    }

    /**
     * Calculate enhanced centroids with health features
     */
    private function calculateEnhancedCentroids(array $clusters): array
    {
        $centroids = [];
        
        foreach ($clusters as $clusterId => $cluster) {
            if (empty($cluster)) {
                $centroids[$clusterId] = array_fill(0, 8, 0.5); // 8 features now
                continue;
            }
            
            $centroid = array_fill(0, 8, 0);
            $count = count($cluster);
            
            foreach ($cluster as $point) {
                for ($i = 0; $i < 8; $i++) {
                    $centroid[$i] += $point['features'][$i];
                }
            }
            
            for ($i = 0; $i < 8; $i++) {
                $centroid[$i] /= $count;
            }
            
            $centroids[$clusterId] = $centroid;
        }
        
        return $centroids;
    }

    /**
     * Determine risk level based on average risk score
     */
    private function determineRiskLevel(float $avgRiskScore): string
    {
        if ($avgRiskScore >= 8) return 'Critical';
        if ($avgRiskScore >= 6) return 'High';
        if ($avgRiskScore >= 4) return 'Medium';
        return 'Low';
    }

    /**
     * Determine intervention priority
     */
    private function determineInterventionPriority(float $avgRiskScore, int $criticalHealthCount, int $totalResidents): string
    {
        if ($avgRiskScore >= 8 || ($criticalHealthCount / $totalResidents) > 0.4) {
            return 'Immediate';
        }
        if ($avgRiskScore >= 6 || ($criticalHealthCount / $totalResidents) > 0.2) {
            return 'High';
        }
        if ($avgRiskScore >= 4) {
            return 'Medium';
        }
        return 'Low';
    }

    /**
     * Normalize health incidence count
     */
    private function normalizeHealthIncidence(Collection $healthIncidence): float
    {
        $count = $healthIncidence->count();
        return min($count / 5.0, 1.0); // Normalize to 0-1, max 5 incidents
    }

    /**
     * Normalize severity level
     */
    private function normalizeSeverityLevel(Collection $healthIncidence): float
    {
        if ($healthIncidence->isEmpty()) return 0.0;
        
        $severityScores = [
            'Mild' => 0.25,
            'Moderate' => 0.5,
            'Severe' => 0.75,
            'Emergency' => 1.0
        ];
        
        $totalScore = 0;
        foreach ($healthIncidence as $incident) {
            $totalScore += $severityScores[$incident->severity] ?? 0.5;
        }
        
        return min($totalScore / $healthIncidence->count(), 1.0);
    }

    /**
     * Simple rule-based clustering using demographic characteristics
     */
    private function simpleRuleBasedClustering(Collection $residents): array
    {
        $clusters = array_fill(0, $this->k, []);
        
        foreach ($residents as $resident) {
            $clusterId = $this->assignToClusterByRules($resident);
            $clusters[$clusterId][] = [
                'id' => $resident->id,
                'features' => [
                    $this->normalizeAge($resident->age ?? 30),
                    $this->normalizeFamilySize($resident->family_size ?? 1),
                    $this->normalizeEducation($resident->education_level ?? 'Elementary'),
                    $this->normalizeIncome($resident->income_level ?? 'Low'),
                    $this->normalizeEmployment($resident->employment_status ?? 'Unemployed'),
                    $this->normalizeHealth($resident->health_status ?? 'Healthy')
                ],
                'resident' => $resident
            ];
        }
        
        return $clusters;
    }

    /**
     * Assign resident to cluster based on simple demographic rules
     */
    private function assignToClusterByRules($resident): int
    {
        $age = $resident->age ?? 30;
        $income = $resident->income_level ?? 'Low';
        $employment = $resident->employment_status ?? 'Unemployed';
        $health = $resident->health_status ?? 'Healthy';
        
        // Rule 1: High-income professionals (Cluster 0)
        if ($income === 'High' || $income === 'Upper Middle') {
            return 0;
        }
        
        // Rule 2: Vulnerable population (Cluster 1)
        if ($income === 'Low' && ($employment === 'Unemployed' || $health === 'Critical' || $health === 'Poor')) {
            return 1;
        }
        
        // Rule 3: Middle-class families (Cluster 2)
        if ($income === 'Middle' || $income === 'Lower Middle') {
            return 2;
        }
        
        // Default: assign to cluster 1 (vulnerable)
        return 1;
    }

    /**
     * Calculate simple centroids for visualization
     */
    private function calculateSimpleCentroids(array $clusters): array
    {
        $centroids = [];
        
        foreach ($clusters as $clusterId => $cluster) {
            if (empty($cluster)) {
                $centroids[$clusterId] = array_fill(0, 6, 0.5);
                continue;
            }
            
            $centroid = array_fill(0, 6, 0);
            $count = count($cluster);
            
            foreach ($cluster as $point) {
                for ($i = 0; $i < 6; $i++) {
                    $centroid[$i] += $point['features'][$i];
                }
            }
            
            for ($i = 0; $i < 6; $i++) {
                $centroid[$i] /= $count;
            }
            
            $centroids[$clusterId] = $centroid;
        }
        
        return $centroids;
    }

    /**
     * Get cluster characteristics (optimized)
     */
    public function getClusterCharacteristics(array $clusteringResult): array
    {
        $characteristics = [];
        
        foreach ($clusteringResult['clusters'] as $clusterId => $cluster) {
            if (empty($cluster)) {
                $characteristics[$clusterId] = [
                    'size' => 0,
                    'avg_age' => 0,
                    'avg_family_size' => 0,
                    'most_common_education' => 'N/A',
                    'most_common_income' => 'N/A',
                    'most_common_employment' => 'N/A',
                    'most_common_health' => 'N/A'
                ];
                continue;
            }
            
            $ages = [];
            $familySizes = [];
            $educations = [];
            $incomes = [];
            $employments = [];
            $healths = [];
            
            foreach ($cluster as $point) {
                $resident = $point['resident'];
                $ages[] = $resident->age ?? 30;
                $familySizes[] = $resident->family_size ?? 1;
                $educations[] = $resident->education_level ?? 'Elementary';
                $incomes[] = $resident->income_level ?? 'Low';
                $employments[] = $resident->employment_status ?? 'Unemployed';
                $healths[] = $resident->health_status ?? 'Healthy';
            }
            
            $characteristics[$clusterId] = [
                'size' => count($cluster),
                'avg_age' => round(array_sum($ages) / count($ages), 1),
                'avg_family_size' => round(array_sum($familySizes) / count($familySizes), 1),
                'most_common_education' => $this->getMostCommon($educations),
                'most_common_income' => $this->getMostCommon($incomes),
                'most_common_employment' => $this->getMostCommon($employments),
                'most_common_health' => $this->getMostCommon($healths)
            ];
        }
        
        return $characteristics;
    }

    /**
     * Find optimal K using simple heuristics
     */
    public function findOptimalK(Collection $residents, int $maxK = 5): int
    {
        // Simple heuristic: optimal K based on data size and diversity
        $count = $residents->count();
        
        if ($count < 5) return 2;
        if ($count < 10) return 3;
        if ($count < 20) return 4;
        
        return 3; // Default for larger datasets
    }

    // Normalization methods (unchanged)
    private function normalizeAge(?int $age): float
    {
        if (!$age) return 0.5;
        return min(1.0, max(0.0, ($age - 18) / (80 - 18)));
    }

    private function normalizeFamilySize(?int $familySize): float
    {
        if (!$familySize) return 0.5;
        return min(1.0, max(0.0, ($familySize - 1) / (10 - 1)));
    }

    private function normalizeEducation(?string $education): float
    {
        $levels = [
            'No Education' => 0.0,
            'Elementary' => 0.2,
            'High School' => 0.4,
            'Vocational' => 0.6,
            'College' => 0.8,
            'Post Graduate' => 1.0
        ];
        
        return $levels[$education] ?? 0.5;
    }

    private function normalizeIncome(?string $income): float
    {
        $levels = [
            'Low' => 0.0,
            'Lower Middle' => 0.25,
            'Middle' => 0.5,
            'Upper Middle' => 0.75,
            'High' => 1.0
        ];
        
        return $levels[$income] ?? 0.5;
    }

    private function normalizeEmployment(?string $employment): float
    {
        $levels = [
            'Unemployed' => 0.0,
            'Part-time' => 0.3,
            'Self-employed' => 0.6,
            'Full-time' => 1.0
        ];
        
        return $levels[$employment] ?? 0.5;
    }

    private function normalizeHealth(?string $health): float
    {
        $levels = [
            'Critical' => 0.0,
            'Poor' => 0.25,
            'Fair' => 0.5,
            'Good' => 0.75,
            'Excellent' => 1.0
        ];
        
        return $levels[$health] ?? 0.5;
    }

    private function getMostCommon(array $array): string
    {
        $counts = array_count_values($array);
        arsort($counts);
        return array_key_first($counts) ?? 'Unknown';
    }
} 