<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .summary {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Attendance Report</h1>
        <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <div class="info">
        <h2>Event / Activity Information</h2>
        <p><strong>Name:</strong> {{ isset($eventName) ? $eventName : ($eventType === 'event' ? $event->title : $event->activity_name) }}</p>
        <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $eventType)) }}</p>
        @if($eventType === 'event')
            <p><strong>Start Date:</strong> {{ $event->start_date ? $event->start_date->format('F d, Y') : 'N/A' }}</p>
            <p><strong>Completion Date:</strong> {{ $event->completion_date ? $event->completion_date->format('F d, Y') : 'N/A' }}</p>
            @if($event->location)
                <p><strong>Location:</strong> {{ $event->location }}</p>
            @endif
        @else
            <p><strong>Date:</strong> {{ $event->activity_date->format('F d, Y') }}</p>
            @if($event->location)
                <p><strong>Location:</strong> {{ $event->location }}</p>
            @endif
        @endif
    </div>

    <div class="summary">
        <h3>Summary</h3>
        <p><strong>Total Attendees:</strong> {{ $logs->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email/Contact</th>
                <th>Type</th>
                <th>Time In</th>
                <th>Scanned By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ $log->resident ? $log->resident->full_name : $log->guest_name }}
                        @if($log->guest_name)
                            (Guest)
                        @endif
                    </td>
                    <td>{{ $log->resident ? $log->resident->email : ($log->guest_contact ?? 'N/A') }}</td>
                    <td>{{ $log->guest_name ? 'Guest' : 'Resident' }}</td>
                    <td>{{ $log->scanned_at->format('M d, Y h:i A') }}</td>
                    <td>{{ $log->scanner->name ?? 'System' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

