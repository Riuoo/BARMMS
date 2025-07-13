<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\AccomplishedProject;
use Illuminate\Http\Request;

class AccomplishProjectController
{
    public function accomplishProject()
    {
        $projects = AccomplishedProject::orderBy('completion_date', 'desc')->get();
        $totalProjects = AccomplishedProject::count();
        $totalBudget = AccomplishedProject::sum('budget');
        $featuredProjects = AccomplishedProject::where('is_featured', true)->get();
        
        return view('admin.accomplished-projects.accomplished-projects', compact('projects', 'totalProjects', 'totalBudget', 'featuredProjects'));
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

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'location' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'completion_date' => 'required|date|after:start_date',
            'beneficiaries' => 'nullable|string',
            'impact' => 'nullable|string',
            'funding_source' => 'nullable|string',
            'implementing_agency' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);

        try {
            AccomplishedProject::create($request->all());
            notify()->success('Project created successfully!', 'Success');
            return redirect()->route('admin.accomplished-projects');
        } catch (\Exception $e) {
            notify()->error('Failed to create project. Please try again.', 'Error');
            return back()->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $project = AccomplishedProject::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'location' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'completion_date' => 'required|date|after:start_date',
            'beneficiaries' => 'nullable|string',
            'impact' => 'nullable|string',
            'funding_source' => 'nullable|string',
            'implementing_agency' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);

        try {
            $project->update($request->all());
            notify()->success('Project updated successfully!', 'Success');
            return redirect()->route('admin.accomplished-projects');
        } catch (\Exception $e) {
            notify()->error('Failed to update project. Please try again.', 'Error');
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $project = AccomplishedProject::findOrFail($id);
            $project->delete();
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
            $project->update(['is_featured' => !$project->is_featured]);
            
            $status = $project->is_featured ? 'featured' : 'unfeatured';
            notify()->success("Project {$status} successfully!", 'Success');
        } catch (\Exception $e) {
            notify()->error('Failed to update featured status. Please try again.', 'Error');
        }

        return redirect()->route('admin.accomplished-projects');
    }
}
