<?php

namespace App\Services;

use App\Models\AccomplishedProject;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AccomplishedProjectService
{
    /**
     * Create a new accomplished project
     */
    public function createProject(array $data): AccomplishedProject
    {
        $data = $this->processProjectData($data);
        
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $this->uploadImage($data['image']);
        }
        
        $project = AccomplishedProject::create($data);

        // If this is a barangay activity, notify its audience
        if ($project->type === 'activity') {
            $this->notifyAudience($project);
        }

        return $project;
    }

    /**
     * Update an existing accomplished project
     */
    public function updateProject(AccomplishedProject $project, array $data): bool
    {
        $data = $this->processProjectData($data);
        
        // Handle image upload
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Delete old image if exists
            $this->deleteImage($project->image);
            $data['image'] = $this->uploadImage($data['image']);
        }
        
        // Handle image removal
        if (isset($data['remove_image']) && $data['remove_image']) {
            $this->deleteImage($project->image);
            $data['image'] = null;
        }
        
        return $project->update($data);
    }

    /**
     * Delete an accomplished project
     */
    public function deleteProject(AccomplishedProject $project): bool
    {
        // Delete image file if exists
        $this->deleteImage($project->image);
        
        return $project->delete();
    }

    /**
     * Toggle featured status of a project
     */
    public function toggleFeatured(AccomplishedProject $project): bool
    {
        return $project->update(['is_featured' => !$project->is_featured]);
    }

    /**
     * Process project data before saving
     */
    private function processProjectData(array $data): array
    {
        // Normalize type and provide a safe default
        $data['type'] = isset($data['type']) && in_array($data['type'], ['project', 'activity'])
            ? $data['type']
            : 'project';

        // Handle is_featured field properly
        $data['is_featured'] = isset($data['is_featured']) ? true : false;

        // Set reminder_sent default to false if not provided
        if (!isset($data['reminder_sent'])) {
            $data['reminder_sent'] = false;
        }

        // Normalize audience for activities only
        if (($data['type'] ?? 'project') === 'activity') {
            $data['audience_scope'] = $data['audience_scope'] ?? 'all';
            if ($data['audience_scope'] !== 'purok') {
                $data['audience_scope'] = 'all';
                $data['audience_purok'] = null;
            }
        } else {
            $data['audience_scope'] = 'all';
            $data['audience_purok'] = null;
        }
        
        return $data;
    }

    /**
     * Notify target audience for a newly created barangay activity.
     */
    private function notifyAudience(AccomplishedProject $activity): void
    {
        try {
            $audienceService = app(\App\Services\ActivityAudienceService::class);
            $residents = $audienceService->getAudienceResidents(
                $activity->audience_scope ?? 'all',
                $activity->audience_purok
            );

            if ($residents->isEmpty()) {
                return;
            }

            foreach ($residents as $resident) {
                \Illuminate\Support\Facades\Mail::to($resident->email)
                    ->queue(new \App\Mail\BarangayActivityNotificationMail($activity));
            }
        } catch (\Throwable $e) {
            // Fail silently – notifications should not break activity creation
        }
    }

    /**
     * Upload project image
     */
    private function uploadImage(UploadedFile $image): string
    {
        $imageName = time() . '_' . $image->getClientOriginalName();
        
        // Ensure uploads directory exists
        $uploadPath = public_path('uploads/projects');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        $image->move($uploadPath, $imageName);
        
        return 'uploads/projects/' . $imageName;
    }

    /**
     * Delete project image
     */
    private function deleteImage(?string $imagePath): void
    {
        if ($imagePath && file_exists(public_path($imagePath))) {
            unlink(public_path($imagePath));
        }
    }

    /**
     * Get project statistics
     */
    public function getProjectStats(): array
    {
        return [
            'total_projects' => AccomplishedProject::where('type', 'project')->count(),
            'total_activities' => AccomplishedProject::where('type', 'activity')->count(),
            'total_budget' => AccomplishedProject::where('type', 'project')->sum('budget'),
            'featured_projects' => AccomplishedProject::where('type', 'project')->where('is_featured', true)->count(),
            'featured_activities' => AccomplishedProject::where('type', 'activity')->where('is_featured', true)->count(),
            'recent_projects' => AccomplishedProject::where('type', 'project')->orderBy('completion_date', 'desc')->take(5)->count(),
            'recent_activities' => AccomplishedProject::where('type', 'activity')->orderBy('completion_date', 'desc')->take(5)->count(),
        ];
    }
} 