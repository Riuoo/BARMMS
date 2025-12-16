<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\Residents;
use App\Models\AttendanceLog;
use App\Models\AccomplishedProject;
use App\Models\HealthCenterActivity;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceController
{
    /**
     * Determine allowed event type based on user role.
     * Barangay-side roles (secretary/captain/councilor/others) use barangay activities/projects ("event"),
     * while health-side role (nurse) uses health center activities.
     */
    private function resolveEventTypeForRole(): string
    {
        $role = Session::get('user_role');
        return $role === 'nurse' ? 'health_center_activity' : 'event';
    }

    /**
     * Determine if strict audience enforcement is enabled.
     */
    private function isStrictAudience(): bool
    {
        return (bool) config('attendance.strict_audience', false);
    }

    /**
     * Show QR scanner interface
     */
    public function scanner(Request $request)
    {
        $eventId = $request->get('event_id');
        $eventType = $this->resolveEventTypeForRole();
        $event = null;
        $eventName = null;
        $strictAudience = $this->isStrictAudience();

        if ($eventId) {
            if ($eventType === 'event') {
                // Barangay activities/projects now use AccomplishedProject
                $event = AccomplishedProject::find($eventId);
                $eventName = $event ? $event->title : null;
            } elseif ($eventType === 'health_center_activity') {
                $event = HealthCenterActivity::find($eventId);
                $eventName = $event ? $event->activity_name : null;
            }
        }

        // Barangay activities/projects for dropdown – show ONGOING only (activities only)
        $events = $eventType === 'event'
            ? AccomplishedProject::where('type', 'activity')
                ->whereIn('status', ['Ongoing', 'ongoing'])
                ->orderBy('start_date', 'asc')
                ->orderBy('completion_date', 'asc')
                ->get()
            : collect();

        // Health activities for dropdown – show ONGOING only
        $healthActivities = $eventType === 'health_center_activity'
            ? HealthCenterActivity::where('status', 'Ongoing')
                ->orderBy('activity_date', 'asc')
                ->get()
            : collect();

        // Pre-format options for clean JavaScript (avoids Blade inside JS)
        $formattedEvents = $events->map(function ($e) {
            $label = $e->title;
            if ($e->completion_date) {
                $label .= ' - ' . $e->completion_date->format('M d, Y');
            }
            return [
                'id' => $e->id,
                'label' => $label,
                'audience_scope' => $e->audience_scope ?? 'all',
                'audience_purok' => $e->audience_purok ?? null,
            ];
        })->values();

        $formattedHealthActivities = $healthActivities->map(function ($act) {
            $statusSuffix = '';
            if ($act->status === 'Ongoing') {
                $statusSuffix = ' 🔴 ONGOING';
            } elseif ($act->activity_date && $act->activity_date->isToday()) {
                $statusSuffix = ' 🟢 TODAY';
            } elseif ($act->activity_date && $act->activity_date->isFuture()) {
                $statusSuffix = ' 🔵 UPCOMING';
            }

            $label = $act->activity_name;
            if ($act->activity_date) {
                $label .= ' - ' . $act->activity_date->format('M d, Y');
            }
            $label .= $statusSuffix;

            return [
                'id' => $act->id,
                'label' => $label,
                'is_ongoing' => $act->status === 'Ongoing',
                'audience_scope' => $act->audience_scope ?? 'all',
                'audience_purok' => $act->audience_purok ?? null,
            ];
        })->values();

        // JSON strings for embedding in JS without Blade helpers
        $formattedEventsJson = $formattedEvents->toJson();
        $formattedHealthActivitiesJson = $formattedHealthActivities->toJson();

        return view('admin.attendance.scanner', compact(
            'event',
            'eventId',
            'eventType',
            'eventName',
            'events',
            'healthActivities',
            'formattedEvents',
            'formattedHealthActivities',
            'formattedEventsJson',
            'formattedHealthActivitiesJson',
            'strictAudience'
        ));
    }

    /**
     * Scan QR code and log attendance
     */
    public function scan(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'event_id' => 'nullable|integer',
            'event_type' => 'nullable|string|in:event,health_center_activity',
        ]);

        $allowedEventType = $this->resolveEventTypeForRole();
        if ($request->filled('event_type') && $request->input('event_type') !== $allowedEventType) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied for this event type.',
            ], 403);
        }

        $token = $request->input('token');
        $eventId = $request->input('event_id');
        $eventType = $allowedEventType;
        $scannedBy = Session::get('user_id');
        $notes = $request->input('notes');

        // Find resident by token
        $resident = Residents::where('qr_code_token', $token)->first();

        if (!$resident) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code. Resident not found.',
            ], 404);
        }

        if (!$resident->active) {
            return response()->json([
                'success' => false,
                'message' => 'Resident account is inactive.',
            ], 403);
        }

        // Validate event belongs to allowed type
        $event = null;
        if ($eventId) {
            if ($eventType === 'event') {
                $event = AccomplishedProject::where('id', $eventId)
                    ->where('type', 'activity')
                    ->first();
            } else {
                $event = HealthCenterActivity::find($eventId);
            }

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found or not accessible.',
                ], 404);
            }
        }

        // Audience check
        $audienceWarning = null;
        $strictAudience = $this->isStrictAudience();
        if ($event && $resident && $event->audience_scope === 'purok' && !empty($event->audience_purok)) {
            $residentAddress = (string) $resident->address;
            $targetPurok = (string) $event->audience_purok;
            if ($residentAddress && stripos($residentAddress, $targetPurok) === false) {
                $audienceWarning = 'Note: This resident is not from Purok ' . $targetPurok . ', the target audience for this event.';
                if ($strictAudience) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access denied: this activity is limited to Purok ' . $targetPurok . '.',
                    ], 403);
                }
            }
        }

        // Check for duplicate scan (only for residents with accounts)
        $existingLog = AttendanceLog::where('resident_id', $resident->id)
            ->where('event_id', $eventId)
            ->where('event_type', $eventType)
            ->whereNotNull('resident_id')
            ->first();

        if ($existingLog) {
            return response()->json([
                'success' => false,
                'message' => 'Already attended. This resident has already been scanned for this event.',
                'resident' => [
                    'id' => $resident->id,
                    'name' => $resident->full_name,
                    'email' => $resident->email,
                ],
                'previous_scan' => $existingLog->scanned_at->format('Y-m-d H:i:s'),
            ], 409);
        }

        // Create attendance log
        try {
            $attendanceLog = AttendanceLog::create([
                'resident_id' => $resident->id,
                'event_id' => $eventId,
                'event_type' => $eventType,
                'scanned_by' => $scannedBy,
                'scanned_at' => now(),
                'notes' => $notes,
            ]);

            // Update event attendance count if applicable (health activities only)
            if ($eventId && $eventType === 'health_center_activity') {
                $activity = HealthCenterActivity::find($eventId);
                if ($activity) {
                    $count = AttendanceLog::where('event_id', $eventId)
                        ->where('event_type', 'health_center_activity')
                        ->count();
                    $activity->actual_participants = $count;
                    $activity->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Attendance logged successfully.',
                'resident' => [
                    'id' => $resident->id,
                    'name' => $resident->full_name,
                    'email' => $resident->email,
                ],
                'warning' => $audienceWarning,
                'attendance_count' => $this->getAttendanceCount($eventId, $eventType),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error logging attendance: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add manual/guest attendance (for elders or people without accounts)
     */
    public function addManualAttendance(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'event_id' => 'nullable|integer',
            'event_type' => 'nullable|string|in:event,health_center_activity',
            'notes' => 'nullable|string|max:1000',
        ]);

        $allowedEventType = $this->resolveEventTypeForRole();
        if ($request->filled('event_type') && $request->input('event_type') !== $allowedEventType) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied for this event type.',
            ], 403);
        }

        $eventId = $request->input('event_id');
        $eventType = $allowedEventType;
        $scannedBy = Session::get('user_id');
        $enteredName = trim($request->input('name'));
        $enteredContact = trim($request->input('contact', ''));
        $notes = $request->input('notes');

        if (!$eventId) {
            return response()->json([
                'success' => false,
                'message' => 'Please select an event first.',
            ], 400);
        }

        // Validate event belongs to allowed type
        if ($eventType === 'event') {
            $event = AccomplishedProject::where('id', $eventId)
                ->where('type', 'activity')
                ->first();
        } else {
            $event = HealthCenterActivity::find($eventId);
        }

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found or not accessible.',
            ], 404);
        }

        // Try to find matching resident by name (case-insensitive, trimmed)
        $resident = Residents::whereRaw(
            "LOWER(TRIM(CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, '')))) = ?",
            [strtolower(trim($enteredName))]
        )
            ->where('active', true)
            ->first();

        $strictAudience = $this->isStrictAudience();

        if ($resident) {
            // Found matching resident - log as resident attendance
            // Check for duplicate scan (same resident, same event)
            $existingLog = AttendanceLog::where('resident_id', $resident->id)
                ->where('event_id', $eventId)
                ->where('event_type', $eventType)
                ->whereNotNull('resident_id')
                ->first();

            if ($existingLog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already attended. This resident has already been scanned for this event.',
                    'resident' => [
                        'id' => $resident->id,
                        'name' => $resident->full_name,
                        'email' => $resident->email,
                    ],
                    'previous_scan' => $existingLog->scanned_at->format('Y-m-d H:i:s'),
                ], 409);
            }

            // Audience check for manual resident logging
            $audienceWarning = null;
            if ($event && $event->audience_scope === 'purok' && !empty($event->audience_purok)) {
                $residentAddress = (string) $resident->address;
                $targetPurok = (string) $event->audience_purok;
                if ($residentAddress && stripos($residentAddress, $targetPurok) === false) {
                    $audienceWarning = 'Note: This resident is not from Purok ' . $targetPurok . ', the target audience for this event.';
                    // In strict mode, we still allow manual logging but return the warning
                }
            }

            try {
                $attendanceLog = AttendanceLog::create([
                    'resident_id' => $resident->id, // Log as resident
                    'guest_name' => null, // Not a guest
                    'guest_contact' => null,
                    'event_id' => $eventId,
                    'event_type' => $eventType,
                    'scanned_by' => $scannedBy,
                    'scanned_at' => now(),
                    'notes' => $notes,
                ]);

                // Update event attendance count if applicable
                if ($eventId && $eventType === 'health_center_activity') {
                    $activity = HealthCenterActivity::find($eventId);
                    if ($activity) {
                        $count = AttendanceLog::where('event_id', $eventId)
                            ->where('event_type', 'health_center_activity')
                            ->count();
                        $activity->actual_participants = $count;
                        $activity->save();
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Resident attendance logged successfully.',
                    'resident' => [
                        'id' => $resident->id,
                        'name' => $resident->full_name,
                        'email' => $resident->email,
                    ],
                    'is_guest' => false,
                    'warning' => $audienceWarning,
                    'attendance_count' => $this->getAttendanceCount($eventId, $eventType),
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error logging attendance: ' . $e->getMessage(),
                ], 500);
            }
        } else {
            // No matching resident found - log as guest
            // Check for duplicate (same name, same event, same day)
            $existingLog = AttendanceLog::where('guest_name', $enteredName)
                ->where('event_id', $eventId)
                ->where('event_type', $eventType)
                ->whereDate('scanned_at', now()->toDateString())
                ->first();

            if ($existingLog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already attended. This person has already been recorded for this event today.',
                    'previous_scan' => $existingLog->scanned_at->format('Y-m-d H:i:s'),
                ], 409);
            }

            $audienceWarning = null;
            if ($event && $event->audience_scope === 'purok' && !empty($event->audience_purok)) {
                // Guest has no address; treat as out-of-audience informationally
                $audienceWarning = 'Note: Guest recorded outside the target Purok ' . $event->audience_purok . '.';
            }

            try {
                $attendanceLog = AttendanceLog::create([
                    'resident_id' => null, // No resident account
                    'guest_name' => $enteredName,
                    'guest_contact' => $enteredContact ?: null,
                    'event_id' => $eventId,
                    'event_type' => $eventType,
                    'scanned_by' => $scannedBy,
                    'scanned_at' => now(),
                    'notes' => $notes,
                ]);

                // Update event attendance count if applicable
                if ($eventId && $eventType === 'health_center_activity') {
                    $activity = HealthCenterActivity::find($eventId);
                    if ($activity) {
                        $count = AttendanceLog::where('event_id', $eventId)
                            ->where('event_type', 'health_center_activity')
                            ->count();
                        $activity->actual_participants = $count;
                        $activity->save();
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Guest attendance logged successfully.',
                    'guest' => [
                        'name' => $enteredName,
                        'contact' => $enteredContact,
                    ],
                    'is_guest' => true,
                    'warning' => $audienceWarning,
                    'attendance_count' => $this->getAttendanceCount($eventId, $eventType),
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error logging attendance: ' . $e->getMessage(),
                ], 500);
            }
        }
    }

    /**
     * Get real-time attendance count
     */
    public function getAttendanceCount($eventId, $eventType)
    {
        if (!$eventId) {
            return 0;
        }

        return AttendanceLog::where('event_id', $eventId)
            ->where('event_type', $eventType)
            ->count();
    }

    /**
     * Get real-time attendance for an event
     */
    public function getAttendance(Request $request)
    {
        $eventId = $request->get('event_id');
        $eventType = $this->resolveEventTypeForRole();

        if (!$eventId) {
            return response()->json([
                'count' => 0,
                'attendees' => [],
            ]);
        }

        // Ensure event belongs to allowed type before returning data
        if ($eventType === 'event') {
            $event = AccomplishedProject::where('id', $eventId)
                ->where('type', 'activity')
                ->first();
        } else {
            $event = HealthCenterActivity::find($eventId);
        }

        if (!$event) {
            return response()->json([
                'count' => 0,
                'attendees' => [],
            ], 404);
        }

        $logs = AttendanceLog::with(['resident', 'event', 'healthCenterActivity'])
            ->where('event_id', $eventId)
            ->where('event_type', $eventType)
            ->orderBy('scanned_at', 'desc')
            ->get();

        return response()->json([
            'count' => $logs->count(),
            'attendees' => $logs->map(function ($log) {
                return [
                    'id' => $log->resident_id ?? null,
                    'name' => $log->resident ? $log->resident->full_name : $log->guest_name,
                    'email' => $log->resident ? $log->resident->email : ($log->guest_contact ?? 'N/A'),
                    'is_guest' => $log->guest_name !== null,
                    'scanned_at' => $log->scanned_at->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }

    /**
     * Show attendance logs
     */
    public function logs(Request $request)
    {
        $search = $request->get('search');
        $eventId = $request->get('event_id');
        $eventType = $this->resolveEventTypeForRole();

        $query = AttendanceLog::with(['resident', 'event', 'healthCenterActivity', 'scanner'])
            ->where('event_type', $eventType);

        if ($search) {
            $query->where(function ($q) use ($search) {
                // Search in residents
                $q->whereHas('resident', function ($subQ) use ($search) {
                    $subQ->whereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix, '')) LIKE ?", ["%{$search}%"])
                          ->orWhere('email', 'like', "%{$search}%");
                })
                // Or search in guest names/contacts
                ->orWhere('guest_name', 'like', "%{$search}%")
                ->orWhere('guest_contact', 'like', "%{$search}%");
            });
        }

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        $logs = $query->orderBy('scanned_at', 'desc')->paginate(20);

        // Filter selectable lists based on allowed event type
        $events = $eventType === 'event'
            ? AccomplishedProject::where('type', 'activity')
                ->orderBy('completion_date', 'desc')
                ->orderBy('start_date', 'desc')
                ->get()
            : collect();

        $healthActivities = $eventType === 'health_center_activity'
            ? HealthCenterActivity::orderBy('activity_date', 'desc')->get()
            : collect();

        return view('admin.attendance.logs', compact('logs', 'events', 'healthActivities', 'search', 'eventId', 'eventType'));
    }

    /**
     * Generate attendance report
     */
    public function report(Request $request)
    {
        $eventId = $request->get('event_id');
        $allowedEventType = $this->resolveEventTypeForRole();
        $eventType = $request->get('event_type', $allowedEventType);
        if ($eventType !== $allowedEventType) {
            notify()->error('Access denied for this event type.');
            return back();
        }
        $format = $request->get('format', 'pdf'); // pdf or excel

        if (!$eventId) {
            notify()->error('Please select an event.');
            return back();
        }

        $event = null;
        if ($eventType === 'event') {
            // Barangay activities/projects now use AccomplishedProject
            $event = AccomplishedProject::find($eventId);
        } elseif ($eventType === 'health_center_activity') {
            $event = HealthCenterActivity::find($eventId);
        }

        if (!$event) {
            notify()->error('Event not found.');
            return back();
        }

        $logs = AttendanceLog::with(['resident', 'event', 'healthCenterActivity', 'scanner'])
            ->where('event_id', $eventId)
            ->where('event_type', $eventType)
            ->orderBy('scanned_at', 'asc')
            ->get();
        
        // Ensure we load the relationships properly even if they are null
        $logs->loadMissing(['resident', 'event', 'healthCenterActivity']);

        if ($format === 'pdf') {
            $eventName = $eventType === 'event'
                ? ($event->title ?? 'Barangay Activity/Project')
                : ($event->activity_name ?? 'Health Activity');

            $pdf = Pdf::loadView('admin.attendance.report-pdf', [
                'event' => $event,
                'logs' => $logs,
                'eventType' => $eventType,
                'eventName' => $eventName,
            ]);

            $filename = 'attendance-report-' . str_replace(' ', '-', strtolower($eventName)) . '-' . now()->format('Y-m-d') . '.pdf';

            return $pdf->download($filename);
        } else {
            // Excel export would go here
            notify()->info('Excel export coming soon.');
            return back();
        }
    }
}
