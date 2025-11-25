<?php

namespace App\Services;

use App\Models\BlotterRequest;

class BlotterAnalysisService
{
    /**
     * Get blotter analysis by purok and respondent type
     */
    public function getAnalysis(): array
    {
        $blotters = BlotterRequest::with('resident')->get();
        
        $purokCounts = $this->countByPurok($blotters);
        $respondentTypeCounts = $this->countByRespondentType($blotters);
        $totalReports = array_sum($purokCounts);
        
        return [
            'purokCounts' => $purokCounts,
            'respondentTypeCounts' => $respondentTypeCounts,
            'totalReports' => $totalReports,
            'totalPuroks' => count($purokCounts),
            'analysis' => $this->generateInsights($purokCounts, $totalReports, $respondentTypeCounts)
        ];
    }

    /**
     * Count blotters by purok
     */
    private function countByPurok($blotters): array
    {
        $counts = [];
        foreach ($blotters as $blotter) {
            if ($blotter->resident_id) {
                // Registered respondent - use their address
                $purok = $this->extractPurok($blotter->resident->address ?? '');
            } else {
                // Unregistered respondent - categorize as "Unregistered"
                $purok = 'Unregistered';
            }
            $counts[$purok] = ($counts[$purok] ?? 0) + 1;
        }
        arsort($counts);
        return $counts;
    }

    /**
     * Count blotters by respondent type (registered vs unregistered)
     */
    private function countByRespondentType($blotters): array
    {
        $counts = [
            'registered' => 0,
            'unregistered' => 0
        ];
        
        foreach ($blotters as $blotter) {
            if ($blotter->resident_id) {
                $counts['registered']++;
            } else {
                $counts['unregistered']++;
            }
        }
        
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
    private function generateInsights($purokCounts, $totalReports, $respondentTypeCounts): array
    {
        $top3Puroks = array_slice($purokCounts, 0, 3, true);
        $top3Total = array_sum($top3Puroks);
        $top3Percentage = $totalReports > 0 ? round(($top3Total / $totalReports) * 100, 1) : 0;
        
        $averagePerPurok = count($purokCounts) > 0 ? round($totalReports / count($purokCounts), 1) : 0;
        $mostActivePurok = !empty($purokCounts) ? array_key_first($purokCounts) : 'N/A';
        
        // Calculate respondent type percentages
        $registeredPercentage = $totalReports > 0 ? round(($respondentTypeCounts['registered'] / $totalReports) * 100, 1) : 0;
        $unregisteredPercentage = $totalReports > 0 ? round(($respondentTypeCounts['unregistered'] / $totalReports) * 100, 1) : 0;
        
        return [
            'top3Puroks' => $top3Puroks,
            'top3Percentage' => $top3Percentage,
            'averagePerPurok' => $averagePerPurok,
            'mostActivePurok' => $mostActivePurok,
            'distribution' => $this->calculateDistribution($purokCounts, $totalReports),
            'respondentTypeAnalysis' => [
                'registered' => [
                    'count' => $respondentTypeCounts['registered'],
                    'percentage' => $registeredPercentage
                ],
                'unregistered' => [
                    'count' => $respondentTypeCounts['unregistered'],
                    'percentage' => $unregisteredPercentage
                ]
            ]
        ];
    }

    /**
     * Calculate distribution statistics
     */
    private function calculateDistribution($purokCounts, $totalReports): array
    {
        $distribution = [];
        foreach ($purokCounts as $purok => $count) {
            $percentage = $totalReports > 0 ? round(($count / $totalReports) * 100, 1) : 0;
            $distribution[$purok] = [
                'count' => $count,
                'percentage' => $percentage
            ];
        }
        return $distribution;
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

