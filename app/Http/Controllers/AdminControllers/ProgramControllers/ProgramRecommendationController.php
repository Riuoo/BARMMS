<?php

namespace App\Http\Controllers\AdminControllers\ProgramControllers;

use App\Models\Program;
use App\Models\Residents;
use App\Services\ProgramEligibilityService;
use App\Services\ResidentDataAggregationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProgramRecommendationController
{
    protected $eligibilityService;
    protected $dataAggregationService;

    public function __construct(
        ProgramEligibilityService $eligibilityService,
        ResidentDataAggregationService $dataAggregationService
    ) {
        $this->eligibilityService = $eligibilityService;
        $this->dataAggregationService = $dataAggregationService;
    }

    /**
     * Dashboard view with program recommendations grouped by purok
     */
    public function index()
    {
        $programs = Program::active()->byPriority()->get();
        
        $programStats = [];
        foreach ($programs as $program) {
            $recommendations = $this->eligibilityService->getProgramRecommendationsByPurok($program);
            $totalEligible = array_sum(array_column($recommendations, 'eligible_count'));
            $targetPuroks = array_filter($recommendations, function ($rec) {
                return $rec['is_recommended'];
            });

            $programStats[] = [
                'program' => $program,
                'total_eligible' => $totalEligible,
                'target_puroks_count' => count($targetPuroks),
                'recommendations' => $recommendations,
            ];
        }

        return view('admin.programs.dashboard', [
            'programs' => $programs,
            'programStats' => $programStats,
        ]);
    }

    /**
     * Shows program recommendations grouped by purok
     */
    public function byProgram($programId)
    {
        $program = Program::findOrFail($programId);
        $recommendations = $this->eligibilityService->getProgramRecommendationsByPurok($program);
        $targetPuroks = $this->eligibilityService->identifyTargetPuroks($program);

        return view('admin.programs.show', [
            'program' => $program,
            'recommendations' => $recommendations,
            'targetPuroks' => $targetPuroks,
        ]);
    }

    /**
     * Program recommendations for a specific purok
     */
    public function byPurok($purok)
    {
        $recommendations = $this->eligibilityService->getPurokProgramRecommendations($purok);
        
        return view('admin.programs.purok', [
            'purok' => $purok === 'n/a' ? 'N/A' : 'Purok ' . strtoupper($purok),
            'purokToken' => $purok,
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * Programs recommended for a specific resident
     */
    public function byResident($residentId)
    {
        $resident = Residents::findOrFail($residentId);
        $programs = $this->eligibilityService->getResidentPrograms($resident);

        return view('admin.programs.resident', [
            'resident' => $resident,
            'programs' => $programs,
        ]);
    }

    /**
     * Export eligible residents list grouped by purok
     */
    public function export($programId)
    {
        $program = Program::findOrFail($programId);
        $recommendations = $this->eligibilityService->getProgramRecommendationsByPurok($program);

        // Generate CSV
        $filename = 'program_' . $program->id . '_eligible_residents_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($program, $recommendations) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['Program: ' . $program->name]);
            fputcsv($file, []); // Empty row
            fputcsv($file, ['Purok', 'Total Residents', 'Eligible Count', 'Eligibility %', 'Status']);
            
            foreach ($recommendations as $rec) {
                fputcsv($file, [
                    $rec['purok'],
                    $rec['total_residents'],
                    $rec['eligible_count'],
                    $rec['eligibility_percentage'] . '%',
                    $rec['is_recommended'] ? 'Recommended' : 'Not Recommended',
                ]);
                
                // Add eligible residents
                if (!empty($rec['eligible_residents'])) {
                    fputcsv($file, []); // Empty row
                    fputcsv($file, ['', 'Name', 'Age', 'Employment Status', 'Income Level']);
                    
                    foreach ($rec['eligible_residents'] as $resident) {
                        fputcsv($file, [
                            '',
                            $resident->full_name,
                            $resident->age,
                            $resident->employment_status,
                            $resident->income_level,
                        ]);
                    }
                    fputcsv($file, []); // Empty row
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API endpoint to get purok groupings for a program
     */
    public function purokGroups($programId)
    {
        $program = Program::findOrFail($programId);
        $recommendations = $this->eligibilityService->getProgramRecommendationsByPurok($program);
        
        return response()->json([
            'program' => $program,
            'recommendations' => $recommendations,
        ]);
    }
}
