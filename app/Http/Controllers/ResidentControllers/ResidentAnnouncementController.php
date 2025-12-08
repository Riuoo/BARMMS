<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\AccomplishedProject;
use App\Models\HealthCenterActivity;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ResidentAnnouncementController
{
    public function announcements(Request $request)
    {
        // Fetch all projects and health activities for the bulletin board
        $allProjects = AccomplishedProject::orderBy('completion_date', 'desc')->get();
        $allActivities = HealthCenterActivity::orderBy('activity_date', 'desc')->get();

        // Build unified bulletin items (projects + activities)
        $bulletinItems = collect();

        foreach ($allProjects as $p) {
            $bulletinItems->push((object) [
                'id' => $p->id,
                'type' => 'project',
                'title' => $p->title,
                'description' => $p->description,
                'date' => optional($p->completion_date),
                'image_url' => $p->image_url,
                'category' => $p->category,
                'is_featured' => (bool) $p->is_featured,
                'status' => 'completed',
                'created_at' => $p->created_at,
            ]);
        }

        foreach ($allActivities as $a) {
            $bulletinItems->push((object) [
                'id' => $a->id,
                'type' => 'activity',
                'title' => $a->activity_name,
                'description' => $a->description,
                'date' => optional($a->activity_date),
                'image_url' => $a->image ? asset('storage/' . $a->image) : null,
                'category' => $a->activity_type,
                'is_featured' => (bool) $a->is_featured,
                'status' => $a->status,
                'created_at' => $a->created_at,
                'location' => $a->location,
                'start_time' => $a->start_time,
                'end_time' => $a->end_time,
            ]);
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = strtolower($request->get('search'));
            $bulletinItems = $bulletinItems->filter(function($item) use ($search) {
                return strpos(strtolower($item->title), $search) !== false
                    || strpos(strtolower($item->category), $search) !== false;
            });
        }

        if ($request->filled('type')) {
            $type = $request->get('type');
            $bulletinItems = $bulletinItems->filter(function($item) use ($type) {
                return $item->type === $type;
            });
        }

        if ($request->filled('status')) {
            $status = $request->get('status');
            $bulletinItems = $bulletinItems->filter(function($item) use ($status) {
                return $item->status === $status;
            });
        }

        if ($request->filled('featured') && $request->get('featured') === 'true') {
            $bulletinItems = $bulletinItems->filter(function($item) {
                return $item->is_featured;
            });
        }

        // Sort unified bulletin by date desc
        $bulletinItems = $bulletinItems->sortByDesc(function($i) {
            return $i->date ?? $i->created_at;
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

        // Get statistics
        $totalProjects = $allProjects->count();
        $totalActivities = $allActivities->count();
        $featuredCount = $bulletinItems->where('is_featured', true)->count();
        $upcomingActivities = $allActivities->where('activity_date', '>=', now())->count();

        return view('resident.announcements', compact('bulletin', 'totalProjects', 'totalActivities', 'featuredCount', 'upcomingActivities'));
    }

    public function showProject($id)
    {
        $project = AccomplishedProject::findOrFail($id);
        return view('resident.project-detail', compact('project'));
    }

    public function showActivity($id)
    {
        $activity = HealthCenterActivity::findOrFail($id);
        return view('resident.activity-detail', compact('activity'));
    }
} 