@extends('admin.main.layout')

@section('title', 'Health Reports Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Health Reports Dashboard</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.health-reports.comprehensive') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-chart-bar mr-2"></i>Comprehensive Report
            </a>
            <a href="{{ route('admin.health-reports.export') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-download mr-2"></i>Export Report
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
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
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <i class="fas fa-user-md text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Patient Records</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalPatientRecords }}</p>
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
                        <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($status->count / $totalResidents) * 100 }}%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ $status->count }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Risk Level Distribution -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Patient Risk Level Distribution</h3>
            <div class="space-y-3">
                @foreach($riskLevelDistribution as $risk)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">{{ $risk->risk_level }}</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                            <div class="bg-red-500 h-2 rounded-full" style="width: {{ ($risk->count / $totalPatientRecords) * 100 }}%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ $risk->count }}</span>
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
                    <p class="text-xs text-gray-400">{{ $consultation->consultation_date->format('M d, Y') }}</p>
                </div>
                @endforeach
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.medical-logbooks.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View All Consultations →
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
                    View All Activities →
                </a>
            </div>
        </div>

        <!-- Due Vaccinations -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Due Vaccinations</h3>
            <div class="space-y-3">
                @foreach($dueVaccinations as $vaccination)
                <div class="border-l-4 border-yellow-500 pl-4">
                    <p class="text-sm font-medium text-gray-900">{{ $vaccination->resident->name }}</p>
                    <p class="text-xs text-gray-500">{{ $vaccination->vaccine_name }}</p>
                    <p class="text-xs text-gray-400">Due: {{ $vaccination->next_dose_date->format('M d, Y') }}</p>
                </div>
                @endforeach
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.vaccination-records.due') }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">
                    View All Due Vaccinations →
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.patient-records.create') }}" class="bg-blue-600 text-white p-4 rounded-lg text-center hover:bg-blue-700 transition">
                <i class="fas fa-user-plus text-2xl mb-2"></i>
                <p class="text-sm font-medium">Add Patient Record</p>
            </a>
            <a href="{{ route('admin.vaccination-records.create') }}" class="bg-green-600 text-white p-4 rounded-lg text-center hover:bg-green-700 transition">
                <i class="fas fa-syringe text-2xl mb-2"></i>
                <p class="text-sm font-medium">Record Vaccination</p>
            </a>
            <a href="{{ route('admin.medical-logbooks.create') }}" class="bg-purple-600 text-white p-4 rounded-lg text-center hover:bg-purple-700 transition">
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
