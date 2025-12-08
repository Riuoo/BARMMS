<?php

namespace App\Http\Controllers;

use App\Models\AccomplishedProject;
use App\Models\HealthCenterActivity;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PublicController
{
    public function accomplishments()
    {
        $projects = AccomplishedProject::orderBy('completion_date', 'desc')->paginate(12);
        $totalProjects = AccomplishedProject::count();
        $totalBudget = AccomplishedProject::sum('budget');

        // Fetch all projects and all health activities for the bulletin board
        $allProjects = AccomplishedProject::orderBy('completion_date', 'desc')->get();
        $allActivities = HealthCenterActivity::orderBy('activity_date', 'desc')->get();

        // Build unified bulletin items (projects + activities)
        $bulletinItems = collect();

        foreach ($allProjects as $p) {
            $bulletinItems->push((object) [
                'type' => $p->type ?? 'project',
                'title' => $p->title,
                'description' => $p->description,
                'date' => optional($p->completion_date),
                'image_url' => $p->image_url,
                'category' => $p->category,
                'is_featured' => (bool) $p->is_featured,
                'link' => route('public.accomplishments.project', $p->id),
            ]);
        }

        foreach ($allActivities as $a) {
            $bulletinItems->push((object) [
                'type' => 'activity',
                'title' => $a->activity_name,
                'description' => $a->description,
                'date' => optional($a->activity_date),
                'image_url' => $a->image ? asset('storage/' . $a->image) : null,
                'category' => $a->activity_type,
                'is_featured' => false,
                'link' => route('public.accomplishments.activity', $a->id),
            ]);
        }

        // Sort unified bulletin by date desc
        $bulletinItems = $bulletinItems->sortByDesc(function($i) {
            return $i->date ?? now();
        })->values();

        // Paginate combined bulletin items
        $perPage = 12;
        $currentPage = (int) request()->get('page', 1);
        $currentItems = $bulletinItems->forPage($currentPage, $perPage)->values();
        $bulletin = new LengthAwarePaginator(
            $currentItems,
            $bulletinItems->count(),
            $perPage,
            $currentPage,
            ['path' => url()->current(), 'query' => request()->query()]
        );
        
        return view('public.accomplishments', compact('projects', 'totalProjects', 'totalBudget', 'bulletin'));
    }

    public function showProject($id)
    {
        $project = AccomplishedProject::findOrFail($id);
        return view('public.project', compact('project'));
    }

    public function showActivity($id)
    {
        $activity = HealthCenterActivity::findOrFail($id);
        return view('public.activity', compact('activity'));
    }

    /**
     * Show the privacy policy page.
     */
    public function privacyPolicy()
    {
        return view('public.privacy-policy');
    }

    /**
     * Show the terms of service page.
     */
    public function termsOfService()
    {
        return view('public.terms-of-service');
    }
} 