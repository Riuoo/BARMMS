<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\HealthCenterActivity;
use Illuminate\Http\Request;

class HealthCenterActivityController
{
    public function index()
    {
        $activities = HealthCenterActivity::orderBy('activity_date', 'desc')->paginate(15);
        return view('admin.health-center-activities.index', compact('activities'));
    }

    public function create()
    {
        return view('admin.health-center-activities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_name' => 'required|string|max:255',
            'activity_type' => 'required|string|in:Vaccination Drive,Health Education,Medical Mission,Screening Program,Nutrition Program,Maternal Care,Child Care,Elderly Care,Dental Care,Mental Health,Other',
            'activity_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'objectives' => 'required|string|max:2000',
            'target_participants' => 'nullable|integer|min:1',
            'organizer' => 'required|string|max:255',
            'materials_needed' => 'nullable|string|max:1000',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:Planned,Ongoing,Completed,Cancelled',
        ]);

        try {
            HealthCenterActivity::create($validated);
            notify()->success('Health center activity created successfully.');
            return redirect()->route('admin.health-center-activities.index');
        } catch (\Exception $e) {
            notify()->error('Error creating health center activity: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function show($id)
    {
        $activity = HealthCenterActivity::findOrFail($id);
        return view('admin.health-center-activities.show', compact('activity'));
    }

    public function edit($id)
    {
        $activity = HealthCenterActivity::findOrFail($id);
        return view('admin.health-center-activities.edit', compact('activity'));
    }

    public function update(Request $request, $id)
    {
        $activity = HealthCenterActivity::findOrFail($id);
        
        $validated = $request->validate([
            'activity_name' => 'required|string|max:255',
            'activity_type' => 'required|string|in:Vaccination Drive,Health Education,Medical Mission,Screening Program,Nutrition Program,Maternal Care,Child Care,Elderly Care,Dental Care,Mental Health,Other',
            'activity_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'objectives' => 'required|string|max:2000',
            'target_participants' => 'nullable|integer|min:1',
            'actual_participants' => 'nullable|integer|min:0',
            'organizer' => 'required|string|max:255',
            'materials_needed' => 'nullable|string|max:1000',
            'budget' => 'nullable|numeric|min:0',
            'outcomes' => 'nullable|string|max:2000',
            'challenges' => 'nullable|string|max:2000',
            'recommendations' => 'nullable|string|max:2000',
            'status' => 'required|string|in:Planned,Ongoing,Completed,Cancelled',
        ]);

        try {
            $activity->update($validated);
            notify()->success('Health center activity updated successfully.');
            return redirect()->route('admin.health-center-activities.index');
        } catch (\Exception $e) {
            notify()->error('Error updating health center activity: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $activity = HealthCenterActivity::findOrFail($id);
            $activity->delete();
            
            notify()->success('Health center activity deleted successfully.');
            return redirect()->route('admin.health-center-activities.index');
        } catch (\Exception $e) {
            notify()->error('Error deleting health center activity: ' . $e->getMessage());
            return back();
        }
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $activities = HealthCenterActivity::where('activity_name', 'like', "%{$query}%")
            ->orWhere('activity_type', 'like', "%{$query}%")
            ->orWhere('location', 'like', "%{$query}%")
            ->orWhere('organizer', 'like', "%{$query}%")
            ->orderBy('activity_date', 'desc')
            ->paginate(15);

        return view('admin.health-center-activities.index', compact('activities', 'query'));
    }

    public function upcoming()
    {
        $upcomingActivities = HealthCenterActivity::upcoming()->orderBy('activity_date', 'asc')->get();
        return view('admin.health-center-activities.upcoming', compact('upcomingActivities'));
    }

    public function completed()
    {
        $completedActivities = HealthCenterActivity::completed()->orderBy('activity_date', 'desc')->paginate(15);
        return view('admin.health-center-activities.completed', compact('completedActivities'));
    }

    public function generateReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        $activityType = $request->get('activity_type');

        $query = HealthCenterActivity::whereBetween('activity_date', [$startDate, $endDate]);

        if ($activityType) {
            $query->where('activity_type', $activityType);
        }

        $activities = $query->orderBy('activity_date', 'desc')->get();

        $summary = [
            'total_activities' => $activities->count(),
            'by_type' => $activities->groupBy('activity_type')->map->count(),
            'by_status' => $activities->groupBy('status')->map->count(),
            'total_budget' => $activities->sum('budget'),
            'total_participants' => $activities->sum('actual_participants'),
            'by_month' => $activities->groupBy(function($activity) {
                return $activity->activity_date->format('Y-m');
            })->map->count(),
        ];

        return view('admin.health-center-activities.report', compact('activities', 'summary', 'startDate', 'endDate', 'activityType'));
    }
} 