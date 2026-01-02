<?php

namespace App\Services;

use App\Models\DocumentRequest;

class DocumentRequestAnalysisService
{
    protected $pythonService;

    public function __construct(PythonAnalyticsService $pythonService = null)
    {
        $this->pythonService = $pythonService ?? app(PythonAnalyticsService::class);
    }

    /**
     * Get document request analysis by purok only
     * Pure Python implementation - requires Python service to be running
     */
    public function getAnalysis(): array
    {
        $requests = DocumentRequest::with('resident')->get();
        
        // Convert Eloquent models to arrays for JSON serialization
        $requestsArray = $requests->map(function($request) {
            return [
                'id' => $request->id,
                'document_type' => $request->document_type,
                'status' => $request->status,
                'resident' => $request->resident ? [
                    'id' => $request->resident->id,
                    'address' => $request->resident->address,
                ] : null,
            ];
        })->toArray();
        
        return $this->pythonService->analyzeDocuments($requestsArray);
    }
}

