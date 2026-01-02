<?php

namespace App\Http\Controllers\AdminControllers\ProgramControllers;

use App\Models\Program;
use App\Models\Residents;
use App\Http\Requests\StoreProgramRequest;
use App\Http\Requests\UpdateProgramRequest;
use App\Services\PythonAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProgramController
{
    protected $pythonService;

    public function __construct(PythonAnalyticsService $pythonService)
    {
        $this->pythonService = $pythonService;
    }

    /**
     * Display a listing of programs
     */
    public function index(Request $request)
    {
        $searchTerm = trim($request->get('search', ''));
        $typeFilter = $request->get('type', '');
        $statusFilter = $request->get('status', '');

        $query = Program::query();

        if ($searchTerm !== '') {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        if ($typeFilter !== '') {
            $query->where('type', $typeFilter);
        }

        if ($statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        $programs = $query->orderBy('priority', 'desc')
            ->orderBy('name', 'asc')
            ->paginate(15)
            ->withQueryString();

        // Get eligible counts for each program using Python
        $residents = Residents::all();
        $residentsData = $this->pythonService->formatResidentsForPrograms($residents);
        
        $programsWithStats = $programs->map(function ($program) use ($residentsData) {
            try {
                $programData = $this->pythonService->formatProgramForEvaluation($program);
                $recommendations = $this->pythonService->getProgramRecommendationsByPurok($residentsData, $programData);
                $totalEligible = array_sum(array_column($recommendations, 'eligible_count'));
                $program->eligible_count = $totalEligible;
            } catch (\Exception $e) {
                Log::error('Error calculating eligible count for program ' . $program->id . ': ' . $e->getMessage());
                $program->eligible_count = 0;
            }
            return $program;
        });

        return view('admin.programs.index', [
            'programs' => $programs,
            'searchTerm' => $searchTerm,
            'typeFilter' => $typeFilter,
            'statusFilter' => $statusFilter,
        ]);
    }

    /**
     * Show the form for creating a new program
     */
    public function create()
    {
        // Get puroks using Python service
        $residents = Residents::all();
        $residentsData = $residents->map(function ($r) {
            return [
                'id' => $r->id,
                'address' => $r->address,
            ];
        })->toArray();
        
        try {
            $puroks = $this->pythonService->getAllPuroks($residentsData);
        } catch (\Exception $e) {
            Log::error('Error getting puroks: ' . $e->getMessage());
            $puroks = [];
        }
        
        return view('admin.programs.create', [
            'puroks' => $puroks,
        ]);
    }

    /**
     * Store a newly created program
     */
    public function store(StoreProgramRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Ensure criteria is properly formatted
            if (isset($data['criteria']) && is_array($data['criteria'])) {
                $data['criteria'] = $this->normalizeCriteria($data['criteria']);
            }

            // Handle target_puroks - convert to null if empty array
            if (isset($data['target_puroks']) && empty($data['target_puroks'])) {
                $data['target_puroks'] = null;
            }

            // Set defaults
            $data['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : true;
            $data['priority'] = $request->input('priority', 0);

            $program = Program::create($data);

            notify()->success('Program created successfully.');
            return redirect()->route('admin.programs.manage.index')->with('success', 'Program created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating program: ' . $e->getMessage());
            notify()->error('Failed to create program. Please try again.');
            return back()->withInput()->withErrors(['error' => 'Failed to create program. Please check your criteria structure.']);
        }
    }

    /**
     * Display the specified program
     */
    public function show($id)
    {
        $program = Program::findOrFail($id);
        
        // Format residents data for Python
        $residents = Residents::all();
        $residentsData = $this->pythonService->formatResidentsForPrograms($residents);
        $programData = $this->pythonService->formatProgramForEvaluation($program);
        
        try {
            $recommendations = $this->pythonService->getProgramRecommendationsByPurok($residentsData, $programData);
            $stats = $this->pythonService->getPurokEligibilityStats($residentsData, $programData);
            $targetPuroks = $this->pythonService->identifyTargetPuroks($stats);
        } catch (\Exception $e) {
            Log::error('Error getting program details: ' . $e->getMessage());
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
     * Show the form for editing the specified program
     */
    public function edit($id)
    {
        $program = Program::findOrFail($id);
        
        // Get puroks using Python service
        $residents = Residents::all();
        $residentsData = $residents->map(function ($r) {
            return [
                'id' => $r->id,
                'address' => $r->address,
            ];
        })->toArray();
        
        try {
            $puroks = $this->pythonService->getAllPuroks($residentsData);
        } catch (\Exception $e) {
            Log::error('Error getting puroks: ' . $e->getMessage());
            $puroks = [];
        }
        
        return view('admin.programs.edit', [
            'program' => $program,
            'puroks' => $puroks,
        ]);
    }

    /**
     * Update the specified program
     */
    public function update(UpdateProgramRequest $request, $id)
    {
        try {
            $program = Program::findOrFail($id);
            $data = $request->validated();
            
            // Ensure criteria is properly formatted
            if (isset($data['criteria']) && is_array($data['criteria'])) {
                $data['criteria'] = $this->normalizeCriteria($data['criteria']);
            }

            // Handle target_puroks - convert to null if empty array
            if (isset($data['target_puroks']) && empty($data['target_puroks'])) {
                $data['target_puroks'] = null;
            }

            // Set defaults
            $data['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : true;
            $data['priority'] = $request->input('priority', 0);

            $program->update($data);

            notify()->success('Program updated successfully.');
            return redirect()->route('admin.programs.manage.index')->with('success', 'Program updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating program: ' . $e->getMessage());
            notify()->error('Failed to update program. Please try again.');
            return back()->withInput()->withErrors(['error' => 'Failed to update program. Please check your criteria structure.']);
        }
    }

    /**
     * Remove the specified program
     */
    public function destroy($id)
    {
        try {
            $program = Program::findOrFail($id);
            $program->delete();

            notify()->success('Program deleted successfully.');
            return redirect()->route('admin.programs.manage.index')->with('success', 'Program deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting program: ' . $e->getMessage());
            notify()->error('Failed to delete program. Please try again.');
            return back()->withErrors(['error' => 'Failed to delete program.']);
        }
    }

    /**
     * Show delete confirmation page
     */
    public function deleteConfirm($id)
    {
        $program = Program::findOrFail($id);
        return view('admin.programs.delete-confirm', [
            'program' => $program,
        ]);
    }

    /**
     * Normalize criteria structure to ensure proper formatting
     */
    private function normalizeCriteria(array $criteria): array
    {
        // Ensure operator is uppercase
        if (isset($criteria['operator'])) {
            $criteria['operator'] = strtoupper($criteria['operator']);
        }

        // Recursively normalize nested conditions
        if (isset($criteria['conditions']) && is_array($criteria['conditions'])) {
            foreach ($criteria['conditions'] as $key => $condition) {
                if (is_array($condition)) {
                    $criteria['conditions'][$key] = $this->normalizeCriteria($condition);
                }
            }
        }

        return $criteria;
    }
}

