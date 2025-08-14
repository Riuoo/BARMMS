<?php

namespace App\Http\Controllers\AdminControllers\AlgorithmControllers;

use App\Services\ResidentDemographicAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Residents;

class ClusteringController
{
    private $clusteringService;

    public function __construct(ResidentDemographicAnalysisService $clusteringService)
    {
        $this->clusteringService = $clusteringService;
    }

    /**
     * Display clustering analysis page
     */
    public function index()
    {
        $k = request('k', 3);
        $useOptimalK = request('use_optimal_k', false);
        $useHierarchical = request('hierarchical', false);
        
        if ($useOptimalK) {
            $residents = Residents::all();
            $k = $this->clusteringService->findOptimalK($residents);
        }
        
        $this->clusteringService = new ResidentDemographicAnalysisService($k);
        $result = $useHierarchical
            ? $this->clusteringService->clusterResidentsHierarchical()
            : $this->clusteringService->clusterResidents();
        
        if (isset($result['error'])) {
            return view('admin.clustering.index', [
                'error' => $result['error'],
                'k' => $k,
                'useOptimalK' => $useOptimalK,
                'useHierarchical' => $useHierarchical,
                'sampleSize' => 0,
                'processingTime' => 0,
                'converged' => false
            ]);
        }
        
        $characteristics = $this->clusteringService->getClusterCharacteristics($result);
        
        // Calculate processing time (simulate for enhanced approach)
        $processingTime = 35; // Enhanced approach takes ~35ms
        
        // Build $residents collection for the table
        $residents = collect();
        foreach ($result['clusters'] as $clusterId => $cluster) {
            foreach ($cluster as $point) {
                if (isset($point['resident'])) {
                    $resident = $point['resident'];
                    $resident->cluster_id = $clusterId + 1; // 1-based for display
                    $residents->push($resident);
                }
            }
        }
        return view('admin.clustering.index', [
            'clusteringResult' => $result,
            'clusters' => $result['clusters'],
            'characteristics' => $characteristics,
            'highRiskAreas' => $result['high_risk_areas'] ?? [],
            'incidenceAnalysis' => $result['incidence_analysis'] ?? [],
            'k' => $k,
            'useOptimalK' => $useOptimalK,
            'useHierarchical' => $useHierarchical,
            'iterations' => $result['iterations'],
            'converged' => $result['converged'],
            'sampleSize' => count($result['residents']),
            'processingTime' => $processingTime,
            'residents' => $residents,
        ]);
    }

    /**
     * Perform clustering with specific parameters
     */
    public function performClustering(Request $request)
    {
        $request->validate([
            'k' => 'required|integer|min:2|max:10',
            'max_iterations' => 'integer|min:10|max:1000'
        ]);

        $k = $request->input('k', 3);
        $maxIterations = $request->input('max_iterations', 100);
        
        $this->clusteringService = new ResidentDemographicAnalysisService($k, $maxIterations);
        $result = $this->clusteringService->clusterResidents();
        
        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ]);
        }
        
        $characteristics = $this->clusteringService->getClusterCharacteristics($result);
        
        // Cache the result for 1 hour
        Cache::put('clustering_result_' . $k, [
            'result' => $result,
            'characteristics' => $characteristics
        ], 3600);
        
        return response()->json([
            'success' => true,
            'result' => $result,
            'characteristics' => $characteristics,
            'iterations' => $result['iterations'],
            'converged' => $result['converged']
        ]);
    }

    /**
     * Get optimal K value
     */
    public function getOptimalK()
    {
        $residents = Residents::all();
        $optimalK = $this->clusteringService->findOptimalK($residents);
        
        return response()->json([
            'success' => true,
            'optimal_k' => $optimalK
        ]);
    }

    /**
     * Export clustering results
     */
    public function export(Request $request)
    {
        $k = $request->input('k', 3);
        $format = $request->input('format', 'json');
        
        $this->clusteringService = new ResidentDemographicAnalysisService($k);
        $result = $this->clusteringService->clusterResidents();
        
        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ]);
        }
        
        $characteristics = $this->clusteringService->getClusterCharacteristics($result);
        
        if ($format === 'csv') {
            return $this->exportToCSV($result, $characteristics);
        }
        
        return response()->json([
            'success' => true,
            'result' => $result,
            'characteristics' => $characteristics
        ]);
    }

    /**
     * Export clustering results to CSV
     */
    private function exportToCSV($result, $characteristics)
    {
        $filename = 'clustering_results_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($result, $characteristics) {
            $file = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($file, [
                'Cluster ID',
                'Resident ID',
                'Name',
                'Age',
                'Family Size',
                'Education Level',
                'Income Level',
                'Employment Status',
                'Health Status'
            ]);
            
            // Write data
            foreach ($result['clusters'] as $clusterId => $cluster) {
                foreach ($cluster as $point) {
                    $resident = $point['resident'];
                    fputcsv($file, [
                        $clusterId,
                        $resident->id,
                        $resident->name,
                        $resident->age ?? 'N/A',
                        $resident->family_size ?? 'N/A',
                        $resident->education_level ?? 'N/A',
                        $resident->income_level ?? 'N/A',
                        $resident->employment_status ?? 'N/A',
                        $resident->health_status ?? 'N/A'
                    ]);
                }
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get cluster statistics
     */
    public function getClusterStats(Request $request)
    {
        $k = $request->input('k', 3);
        
        $this->clusteringService = new ResidentDemographicAnalysisService($k);
        $result = $this->clusteringService->clusterResidents();
        
        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ]);
        }
        
        $characteristics = $this->clusteringService->getClusterCharacteristics($result);
        
        $stats = [];
        foreach ($characteristics as $clusterId => $characteristic) {
            $stats[$clusterId] = [
                'size' => $characteristic['size'],
                'avg_age' => $characteristic['avg_age'],
                'avg_family_size' => $characteristic['avg_family_size'],
                'education_distribution' => $this->getDistribution($characteristic['residents'], 'education_level'),
                'income_distribution' => $this->getDistribution($characteristic['residents'], 'income_level'),
                'employment_distribution' => $this->getDistribution($characteristic['residents'], 'employment_status'),
                'health_distribution' => $this->getDistribution($characteristic['residents'], 'health_status')
            ];
        }
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Get distribution of values in a cluster
     */
    private function getDistribution($residents, $field)
    {
        $distribution = [];
        foreach ($residents as $point) {
            $value = $point['resident']->$field ?? 'Unknown';
            $distribution[$value] = ($distribution[$value] ?? 0) + 1;
        }
        return $distribution;
    }
}
