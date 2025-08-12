<?php

namespace App\Services;

use App\Models\AccomplishedProject;
use App\Models\HealthCenterActivity;

class FeaturedItemsService
{
    /**
     * Get the total count of featured items across both types
     */
    public function getTotalFeaturedCount(): int
    {
        $featuredProjects = AccomplishedProject::where('is_featured', true)->count();
        $featuredActivities = HealthCenterActivity::where('is_featured', true)->count();
        
        return $featuredProjects + $featuredActivities;
    }

    /**
     * Get detailed featured counts for both types
     */
    public function getFeaturedCounts(): array
    {
        return [
            'projects' => AccomplishedProject::where('is_featured', true)->count(),
            'activities' => HealthCenterActivity::where('is_featured', true)->count(),
            'total' => $this->getTotalFeaturedCount(),
            'limit' => 6,
            'can_add_more' => $this->canAddMoreFeatured(),
            'remaining' => max(0, 6 - $this->getTotalFeaturedCount())
        ];
    }

    /**
     * Check if more items can be marked as featured
     */
    public function canAddMoreFeatured(): bool
    {
        return $this->getTotalFeaturedCount() < 6;
    }

    /**
     * Check if a specific item can be marked as featured
     */
    public function canMarkAsFeatured(string $type, int $itemId): bool
    {
        // If already featured, can always unfeature
        if ($type === 'project') {
            $item = AccomplishedProject::find($itemId);
        } else {
            $item = HealthCenterActivity::find($itemId);
        }

        if (!$item) {
            return false;
        }

        // If already featured, can unfeature
        if ($item->is_featured) {
            return true;
        }

        // If not featured, check if we can add more
        return $this->canAddMoreFeatured();
    }

    /**
     * Get warning message based on current featured count
     */
    public function getWarningMessage(): ?array
    {
        $counts = $this->getFeaturedCounts();
        
        if ($counts['total'] >= 6) {
            return [
                'type' => 'error',
                'title' => 'Featured Limit Reached',
                'message' => "You currently have {$counts['total']} featured items ({$counts['projects']} projects + {$counts['activities']} activities). The landing page displays only 6 items total. Consider unfeaturing older items to highlight new ones.",
                'icon' => 'exclamation-triangle',
                'color' => 'yellow'
            ];
        } elseif ($counts['total'] >= 4) {
            return [
                'type' => 'warning',
                'title' => 'Featured Items',
                'message' => "You have {$counts['total']}/6 featured items ({$counts['projects']} projects + {$counts['activities']} activities). {$counts['remaining']} more items can be featured for the landing page.",
                'icon' => 'info-circle',
                'color' => 'blue'
            ];
        }
        
        return null;
    }

    /**
     * Get suggestions for which items to unfeature
     */
    public function getUnfeatureSuggestions(): array
    {
        $suggestions = [];
        
        // Get oldest featured projects
        $oldProjects = AccomplishedProject::where('is_featured', true)
            ->orderBy('completion_date', 'asc')
            ->take(3)
            ->get(['id', 'title', 'completion_date']);
            
        // Get oldest featured activities
        $oldActivities = HealthCenterActivity::where('is_featured', true)
            ->orderBy('activity_date', 'asc')
            ->take(3)
            ->get(['id', 'activity_name', 'activity_date']);
            
        if ($oldProjects->isNotEmpty()) {
            $suggestions['projects'] = $oldProjects->map(function($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->title,
                    'date' => $project->completion_date,
                    'type' => 'project'
                ];
            });
        }
        
        if ($oldActivities->isNotEmpty()) {
            $suggestions['activities'] = $oldActivities->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'name' => $activity->activity_name,
                    'date' => $activity->activity_date,
                    'type' => 'activity'
                ];
            });
        }
        
        return $suggestions;
    }
}
