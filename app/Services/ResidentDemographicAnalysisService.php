<?php

namespace App\Services;

use App\Models\Residents;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Phpml\Clustering\KMeans;
use Phpml\Math\Distance\Euclidean;

class ResidentDemographicAnalysisService
{
    private $k;
	private $maxIterations;

	// Feature weights (tune as needed)
	private float $weightAge = 1.0;
	private float $weightFamilySize = 0.8;
	private float $weightEducation = 0.6;
	private float $weightIncome = 1.0;
	private float $weightEmployment = 0.7;
	private float $weightHealth = 1.0;
	private float $weightPurok = 0.6;

	public function __construct(int $k = 3, int $maxIterations = 100)
    {
		$this->k = $k;
		$this->maxIterations = $maxIterations;
    }

    /**
     * Clustering with demographic profile only (no health status/incidence)
     */
	public function clusterResidents(): array
    {
        // Check cache first
		$cacheKey = "clustering_k{$this->k}_it{$this->maxIterations}_" . Residents::count();
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

		$residents = Residents::select('id', 'name', 'age', 'family_size', 'education_level', 'income_level', 'employment_status', 'health_status', 'address')->get();

        if ($residents->count() < $this->k) {
            return [
                'clusters' => [],
                'centroids' => [],
				'iterations' => 0,
				'converged' => false,
                'error' => 'Not enough data points for clustering',
                'residents' => $residents
            ];
        }

		// Build numeric feature samples
		[$samples, $indexMap] = $this->buildSamples($residents);

		// Run K-Means clustering using k-means++ initialization to support float features
		$kmeans = new KMeans($this->k, KMeans::INIT_KMEANS_PLUS_PLUS);
		$rawClusters = $kmeans->cluster($samples);

		// Map clustered samples back to residents and enrich with features
		$clusters = array_fill(0, $this->k, []);
		$sampleLookup = $this->buildSampleLookup($samples);
		foreach ($rawClusters as $clusterId => $clusterSamples) {
			foreach ($clusterSamples as $sample) {
				$key = $this->encodeSampleKey($sample);
				if (!isset($sampleLookup[$key]) || count($sampleLookup[$key]) === 0) {
					continue;
				}
				$sampleIndex = array_shift($sampleLookup[$key]);
				$resident = $indexMap[$sampleIndex];
				$clusters[$clusterId][] = [
					'id' => $resident->id,
					'features' => $sample,
					'resident' => $resident
				];
			}
		}

		$centroids = $this->calculateSimpleCentroids($clusters);
		$iterations = $this->maxIterations; // PHP-ML does not expose actual iterations; use configured max as upper bound
		$converged = true; // Assume convergence for display purposes

		$result = [
			'clusters' => $clusters,
			'centroids' => $centroids,
			'iterations' => $iterations,
			'converged' => $converged,
			'residents' => $residents
		];

        // Cache the result for 30 minutes
        Cache::put($cacheKey, $result, 1800);

        return $result;
    }

    /**
     * Hierarchical clustering: first split by Purok, then cluster demographics per-purok with auto K.
     */
    public function clusterResidentsHierarchical(): array
    {
        $residents = Residents::select('id', 'name', 'age', 'family_size', 'education_level', 'income_level', 'employment_status', 'health_status', 'address')->get();
        if ($residents->isEmpty()) {
            return [
                'clusters' => [],
                'centroids' => [],
                'iterations' => 0,
                'converged' => true,
                'residents' => $residents
            ];
        }

        // Group residents by purok token (strict match against normalized token)
        $purokGroups = [];
        foreach ($residents as $resident) {
            $token = $this->extractPurokToken($resident->address ?? '');
            $key = $token !== '' ? $token : 'other';
            $purokGroups[$key] = $purokGroups[$key] ?? [];
            $purokGroups[$key][] = $resident;
        }

        $allClusters = [];
        $iterations = 0;
        foreach ($purokGroups as $purok => $groupResidents) {
            $groupCollection = collect($groupResidents);
            if ($groupCollection->count() < 2) {
                // Single-item group -> own cluster
                $allClusters[] = [
                    [
                        'id' => $groupResidents[0]->id,
                        'features' => [0.0],
                        'resident' => $groupResidents[0],
                        'purok' => $purok
                    ]
                ];
                continue;
            }

            // Build samples without purok (demographics-only)
            [$samples, $indexMap] = $this->buildSamplesDemographicsOnly($groupCollection);

            // Auto K for this purok
            $localMaxK = min(5, max(2, $groupCollection->count() - 1));
            $bestK = $this->findBestKForSamples($samples, $localMaxK);

            $kmeans = new KMeans($bestK, KMeans::INIT_KMEANS_PLUS_PLUS);
            $rawClusters = $kmeans->cluster($samples);

            // Map back to residents
            $clusters = array_fill(0, $bestK, []);
            $sampleLookup = $this->buildSampleLookup($samples);
            foreach ($rawClusters as $clusterId => $clusterSamples) {
                foreach ($clusterSamples as $sample) {
                    $key = $this->encodeSampleKey($sample);
                    if (!isset($sampleLookup[$key]) || count($sampleLookup[$key]) === 0) {
                        continue;
                    }
                    $sampleIndex = array_shift($sampleLookup[$key]);
                    $resident = $indexMap[$sampleIndex];
                    $clusters[$clusterId][] = [
                        'id' => $resident->id,
                        'features' => $sample,
                        'resident' => $resident,
                        'purok' => $purok
                    ];
                }
            }

            foreach ($clusters as $c) {
                $allClusters[] = $c;
            }
            $iterations += 1;
        }

        return [
            'clusters' => $allClusters,
            'centroids' => [],
            'iterations' => $iterations,
            'converged' => true,
            'residents' => $residents
        ];
    }

    private function buildSamplesDemographicsOnly(Collection $residents): array
    {
        $samples = [];
        $indexMap = [];

        $educationLevels = ['No Education','Elementary','High School','Vocational','College','Post Graduate'];
        $incomeLevels = ['Low','Lower Middle','Middle','Upper Middle','High'];
        $employmentStatuses = ['Unemployed','Part-time','Self-employed','Full-time'];
        $healthStatuses = ['Critical','Poor','Fair','Good','Excellent'];

        foreach ($residents as $i => $resident) {
            $vector = [];
            $vector[] = $this->weightAge * $this->normalizeAge($resident->age ?? 30);
            $vector[] = $this->weightFamilySize * $this->normalizeFamilySize($resident->family_size ?? 1);

            $edu = $resident->education_level ?? 'Elementary';
            foreach ($educationLevels as $level) {
                $vector[] = $this->weightEducation * (($edu === $level) ? 1.0 : 0.0);
            }
            $inc = $resident->income_level ?? 'Low';
            foreach ($incomeLevels as $level) {
                $vector[] = $this->weightIncome * (($inc === $level) ? 1.0 : 0.0);
            }
            $emp = $resident->employment_status ?? 'Unemployed';
            foreach ($employmentStatuses as $status) {
                $vector[] = $this->weightEmployment * (($emp === $status) ? 1.0 : 0.0);
            }
            $hlth = $resident->health_status ?? 'Healthy';
            foreach ($healthStatuses as $status) {
                $vector[] = $this->weightHealth * (($hlth === $status) ? 1.0 : 0.0);
            }

            $samples[] = $vector;
            $indexMap[$i] = $resident;
        }

        return [$samples, $indexMap];
    }

    private function findBestKForSamples(array $samples, int $maxK): int
    {
        if (count($samples) < 3) return 2;
        $inertias = [];
        for ($k = 2; $k <= $maxK; $k++) {
            $kmeans = new KMeans($k, KMeans::INIT_KMEANS_PLUS_PLUS);
            $clusters = $kmeans->cluster($samples);
            $centroids = $this->calculateSimpleCentroids($this->wrapClustersForCentroid($clusters));
            $inertias[$k] = $this->computeInertia($clusters, $centroids);
        }
        $bestK = array_key_first($inertias) ?? 2;
        $prev = null;
        foreach ($inertias as $k => $inertia) {
            if ($prev === null) { $prev = $inertia; $bestK = $k; continue; }
            $drop = ($prev - $inertia) / max($prev, 1e-9);
            if ($drop < 0.15) { break; }
            $bestK = $k;
            $prev = $inertia;
        }
        return $bestK;
    }

	/**
	 * Build numeric feature vectors and index mapping
	 */
	private function buildSamples(Collection $residents): array
	{
		$samples = [];
		$indexMap = [];

		// Domain categories for one-hot encoding
		$educationLevels = ['No Education','Elementary','High School','Vocational','College','Post Graduate'];
		$incomeLevels = ['Low','Lower Middle','Middle','Upper Middle','High'];
		$employmentStatuses = ['Unemployed','Part-time','Self-employed','Full-time'];
		$healthStatuses = ['Critical','Poor','Fair','Good','Excellent'];

		// Build mapping for Purok token from address (cap dimensions)
		$purokToIndex = [];
		$nextIndex = 0;
		$MAX_PUROK_DIM = 10; // 9 distinct + 1 "other"
		foreach ($residents as $resident) {
			$token = $this->extractPurokToken($resident->address ?? '');
			if ($token !== '' && !isset($purokToIndex[$token]) && $nextIndex < $MAX_PUROK_DIM - 1) {
				$purokToIndex[$token] = $nextIndex++;
			}
		}

		foreach ($residents as $i => $resident) {
			$vector = [];

			// Numeric features (scaled and weighted)
			$vector[] = $this->weightAge * $this->normalizeAge($resident->age ?? 30);
			$vector[] = $this->weightFamilySize * $this->normalizeFamilySize($resident->family_size ?? 1);

			// One-hot: Education
			$edu = $resident->education_level ?? 'Elementary';
			foreach ($educationLevels as $level) {
				$vector[] = $this->weightEducation * (($edu === $level) ? 1.0 : 0.0);
			}

			// One-hot: Income
			$inc = $resident->income_level ?? 'Low';
			foreach ($incomeLevels as $level) {
				$vector[] = $this->weightIncome * (($inc === $level) ? 1.0 : 0.0);
			}

			// One-hot: Employment
			$emp = $resident->employment_status ?? 'Unemployed';
			foreach ($employmentStatuses as $status) {
				$vector[] = $this->weightEmployment * (($emp === $status) ? 1.0 : 0.0);
			}

			// One-hot: Health
			$hlth = $resident->health_status ?? 'Healthy';
			foreach ($healthStatuses as $status) {
				$vector[] = $this->weightHealth * (($hlth === $status) ? 1.0 : 0.0);
			}

			// One-hot: Purok (capped; overflow -> other)
			$purokVec = array_fill(0, $MAX_PUROK_DIM, 0.0);
			$token = $this->extractPurokToken($resident->address ?? '');
			if ($token !== '') {
				$idx = $purokToIndex[$token] ?? ($MAX_PUROK_DIM - 1);
				$purokVec[$idx] = 1.0 * $this->weightPurok;
			}
			$vector = array_merge($vector, $purokVec);

			$samples[] = $vector;
			$indexMap[$i] = $resident;
		}

		return [$samples, $indexMap];
	}

	private function buildSampleLookup(array $samples): array
	{
		$lookup = [];
		foreach ($samples as $index => $sample) {
			$key = $this->encodeSampleKey($sample);
			$lookup[$key] = $lookup[$key] ?? [];
			$lookup[$key][] = $index;
		}
		return $lookup;
	}

	private function encodeSampleKey(array $sample): string
	{
		return json_encode(array_map(fn($v) => round($v, 6), $sample));
	}

    /**
     * Calculate simple centroids for visualization
     */
	private function calculateSimpleCentroids(array $clusters): array
    {
        $centroids = [];

        foreach ($clusters as $clusterId => $cluster) {
            if (empty($cluster)) {
                // infer feature count from any other cluster, fallback to 4 (age, fam, minimal)
                $someCluster = current(array_filter($clusters));
                $featureCount = ($someCluster && isset($someCluster[0]['features'])) ? count($someCluster[0]['features']) : 4;
                $centroids[$clusterId] = array_fill(0, $featureCount, 0.0);
                continue;
            }

            $featureCount = isset($cluster[0]['features']) ? count($cluster[0]['features']) : 4;
            $centroid = array_fill(0, $featureCount, 0);
            $count = count($cluster);

            foreach ($cluster as $point) {
                for ($i = 0; $i < $featureCount; $i++) {
                    $centroid[$i] += $point['features'][$i];
                }
            }

            for ($i = 0; $i < $featureCount; $i++) {
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
                    'most_common_health' => 'N/A',
                    'most_common_purok' => 'N/A'
                ];
                continue;
            }

            $ages = [];
            $familySizes = [];
            $educations = [];
            $incomes = [];
            $employments = [];
            $healths = [];
            $puroks = [];

            foreach ($cluster as $point) {
                $resident = $point['resident'];
                $ages[] = $resident->age ?? 30;
                $familySizes[] = $resident->family_size ?? 1;
                $educations[] = $resident->education_level ?? 'Elementary';
                $incomes[] = $resident->income_level ?? 'Low';
                $employments[] = $resident->employment_status ?? 'Unemployed';
                $healths[] = $resident->health_status ?? 'Healthy';
                $puroks[] = $this->extractPurokToken($resident->address ?? '');
            }

            $characteristics[$clusterId] = [
                'size' => count($cluster),
                'avg_age' => round(array_sum($ages) / count($ages), 1),
                'avg_family_size' => round(array_sum($familySizes) / count($familySizes), 1),
                'most_common_age' => $this->getMostCommon($ages),
                'most_common_family_size' => $this->getMostCommon($familySizes),
                'most_common_education' => $this->getMostCommon($educations),
                'most_common_income' => $this->getMostCommon($incomes),
                'most_common_employment' => $this->getMostCommon($employments),
                'most_common_health' => $this->getMostCommon($healths),
                'most_common_purok' => $this->getMostCommon(array_values(array_filter($puroks))),
                'income_distribution' => array_count_values($incomes),
                'employment_distribution' => array_count_values($employments),
                'health_distribution' => array_count_values($healths),
                'education_distribution' => array_count_values($educations)
            ];
        }

        return $characteristics;
    }

    /**
     * Find optimal K using simple heuristics
     */
	public function findOptimalK(Collection $residents, int $maxK = 5): int
    {
		$count = $residents->count();
		if ($count < 4) return 2;

		// Build samples once
		[$samples] = $this->buildSamples($residents);
		return $this->findOptimalKEnhanced($samples);
    }

    /**
     * Enhanced K selection using multiple heuristics
     */
    private function findOptimalKEnhanced(array $samples): int
    {
        $maxK = min(10, count($samples) - 1);
        if ($maxK < 2) return 2;

        $elbowK = $this->findElbowK($samples, $maxK);
        $silhouetteK = $this->findSilhouetteK($samples, $maxK);
        $gapK = $this->findGapK($samples, $maxK);

        // Combine results with weights
        $kScores = [];
        for ($k = 2; $k <= $maxK; $k++) {
            $score = 0;
            if ($k === $elbowK) $score += 0.4;
            if ($k === $silhouetteK) $score += 0.4;
            if ($k === $gapK) $score += 0.2;
            $kScores[$k] = $score;
        }

        return array_search(max($kScores), $kScores) ?: 3;
    }

    private function findElbowK(array $samples, int $maxK): int
    {
        $inertias = [];
        for ($k = 2; $k <= $maxK; $k++) {
            $kmeans = new KMeans($k, 100, new Euclidean(), KMeans::INIT_KMEANS_PLUS_PLUS);
            $clusters = $kmeans->cluster($samples);
            $centroids = $this->calculateSimpleCentroids($this->wrapClustersForCentroid($clusters));
            $inertias[$k] = $this->computeInertia($clusters, $centroids);
        }

        $bestK = 2;
        $maxCurvature = 0;
        for ($k = 3; $k <= $maxK - 1; $k++) {
            $curvature = abs($inertias[$k-1] - 2*$inertias[$k] + $inertias[$k+1]);
            if ($curvature > $maxCurvature) {
                $maxCurvature = $curvature;
                $bestK = $k;
            }
        }
        return $bestK;
    }

    private function findSilhouetteK(array $samples, int $maxK): int
    {
        $bestK = 2;
        $bestScore = -1;

        for ($k = 2; $k <= $maxK; $k++) {
            $kmeans = new KMeans($k, 100, new Euclidean(), KMeans::INIT_KMEANS_PLUS_PLUS);
            $clusters = $kmeans->cluster($samples);
            $centroids = $this->calculateSimpleCentroids($this->wrapClustersForCentroid($clusters));
            $score = $this->computeSilhouetteScore($samples, $clusters, $centroids);
            
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestK = $k;
            }
        }
        return $bestK;
    }

    private function computeSilhouetteScore(array $samples, array $clusters, array $centroids): float
    {
        $totalScore = 0;
        $totalPoints = 0;
        $distance = new Euclidean();

        foreach ($clusters as $clusterId => $clusterSamples) {
            foreach ($clusterSamples as $sample) {
                $a = 0;
                $clusterSize = count($clusterSamples);
                if ($clusterSize > 1) {
                    foreach ($clusterSamples as $otherSample) {
                        if ($sample !== $otherSample) {
                            $a += $distance->distance($sample, $otherSample);
                        }
                    }
                    $a /= ($clusterSize - 1);
                }

                $b = PHP_FLOAT_MAX;
                foreach ($clusters as $otherClusterId => $otherClusterSamples) {
                    if ($otherClusterId !== $clusterId) {
                        $avgDistance = 0;
                        foreach ($otherClusterSamples as $otherSample) {
                            $avgDistance += $distance->distance($sample, $otherSample);
                        }
                        $avgDistance /= count($otherClusterSamples);
                        $b = min($b, $avgDistance);
                    }
                }

                if ($a < $b) {
                    $totalScore += 1 - ($a / $b);
                } elseif ($a > $b) {
                    $totalScore += ($b / $a) - 1;
                }
                $totalPoints++;
            }
        }

        return $totalPoints > 0 ? $totalScore / $totalPoints : 0;
    }

    private function findGapK(array $samples, int $maxK): int
    {
        $originalInertias = [];
        $referenceInertias = [];

        for ($k = 2; $k <= $maxK; $k++) {
            $kmeans = new KMeans($k, 100, new Euclidean(), KMeans::INIT_KMEANS_PLUS_PLUS);
            $clusters = $kmeans->cluster($samples);
            $centroids = $this->calculateSimpleCentroids($this->wrapClustersForCentroid($clusters));
            $originalInertias[$k] = log($this->computeInertia($clusters, $centroids));

            $referenceInertia = 0;
            $numReferences = 5;
            for ($ref = 0; $ref < $numReferences; $ref++) {
                $referenceSamples = $this->generateReferenceData($samples);
                $refKmeans = new KMeans($k, 100, new Euclidean(), KMeans::INIT_KMEANS_PLUS_PLUS);
                $refClusters = $refKmeans->cluster($referenceSamples);
                $refCentroids = $this->calculateSimpleCentroids($this->wrapClustersForCentroid($refClusters));
                $referenceInertia += log($this->computeInertia($refClusters, $refCentroids));
            }
            $referenceInertias[$k] = $referenceInertia / $numReferences;
        }

        $bestK = 2;
        $maxGap = 0;
        for ($k = 2; $k <= $maxK; $k++) {
            $gap = $referenceInertias[$k] - $originalInertias[$k];
            if ($gap > $maxGap) {
                $maxGap = $gap;
                $bestK = $k;
            }
        }
        return $bestK;
    }

    private function generateReferenceData(array $samples): array
    {
        $referenceSamples = [];
        $numFeatures = count($samples[0]);
        
        $mins = array_fill(0, $numFeatures, PHP_FLOAT_MAX);
        $maxs = array_fill(0, $numFeatures, PHP_FLOAT_MIN);
        
        foreach ($samples as $sample) {
            for ($i = 0; $i < $numFeatures; $i++) {
                $mins[$i] = min($mins[$i], $sample[$i]);
                $maxs[$i] = max($maxs[$i], $sample[$i]);
            }
        }

        for ($i = 0; $i < count($samples); $i++) {
            $referenceSample = [];
            for ($j = 0; $j < $numFeatures; $j++) {
                $referenceSample[] = $mins[$j] + (mt_rand() / mt_getrandmax()) * ($maxs[$j] - $mins[$j]);
            }
            $referenceSamples[] = $referenceSample;
        }

        return $referenceSamples;
    }

	private function wrapClustersForCentroid(array $rawClusters): array
	{
		$wrapped = [];
		foreach ($rawClusters as $clusterId => $clusterSamples) {
			$wrapped[$clusterId] = [];
			foreach ($clusterSamples as $sample) {
				$wrapped[$clusterId][] = [ 'features' => $sample ];
			}
		}
		return $wrapped;
	}

	private function computeInertia(array $rawClusters, array $centroids): float
	{
		$distance = new Euclidean();
		$sum = 0.0;
        foreach ($rawClusters as $clusterId => $clusterSamples) {
            $featureCount = isset($centroids[$clusterId]) ? count($centroids[$clusterId]) : 4;
            $centroid = $centroids[$clusterId] ?? array_fill(0, $featureCount, 0.0);
			foreach ($clusterSamples as $sample) {
				$sum += pow($distance->distance($sample, $centroid), 2);
			}
		}
		return $sum;
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

    private function extractPurokToken(string $address): string
    {
        $address = trim(strtolower($address));
        if ($address === '') return '';
        if (preg_match('/purok\s*([a-z0-9]+)/i', $address, $m)) {
            $raw = (string) $m[1];
            return $this->normalizePurokToken($raw);
        }
        return '';
    }

    private function normalizePurokToken(string $token): string
    {
        $t = strtolower(trim($token));
        $map = [
            'one'=>1,'two'=>2,'three'=>3,'four'=>4,'five'=>5,'six'=>6,'seven'=>7,'eight'=>8,'nine'=>9,'ten'=>10,
            'eleven'=>11,'twelve'=>12,'thirteen'=>13,'fourteen'=>14,'fifteen'=>15,'sixteen'=>16,'seventeen'=>17,'eighteen'=>18,'nineteen'=>19,'twenty'=>20,
            'i'=>1,'ii'=>2,'iii'=>3,'iv'=>4,'v'=>5,'vi'=>6,'vii'=>7,'viii'=>8,'ix'=>9,'x'=>10,
            'xi'=>11,'xii'=>12,'xiii'=>13,'xiv'=>14,'xv'=>15,'xvi'=>16,'xvii'=>17,'xviii'=>18,'xix'=>19,'xx'=>20
        ];
        if (isset($map[$t])) return (string)$map[$t];
        if (preg_match('/^\d+$/', $t)) return $t;
        if (preg_match('/(\d{1,3})/', $t, $m)) return $m[1];
        return $t;
    }
}