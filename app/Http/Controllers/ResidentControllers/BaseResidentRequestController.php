<?php

namespace App\Http\Controllers\ResidentControllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

abstract class BaseResidentRequestController
{
    /**
     * Get the resident user ID from authentication or session
     */
    protected function getResidentId()
    {
        return Auth::guard('residents')->id() ?? session('user_id');
    }

    /**
     * Check if user is authenticated
     */
    protected function ensureAuthenticated()
    {
        $userId = $this->getResidentId();
        if (!$userId) {
            Log::warning('Resident user ID not found in session for request submission.');
            notify()->error('You must be logged in to submit a request.');
            return false;
        }
        return $userId;
    }

    /**
     * Handle file uploads for media
     */
    protected function handleMediaUploads(Request $request, string $storagePath): array
    {
        $mediaFiles = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store($storagePath, 'public');
                $mediaFiles[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];
            }
        }
        return $mediaFiles;
    }

    /**
     * Check for existing ongoing requests
     */
    protected function checkExistingRequests($model, $userId, array $statuses, string $type = 'request'): bool
    {
        $existing = $model::where('resident_id', $userId)
            ->whereIn('status', $statuses)
            ->first();
            
        if ($existing) {
            notify()->error("You already have an ongoing {$type}. Please wait until it is resolved before submitting another.");
            return true;
        }
        return false;
    }

    /**
     * Standard error handling for request creation
     */
    protected function handleRequestCreation($model, string $successMessage, string $redirectRoute)
    {
        try {
            $model->save();
            notify()->success($successMessage);
            return redirect()->route($redirectRoute);
        } catch (\Exception $e) {
            Log::error("Error creating request: " . $e->getMessage());
            notify()->error('Error creating request: ' . $e->getMessage());
            return back();
        }
    }
}
