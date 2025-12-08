<?php

namespace App\Services;

use App\Models\DocumentRequest;

class DocumentRequestAnalysisService
{
    /**
     * Get document request analysis by purok only
     */
    public function getAnalysis(): array
    {
        $requests = DocumentRequest::with('resident')->get();
        
        $purokCounts = $this->countByPurok($requests);
        $purokTypeBreakdown = $this->getPurokTypeBreakdown($requests);
        $totalRequests = array_sum($purokCounts);
        
        return [
            'purokCounts' => $purokCounts,
            'purokTypeBreakdown' => $purokTypeBreakdown,
            'totalRequests' => $totalRequests,
            'totalPuroks' => count($purokCounts),
            'analysis' => $this->generateInsights($purokCounts, $totalRequests)
        ];
    }

    /**
     * Count document requests by purok
     */
    private function countByPurok($requests): array
    {
        $counts = [];
        foreach ($requests as $request) {
            $purok = $this->extractPurok(optional($request->resident)->address ?? '');
            $counts[$purok] = ($counts[$purok] ?? 0) + 1;
        }
        arsort($counts);
        return $counts;
    }

    /**
     * Extract purok from address string
     */
    private function extractPurok($address): string
    {
        if (preg_match('/Purok\s*\d+/i', $address, $matches)) {
            return $matches[0];
        }
        return 'Unknown';
    }

    /**
     * Generate insights from purok data
     */
    private function generateInsights($purokCounts, $totalRequests): array
    {
        $top3Puroks = array_slice($purokCounts, 0, 3, true);
        $top3Total = array_sum($top3Puroks);
        $top3Percentage = $totalRequests > 0 ? round(($top3Total / $totalRequests) * 100, 1) : 0;
        
        $averagePerPurok = count($purokCounts) > 0 ? round($totalRequests / count($purokCounts), 1) : 0;
        $mostActivePurok = !empty($purokCounts) ? array_key_first($purokCounts) : 'N/A';
        
        return [
            'top3Puroks' => $top3Puroks,
            'top3Percentage' => $top3Percentage,
            'averagePerPurok' => $averagePerPurok,
            'mostActivePurok' => $mostActivePurok,
            'distribution' => $this->calculateDistribution($purokCounts, $totalRequests)
        ];
    }

    /**
     * Calculate distribution statistics
     */
    private function calculateDistribution($purokCounts, $totalRequests): array
    {
        $distribution = [];
        foreach ($purokCounts as $purok => $count) {
            $percentage = $totalRequests > 0 ? round(($count / $totalRequests) * 100, 1) : 0;
            $distribution[$purok] = [
                'count' => $count,
                'percentage' => $percentage
            ];
        }
        return $distribution;
    }

    /**
     * Get breakdown of document types by purok
     */
    private function getPurokTypeBreakdown($requests): array
    {
        $breakdown = [];
        
        foreach ($requests as $request) {
            $purok = $this->extractPurok(optional($request->resident)->address ?? '');
            $documentType = $request->document_type ?? 'Unknown';
            
            if (!isset($breakdown[$purok])) {
                $breakdown[$purok] = [];
            }
            
            if (!isset($breakdown[$purok][$documentType])) {
                $breakdown[$purok][$documentType] = 0;
            }
            
            $breakdown[$purok][$documentType]++;
        }
        
        return $breakdown;
    }
}

