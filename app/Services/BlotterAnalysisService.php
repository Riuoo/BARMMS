<?php

namespace App\Services;

use App\Models\BlotterRequest;

class BlotterAnalysisService
{
    protected $pythonService;

    public function __construct(PythonAnalyticsService $pythonService = null)
    {
        $this->pythonService = $pythonService ?? app(PythonAnalyticsService::class);
    }

    /**
     * Get blotter analysis by purok and respondent type
     * Pure Python implementation - requires Python service to be running
     */
    public function getAnalysis(): array
    {
        $blotters = BlotterRequest::with(['respondent' => function($query) {
            $query->select('id', 'first_name', 'middle_name', 'last_name', 'suffix', 'email', 'active', 'address');
        }])->get();
        
        // Convert Eloquent models to arrays for JSON serialization
        $blottersArray = $blotters->map(function($blotter) {
            return [
                'id' => $blotter->id,
                'respondent_id' => $blotter->respondent_id,
                'type' => $blotter->type,
                'status' => $blotter->status,
                'respondent' => $blotter->respondent ? [
                    'id' => $blotter->respondent->id,
                    'address' => $blotter->respondent->address,
                ] : null,
            ];
        })->toArray();
        
        return $this->pythonService->analyzeBlotters($blottersArray);
    }

    /**
     * Get unregistered respondent names for analysis
     * Note: Since respondents must be registered, this method returns empty array
     */
    public function getUnregisteredRespondents(): array
    {
        // All respondents must be registered residents now
        return [];
    }
}

