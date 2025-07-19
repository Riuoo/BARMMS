<?php

namespace App\Http\Controllers\AdminControllers\ProjectControllers;

use App\Models\AccomplishedProject;
use App\Http\Requests\AccomplishedProjectRequest;
use App\Services\AccomplishedProjectService;
use Illuminate\Http\Request;

class AccomplishProjectController
{
    protected $projectService;

    public function __construct(AccomplishedProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function accomplishProject()
    {
        $projects = AccomplishedProject::orderBy('completion_date', 'desc')->get();
        $stats = $this->projectService->getProjectStats();
        $featuredProjects = AccomplishedProject::where('is_featured', true)->get();
        
        return view('admin.accomplished-projects.accomplished-projects', compact('projects', 'stats', 'featuredProjects'));
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
            $this->projectService->toggleFeatured($project);
            
            $status = $project->fresh()->is_featured ? 'featured' : 'unfeatured';
            notify()->success("Project {$status} successfully!", 'Success');
        } catch (\Exception $e) {
            notify()->error('Failed to update featured status. Please try again.', 'Error');
        }

        return redirect()->route('admin.accomplished-projects');
    }
}
