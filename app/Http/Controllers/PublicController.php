<?php

namespace App\Http\Controllers;

use App\Models\AccomplishedProject;
use Illuminate\Http\Request;

class PublicController
{
    public function accomplishments()
    {
        $projects = AccomplishedProject::orderBy('completion_date', 'desc')->paginate(12);
        $totalProjects = AccomplishedProject::count();
        $totalBudget = AccomplishedProject::sum('budget');
        $featuredProjects = AccomplishedProject::where('is_featured', true)->get();
        
        return view('public.accomplishments', compact('projects', 'totalProjects', 'totalBudget', 'featuredProjects'));
    }
} 