@extends('admin.main.layout')

@section('title', 'Medical Record Entry Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Medical Record Entry Details</h1>
    </div>

    <!-- Patient Information Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-16 w-16">
                    <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-user-md text-blue-600 text-2xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $medicalRecord->resident->name }}</h2>
                    <p class="text-gray-600">Consultation Date: {{ $medicalRecord->consultation_datetime->format('M d, Y') }}</p>
                    <p class="text-gray-600">{{ $medicalRecord->resident->email }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-stethoscope mr-2"></i>{{ $medicalRecord->consultation_type }}
                </span>
            </div>
        </div>
    </div>

    <!-- Detailed Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Consultation Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>Consultation Details
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Consultation Date:</span>
                    <span class="font-medium">{{ $medicalRecord->consultation_datetime->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Consultation Type:</span>
                    <span class="font-medium">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $medicalRecord->consultation_type }}
                        </span>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Healthcare Provider:</span>
                    <span class="font-medium">{{ optional($medicalRecord->attendingHealthWorker)->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Follow-up Date:</span>
                    <span class="font-medium">
                        @if($medicalRecord->follow_up_date)
                            @if($medicalRecord->follow_up_date->isPast())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    OVERDUE - {{ $medicalRecord->follow_up_date->format('M d, Y') }}
                                </span>
                            @elseif($medicalRecord->follow_up_date->diffInDays(now()) <= 7)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Due Soon - {{ $medicalRecord->follow_up_date->format('M d, Y') }}
                                </span>
                            @else
                                {{ $medicalRecord->follow_up_date->format('M d, Y') }}
                            @endif
                        @else
                            <span class="text-gray-400">Not scheduled</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Patient Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-user text-purple-500 mr-2"></i>Patient Information
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Full Name:</span>
                    <span class="font-medium">{{ $medicalRecord->resident->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Email Address:</span>
                    <span class="font-medium">{{ $medicalRecord->resident->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Address:</span>
                    <span class="font-medium">{{ $medicalRecord->resident->address }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chief Complaint -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>Chief Complaint
        </h3>
        <p class="text-gray-700">{{ $medicalRecord->chief_complaint }}</p>
    </div>

    <!-- Vital Signs -->
    @if($medicalRecord->blood_pressure || $medicalRecord->temperature || $medicalRecord->pulse_rate || $medicalRecord->respiratory_rate)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-heartbeat text-red-500 mr-2"></i>Vital Signs
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @if($medicalRecord->blood_pressure)
            <div>
                <h4 class="text-sm font-medium text-gray-600 mb-1">Blood Pressure</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $medicalRecord->blood_pressure }}</p>
            </div>
            @endif
            @if($medicalRecord->temperature)
            <div>
                <h4 class="text-sm font-medium text-gray-600 mb-1">Temperature</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $medicalRecord->temperature }}°C</p>
            </div>
            @endif
            @if($medicalRecord->pulse_rate)
            <div>
                <h4 class="text-sm font-medium text-gray-600 mb-1">Pulse Rate</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $medicalRecord->pulse_rate }} bpm</p>
            </div>
            @endif
            @if($medicalRecord->respiratory_rate)
            <div>
                <h4 class="text-sm font-medium text-gray-600 mb-1">Respiratory Rate</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $medicalRecord->respiratory_rate }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Medical Information -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-stethoscope text-green-500 mr-2"></i>Medical Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Diagnosis</h4>
                <p class="text-gray-700">
                    @if($medicalRecord->diagnosis)
                        {{ $medicalRecord->diagnosis }}
                    @else
                        <span class="text-gray-400">No diagnosis recorded</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Treatment Information -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-pills text-yellow-500 mr-2"></i>Treatment Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Prescribed Medications</h4>
                <p class="text-gray-700">
                    @if($medicalRecord->prescribed_medications)
                        {{ $medicalRecord->prescribed_medications }}
                    @else
                        <span class="text-gray-400">No medications prescribed</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Follow-up Information -->
    @if($medicalRecord->follow_up_notes)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-calendar-check text-blue-500 mr-2"></i>Follow-up Information
        </h3>
        <p class="text-gray-700">{{ $medicalRecord->follow_up_notes }}</p>
    </div>
    @endif

    <!-- Medicine Requests -->
    @if($medicalRecord->medicineRequests->count() > 0)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-pills text-green-500 mr-2"></i>Medicine Requests
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medicine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($medicalRecord->medicineRequests as $request)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $request->medicine->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $request->quantity_approved }}/{{ $request->quantity_requested }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $request->request_date->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $request->approvedByUser->name ?? 'Unknown User' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                @if($request->notes)
                                    {{ Str::limit($request->notes, 50) }}
                                @else
                                    <span class="text-gray-400">No notes</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($request->quantity_approved > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Dispensed
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-pills text-gray-400 mr-2"></i>Medicine Requests
            </h3>
        </div>
        <p class="text-gray-500 text-center py-4">No medicine requests associated with this medical record.</p>
    </div>
    @endif

    <!-- Additional Notes -->
    @if($medicalRecord->notes)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-sticky-note text-purple-500 mr-2"></i>Additional Notes
        </h3>
        <p class="text-gray-700">{{ $medicalRecord->notes }}</p>
    </div>
    @endif

    <!-- Record Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
            <div>
                <span class="font-medium">Created:</span> {{ $medicalRecord->created_at->format('M d, Y g:i A') }}
            </div>
            <div>
                <span class="font-medium">Last Updated:</span> {{ $medicalRecord->updated_at->format('M d, Y g:i A') }}
            </div>
            <div>
                <span class="font-medium">Record ID:</span> {{ $medicalRecord->id }}
            </div>
        </div>
    </div>
</div>
@endsection
