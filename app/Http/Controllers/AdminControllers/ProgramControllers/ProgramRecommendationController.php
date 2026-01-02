<?php

namespace App\Http\Controllers\AdminControllers\ProgramControllers;

use App\Models\Program;
use App\Models\Residents;
use App\Models\BlotterRequest;
use App\Models\MedicalRecord;
use App\Services\PythonAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProgramRecommendationController
{
    protected $pythonService;

    public function __construct(PythonAnalyticsService $pythonService)
    {
        $this->pythonService = $pythonService;
    }
    
    /**
     * Ensure Python analytics service is available
     */
    private function ensurePythonAvailable(): void
    {
        if (!config('services.python_analytics.enabled', true)) {
            throw new \Exception('Python analytics service is disabled. Please enable it in .env');
        }
        
        if (!$this->pythonService->isAvailable()) {
            $baseUrl = config('services.python_analytics.url', 'http://localhost:5000');
            throw new \Exception('The Python analytics service (app.py) is not running. Please start the Python service at ' . $baseUrl . ' to use this feature.');
        }
    }

    /**
     * Dashboard view with program recommendations grouped by purok
     */
    public function index()
    {
        // Python service is required
        try {
            $this->ensurePythonAvailable();
        } catch (\Throwable $e) {
            // Return empty collection instead of array to match view expectations
            return view('admin.programs.dashboard', [
                'error' => $e->getMessage(),
                'programs' => collect([]), // Empty Collection, not array
                'programStats' => [],
            ]);
        }
        
        $programs = Program::active()->byPriority()->get();
        
        // Format residents data for Python
        $residents = Residents::all();
        $residentsData = $this->pythonService->formatResidentsForPrograms($residents);
        
        $programStats = [];
        foreach ($programs as $program) {
            try {
                $programData = $this->pythonService->formatProgramForEvaluation($program);
                $recommendations = $this->pythonService->getProgramRecommendationsByPurok($residentsData, $programData);
                
                // Convert eligible_residents arrays back to models for view compatibility
                foreach ($recommendations as &$rec) {
                    if (!empty($rec['eligible_residents'])) {
                        $eligibleModels = [];
                        foreach ($rec['eligible_residents'] as $residentData) {
                            $resident = Residents::find($residentData['id'] ?? null);
                            if ($resident) {
                                $eligibleModels[] = $resident;
                            }
                        }
                        $rec['eligible_residents'] = $eligibleModels;
                    }
                }
                
                $totalEligible = array_sum(array_column($recommendations, 'eligible_count'));
                $targetPuroks = array_filter($recommendations, function ($rec) {
                    return $rec['is_recommended'] ?? false;
                });

                $programStats[] = [
                    'program' => $program,
                    'total_eligible' => $totalEligible,
                    'target_puroks_count' => count($targetPuroks),
                    'recommendations' => $recommendations,
                ];
            } catch (\Exception $e) {
                Log::error('Error getting program recommendations for program ' . $program->id . ': ' . $e->getMessage());
                $programStats[] = [
                    'program' => $program,
                    'total_eligible' => 0,
                    'target_puroks_count' => 0,
                    'recommendations' => [],
                ];
            }
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
        
        // Format residents data for Python
        $residents = Residents::all();
        $residentsData = $this->pythonService->formatResidentsForPrograms($residents);
        $programData = $this->pythonService->formatProgramForEvaluation($program);
        
        try {
            $recommendations = $this->pythonService->getProgramRecommendationsByPurok($residentsData, $programData);
            $stats = $this->pythonService->getPurokEligibilityStats($residentsData, $programData);
            $targetPuroks = $this->pythonService->identifyTargetPuroks($stats);
            
            // Convert eligible_residents arrays back to models for view compatibility
            foreach ($recommendations as &$rec) {
                if (!empty($rec['eligible_residents'])) {
                    $eligibleModels = [];
                    foreach ($rec['eligible_residents'] as $residentData) {
                        $resident = Residents::find($residentData['id'] ?? null);
                        if ($resident) {
                            $eligibleModels[] = $resident;
                        }
                    }
                    $rec['eligible_residents'] = $eligibleModels;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error getting program recommendations: ' . $e->getMessage());
            $recommendations = [];
            $targetPuroks = [];
        }

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
        $programs = Program::active()->get();
        
        // Format residents data for Python
        $residents = Residents::all();
        $residentsData = $this->pythonService->formatResidentsForPrograms($residents);
        
        $recommendations = [];
        foreach ($programs as $program) {
            try {
                $programData = $this->pythonService->formatProgramForEvaluation($program);
                $stats = $this->pythonService->getPurokEligibilityStats($residentsData, $programData, $purok);
                
                if (!empty($stats)) {
                    $stat = $stats[0]; // Get stats for this specific purok
                    if (($stat['eligible_count'] ?? 0) > 0) {
                        $recommendations[] = [
                            'program' => $program,
                            'stats' => $stat,
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error getting purok recommendations: ' . $e->getMessage());
            }
        }
        
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
        
        // Get blotters and medical records
        $blotters = BlotterRequest::where('respondent_id', $residentId)->get();
        $medicalRecords = MedicalRecord::where('resident_id', $residentId)->get();
        
        // Format resident data
        $residentData = [
            'id' => $resident->id,
            'address' => $resident->address,
            'age' => $resident->age,
            'gender' => $resident->gender,
            'marital_status' => $resident->marital_status,
            'employment_status' => $resident->employment_status,
            'income_level' => $resident->income_level,
            'education_level' => $resident->education_level,
            'family_size' => $resident->family_size,
            'is_pwd' => $resident->is_pwd,
            'occupation' => $resident->occupation,
        ];
        
        // Aggregate profile
        $profile = $this->pythonService->aggregateResidentData(
            $residentData,
            $blotters->toArray(),
            $medicalRecords->toArray()
        );
        
        // Format programs data
        $programs = Program::active()->get();
        $programsData = $programs->map(function ($program) {
            return $this->pythonService->formatProgramForEvaluation($program);
        })->toArray();
        
        try {
            $residentDataForPython = [
                'resident' => $residentData,
                'profile' => $profile,
            ];
            $eligibleProgramsData = $this->pythonService->getResidentPrograms($residentDataForPython, $programsData);
            
            // Convert back to Program models for view
            $eligiblePrograms = [];
            foreach ($eligibleProgramsData as $programData) {
                $program = Program::find($programData['id']);
                if ($program) {
                    $eligiblePrograms[] = $program;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error getting resident programs: ' . $e->getMessage());
            $eligiblePrograms = [];
        }

        return view('admin.programs.resident', [
            'resident' => $resident,
            'programs' => $eligiblePrograms,
        ]);
    }

    /**
     * Export eligible residents list grouped by purok
     */
    public function export($programId)
    {
        $program = Program::findOrFail($programId);
        
        // Format residents data for Python
        $residents = Residents::all();
        $residentsData = $this->pythonService->formatResidentsForPrograms($residents);
        $programData = $this->pythonService->formatProgramForEvaluation($program);
        
        try {
            $recommendations = $this->pythonService->getProgramRecommendationsByPurok($residentsData, $programData);
        } catch (\Exception $e) {
            Log::error('Error exporting program: ' . $e->getMessage());
            $recommendations = [];
        }

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
                        // Handle both array and object formats
                        if (is_array($resident)) {
                            $fullName = trim(($resident['first_name'] ?? '') . ' ' . 
                                           ($resident['middle_name'] ?? '') . ' ' . 
                                           ($resident['last_name'] ?? '') . ' ' . 
                                           ($resident['suffix'] ?? ''));
                            $age = $resident['age'] ?? '';
                            $employment = $resident['employment_status'] ?? '';
                            $income = $resident['income_level'] ?? '';
                        } else {
                            $fullName = $resident->full_name;
                            $age = $resident->age;
                            $employment = $resident->employment_status;
                            $income = $resident->income_level;
                        }
                        
                        fputcsv($file, [
                            '',
                            $fullName,
                            $age,
                            $employment,
                            $income,
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
        
        // Format residents data for Python
        $residents = Residents::all();
        $residentsData = $this->pythonService->formatResidentsForPrograms($residents);
        $programData = $this->pythonService->formatProgramForEvaluation($program);
        
        try {
            $recommendations = $this->pythonService->getProgramRecommendationsByPurok($residentsData, $programData);
        } catch (\Exception $e) {
            Log::error('Error getting purok groups: ' . $e->getMessage());
            $recommendations = [];
        }
        
        return response()->json([
            'program' => $program,
            'recommendations' => $recommendations,
        ]);
    }
}
