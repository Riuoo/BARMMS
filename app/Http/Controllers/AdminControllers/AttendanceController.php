<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\Residents;
use App\Models\AttendanceLog;
use App\Models\Event;
use App\Models\HealthCenterActivity;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceController
{
    /**
     * Show QR scanner interface
     */
    public function scanner(Request $request)
    {
        $eventId = $request->get('event_id');
        $eventType = $request->get('event_type', 'event');
        $event = null;
        $eventName = null;

        if ($eventId) {
            if ($eventType === 'event') {
                $event = Event::find($eventId);
                $eventName = $event ? $event->event_name : null;
            } elseif ($eventType === 'health_center_activity') {
                $event = HealthCenterActivity::find($eventId);
                $eventName = $event ? $event->activity_name : null;
            }
        }

        // Get relevant events for dropdown (ongoing, today, or upcoming within 7 days)
        $today = now()->startOfDay();
        $nextWeek = now()->addDays(7)->endOfDay();
        $lastWeek = now()->subDays(7)->startOfDay();

        $events = Event::where('qr_attendance_enabled', true)
            ->where('status', '!=', 'Cancelled')
            ->where(function($query) use ($today, $nextWeek, $lastWeek) {
                // Show ongoing events
                $query->where('status', 'Ongoing')
                    // Or planned/ongoing events today or in the next 7 days
                    ->orWhere(function($q) use ($today, $nextWeek) {
                        $q->whereIn('status', ['Planned', 'Ongoing'])
                          ->whereBetween('event_date', [$today, $nextWeek]);
                    })
                    // Or completed events from today (for same-day attendance)
                    ->orWhere(function($q) use ($today) {
                        $q->where('status', 'Completed')
                          ->whereDate('event_date', $today);
                    })
                    // Or recently completed events (last 7 days) for late attendance
                    ->orWhere(function($q) use ($lastWeek, $today) {
                        $q->where('status', 'Completed')
                          ->whereBetween('event_date', [$lastWeek, $today]);
                    });
            })
            ->orderByRaw("CASE 
                WHEN status = 'Ongoing' THEN 1 
                WHEN event_date = CURDATE() THEN 2 
                WHEN event_date > CURDATE() THEN 3 
                ELSE 4 
            END")
            ->orderBy('event_date', 'asc')
            ->get();

        $healthActivities = HealthCenterActivity::where('status', '!=', 'Cancelled')
            ->where(function($query) use ($today, $nextWeek, $lastWeek) {
                // Show ongoing activities
                $query->where('status', 'Ongoing')
                    // Or planned/ongoing activities today or in the next 7 days
                    ->orWhere(function($q) use ($today, $nextWeek) {
                        $q->whereIn('status', ['Planned', 'Ongoing'])
                          ->whereBetween('activity_date', [$today, $nextWeek]);
                    })
                    // Or completed activities from today
                    ->orWhere(function($q) use ($today) {
                        $q->where('status', 'Completed')
                          ->whereDate('activity_date', $today);
                    })
                    // Or recently completed activities (last 7 days)
                    ->orWhere(function($q) use ($lastWeek, $today) {
                        $q->where('status', 'Completed')
                          ->whereBetween('activity_date', [$lastWeek, $today]);
                    });
            })
            ->orderByRaw("CASE 
                WHEN status = 'Ongoing' THEN 1 
                WHEN activity_date = CURDATE() THEN 2 
                WHEN activity_date > CURDATE() THEN 3 
                ELSE 4 
            END")
            ->orderBy('activity_date', 'asc')
            ->get();

        return view('admin.attendance.scanner', compact('event', 'eventId', 'eventType', 'eventName', 'events', 'healthActivities'));
    }

    /**
     * Scan QR code and log attendance
     */
    public function scan(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'event_id' => 'nullable|integer',
            'event_type' => 'nullable|string|in:event,health_center_activity,medical_consultation,medicine_claim',
        ]);

        $token = $request->input('token');
        $eventId = $request->input('event_id');
        $eventType = $request->input('event_type', 'event');
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
                    'name' => $resident->name,
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

            // Update event attendance count if applicable
            if ($eventId && $eventType === 'event') {
                $event = Event::find($eventId);
                if ($event) {
                    // Count is calculated via relationship, but we can update actual_participants if needed
                }
            } elseif ($eventId && $eventType === 'health_center_activity') {
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
                    'name' => $resident->name,
                    'email' => $resident->email,
                ],
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
            'event_type' => 'nullable|string|in:event,health_center_activity,medical_consultation,medicine_claim',
            'notes' => 'nullable|string|max:1000',
        ]);

        $eventId = $request->input('event_id');
        $eventType = $request->input('event_type', 'event');
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

        // Try to find matching resident by name (case-insensitive, trimmed)
        $resident = Residents::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($enteredName))])
            ->where('active', true)
            ->first();

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
                        'name' => $resident->name,
                        'email' => $resident->email,
                    ],
                    'previous_scan' => $existingLog->scanned_at->format('Y-m-d H:i:s'),
                ], 409);
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
                        'name' => $resident->name,
                        'email' => $resident->email,
                    ],
                    'is_guest' => false,
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
        $eventType = $request->get('event_type', 'event');

        if (!$eventId) {
            return response()->json([
                'count' => 0,
                'attendees' => [],
            ]);
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
                    'name' => $log->resident ? $log->resident->name : $log->guest_name,
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
        $eventType = $request->get('event_type');

        $query = AttendanceLog::with(['resident', 'event', 'healthCenterActivity', 'scanner']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                // Search in residents
                $q->whereHas('resident', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
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

        if ($eventType) {
            $query->where('event_type', $eventType);
        }

        $logs = $query->orderBy('scanned_at', 'desc')->paginate(20);

        $events = Event::orderBy('event_date', 'desc')->get();
        $healthActivities = HealthCenterActivity::orderBy('activity_date', 'desc')->get();

        return view('admin.attendance.logs', compact('logs', 'events', 'healthActivities', 'search', 'eventId', 'eventType'));
    }

    /**
     * Generate attendance report
     */
    public function report(Request $request)
    {
        $eventId = $request->get('event_id');
        $eventType = $request->get('event_type', 'event');
        $format = $request->get('format', 'pdf'); // pdf or excel

        if (!$eventId) {
            notify()->error('Please select an event.');
            return back();
        }

        $event = null;
        if ($eventType === 'event') {
            $event = Event::find($eventId);
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
            $pdf = Pdf::loadView('admin.attendance.report-pdf', [
                'event' => $event,
                'logs' => $logs,
                'eventType' => $eventType,
            ]);

            $eventName = $eventType === 'event' ? $event->event_name : $event->activity_name;
            $filename = 'attendance-report-' . str_replace(' ', '-', strtolower($eventName)) . '-' . now()->format('Y-m-d') . '.pdf';

            return $pdf->download($filename);
        } else {
            // Excel export would go here
            notify()->info('Excel export coming soon.');
            return back();
        }
    }
}
