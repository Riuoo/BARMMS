<?php

namespace App\Http\Controllers\AdminControllers\AlgorithmControllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\BlotterClusteringService;
use App\Services\DocumentRequestClusteringService;

class ClusteringAnalysisController extends Controller
{
    protected $blotterService;
    protected $documentService;

    public function __construct(
        BlotterClusteringService $blotterService,
        DocumentRequestClusteringService $documentService
    ) {
        $this->blotterService = $blotterService;
        $this->documentService = $documentService;
    }



    // --- Unified Analysis Views ---
    
    public function blotterAnalysis(Request $request)
    {
        $k = $request->input('k', 3);
        $useHierarchical = $request->has('hierarchical');
        $useOptimalK = $request->has('use_optimal_k');
        
        try {
            $purokCounts = $this->blotterService->countByPurok();
            $clusteringResult = $this->blotterService->clusterBlotters($k);
            $clusters = $clusteringResult['clusters'] ?? [];
            $totalReports = array_sum($purokCounts);
            
            return view('admin.clustering.blotter-analysis', compact(
                'purokCounts', 
                'clusters', 
                'totalReports', 
                'k', 
                'useHierarchical', 
                'useOptimalK'
            ));
        } catch (\Exception $e) {
            return view('admin.clustering.blotter-analysis', [
                'error' => 'Error performing analysis: ' . $e->getMessage(),
                'purokCounts' => [],
                'clusters' => [],
                'totalReports' => 0,
                'k' => $k,
                'useHierarchical' => $useHierarchical,
                'useOptimalK' => $useOptimalK
            ]);
        }
    }
    
    public function documentAnalysis(Request $request)
    {
        $k = $request->input('k', 3);
        $useHierarchical = $request->has('hierarchical');
        $useOptimalK = $request->has('use_optimal_k');
        
        try {
            $purokCounts = $this->documentService->countByPurok();
            $clusteringResult = $this->documentService->clusterRequests($k);
            $clusters = $clusteringResult['clusters'] ?? [];
            $totalRequests = array_sum($purokCounts);
            
            return view('admin.clustering.document-analysis', compact(
                'purokCounts', 
                'clusters', 
                'totalRequests', 
                'k', 
                'useHierarchical', 
                'useOptimalK'
            ));
        } catch (\Exception $e) {
            return view('admin.clustering.document-analysis', [
                'error' => 'Error performing analysis: ' . $e->getMessage(),
                'purokCounts' => [],
                'clusters' => [],
                'totalRequests' => 0,
                'k' => $k,
                'useHierarchical' => $useHierarchical,
                'useOptimalK' => $useOptimalK
            ]);
        }
    }
    

}
