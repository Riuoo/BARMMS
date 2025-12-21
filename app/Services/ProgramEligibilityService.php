<?php

namespace App\Services;

use App\Models\Program;
use App\Models\Residents;
use App\Services\ResidentDataAggregationService;

class ProgramEligibilityService
{
    protected $dataAggregationService;

    public function __construct(ResidentDataAggregationService $dataAggregationService)
    {
        $this->dataAggregationService = $dataAggregationService;
    }

    /**
     * Evaluate a single resident against program criteria
     */
    public function evaluateResident($resident, $program): bool
    {
        if (!$program instanceof Program) {
            return false;
        }

        $criteria = $program->criteria;
        if (empty($criteria)) {
            return false;
        }

        $profile = $this->dataAggregationService->getResidentProfile($resident->id);
        
        return $this->evaluateCriteria($profile, $criteria);
    }

    /**
     * Recursive method to evaluate decision tree criteria
     */
    public function evaluateCriteria(array $profile, array $criteria): bool
    {
        if (!isset($criteria['operator'])) {
            return false;
        }

        $operator = strtoupper($criteria['operator']);
        $conditions = $criteria['conditions'] ?? [];

        if (empty($conditions)) {
            return false;
        }

        $results = [];
        
        foreach ($conditions as $condition) {
            if (isset($condition['field'])) {
                // Simple field condition
                $results[] = $this->evaluateFieldCondition($profile, $condition);
            } elseif (isset($condition['operator'])) {
                // Nested condition
                $results[] = $this->evaluateCriteria($profile, $condition);
            }
        }

        if (empty($results)) {
            return false;
        }

        // Apply operator
        if ($operator === 'AND') {
            return !in_array(false, $results, true);
        } elseif ($operator === 'OR') {
            return in_array(true, $results, true);
        }

        return false;
    }

    /**
     * Evaluate a single field condition
     */
    private function evaluateFieldCondition(array $profile, array $condition): bool
    {
        $field = $condition['field'];
        $operator = $condition['operator'];
        $value = $condition['value'];

        // Get field value from profile (supports nested fields like 'medical.has_chronic_conditions')
        $fieldValue = $this->getFieldValue($profile, $field);

        return $this->compareValues($fieldValue, $operator, $value);
    }

    /**
     * Get field value from profile (supports nested fields)
     */
    private function getFieldValue(array $profile, string $field)
    {
        // Handle nested fields like 'medical.has_chronic_conditions'
        if (strpos($field, '.') !== false) {
            $parts = explode('.', $field);
            $current = $profile;
            
            foreach ($parts as $part) {
                if (isset($current[$part])) {
                    $current = $current[$part];
                } else {
                    return null;
                }
            }
            
            return $current;
        }

        // Direct field access
        if (isset($profile['demographics'][$field])) {
            return $profile['demographics'][$field];
        }

        if (isset($profile['resident'][$field])) {
            return $profile['resident'][$field];
        }

        return null;
    }

    /**
     * Compare values based on operator
     */
    private function compareValues($fieldValue, string $operator, $compareValue): bool
    {
        switch ($operator) {
            case 'equals':
                return $fieldValue == $compareValue;
            
            case 'not_equals':
                return $fieldValue != $compareValue;
            
            case 'in':
                if (!is_array($compareValue)) {
                    $compareValue = [$compareValue];
                }
                return in_array($fieldValue, $compareValue);
            
            case 'not_in':
                if (!is_array($compareValue)) {
                    $compareValue = [$compareValue];
                }
                return !in_array($fieldValue, $compareValue);
            
            case 'greater_than':
                return is_numeric($fieldValue) && is_numeric($compareValue) && $fieldValue > $compareValue;
            
            case 'less_than':
                return is_numeric($fieldValue) && is_numeric($compareValue) && $fieldValue < $compareValue;
            
            case 'greater_than_or_equal':
                return is_numeric($fieldValue) && is_numeric($compareValue) && $fieldValue >= $compareValue;
            
            case 'less_than_or_equal':
                return is_numeric($fieldValue) && is_numeric($compareValue) && $fieldValue <= $compareValue;
            
            default:
                return false;
        }
    }

    /**
     * Get all eligible residents for a program (optionally filtered by purok)
     */
    public function getEligibleResidents($program, $purok = null): array
    {
        $residents = Residents::all();
        $eligible = [];

        foreach ($residents as $resident) {
            // Filter by purok if specified
            if ($purok !== null) {
                $profile = $this->dataAggregationService->getResidentProfile($resident->id);
                $residentPurok = $profile['demographics']['purok'] ?? 'n/a';
                
                if ($residentPurok !== strtolower($purok)) {
                    continue;
                }
            }

            if ($this->evaluateResident($resident, $program)) {
                $eligible[] = $resident;
            }
        }

        return $eligible;
    }

    /**
     * Get all programs a resident is eligible for
     */
    public function getResidentPrograms($resident): array
    {
        $programs = Program::active()->get();
        $eligiblePrograms = [];

        foreach ($programs as $program) {
            if ($this->evaluateResident($resident, $program)) {
                $eligiblePrograms[] = $program;
            }
        }

        return $eligiblePrograms;
    }

    /**
     * Get program recommendations for a specific purok
     */
    public function getPurokProgramRecommendations($purok): array
    {
        $programs = Program::active()->get();
        $recommendations = [];

        foreach ($programs as $program) {
            $stats = $this->getPurokEligibilityStats($program, $purok);
            
            if ($stats['eligible_count'] > 0) {
                $recommendations[] = [
                    'program' => $program,
                    'stats' => $stats,
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Groups eligible residents by purok and identifies target puroks
     */
    public function getProgramRecommendationsByPurok($program): array
    {
        $stats = $this->getPurokEligibilityStats($program);
        $targetPuroks = $this->identifyTargetPuroks($program);
        
        $recommendations = [];
        
        foreach ($stats as $purokStat) {
            $purok = $purokStat['purok'];
            $eligibleResidents = $this->getEligibleResidents($program, $purok);
            
            $recommendations[] = [
                'purok' => $purokStat['purok_display'],
                'purok_token' => $purok,
                'total_residents' => $purokStat['total_residents'],
                'eligible_count' => $purokStat['eligible_count'],
                'eligibility_percentage' => $purokStat['eligibility_percentage'],
                'is_recommended' => in_array($purok, $targetPuroks),
                'eligible_residents' => $eligibleResidents,
            ];
        }

        // Sort by eligibility percentage descending
        usort($recommendations, function ($a, $b) {
            return $b['eligibility_percentage'] <=> $a['eligibility_percentage'];
        });

        return $recommendations;
    }

    /**
     * Identifies which puroks should be recommended based on eligibility percentage
     */
    public function identifyTargetPuroks($program, $threshold = 0.5): array
    {
        $stats = $this->getPurokEligibilityStats($program);
        $targetPuroks = [];

        foreach ($stats as $stat) {
            if ($stat['eligibility_percentage'] >= ($threshold * 100)) {
                $targetPuroks[] = $stat['purok'];
            }
        }

        return $targetPuroks;
    }

    /**
     * Returns statistics per purok (total residents, eligible count, percentage)
     */
    public function getPurokEligibilityStats($program, $specificPurok = null): array
    {
        $puroks = $this->dataAggregationService->getAllPuroks();
        $stats = [];

        foreach ($puroks as $purokData) {
            $purok = $purokData['token'];
            
            // Skip if specific purok is requested and doesn't match
            if ($specificPurok !== null && $purok !== strtolower($specificPurok)) {
                continue;
            }

            $residents = Residents::all();
            $purokResidents = [];
            $eligibleCount = 0;

            foreach ($residents as $resident) {
                $profile = $this->dataAggregationService->getResidentProfile($resident->id);
                $residentPurok = $profile['demographics']['purok'] ?? 'n/a';
                
                if ($residentPurok === $purok) {
                    $purokResidents[] = $resident;
                    
                    if ($this->evaluateResident($resident, $program)) {
                        $eligibleCount++;
                    }
                }
            }

            $totalResidents = count($purokResidents);
            $eligibilityPercentage = $totalResidents > 0 
                ? ($eligibleCount / $totalResidents) * 100 
                : 0;

            $stats[] = [
                'purok' => $purok,
                'purok_display' => $purokData['name'],
                'total_residents' => $totalResidents,
                'eligible_count' => $eligibleCount,
                'eligibility_percentage' => round($eligibilityPercentage, 2),
            ];
        }

        return $stats;
    }
}

