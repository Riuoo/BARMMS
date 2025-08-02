<?php

namespace App\Http\Controllers\ResidentControllers;

use Illuminate\Http\Request;

class ResidentAnnouncementController
{
    /**
     * Show the announcements page (Recommendation).
     *
     * @return \Illuminate\View\View
     */
    public function announcements(Request $request)
    {
        // For now, provide an empty collection since there's no Announcement model
        // In the future, you would fetch announcements from a database
        $announcements = collect();

        // Example: If you have an Announcement model, replace the above with:
        // $announcements = Announcement::query();
        // if ($request->filled('search')) {
        //     $search = trim($request->get('search'));
        //     $announcements->where('title', 'like', "%{$search}%")
        //         ->orWhere('body', 'like', "%{$search}%");
        // }
        // if ($request->filled('priority') && $request->priority === 'important') {
        //     $announcements->where('priority', 'high');
        // }
        // if ($request->filled('recent') && $request->recent === 'recent') {
        //     $announcements->where('created_at', '>=', now()->subDays(7));
        // }
        // $announcements = $announcements->orderBy('created_at', 'desc')->get();

        // If using a collection (for demonstration), filter manually:
        if ($request->filled('search')) {
            $search = strtolower($request->get('search'));
            $announcements = $announcements->filter(function($a) use ($search) {
                return (isset($a['title']) && strpos(strtolower($a['title']), $search) !== false)
                    || (isset($a['body']) && strpos(strtolower($a['body']), $search) !== false);
            });
        }
        if ($request->filled('priority') && $request->priority === 'important') {
            $announcements = $announcements->where('priority', 'high');
        }
        if ($request->filled('recent') && $request->recent === 'recent') {
            $announcements = $announcements->filter(function($a) {
                return isset($a['created_at']) && \Carbon\Carbon::parse($a['created_at'])->gte(now()->subDays(7));
            });
        }

        return view('resident.announcements', compact('announcements'));
    }
} 