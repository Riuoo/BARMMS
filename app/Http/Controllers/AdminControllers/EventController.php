<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\Event;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EventController
{
    /**
     * List all events
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Event::query();

        if ($search) {
            $query->where('event_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        $events = $query->orderBy('event_date', 'desc')->paginate(20);

        return view('admin.events.index', compact('events', 'search', 'status'));
    }

    /**
     * Show create event form
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Store new event
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_type' => 'required|string|in:Seminar,Barangay Program,Meeting,Relief Distribution,Community Assembly,Training Workshop,Cultural Event,Sports Event,Other',
            'event_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'status' => 'required|string|in:Planned,Ongoing,Completed,Cancelled',
            'qr_attendance_enabled' => 'nullable|boolean',
        ]);

        $validated['created_by'] = Session::get('user_id');
        $validated['qr_attendance_enabled'] = $request->has('qr_attendance_enabled');

        Event::create($validated);

        notify()->success('Event created successfully.');
        return redirect()->route('admin.events.index');
    }

    /**
     * Show event details
     */
    public function show($id)
    {
        $event = Event::with(['attendanceLogs.resident', 'attendanceLogs.scanner'])->findOrFail($id);
        $attendanceCount = $event->attendanceLogs()->count();

        return view('admin.events.show', compact('event', 'attendanceCount'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $event = Event::findOrFail($id);
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update event
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_type' => 'required|string|in:Seminar,Barangay Program,Meeting,Relief Distribution,Community Assembly,Training Workshop,Cultural Event,Sports Event,Other',
            'event_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'status' => 'required|string|in:Planned,Ongoing,Completed,Cancelled',
            'qr_attendance_enabled' => 'nullable|boolean',
        ]);

        $validated['qr_attendance_enabled'] = $request->has('qr_attendance_enabled');

        $event->update($validated);

        notify()->success('Event updated successfully.');
        return redirect()->route('admin.events.show', $id);
    }

    /**
     * Delete event
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        notify()->success('Event deleted successfully.');
        return redirect()->route('admin.events.index');
    }
}
