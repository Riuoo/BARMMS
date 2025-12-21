<?php

namespace App\Services;

use App\Models\Residents;
use App\Models\BlotterRequest;
use App\Models\MedicalRecord;

class ResidentDataAggregationService
{
    /**
     * Extract purok from address string
     */
    private function extractPurok($address): string
    {
        if (empty($address)) {
            return 'n/a';
        }
        
        if (preg_match('/Purok\s*(\d+)/i', $address, $matches)) {
            return strtolower($matches[1]);
        }
        
        return 'n/a';
    }

    /**
     * Get combined resident profile with demographics, blotter, and medical data
     */
    public function getResidentProfile($residentId): array
    {
        $resident = Residents::find($residentId);
        
        if (!$resident) {
            return [];
        }

        $blotterMetrics = $this->getBlotterMetrics($residentId);
        $medicalMetrics = $this->getMedicalMetrics($residentId);

        return [
            'resident' => $resident,
            'demographics' => [
                'age' => $resident->age,
                'gender' => $resident->gender,
                'marital_status' => $resident->marital_status,
                'employment_status' => $resident->employment_status,
                'income_level' => $resident->income_level,
                'education_level' => $resident->education_level,
                'family_size' => $resident->family_size,
                'is_pwd' => $resident->is_pwd,
                'occupation' => $resident->occupation,
                'purok' => $this->extractPurok($resident->address),
            ],
            'blotter' => $blotterMetrics,
            'medical' => $medicalMetrics,
        ];
    }

    /**
     * Get blotter metrics for a resident
     */
    public function getBlotterMetrics($residentId): array
    {
        $blotters = BlotterRequest::where('respondent_id', $residentId)->get();
        
        $recentBlotters = $blotters->filter(function ($blotter) {
            return $blotter->created_at && $blotter->created_at->greaterThan(now()->subMonths(12));
        });

        return [
            'total_count' => $blotters->count(),
            'recent_count' => $recentBlotters->count(),
            'types' => $blotters->pluck('type')->toArray(),
            'has_recent_incidents' => $recentBlotters->count() > 0,
            'last_incident_date' => $blotters->max('created_at')?->format('Y-m-d'),
        ];
    }

    /**
     * Get medical metrics for a resident
     */
    public function getMedicalMetrics($residentId): array
    {
        $medicalRecords = MedicalRecord::where('resident_id', $residentId)->get();
        
        $recentRecords = $medicalRecords->filter(function ($record) {
            return $record->consultation_datetime && 
                   $record->consultation_datetime->greaterThan(now()->subMonths(6));
        });

        $diagnoses = $medicalRecords->pluck('diagnosis')
            ->filter()
            ->map(function ($diagnosis) {
                return strtolower($diagnosis);
            })
            ->toArray();

        $chronicConditions = $this->identifyChronicConditions($diagnoses);

        return [
            'total_visits' => $medicalRecords->count(),
            'recent_visits' => $recentRecords->count(),
            'has_recent_visits' => $recentRecords->count() > 0,
            'diagnoses' => $diagnoses,
            'chronic_conditions' => $chronicConditions,
            'has_chronic_conditions' => !empty($chronicConditions),
            'last_visit_date' => $medicalRecords->max('consultation_datetime')?->format('Y-m-d'),
        ];
    }

    /**
     * Identify chronic conditions from diagnoses
     */
    private function identifyChronicConditions(array $diagnoses): array
    {
        $chronicKeywords = [
            'diabetes', 'hypertension', 'high blood pressure', 'asthma',
            'copd', 'heart disease', 'kidney disease', 'arthritis',
            'osteoporosis', 'cancer', 'stroke', 'epilepsy',
        ];

        $chronicConditions = [];
        
        foreach ($diagnoses as $diagnosis) {
            foreach ($chronicKeywords as $keyword) {
                if (stripos($diagnosis, $keyword) !== false) {
                    $chronicConditions[] = $diagnosis;
                    break;
                }
            }
        }

        return array_unique($chronicConditions);
    }

    /**
     * Get all residents in a purok with aggregated data
     */
    public function getPurokResidents($purok): array
    {
        $residents = Residents::all();
        $purokResidents = [];

        foreach ($residents as $resident) {
            $residentPurok = $this->extractPurok($resident->address);
            
            if ($residentPurok === strtolower($purok)) {
                $purokResidents[] = $this->getResidentProfile($resident->id);
            }
        }

        return $purokResidents;
    }

    /**
     * Get all puroks with resident counts
     */
    public function getAllPuroks(): array
    {
        $residents = Residents::all();
        $puroks = [];

        foreach ($residents as $resident) {
            $purok = $this->extractPurok($resident->address);
            
            if (!isset($puroks[$purok])) {
                $puroks[$purok] = [
                    'name' => $purok === 'n/a' ? 'N/A' : 'Purok ' . strtoupper($purok),
                    'token' => $purok,
                    'resident_count' => 0,
                ];
            }
            
            $puroks[$purok]['resident_count']++;
        }

        return array_values($puroks);
    }
}

