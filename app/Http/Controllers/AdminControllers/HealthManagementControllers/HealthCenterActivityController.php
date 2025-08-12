<?php

namespace App\Http\Controllers\AdminControllers\HealthManagementControllers;

use App\Models\HealthCenterActivity;
use App\Services\FeaturedItemsService;
use Illuminate\Http\Request;

class HealthCenterActivityController
{
    protected $featuredService;

    public function __construct(FeaturedItemsService $featuredService)
    {
        $this->featuredService = $featuredService;
    }

    public function index(Request $request)
    {
        $searchTerm = trim($request->get('search', ''));
        $featuredFilter = $request->get('featured', '');

        $query = HealthCenterActivity::query();

        if ($searchTerm !== '') {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('activity_name', 'like', "%{$searchTerm}%")
                    ->orWhere('activity_type', 'like', "%{$searchTerm}%")
                    ->orWhere('organizer', 'like', "%{$searchTerm}%")
                    ->orWhere('location', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by featured status
        if ($featuredFilter === 'featured') {
            $query->where('is_featured', true);
        } elseif ($featuredFilter === 'non-featured') {
            $query->where('is_featured', false);
        }

        $activities = $query
            ->orderBy('activity_date', 'desc')
            ->paginate(15)
            ->withQueryString();

        $featuredCounts = $this->featuredService->getFeaturedCounts();
        $warningMessage = $this->featuredService->getWarningMessage();
        $unfeatureSuggestions = $this->featuredService->getUnfeatureSuggestions();

        return view('admin.health-center-activities.index', [
            'activities' => $activities,
            'search' => $searchTerm,
            'featuredCounts' => $featuredCounts,
            'warningMessage' => $warningMessage,
            'unfeatureSuggestions' => $unfeatureSuggestions,
        ]);
    }

    public function create()
    {
        return view('admin.health-center-activities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_name' => 'required|string|max:255',
            'activity_type' => 'required|string|in:Vaccination,Health Check-up,Health Education,Medical Consultation,Emergency Response,Preventive Care,Maternal Care,Child Care,Other',
            'activity_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'objectives' => 'nullable|string|max:2000',
            'target_participants' => 'nullable|integer|min:1',
            'organizer' => 'nullable|string|max:255',
            'materials_needed' => 'nullable|string|max:1000',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:Planned,Ongoing,Completed,Cancelled',
            'is_featured' => 'nullable|boolean',
        ]);

        try {
            // Check featured limit before creating
            if ($request->has('is_featured') && !$this->featuredService->canAddMoreFeatured()) {
                $counts = $this->featuredService->getFeaturedCounts();
                notify()->error("Cannot create featured activity. You already have {$counts['total']}/6 featured items ({$counts['projects']} projects + {$counts['activities']} activities). Please unfeature some items first.", 'Featured Limit Reached');
                return back()->withInput();
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('health-activities', 'public');
                $validated['image'] = $imagePath;
            }

            // Handle featured status
            $validated['is_featured'] = $request->has('is_featured');

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
            'activity_type' => 'required|string|in:Vaccination,Health Check-up,Health Education,Medical Consultation,Emergency Response,Preventive Care,Maternal Care,Child Care,Other',
            'activity_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'objectives' => 'nullable|string|max:2000',
            'target_participants' => 'nullable|integer|min:1',
            'actual_participants' => 'nullable|integer|min:0',
            'organizer' => 'nullable|string|max:255',
            'materials_needed' => 'nullable|string|max:1000',
            'budget' => 'nullable|numeric|min:0',
            'outcomes' => 'nullable|string|max:2000',
            'challenges' => 'nullable|string|max:2000',
            'recommendations' => 'nullable|string|max:2000',
            'status' => 'required|string|in:Planned,Ongoing,Completed,Cancelled',
            'is_featured' => 'nullable|boolean',
        ]);

        try {
            // Check featured limit before updating (only if trying to mark as featured)
            if ($request->has('is_featured') && !$activity->is_featured && !$this->featuredService->canAddMoreFeatured()) {
                $counts = $this->featuredService->getFeaturedCounts();
                notify()->error("Cannot mark activity as featured. You already have {$counts['total']}/6 featured items ({$counts['projects']} projects + {$counts['activities']} activities). Please unfeature some items first.", 'Featured Limit Reached');
                return back()->withInput();
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($activity->image) {
                    \Storage::disk('public')->delete($activity->image);
                }
                $imagePath = $request->file('image')->store('health-activities', 'public');
                $validated['image'] = $imagePath;
            }

            // Handle featured status
            $validated['is_featured'] = $request->has('is_featured');

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
            
            // Delete associated image if exists
            if ($activity->image) {
                \Storage::disk('public')->delete($activity->image);
            }
            
            $activity->delete();
            
            notify()->success('Health center activity deleted successfully.');
            return redirect()->route('admin.health-center-activities.index');
        } catch (\Exception $e) {
            notify()->error('Error deleting health center activity: ' . $e->getMessage());
            return back();
        }
    }

    public function upcoming()
    {
        $upcomingActivities = HealthCenterActivity::upcoming()
            ->orderBy('activity_date', 'asc')
            ->paginate(15)
            ->withQueryString();
        return view('admin.health-center-activities.upcoming', compact('upcomingActivities'));
    }

    public function toggleFeatured($id)
    {
        try {
            $activity = HealthCenterActivity::findOrFail($id);
            
            // If trying to mark as featured, check the limit
            if (!$activity->is_featured && !$this->featuredService->canAddMoreFeatured()) {
                $counts = $this->featuredService->getFeaturedCounts();
                notify()->error("Cannot mark activity as featured. You already have {$counts['total']}/6 featured items ({$counts['projects']} projects + {$counts['activities']} activities). Please unfeature some items first.", 'Featured Limit Reached');
                return redirect()->route('admin.health-center-activities.index');
            }
            
            $activity->update(['is_featured' => !$activity->is_featured]);
            
            $status = $activity->fresh()->is_featured ? 'featured' : 'unfeatured';
            notify()->success("Activity {$status} successfully!", 'Success');
        } catch (\Exception $e) {
            notify()->error('Failed to update featured status. Please try again.', 'Error');
        }

        return redirect()->route('admin.health-center-activities.index');
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