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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $data = $request->all();
            
            // Handle is_featured field properly
            $data['is_featured'] = $request->has('is_featured') ? true : false;
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                
                // Ensure uploads directory exists
                $uploadPath = public_path('uploads/projects');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $image->move($uploadPath, $imageName);
                $data['image'] = 'uploads/projects/' . $imageName;
            }

            AccomplishedProject::create($data);
            notify()->success('Project created successfully!', 'Success');
            return redirect()->route('admin.accomplished-projects');
        } catch (\Exception $e) {
            notify()->error('Failed to create project: ' . $e->getMessage(), 'Error');
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $data = $request->all();
            
            // Handle is_featured field properly
            $data['is_featured'] = $request->has('is_featured') ? true : false;
            
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($project->image && file_exists(public_path($project->image))) {
                    unlink(public_path($project->image));
                }
                
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                
                // Ensure uploads directory exists
                $uploadPath = public_path('uploads/projects');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $image->move($uploadPath, $imageName);
                $data['image'] = 'uploads/projects/' . $imageName;
            }
            
            // Handle image removal
            if ($request->has('remove_image') && $request->remove_image == '1') {
                if ($project->image && file_exists(public_path($project->image))) {
                    unlink(public_path($project->image));
                }
                $data['image'] = null;
            }

            $project->update($data);
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
            
            // Delete image file if exists
            if ($project->image && file_exists(public_path($project->image))) {
                unlink(public_path($project->image));
            }
            
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
