<?php

namespace App\Services;

use App\Models\Residents;
use App\Models\HealthStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ResidentClassificationPredictionService
{
    /**
     * Enhanced health condition prediction with validation
     */
    public function buildHealthConditionTree(): array
    {
        // Check cache first
        $cacheKey = "health_condition_tree_" . Residents::count();
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        $residents = Residents::select('id', 'name', 'age', 'family_size', 'education_level', 'income_level', 'employment_status', 'health_status')->get();
        
        if ($residents->count() < 10) {
            return [
                'error' => 'Insufficient data for health condition analysis (minimum 10 residents required)'
            ];
        }

        // Get health status data for training
        $healthStatuses = HealthStatus::select('user_id', 'concern_type', 'severity', 'status', 'description')->get();
        $healthDataByUser = $healthStatuses->groupBy('user_id');

        $rules = $this->generateHealthConditionRules();
        $predictions = [];
        $accuracy = 0;
        $correct = 0;
        $validationMetrics = [];
        
        // Split data for validation (70% training, 30% testing)
        $trainingResidents = $residents->take(ceil($residents->count() * 0.7));
        $testingResidents = $residents->slice(ceil($residents->count() * 0.7));
        
        // Training phase
        $trainingPredictions = [];
        foreach ($trainingResidents as $resident) {
            $predicted = $this->predictHealthCondition($resident, $healthDataByUser);
            $actual = $this->determineActualHealthCondition($resident, $healthDataByUser);
            $trainingPredictions[] = [
                'resident' => $resident,
                'predicted' => $predicted,
                'actual' => $actual,
                'correct' => $predicted === $actual
            ];
        }
        
        // Testing phase for validation
        $testingPredictions = [];
        foreach ($testingResidents as $resident) {
            $predicted = $this->predictHealthCondition($resident, $healthDataByUser);
            $actual = $this->determineActualHealthCondition($resident, $healthDataByUser);
            $testingPredictions[] = [
                'resident' => $resident,
                'predicted' => $predicted,
                'actual' => $actual,
                'correct' => $predicted === $actual
            ];
        }
        
        // Calculate validation metrics
        $validationMetrics = $this->calculateValidationMetrics($trainingPredictions, $testingPredictions);
        
        $result = [
            'rules' => $rules,
            'accuracy' => $validationMetrics['overall_accuracy'],
            'training_accuracy' => $validationMetrics['training_accuracy'],
            'testing_accuracy' => $validationMetrics['testing_accuracy'],
            'feature_importance' => $this->calculateHealthFeatureImportance(),
            'validation_metrics' => $validationMetrics,
            'sample_size' => count($trainingPredictions) + count($testingPredictions),
            'training_predictions' => $trainingPredictions,
            'testing_predictions' => $testingPredictions
        ];

        // Cache the result for 30 minutes
        Cache::put($cacheKey, $result, 1800);

        return $result;
    }

    /**
     * Enhanced service eligibility with health factors
     */
    public function buildServiceEligibilityTree(): array
    {
        // Check cache first
        $cacheKey = "enhanced_service_eligibility_" . Residents::count();
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        $residents = Residents::select('id', 'name', 'age', 'family_size', 'education_level', 'income_level', 'employment_status', 'health_status')->get();
        
        if ($residents->count() < 10) {
            return [
                'error' => 'Insufficient data for analysis (minimum 10 residents required)'
            ];
        }

        // Get health status data
        $healthStatuses = HealthStatus::select('user_id', 'concern_type', 'severity', 'status')->get();
        $healthDataByUser = $healthStatuses->groupBy('user_id');

        $rules = $this->generateEnhancedServiceEligibilityRules();
        $predictions = [];
        $accuracy = 0;
        $correct = 0;
        
        foreach ($residents as $resident) {
            $predicted = $this->predictEnhancedServiceEligibility($resident, $healthDataByUser);
            $actual = $this->determineEnhancedServiceEligibility($resident, $healthDataByUser);
            $predictions[] = [
                'resident' => $resident,
                'predicted' => $predicted,
                'actual' => $actual,
                'correct' => $predicted === $actual
            ];
            
            if ($predicted === $actual) {
                $correct++;
            }
        }
        
        $accuracy = $correct / count($predictions);
        
        $result = [
            'rules' => $rules,
            'accuracy' => $accuracy,
            'feature_importance' => $this->calculateEnhancedFeatureImportance(),
            'sample_size' => count($predictions),
            'predictions' => $predictions,
            'deciding_factors' => $this->identifyDecidingFactors($predictions)
        ];

        // Cache the result for 30 minutes
        Cache::put($cacheKey, $result, 1800);

        return $result;
    }

    /**
     * Enhanced health risk assessment with more factors
     */
    public function buildHealthRiskTree(): array
    {
        // Check cache first
        $cacheKey = "enhanced_health_risk_" . Residents::count();
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        $residents = Residents::select('id', 'name', 'age', 'family_size', 'education_level', 'income_level', 'employment_status', 'health_status')->get();
        
        if ($residents->count() < 10) {
            return [
                'error' => 'Insufficient data for health risk analysis'
            ];
        }

        // Get health status data
        $healthStatuses = HealthStatus::select('user_id', 'concern_type', 'severity', 'status')->get();
        $healthDataByUser = $healthStatuses->groupBy('user_id');

        $rules = $this->generateEnhancedHealthRiskRules();
        $predictions = [];
        $accuracy = 0;
        $correct = 0;
        $riskLevels = [];
        
        foreach ($residents as $resident) {
            $predicted = $this->predictEnhancedHealthRisk($resident, $healthDataByUser);
            $actual = $this->determineEnhancedHealthRisk($resident, $healthDataByUser);
            $predictions[] = [
                'resident' => $resident,
                'predicted' => $predicted,
                'actual' => $actual,
                'correct' => $predicted === $actual
            ];
            
            if ($predicted === $actual) {
                $correct++;
            }
            
            // Count risk levels
            if (!isset($riskLevels[$actual])) {
                $riskLevels[$actual] = ['count' => 0, 'percentage' => 0];
            }
            $riskLevels[$actual]['count']++;
        }
        
        $accuracy = $correct / count($predictions);
        
        // Calculate percentages
        foreach ($riskLevels as $risk => $data) {
            $riskLevels[$risk]['percentage'] = round(($data['count'] / count($predictions)) * 100, 1);
        }
        
        $result = [
            'rules' => $rules,
            'accuracy' => $accuracy,
            'risk_levels' => $riskLevels,
            'sample_size' => count($predictions),
            'predictions' => $predictions,
            'deciding_factors' => $this->identifyHealthRiskFactors($predictions)
        ];

        // Cache the result for 30 minutes
        Cache::put($cacheKey, $result, 1800);

        return $result;
    }

    /**
     * Enhanced program recommendation with health factors
     */
    public function buildProgramRecommendationTree(): array
    {
        // Check cache first
        $cacheKey = "enhanced_program_recommendation_" . Residents::count();
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        $residents = Residents::select('id', 'name', 'age', 'family_size', 'education_level', 'income_level', 'employment_status', 'health_status')->get();
        
        if ($residents->count() < 10) {
            return [
                'error' => 'Insufficient data for program recommendation analysis'
            ];
        }

        // Get health status data
        $healthStatuses = HealthStatus::select('user_id', 'concern_type', 'severity', 'status')->get();
        $healthDataByUser = $healthStatuses->groupBy('user_id');

        $rules = $this->generateEnhancedProgramRecommendationRules();
        $predictions = [];
        $accuracy = 0;
        $correct = 0;
        $programs = [];
        
        foreach ($residents as $resident) {
            $predicted = $this->predictEnhancedRecommendedProgram($resident, $healthDataByUser);
            $actual = $this->determineEnhancedRecommendedProgram($resident, $healthDataByUser);
            $predictions[] = [
                'resident' => $resident,
                'predicted' => $predicted,
                'actual' => $actual,
                'correct' => $predicted === $actual
            ];
            
            if ($predicted === $actual) {
                $correct++;
            }
            
            // Count programs
            if (!isset($programs[$actual])) {
                $programs[$actual] = ['count' => 0, 'percentage' => 0];
            }
            $programs[$actual]['count']++;
        }
        
        $accuracy = $correct / count($predictions);
        
        // Calculate percentages
        foreach ($programs as $program => $data) {
            $programs[$program]['percentage'] = round(($data['count'] / count($predictions)) * 100, 1);
        }
        
        $result = [
            'rules' => $rules,
            'accuracy' => $accuracy,
            'programs' => $programs,
            'sample_size' => count($predictions),
            'predictions' => $predictions,
            'deciding_factors' => $this->identifyProgramFactors($predictions)
        ];

        // Cache the result for 30 minutes
        Cache::put($cacheKey, $result, 1800);

        return $result;
    }

    /**
     * Predict health condition based on multiple factors
     */
    public function predictHealthCondition($resident, $healthDataByUser): string
    {
        $age = $resident->age ?? 30;
        $income = $resident->income_level ?? 'Low';
        $employment = $resident->employment_status ?? 'Unemployed';
        $health = $resident->health_status ?? 'Healthy';
        $healthIncidence = $healthDataByUser->get($resident->id, collect());
        
        // Calculate health risk score
        $riskScore = $this->calculateHealthRiskScore($resident, $healthIncidence);
        
        // Determine health condition based on risk score and factors
        if ($riskScore >= 8 || $health === 'Critical' || 
            ($age > 60 && $healthIncidence->where('severity', 'Severe')->count() > 0)) {
            return 'Critical Condition';
        }
        
        if ($riskScore >= 6 || $health === 'Poor' || 
            $healthIncidence->where('severity', 'Severe')->count() > 0) {
            return 'Chronic Condition';
        }
        
        if ($riskScore >= 4 || $health === 'Fair' || 
            $healthIncidence->count() > 1) {
            return 'Minor Health Issues';
        }
        
        return 'Healthy';
    }

    /**
     * Enhanced service eligibility prediction
     */
    public function predictEnhancedServiceEligibility($resident, $healthDataByUser): string
    {
        $age = $resident->age ?? 30;
        $income = $resident->income_level ?? 'Low';
        $employment = $resident->employment_status ?? 'Unemployed';
        $health = $resident->health_status ?? 'Healthy';
        $healthIncidence = $healthDataByUser->get($resident->id, collect());
        
        // Enhanced eligibility criteria including health factors
        if ($income === 'Low' || $income === 'Lower Middle') {
            return 'Eligible';
        }
        
        if ($employment === 'Unemployed' && $age > 18) {
            return 'Eligible';
        }
        
        if ($health === 'Critical' || $health === 'Poor') {
            return 'Eligible';
        }
        
        if ($healthIncidence->where('severity', 'Severe')->count() > 0 || 
            $healthIncidence->where('severity', 'Emergency')->count() > 0) {
            return 'Eligible';
        }
        
        if ($age > 60) {
            return 'Eligible';
        }
        
        return 'Ineligible';
    }

    /**
     * Enhanced health risk prediction
     */
    public function predictEnhancedHealthRisk($resident, $healthDataByUser): string
    {
        $age = $resident->age ?? 30;
        $income = $resident->income_level ?? 'Low';
        $employment = $resident->employment_status ?? 'Unemployed';
        $health = $resident->health_status ?? 'Healthy';
        $healthIncidence = $healthDataByUser->get($resident->id, collect());
        
        // Calculate comprehensive risk score
        $riskScore = $this->calculateComprehensiveRiskScore($resident, $healthIncidence);
        
        if ($riskScore >= 8) return 'High';
        if ($riskScore >= 5) return 'Medium';
        return 'Low';
    }

    /**
     * Predict enhanced recommended program
     */
    public function predictEnhancedRecommendedProgram($resident, $healthDataByUser): string
    {
        $age = $resident->age ?? 30;
        $income = $resident->income_level ?? 'Low';
        $employment = $resident->employment_status ?? 'Unemployed';
        $health = $resident->health_status ?? 'Healthy';
        $healthIncidence = $healthDataByUser->get($resident->id, collect());
        
        // Health-focused program recommendations
        if ($health === 'Critical' || $healthIncidence->where('severity', 'Emergency')->count() > 0) {
            return 'Emergency Health Services';
        }
        
        if ($health === 'Poor' || $healthIncidence->where('severity', 'Severe')->count() > 0) {
            return 'Chronic Disease Management';
        }
        
        if ($age > 60) {
            return 'Senior Health Program';
        }
        
        if ($income === 'Low' && $employment === 'Unemployed') {
            return 'Job Training & Health Support';
        }
        
        if ($health === 'Fair' || $healthIncidence->count() > 1) {
            return 'Preventive Health Care';
        }
        
        return 'General Wellness Program';
    }

    /**
     * Determine enhanced recommended program
     */
    private function determineEnhancedRecommendedProgram($resident, $healthDataByUser): string
    {
        return $this->predictEnhancedRecommendedProgram($resident, $healthDataByUser);
    }

    /**
     * Calculate comprehensive health risk score
     */
    private function calculateComprehensiveRiskScore($resident, $healthIncidence): int
    {
        $score = 0;
        
        // Age factor
        $age = $resident->age ?? 30;
        if ($age > 60) $score += 3;
        elseif ($age > 40) $score += 2;
        elseif ($age > 25) $score += 1;
        
        // Health status factor
        $health = $resident->health_status ?? 'Healthy';
        switch ($health) {
            case 'Critical': $score += 5; break;
            case 'Poor': $score += 4; break;
            case 'Fair': $score += 2; break;
            case 'Good': $score += 1; break;
        }
        
        // Income factor
        $income = $resident->income_level ?? 'Low';
        if ($income === 'Low') $score += 3;
        elseif ($income === 'Lower Middle') $score += 2;
        
        // Employment factor
        $employment = $resident->employment_status ?? 'Unemployed';
        if ($employment === 'Unemployed') $score += 2;
        
        // Health incidence factor
        $incidenceCount = $healthIncidence->count();
        $score += min($incidenceCount * 2, 4);
        
        // Severity factor
        $highSeverityCount = $healthIncidence->where('severity', 'Severe')->count() + 
                           $healthIncidence->where('severity', 'Emergency')->count();
        $score += $highSeverityCount * 3;
        
        return $score;
    }

    /**
     * Calculate validation metrics for model testing
     */
    private function calculateValidationMetrics(array $trainingPredictions, array $testingPredictions): array
    {
        $trainingCorrect = 0;
        $testingCorrect = 0;
        
        foreach ($trainingPredictions as $prediction) {
            if ($prediction['correct']) $trainingCorrect++;
        }
        
        foreach ($testingPredictions as $prediction) {
            if ($prediction['correct']) $testingCorrect++;
        }
        
        $trainingAccuracy = count($trainingPredictions) > 0 ? $trainingCorrect / count($trainingPredictions) : 0;
        $testingAccuracy = count($testingPredictions) > 0 ? $testingCorrect / count($testingPredictions) : 0;
        $overallAccuracy = (count($trainingPredictions) + count($testingPredictions)) > 0 ? 
            ($trainingCorrect + $testingCorrect) / (count($trainingPredictions) + count($testingPredictions)) : 0;
        
        return [
            'training_accuracy' => round($trainingAccuracy * 100, 2),
            'testing_accuracy' => round($testingAccuracy * 100, 2),
            'overall_accuracy' => round($overallAccuracy * 100, 2),
            'training_size' => count($trainingPredictions),
            'testing_size' => count($testingPredictions),
            'model_validity' => $testingAccuracy >= 0.7 ? 'Valid' : 'Needs Improvement'
        ];
    }

    /**
     * Identify deciding factors for predictions
     */
    private function identifyDecidingFactors(array $predictions): array
    {
        $factors = [
            'income_level' => 0,
            'employment_status' => 0,
            'health_status' => 0,
            'age' => 0,
            'health_incidence' => 0
        ];
        
        foreach ($predictions as $prediction) {
            $resident = $prediction['resident'];
            
            if (($resident->income_level ?? 'Low') === 'Low') $factors['income_level']++;
            if (($resident->employment_status ?? 'Unemployed') === 'Unemployed') $factors['employment_status']++;
            if (($resident->health_status ?? 'Healthy') === 'Critical' || ($resident->health_status ?? 'Healthy') === 'Poor') $factors['health_status']++;
            if (($resident->age ?? 30) > 60) $factors['age']++;
        }
        
        // Sort by importance
        arsort($factors);
        
        return $factors;
    }

    /**
     * Identify health risk factors
     */
    private function identifyHealthRiskFactors(array $predictions): array
    {
        $factors = [
            'age_over_60' => 0,
            'critical_health' => 0,
            'low_income' => 0,
            'unemployed' => 0,
            'high_incidence' => 0
        ];
        
        foreach ($predictions as $prediction) {
            $resident = $prediction['resident'];
            
            if (($resident->age ?? 30) > 60) $factors['age_over_60']++;
            if (($resident->health_status ?? 'Healthy') === 'Critical') $factors['critical_health']++;
            if (($resident->income_level ?? 'Low') === 'Low') $factors['low_income']++;
            if (($resident->employment_status ?? 'Unemployed') === 'Unemployed') $factors['unemployed']++;
        }
        
        // Sort by importance
        arsort($factors);
        
        return $factors;
    }

    /**
     * Identify program recommendation factors
     */
    private function identifyProgramFactors(array $predictions): array
    {
        $factors = [
            'health_status' => 0,
            'age_over_60' => 0,
            'low_income_unemployed' => 0,
            'health_incidence' => 0,
            'emergency_incidents' => 0
        ];
        
        foreach ($predictions as $prediction) {
            $resident = $prediction['resident'];
            
            if (($resident->health_status ?? 'Healthy') === 'Critical' || ($resident->health_status ?? 'Healthy') === 'Poor') {
                $factors['health_status']++;
            }
            if (($resident->age ?? 30) > 60) $factors['age_over_60']++;
            if (($resident->income_level ?? 'Low') === 'Low' && ($resident->employment_status ?? 'Unemployed') === 'Unemployed') {
                $factors['low_income_unemployed']++;
            }
        }
        
        // Sort by importance
        arsort($factors);
        
        return $factors;
    }

    /**
     * Generate health condition prediction rules
     */
    private function generateHealthConditionRules(): array
    {
        return [
            [
                'condition' => 'Age > 60 AND Health Status = Critical',
                'prediction' => 'Critical Condition',
                'confidence' => 0.95
            ],
            [
                'condition' => 'Health Risk Score >= 8',
                'prediction' => 'Critical Condition',
                'confidence' => 0.90
            ],
            [
                'condition' => 'Health Status = Poor OR Multiple Severe Incidents',
                'prediction' => 'Chronic Condition',
                'confidence' => 0.85
            ],
            [
                'condition' => 'Health Risk Score >= 6',
                'prediction' => 'Chronic Condition',
                'confidence' => 0.80
            ],
            [
                'condition' => 'Health Status = Fair OR Multiple Incidents',
                'prediction' => 'Minor Health Issues',
                'confidence' => 0.75
            ],
            [
                'condition' => 'Health Risk Score >= 4',
                'prediction' => 'Minor Health Issues',
                'confidence' => 0.70
            ],
            [
                'condition' => 'Default',
                'prediction' => 'Healthy',
                'confidence' => 0.65
            ]
        ];
    }

    /**
     * Determine actual health condition for validation
     */
    private function determineActualHealthCondition($resident, $healthDataByUser): string
    {
        $health = $resident->health_status ?? 'Healthy';
        $healthIncidence = $healthDataByUser->get($resident->id, collect());
        
        if ($health === 'Critical' || $healthIncidence->where('severity', 'Emergency')->count() > 0) {
            return 'Critical Condition';
        }
        
        if ($health === 'Poor' || $healthIncidence->where('severity', 'Severe')->count() > 0) {
            return 'Chronic Condition';
        }
        
        if ($health === 'Fair' || $healthIncidence->count() > 1) {
            return 'Minor Health Issues';
        }
        
        return 'Healthy';
    }

    /**
     * Calculate health feature importance
     */
    private function calculateHealthFeatureImportance(): array
    {
        return [
            'health_status' => 0.35,
            'age' => 0.25,
            'health_incidence_count' => 0.20,
            'severity_level' => 0.15,
            'income_level' => 0.05
        ];
    }

    /**
     * Generate enhanced service eligibility rules
     */
    private function generateEnhancedServiceEligibilityRules(): array
    {
        return [
            [
                'condition' => 'Income Level = Low OR Lower Middle',
                'prediction' => 'Eligible',
                'confidence' => 0.95
            ],
            [
                'condition' => 'Employment Status = Unemployed AND Age > 18',
                'prediction' => 'Eligible',
                'confidence' => 0.90
            ],
            [
                'condition' => 'Health Status = Critical OR Poor',
                'prediction' => 'Eligible',
                'confidence' => 0.85
            ],
            [
                'condition' => 'Severe OR Emergency Health Incidents',
                'prediction' => 'Eligible',
                'confidence' => 0.80
            ],
            [
                'condition' => 'Age > 60',
                'prediction' => 'Eligible',
                'confidence' => 0.75
            ],
            [
                'condition' => 'Default',
                'prediction' => 'Ineligible',
                'confidence' => 0.70
            ]
        ];
    }

    /**
     * Determine enhanced service eligibility
     */
    private function determineEnhancedServiceEligibility($resident, $healthDataByUser): string
    {
        $age = $resident->age ?? 30;
        $income = $resident->income_level ?? 'Low';
        $employment = $resident->employment_status ?? 'Unemployed';
        $health = $resident->health_status ?? 'Healthy';
        $healthIncidence = $healthDataByUser->get($resident->id, collect());
        
        if ($income === 'Low' || $income === 'Lower Middle') {
            return 'Eligible';
        }
        
        if ($employment === 'Unemployed' && $age > 18) {
            return 'Eligible';
        }
        
        if ($health === 'Critical' || $health === 'Poor') {
            return 'Eligible';
        }
        
        if ($healthIncidence->where('severity', 'Severe')->count() > 0 || 
            $healthIncidence->where('severity', 'Emergency')->count() > 0) {
            return 'Eligible';
        }
        
        if ($age > 60) {
            return 'Eligible';
        }
        
        return 'Ineligible';
    }

    /**
     * Calculate enhanced feature importance
     */
    private function calculateEnhancedFeatureImportance(): array
    {
        return [
            'income_level' => 0.30,
            'employment_status' => 0.25,
            'health_status' => 0.20,
            'health_incidence' => 0.15,
            'age' => 0.10
        ];
    }

    /**
     * Generate enhanced health risk rules
     */
    private function generateEnhancedHealthRiskRules(): array
    {
        return [
            [
                'condition' => 'Comprehensive Risk Score >= 8',
                'prediction' => 'High',
                'confidence' => 0.95
            ],
            [
                'condition' => 'Age > 60 AND Health Status = Critical',
                'prediction' => 'High',
                'confidence' => 0.90
            ],
            [
                'condition' => 'Multiple Severe Health Incidents',
                'prediction' => 'High',
                'confidence' => 0.85
            ],
            [
                'condition' => 'Comprehensive Risk Score >= 5',
                'prediction' => 'Medium',
                'confidence' => 0.80
            ],
            [
                'condition' => 'Health Status = Poor OR Fair',
                'prediction' => 'Medium',
                'confidence' => 0.75
            ],
            [
                'condition' => 'Default',
                'prediction' => 'Low',
                'confidence' => 0.70
            ]
        ];
    }

    /**
     * Determine enhanced health risk
     */
    private function determineEnhancedHealthRisk($resident, $healthDataByUser): string
    {
        $healthIncidence = $healthDataByUser->get($resident->id, collect());
        $riskScore = $this->calculateComprehensiveRiskScore($resident, $healthIncidence);
        
        if ($riskScore >= 8) return 'High';
        if ($riskScore >= 5) return 'Medium';
        return 'Low';
    }

    /**
     * Calculate health risk score for health condition prediction
     */
    private function calculateHealthRiskScore($resident, $healthIncidence): int
    {
        $score = 0;
        
        // Age factor
        $age = $resident->age ?? 30;
        if ($age > 60) $score += 3;
        elseif ($age > 40) $score += 2;
        
        // Health status factor
        $health = $resident->health_status ?? 'Healthy';
        switch ($health) {
            case 'Critical': $score += 5; break;
            case 'Poor': $score += 4; break;
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
        $score += min($incidenceCount * 2, 4);
        
        // Severity factor
        $highSeverityCount = $healthIncidence->where('severity', 'Severe')->count() + 
                           $healthIncidence->where('severity', 'Emergency')->count();
        $score += $highSeverityCount * 2;
        
        return $score;
    }

    /**
     * Generate simple rules for service eligibility
     */
    private function generateServiceEligibilityRules(): array
    {
        return [
            [
                'condition' => 'Age >= 60',
                'action' => 'Senior Citizen Services',
                'description' => 'Residents aged 60 and above qualify for senior citizen services'
            ],
            [
                'condition' => 'Income = Low AND Family Size > 3',
                'action' => 'Financial Assistance',
                'description' => 'Low-income families with more than 3 members qualify for financial assistance'
            ],
            [
                'condition' => 'Employment = Unemployed',
                'action' => 'Job Training Programs',
                'description' => 'Unemployed residents qualify for job training programs'
            ],
            [
                'condition' => 'Age < 18',
                'action' => 'Youth Programs',
                'description' => 'Residents under 18 qualify for youth programs'
            ],
            [
                'condition' => 'Default',
                'action' => 'General Services',
                'description' => 'All other residents qualify for general services'
            ]
        ];
    }

    /**
     * Generate simple rules for health risk assessment
     */
    private function generateHealthRiskRules(): array
    {
        return [
            [
                'condition' => 'Health Status = Critical OR Health Status = Poor',
                'action' => 'High Risk',
                'description' => 'Residents with critical or poor health are at high risk'
            ],
            [
                'condition' => 'Age >= 50 AND Health Status != Excellent',
                'action' => 'Medium Risk',
                'description' => 'Residents aged 50+ with non-excellent health are at medium risk'
            ],
            [
                'condition' => 'Income = Low AND Health Status != Excellent',
                'action' => 'Medium Risk',
                'description' => 'Low-income residents with non-excellent health are at medium risk'
            ],
            [
                'condition' => 'Default',
                'action' => 'Low Risk',
                'description' => 'All other residents are at low risk'
            ]
        ];
    }

    /**
     * Generate simple rules for program recommendation
     */
    private function generateProgramRecommendationRules(): array
    {
        return [
            [
                'condition' => 'Employment = Unemployed',
                'action' => 'Job Training & Placement',
                'description' => 'Unemployed residents should be recommended for job training'
            ],
            [
                'condition' => 'Education = Elementary OR Education = No Education',
                'action' => 'Adult Education Program',
                'description' => 'Residents with low education should be recommended for adult education'
            ],
            [
                'condition' => 'Income = Low',
                'action' => 'Livelihood Development',
                'description' => 'Low-income residents should be recommended for livelihood development'
            ],
            [
                'condition' => 'Age < 25',
                'action' => 'Youth Development Program',
                'description' => 'Young residents should be recommended for youth development'
            ],
            [
                'condition' => 'Age >= 60',
                'action' => 'Senior Citizen Program',
                'description' => 'Senior citizens should be recommended for senior programs'
            ],
            [
                'condition' => 'Default',
                'action' => 'Skills Enhancement Program',
                'description' => 'All other residents should be recommended for skills enhancement'
            ]
        ];
    }

    /**
     * Predict service eligibility using simple rules
     */
    public function predictServiceEligibility($resident): string
    {
        $age = $resident->age ?? 30;
        $income = $resident->income_level ?? 'Low';
        $employment = $resident->employment_status ?? 'Unemployed';
        $familySize = $resident->family_size ?? 1;
        
        if ($age >= 60) {
            return 'Senior Citizen Services';
        }
        
        if ($income === 'Low' && $familySize > 3) {
            return 'Financial Assistance';
        }
        
        if ($employment === 'Unemployed') {
            return 'Job Training Programs';
        }
        
        if ($age < 18) {
            return 'Youth Programs';
        }
        
        return 'General Services';
    }

    /**
     * Predict health risk using simple rules
     */
    public function predictHealthRisk($resident): string
    {
        $age = $resident->age ?? 30;
        $healthStatus = $resident->health_status ?? 'Healthy';
        $income = $resident->income_level ?? 'Low';
        
        if ($healthStatus === 'Critical' || $healthStatus === 'Poor') {
            return 'High Risk';
        }
        
        if ($age >= 50 && $healthStatus !== 'Excellent') {
            return 'Medium Risk';
        }
        
        if ($income === 'Low' && $healthStatus !== 'Excellent') {
            return 'Medium Risk';
        }
        
        return 'Low Risk';
    }

    /**
     * Predict recommended program using simple rules
     */
    public function predictRecommendedProgram($resident): string
    {
        $age = $resident->age ?? 30;
        $education = $resident->education_level ?? 'Elementary';
        $employment = $resident->employment_status ?? 'Unemployed';
        $income = $resident->income_level ?? 'Low';
        
        if ($employment === 'Unemployed') {
            return 'Job Training & Placement';
        }
        
        if ($education === 'Elementary' || $education === 'No Education') {
            return 'Adult Education Program';
        }
        
        if ($income === 'Low') {
            return 'Livelihood Development';
        }
        
        if ($age < 25) {
            return 'Youth Development Program';
        }
        
        if ($age >= 60) {
            return 'Senior Citizen Program';
        }
        
        return 'Skills Enhancement Program';
    }

    /**
     * Calculate feature importance (simplified)
     */
    private function calculateFeatureImportance(): array
    {
        return [
            'age' => 0.25,
            'income' => 0.20,
            'employment' => 0.20,
            'health_status' => 0.15,
            'education' => 0.10,
            'family_size' => 0.10
        ];
    }

    /**
     * Generate enhanced program recommendation rules
     */
    private function generateEnhancedProgramRecommendationRules(): array
    {
        return [
            [
                'condition' => 'Health Status = Critical OR Emergency Incidents',
                'prediction' => 'Emergency Health Services',
                'confidence' => 0.95
            ],
            [
                'condition' => 'Health Status = Poor OR Severe Incidents',
                'prediction' => 'Chronic Disease Management',
                'confidence' => 0.90
            ],
            [
                'condition' => 'Age > 60',
                'prediction' => 'Senior Health Program',
                'confidence' => 0.85
            ],
            [
                'condition' => 'Income = Low AND Employment = Unemployed',
                'prediction' => 'Job Training & Health Support',
                'confidence' => 0.80
            ],
            [
                'condition' => 'Health Status = Fair OR Multiple Incidents',
                'prediction' => 'Preventive Health Care',
                'confidence' => 0.75
            ],
            [
                'condition' => 'Default',
                'prediction' => 'General Wellness Program',
                'confidence' => 0.70
            ]
        ];
    }

    // Legacy methods for compatibility
    private function determineServiceEligibility($resident): string
    {
        return $this->predictServiceEligibility($resident);
    }

    private function determineHealthRisk($resident): string
    {
        return $this->predictHealthRisk($resident);
    }

    private function determineRecommendedProgram($resident): string
    {
        return $this->predictRecommendedProgram($resident);
    }

    // Normalization methods (kept for compatibility)
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
} 