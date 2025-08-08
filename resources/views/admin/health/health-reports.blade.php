@extends('admin.main.layout')

@section('title', 'Health Reports Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- BHW Notifications/Reminders Section (Improved UI/UX) -->
    @if(isset($pendingAppointments) || isset($overdueVaccinations) || isset($analyticsAlerts))
    <div class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Pending Appointments -->
            @if(isset($pendingAppointments) && count($pendingAppointments))
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded shadow">
                <div class="flex items-center mb-2">
                    <i class="fas fa-calendar-check text-yellow-500 text-xl mr-2"></i>
                    <span class="font-bold text-yellow-700">Pending Consultations</span>
                </div>
                <ul class="list-disc ml-6 text-yellow-800 text-sm">
                    @foreach($pendingAppointments as $appointment)
                        <li>
                            <span class="font-semibold">{{ $appointment->resident->name }}</span>
                            <span class="ml-1">({{ $appointment->consultation_datetime->format('M d, Y') }})</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
            <!-- Overdue Vaccinations -->
            @if(isset($overdueVaccinations) && count($overdueVaccinations))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded shadow">
                <div class="flex items-center mb-2">
                    <i class="fas fa-syringe text-red-500 text-xl mr-2"></i>
                    <span class="font-bold text-red-700">Overdue Vaccinations</span>
                </div>
                <ul class="list-disc ml-6 text-red-800 text-sm">
                    @foreach($overdueVaccinations as $vaccination)
                        <li>
                            <span class="font-semibold">{{ $vaccination->resident->name }}</span>
                            <span class="ml-1">({{ $vaccination->vaccine_name }})</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
            <!-- Analytics Alerts -->
            @if(isset($analyticsAlerts) && count($analyticsAlerts))
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded shadow">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle text-blue-500 text-xl mr-2"></i>
                    <span class="font-bold text-blue-700">Analytics Alerts</span>
                </div>
                <ul class="list-disc ml-6 text-blue-800 text-sm">
                    @foreach($analyticsAlerts as $alert)
                        <li>{{ $alert }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- BHW Performance Summary Section (Improved UI/UX) -->
    @if(isset($bhwStats))
    <div class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-100 rounded-lg p-4 flex items-center shadow">
                <i class="fas fa-stethoscope text-green-500 text-2xl mr-3"></i>
                <div>
                    <div class="text-lg font-bold text-green-700">{{ $bhwStats['consultations'] ?? 0 }}</div>
                    <div class="text-sm text-green-800">Consultations Logged</div>
                </div>
            </div>
            <div class="bg-yellow-100 rounded-lg p-4 flex items-center shadow">
                <i class="fas fa-syringe text-yellow-500 text-2xl mr-3"></i>
                <div>
                    <div class="text-lg font-bold text-yellow-700">{{ $bhwStats['vaccinations'] ?? 0 }}</div>
                    <div class="text-sm text-yellow-800">Vaccinations Recorded</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- BHW Health Analytics Viewer Section (Improved UI/UX) -->
    @if((isset($kmeansResults) && count($kmeansResults)) || (isset($decisionTreeResults) && count($decisionTreeResults)))
    <div class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if(isset($kmeansResults) && count($kmeansResults))
            <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 rounded shadow">
                <div class="flex items-center mb-2">
                    <i class="fas fa-project-diagram text-indigo-500 text-xl mr-2"></i>
                    <span class="font-bold text-indigo-700">K-Means Clusters</span>
                </div>
                <ul class="list-disc ml-6 text-indigo-800 text-sm">
                    @foreach($kmeansResults as $cluster)
                        <li>{{ $cluster['description'] }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            @if(isset($decisionTreeResults) && count($decisionTreeResults))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded shadow">
                <div class="flex items-center mb-2">
                    <i class="fas fa-tree text-green-500 text-xl mr-2"></i>
                    <span class="font-bold text-green-700">Decision Tree Insights</span>
                </div>
                <ul class="list-disc ml-6 text-green-800 text-sm">
                    @foreach($decisionTreeResults as $insight)
                        <li>{{ $insight['description'] }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Residents</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalResidents }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                    <i class="fas fa-syringe text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Vaccinations</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalVaccinations }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                    <i class="fas fa-stethoscope text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Consultations</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalConsultations }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                    <i class="fas fa-calendar-alt text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Activities</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalActivities }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                    <i class="fas fa-pills text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Medicines (Low/Expiring)</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $medicineStats['low_stock'] ?? 0 }}/{{ $medicineStats['expiring_soon'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Requested Medicines (30 days)</h3>
            <ul class="list-disc ml-6 text-sm">
                @foreach(($topRequestedMedicines ?? []) as $row)
                    <li>{{ data_get($row, 'medicine.name', 'Unknown') }} — {{ (int) data_get($row, 'requests', 0) }} requests</li>
                @endforeach
            </ul>
            <div class="mt-4">
                <a href="{{ route('admin.medicines.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Manage Inventory &rarr;</a>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Dispensed Medicines (30 days)</h3>
            <ul class="list-disc ml-6 text-sm">
                @foreach(($topDispensedMedicines ?? []) as $row)
                    <li>{{ data_get($row, 'medicine.name', 'Unknown') }} — {{ (int) data_get($row, 'total_qty', 0) }} units</li>
                @endforeach
            </ul>
            <div class="mt-4">
                <a href="{{ route('admin.medicines.report') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Dispense Report &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Health Status Distribution -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Health Status Distribution</h3>
            <div class="space-y-3">
                @foreach($healthStatusDistribution as $status)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">{{ $status->health_status }}</span>
                    <div class="flex items-center">
                        @php
                            $statusCount = (int) ($status->count ?? 0);
                            $resCount = max(1, (int) ($totalResidents ?? 1));
                            $percent = (int) max(0, min(100, ($statusCount / $resCount) * 100));
                        @endphp
                        <span class="text-sm font-medium text-gray-900">{{ $statusCount }} ({{ $percent }}%)</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Activities and Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Consultations -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Consultations</h3>
            <div class="space-y-3">
                @foreach($recentConsultations as $consultation)
                <div class="border-l-4 border-blue-500 pl-4">
                    <p class="text-sm font-medium text-gray-900">{{ $consultation->resident->name }}</p>
                    <p class="text-xs text-gray-500">{{ $consultation->chief_complaint }}</p>
                    <p class="text-xs text-gray-400">{{ $consultation->consultation_datetime->format('M d, Y') }}</p>
                </div>
                @endforeach
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.medical-records.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View All Consultations &rarr;
                </a>
            </div>
        </div>

        <!-- Upcoming Activities -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Activities</h3>
            <div class="space-y-3">
                @foreach($upcomingActivities as $activity)
                <div class="border-l-4 border-green-500 pl-4">
                    <p class="text-sm font-medium text-gray-900">{{ $activity->activity_name }}</p>
                    <p class="text-xs text-gray-500">{{ $activity->activity_type }}</p>
                    <p class="text-xs text-gray-400">{{ $activity->activity_date->format('M d, Y') }}</p>
                </div>
                @endforeach
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.health-center-activities.index') }}" class="text-green-600 hover:text-green-800 text-sm font-medium">
                    View All Activities &rarr;
                </a>
            </div>
        </div>

        <!-- Due Vaccinations -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Due Vaccinations</h3>
            <div class="space-y-3">
                @foreach($dueVaccinations as $vaccination)
                <div class="border-l-4 border-yellow-500 pl-4">
                    <p class="text-sm font-medium text-gray-900">{{ optional($vaccination->resident)->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-500">{{ $vaccination->vaccine_name ?? '' }}</p>
                    <p class="text-xs text-gray-400">Due: {{ optional($vaccination->next_dose_date)->format('M d, Y') }}</p>
                </div>
                @endforeach
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.vaccination-records.due') }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">
                    View All Due Vaccinations &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.vaccination-records.create') }}" class="bg-green-600 text-white p-4 rounded-lg text-center hover:bg-green-700 transition">
                <i class="fas fa-syringe text-2xl mb-2"></i>
                <p class="text-sm font-medium">Record Vaccination</p>
            </a>
            <a href="{{ route('admin.medical-records.create') }}" class="bg-purple-600 text-white p-4 rounded-lg text-center hover:bg-purple-700 transition">
                <i class="fas fa-stethoscope text-2xl mb-2"></i>
                <p class="text-sm font-medium">Log Consultation</p>
            </a>
            <a href="{{ route('admin.health-center-activities.create') }}" class="bg-red-600 text-white p-4 rounded-lg text-center hover:bg-red-700 transition">
                <i class="fas fa-calendar-plus text-2xl mb-2"></i>
                <p class="text-sm font-medium">Schedule Activity</p>
            </a>
        </div>
    </div>
</div>
@endsection
