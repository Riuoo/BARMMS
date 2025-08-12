<?php

namespace App\Http\Controllers\AdminControllers\ProjectControllers;

use App\Models\AccomplishedProject;
use App\Http\Requests\AccomplishedProjectRequest;
use App\Services\AccomplishedProjectService;
use App\Services\FeaturedItemsService;
use Illuminate\Http\Request;

class AccomplishProjectController
{
    protected $projectService;
    protected $featuredService;

    public function __construct(AccomplishedProjectService $projectService, FeaturedItemsService $featuredService)
    {
        $this->projectService = $projectService;
        $this->featuredService = $featuredService;
    }

    public function accomplishProject(Request $request)
    {
        $query = AccomplishedProject::query();

        // Search by title, description, or category
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        // Filter by featured
        if ($request->filled('featured')) {
            if ($request->get('featured') === 'featured') {
                $query->where('is_featured', true);
            } elseif ($request->get('featured') === 'non-featured') {
                $query->where('is_featured', false);
            }
        }

        $projects = $query->orderBy('completion_date', 'desc')->paginate(9);
        $stats = $this->projectService->getProjectStats();
        $featuredProjects = AccomplishedProject::where('is_featured', true)->get();
        $featuredCounts = $this->featuredService->getFeaturedCounts();
        $warningMessage = $this->featuredService->getWarningMessage();
        $unfeatureSuggestions = $this->featuredService->getUnfeatureSuggestions();
        
        return view('admin.accomplished-projects.accomplished-projects', compact('projects', 'stats', 'featuredProjects', 'featuredCounts', 'warningMessage', 'unfeatureSuggestions'));
    }

    public function create()
    {
        return view('admin.accomplished-projects.create_accomplished_project');
    }

    public function show($id)
    {
        $project = AccomplishedProject::findOrFail($id);
        return view('admin.accomplished-projects.show_accomplished_project', compact('project'));
    }

    public function edit($id)
    {
        $project = AccomplishedProject::findOrFail($id);
        return view('admin.accomplished-projects.edit_accomplished_project', compact('project'));
    }

    public function store(AccomplishedProjectRequest $request)
    {
        try {
            $this->projectService->createProject($request->all());
            notify()->success('Project created successfully!', 'Success');
            return redirect()->route('admin.accomplished-projects');
        } catch (\Exception $e) {
            notify()->error('Failed to create project: ' . $e->getMessage(), 'Error');
            return back()->withInput();
        }
    }

    public function update(AccomplishedProjectRequest $request, $id)
    {
        try {
            $project = AccomplishedProject::findOrFail($id);
            $this->projectService->updateProject($project, $request->all());
            notify()->success('Project updated successfully!', 'Success');
            return redirect()->route('admin.accomplished-projects');
        } catch (\Exception $e) {
            notify()->error('Failed to update project: ' . $e->getMessage(), 'Error');
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $project = AccomplishedProject::findOrFail($id);
            $this->projectService->deleteProject($project);
            notify()->success('Project deleted successfully!', 'Success');
        } catch (\Exception $e) {
            notify()->error('Failed to delete project. Please try again.', 'Error');
        }

        return redirect()->route('admin.accomplished-projects');
    }

    public function toggleFeatured($id)
    {
        try {
            $project = AccomplishedProject::findOrFail($id);
            
            // If trying to mark as featured, check the limit
            if (!$project->is_featured && !$this->featuredService->canAddMoreFeatured()) {
                $counts = $this->featuredService->getFeaturedCounts();
                notify()->error("Cannot mark project as featured. You already have {$counts['total']}/6 featured items ({$counts['projects']} projects + {$counts['activities']} activities). Please unfeature some items first.", 'Featured Limit Reached');
                return redirect()->route('admin.accomplished-projects');
            }
            
            $this->projectService->toggleFeatured($project);
            
            $status = $project->fresh()->is_featured ? 'featured' : 'unfeatured';
            notify()->success("Project {$status} successfully!", 'Success');
        } catch (\Exception $e) {
            notify()->error('Failed to update featured status. Please try again.', 'Error');
        }

        return redirect()->route('admin.accomplished-projects');
    }
}
