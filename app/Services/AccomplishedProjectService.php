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
        
        return AccomplishedProject::create($data);
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
        // Handle is_featured field properly
        $data['is_featured'] = isset($data['is_featured']) ? true : false;
        
        return $data;
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
            'total_projects' => AccomplishedProject::count(),
            'total_budget' => AccomplishedProject::sum('budget'),
            'featured_projects' => AccomplishedProject::where('is_featured', true)->count(),
            'recent_projects' => AccomplishedProject::orderBy('completion_date', 'desc')->take(5)->count(),
        ];
    }
} 