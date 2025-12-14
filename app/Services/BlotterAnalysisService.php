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
        $blotters = BlotterRequest::with(['respondent' => function($query) {
            $query->select('id', 'first_name', 'middle_name', 'last_name', 'suffix', 'email', 'active', 'address');
        }])->get();
        
        $purokCounts = $this->countByPurok($blotters);
        $respondentTypeCounts = $this->countByRespondentType($blotters);
        $purokTypeBreakdown = $this->getPurokTypeBreakdown($blotters);
        $totalReports = array_sum($purokCounts);
        
        return [
            'purokCounts' => $purokCounts,
            'respondentTypeCounts' => $respondentTypeCounts,
            'purokTypeBreakdown' => $purokTypeBreakdown,
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
            if ($blotter->respondent_id) {
                // Registered respondent - use their address
                $purok = $this->extractPurok($blotter->respondent->address ?? '');
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
            if ($blotter->respondent_id) {
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
        if (empty($address)) {
            return 'Unknown';
        }
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
     * Get breakdown of case types by purok
     */
    private function getPurokTypeBreakdown($blotters): array
    {
        $breakdown = [];
        
        foreach ($blotters as $blotter) {
            $purok = 'N/A';
            if ($blotter->respondent_id) {
                $purok = $this->extractPurok($blotter->respondent->address ?? '');
            } else {
                $purok = 'Unregistered';
            }
            
            $type = $blotter->type ?? 'Unknown';
            
            if (!isset($breakdown[$purok])) {
                $breakdown[$purok] = [];
            }
            
            if (!isset($breakdown[$purok][$type])) {
                $breakdown[$purok][$type] = 0;
            }
            
            $breakdown[$purok][$type]++;
        }
        
        return $breakdown;
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

