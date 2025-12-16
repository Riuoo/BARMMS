<?php

namespace App\Services;

use App\Models\HealthCenterActivity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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
        
        $activity = HealthCenterActivity::create($data);

        // Send notification emails to audience
        $this->notifyAudience($activity);

        return $activity;
    }

    /**
     * Update an existing health center activity
     */
    public function updateActivity(HealthCenterActivity $activity, array $data): bool
    {
        $data = $this->processActivityData($data, $activity);
        
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
    private function processActivityData(array $data, ?HealthCenterActivity $activity = null): array
    {
        // Handle is_featured field properly
        $data['is_featured'] = isset($data['is_featured']) && $data['is_featured'] ? true : false;

        // Normalize audience fields
        $data['audience_scope'] = $data['audience_scope'] ?? 'all';
        if ($data['audience_scope'] !== 'purok') {
            $data['audience_scope'] = 'all';
            $data['audience_purok'] = null;
        }

        // Derive status automatically based on date/time unless explicitly cancelled
        $data['status'] = $this->determineStatus($data, $activity);
        
        return $data;
    }

    /**
     * Determine the activity status based on date/time.
     */
    private function determineStatus(array $data, ?HealthCenterActivity $activity = null): string
    {
        // Preserve explicit cancellation requests or existing cancelled state
        if (isset($data['status']) && $data['status'] === 'Cancelled') {
            return 'Cancelled';
        }
        if ($activity && $activity->status === 'Cancelled' && !isset($data['status'])) {
            return 'Cancelled';
        }

        // Validate presence of date
        if (empty($data['activity_date'])) {
            return $activity->status ?? 'Planned';
        }

        $activityDate = Carbon::parse($data['activity_date']);
        $now = Carbon::now();

        $startDateTime = $this->buildDateTime($activityDate, $data['start_time'] ?? null);
        $endDateTime = $this->buildDateTime($activityDate, $data['end_time'] ?? null);

        if ($activityDate->isFuture()) {
            return 'Planned';
        }

        if ($activityDate->isToday()) {
            if ($startDateTime && $endDateTime) {
                if ($now->betweenIncluded($startDateTime, $endDateTime)) {
                    return 'Ongoing';
                }

                if ($now->lt($startDateTime)) {
                    return 'Planned';
                }

                return 'Completed';
            }

            // Without time bounds, treat same-day activities as ongoing
            return 'Ongoing';
        }

        // Date has passed
        return 'Completed';
    }

    /**
     * Build a Carbon instance for a date with optional time.
     */
    private function buildDateTime(Carbon $date, ?string $time): ?Carbon
    {
        if (!$time) {
            return null;
        }

        try {
            // Support both H:i and H:i:s inputs
            $parsed = strlen($time) === 5
                ? Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $time)
                : Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . ' ' . $time);

            return $parsed;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Notify target audience for a newly created activity.
     */
    private function notifyAudience(HealthCenterActivity $activity): void
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
                    ->queue(new \App\Mail\HealthActivityNotificationMail($activity));
            }
        } catch (\Throwable $e) {
            // Fail silently – notifications should not break activity creation
        }
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
