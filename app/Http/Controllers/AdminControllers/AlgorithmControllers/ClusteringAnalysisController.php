<?php

namespace App\Http\Controllers\AdminControllers\AlgorithmControllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\BlotterAnalysisService;
use App\Services\DocumentRequestAnalysisService;

class ClusteringAnalysisController extends Controller
{
    protected $blotterService;
    protected $documentService;

    public function __construct(
        BlotterAnalysisService $blotterService,
        DocumentRequestAnalysisService $documentService
    ) {
        $this->blotterService = $blotterService;
        $this->documentService = $documentService;
    }



    // --- Unified Analysis Views ---
    
    public function blotterAnalysis(Request $request)
    {
        try {
            $analysis = $this->blotterService->getAnalysis();
            
            return view('admin.clustering.blotter-analysis', [
                'purokCounts' => $analysis['purokCounts'],
                'purokTypeBreakdown' => $analysis['purokTypeBreakdown'],
                'totalReports' => $analysis['totalReports'],
                'totalPuroks' => $analysis['totalPuroks'],
                'analysis' => $analysis['analysis']
            ]);
        } catch (\Exception $e) {
            return view('admin.clustering.blotter-analysis', [
                'error' => 'Error performing analysis: ' . $e->getMessage(),
                'purokCounts' => [],
                'purokTypeBreakdown' => [],
                'totalReports' => 0,
                'totalPuroks' => 0,
                'analysis' => []
            ]);
        }
    }
    
    public function documentAnalysis(Request $request)
    {
        try {
            $analysis = $this->documentService->getAnalysis();
            
            return view('admin.clustering.document-analysis', [
                'purokCounts' => $analysis['purokCounts'],
                'purokTypeBreakdown' => $analysis['purokTypeBreakdown'],
                'totalRequests' => $analysis['totalRequests'],
                'totalPuroks' => $analysis['totalPuroks'],
                'analysis' => $analysis['analysis']
            ]);
        } catch (\Exception $e) {
            return view('admin.clustering.document-analysis', [
                'error' => 'Error performing analysis: ' . $e->getMessage(),
                'purokCounts' => [],
                'purokTypeBreakdown' => [],
                'totalRequests' => 0,
                'totalPuroks' => 0,
                'analysis' => []
            ]);
        }
    }
    

}
