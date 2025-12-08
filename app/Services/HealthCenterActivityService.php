<?php

namespace App\Services;

use App\Models\HealthCenterActivity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class HealthCenterActivityService
{
    /**
     * Create a new health center activity
     */
    public function createActivity(array $data): HealthCenterActivity
    {
        $data = $this->processActivityData($data);
        
        // Handle image upload
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $this->uploadImage($data['image']);
        }
        
        return HealthCenterActivity::create($data);
    }

    /**
     * Update an existing health center activity
     */
    public function updateActivity(HealthCenterActivity $activity, array $data): bool
    {
        $data = $this->processActivityData($data);
        
        // Handle image upload
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Delete old image if exists
            $this->deleteImage($activity->image);
            $data['image'] = $this->uploadImage($data['image']);
        }
        
        // Handle image removal
        if (isset($data['remove_image']) && $data['remove_image']) {
            $this->deleteImage($activity->image);
            $data['image'] = null;
        }
        
        return $activity->update($data);
    }

    /**
     * Delete a health center activity
     */
    public function deleteActivity(HealthCenterActivity $activity): bool
    {
        // Delete image file if exists
        $this->deleteImage($activity->image);
        
        return $activity->delete();
    }

    /**
     * Toggle featured status of an activity
     */
    public function toggleFeatured(HealthCenterActivity $activity): bool
    {
        return $activity->update(['is_featured' => !$activity->is_featured]);
    }

    /**
     * Process activity data before saving
     */
    private function processActivityData(array $data): array
    {
        // Handle is_featured field properly
        $data['is_featured'] = isset($data['is_featured']) && $data['is_featured'] ? true : false;
        
        return $data;
    }

    /**
     * Upload activity image using Laravel Storage
     */
    private function uploadImage(UploadedFile $image): string
    {
        return $image->store('health-activities', 'public');
    }

    /**
     * Delete activity image from storage
     */
    private function deleteImage(?string $imagePath): void
    {
        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    /**
     * Get activity statistics
     */
    public function getActivityStats(): array
    {
        return [
            'total_activities' => HealthCenterActivity::count(),
            'total_budget' => HealthCenterActivity::sum('budget'),
            'featured_activities' => HealthCenterActivity::where('is_featured', true)->count(),
            'ongoing_activities' => HealthCenterActivity::where('status', 'Ongoing')->count(),
            'upcoming_activities' => HealthCenterActivity::upcoming()->count(),
            'completed_activities' => HealthCenterActivity::completed()->count(),
        ];
    }
}
