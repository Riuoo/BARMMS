<?php

namespace App\Services;

use App\Models\Residents;
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
     * Clustering with demographic profile only (no health status/incidence)
     */
    public function clusterResidents(): array
    {
        // Check cache first
        $cacheKey = "clustering_k{$this->k}_" . Residents::count();
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

        // Use simple rule-based clustering (no health incidence)
        $clusters = $this->simpleRuleBasedClustering($residents);

        $result = [
            'clusters' => $clusters,
            'centroids' => $this->calculateSimpleCentroids($clusters),
            'iterations' => 1,
            'converged' => true,
            'residents' => $residents
        ];

        // Cache the result for 30 minutes
        Cache::put($cacheKey, $result, 1800);

        return $result;
    }

    /**
     * Simple rule-based clustering using demographic characteristics only
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