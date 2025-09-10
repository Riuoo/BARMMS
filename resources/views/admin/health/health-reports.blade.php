@extends('admin.main.layout')

@section('title', 'Health Dashboard')

@php
    // Helper function to safely get items from arrays or collections
    function safeTake($data, $count) {
        if (is_array($data)) {
            return array_slice($data, 0, $count);
        }
        if (is_object($data) && method_exists($data, 'take')) {
            return $data->take($count);
        }
        return [];
    }
    
    function safeCount($data) {
        if (is_array($data)) {
            return count($data);
        }
        if (is_object($data) && method_exists($data, 'count')) {
            return $data->count();
        }
        return 0;
    }
@endphp

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Main Skeleton Container -->
    <div id="healthDashboardSkeleton">
        @include('components.loading.dashboard-skeleton', ['variant' => 'health'])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="healthContent" style="display: none;">
    <!-- Enhanced Header Section -->
    <div class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Health Dashboard</h1>
                <p class="text-sm md:text-base text-gray-600">Comprehensive health analytics and management overview</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-2">
                <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-2">
                    <span class="text-green-800 text-sm font-medium">Last updated: {{ now()->format('M d, Y g:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Health Statistics Cards Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-4 mb-2">
        <!-- Total Residents Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-xs font-medium">Total Residents</p>
                        <p class="text-white text-2xl font-bold">{{ $totalResidents }}</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-2">
                        <i class="fas fa-users text-white text-lg"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('admin.residents') }}" class="text-blue-100 hover:text-white text-xs font-medium flex items-center">
                        View all <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Vaccinations Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-xs font-medium">Vaccinations</p>
                        <p class="text-white text-2xl font-bold">{{ $totalVaccinations }}</p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-2">
                        <i class="fas fa-syringe text-white text-lg"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('admin.vaccination-records.index') }}" class="text-purple-100 hover:text-white text-xs font-medium flex items-center">
                        Manage <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Consultations Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-xs font-medium">Consultations</p>
                        <p class="text-white text-2xl font-bold">{{ $totalConsultations }}</p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-2">
                        <i class="fas fa-stethoscope text-white text-lg"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('admin.medical-records.index') }}" class="text-green-100 hover:text-white text-xs font-medium flex items-center">
                        View all <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Activities Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-xs font-medium">Health Activities</p>
                        <p class="text-white text-2xl font-bold">{{ $totalActivities }}</p>
                    </div>
                    <div class="bg-orange-400 bg-opacity-30 rounded-full p-2">
                        <i class="fas fa-calendar-alt text-white text-lg"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('admin.health-center-activities.index') }}" class="text-orange-100 hover:text-white text-xs font-medium flex items-center">
                        Manage <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Health Alerts Section -->
    @if(isset($overdueVaccinations) && count($overdueVaccinations) > 0 || isset($analyticsAlerts) && count($analyticsAlerts) > 0)
    <div class="mb-2">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if(isset($overdueVaccinations) && count($overdueVaccinations) > 0)
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-2"></i>
                    <span class="font-bold text-red-700">Overdue Vaccinations</span>
                </div>
                <ul class="list-disc ml-6 text-red-800 text-sm">
                    @foreach(safeTake($overdueVaccinations, 3) as $vaccination)
                        <li>
                            <span class="font-semibold">{{ optional($vaccination->resident)->name ?? 'Unknown' }}</span>
                            <span class="ml-1">({{ $vaccination->vaccine_name ?? 'Unknown' }})</span>
                        </li>
                    @endforeach
                </ul>
                @if(safeCount($overdueVaccinations) > 3)
                    <p class="text-red-600 text-xs mt-2">+{{ safeCount($overdueVaccinations) - 3 }} more overdue</p>
                @endif
            </div>
            @endif

            @if(isset($analyticsAlerts) && count($analyticsAlerts) > 0)
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg shadow">
                <div class="flex items-center mb-2">
                    <i class="fas fa-chart-line text-blue-500 text-xl mr-2"></i>
                    <span class="font-bold text-blue-700">Health Analytics Alerts</span>
                </div>
                <ul class="list-disc ml-6 text-blue-800 text-sm">
                    @foreach(safeTake($analyticsAlerts, 3) as $alert)
                        <li>{{ $alert }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Enhanced Data Visualization Section -->
    <div class="mt-2 grid grid-cols-1 lg:grid-cols-2 gap-2">
        <!-- Health Status Distribution Chart -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Health Status Distribution</h3>
                <div class="chart-container" style="position: relative; height:200px; width:100%">
                    <canvas id="healthStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Consultation Trends Chart -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Monthly Consultation Trends</h3>
                <div class="chart-container" style="position: relative; height:200px; width:100%">
                    <canvas id="consultationTrendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Medicine Analytics Section -->
    <div class="mt-2 grid grid-cols-1 lg:grid-cols-2 gap-2">
        <!-- Critical Medicine Inventory -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Critical Medicine Inventory</h3>
                
                <!-- Summary Cards -->
                <div class="grid grid-cols-2 gap-4 mb-2">
                    <div class="text-center p-3 bg-red-50 rounded-lg border border-red-200">
                        <div class="text-2xl font-bold text-red-600">{{ $medicineStats['low_stock'] ?? 0 }}</div>
                        <div class="text-sm text-red-700">Low Stock</div>
                    </div>
                    <div class="text-center p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="text-2xl font-bold text-yellow-600">{{ $medicineStats['expiring_soon'] ?? 0 }}</div>
                        <div class="text-sm text-yellow-700">Expiring Soon</div>
                    </div>
                </div>

                <!-- Critical Priority Medicines (Combined) -->
                @php
                    $criticalMedicines = collect();
                    
                    // Add low stock medicines with priority score
                    if (isset($medicineStats['low_stock_details'])) {
                        foreach ($medicineStats['low_stock_details'] as $medicine) {
                            $priorityScore = ($medicine->minimum_stock - $medicine->current_stock) * 10; // Higher score for more critical shortage
                            $criticalMedicines->push([
                                'medicine' => $medicine,
                                'type' => 'low_stock',
                                'priority_score' => $priorityScore,
                                'urgency' => 'Critical Shortage'
                            ]);
                        }
                    }
                    
                    // Add expiring medicines with priority score
                    if (isset($medicineStats['expiring_details'])) {
                        foreach ($medicineStats['expiring_details'] as $medicine) {
                            $daysUntilExpiry = now()->diffInDays($medicine->expiry_date, false);
                            $priorityScore = max(1, 30 - $daysUntilExpiry) * 5; // Higher score for sooner expiry
                            $criticalMedicines->push([
                                'medicine' => $medicine,
                                'type' => 'expiring',
                                'priority_score' => $priorityScore,
                                'urgency' => $daysUntilExpiry <= 7 ? 'Expires This Week' : 'Expires Soon'
                            ]);
                        }
                    }
                    
                    // Sort by priority score (highest first)
                    $criticalMedicines = $criticalMedicines->sortByDesc('priority_score');
                @endphp

                @if($criticalMedicines->count() > 0)
                <div class="mb-2">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        <span class="text-sm font-semibold text-red-700">Priority Actions Required ({{ $criticalMedicines->count() }})</span>
                    </div>
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($criticalMedicines->take(8) as $item)
                        <div class="flex justify-between items-center p-3 rounded-lg border-l-4 {{ $item['type'] === 'low_stock' ? 'bg-red-50 border-red-400' : 'bg-yellow-50 border-yellow-400' }}">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $item['medicine']->name }}</p>
                                    <span class="text-xs px-2 py-1 rounded-full {{ $item['type'] === 'low_stock' ? 'bg-red-200 text-red-800' : 'bg-yellow-200 text-yellow-800' }}">
                                        {{ $item['urgency'] }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-600">{{ optional($item['medicine']->category)->name ?? 'No Category' }}</p>
                            </div>
                            <div class="text-right ml-3">
                                @if($item['type'] === 'low_stock')
                                    <p class="text-sm font-bold text-red-600">{{ $item['medicine']->current_stock }}</p>
                                    <p class="text-xs text-gray-500">of {{ $item['medicine']->minimum_stock }}</p>
                                @else
                                    <p class="text-sm font-bold text-yellow-600">{{ \Carbon\Carbon::parse($item['medicine']->expiry_date)->format('M d') }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($item['medicine']->expiry_date)->diffForHumans() }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($criticalMedicines->count() > 8)
                        <p class="text-xs text-gray-500 text-center mt-2">+{{ $criticalMedicines->count() - 8 }} more critical items</p>
                    @endif
                </div>
                @else
                <div class="text-center py-6">
                    <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                    <p class="text-sm text-gray-600">All medicines are well-stocked and not expiring soon</p>
                </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('admin.medicines.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Manage Inventory &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Top Requested Medicines Chart -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Top Requested Medicines (30 days)</h3>
                <div class="chart-container" style="position: relative; height:200px; width:100%">
                    <canvas id="topRequestedMedicinesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities Section -->
    <div class="mt-2 grid grid-cols-1 lg:grid-cols-3 gap-2">
        <!-- Recent Consultations -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Recent Consultations</h3>
                <div class="space-y-3">
                    @foreach(safeTake($recentConsultations, 4) as $consultation)
                    <div class="border-l-4 border-blue-500 pl-4">
                        <p class="text-sm font-medium text-gray-900">{{ $consultation->resident->name }}</p>
                        <p class="text-xs text-gray-500">{{ Str::limit($consultation->chief_complaint, 50) }}</p>
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
        </div>

        <!-- Upcoming Activities -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Upcoming Activities</h3>
                <div class="space-y-3">
                    @foreach(safeTake($upcomingActivities, 4) as $activity)
                    <div class="border-l-4 border-green-500 pl-4">
                        <p class="text-sm font-medium text-gray-900">{{ Str::limit($activity->activity_name, 40) }}</p>
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
        </div>

        <!-- Due Vaccinations -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Due Vaccinations</h3>
                <div class="space-y-3">
                    @foreach(safeTake($dueVaccinations, 4) as $vaccination)
                    <div class="border-l-4 border-yellow-500 pl-4">
                        <p class="text-sm font-medium text-gray-900">{{ optional($vaccination->resident)->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500">{{ $vaccination->vaccine_name ?? '' }}</p>
                        <p class="text-xs text-gray-400">Due: {{ optional($vaccination->next_dose_date)->format('M d, Y') }}</p>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.vaccination-records.due') }}" class="text-green-600 hover:text-green-800 text-sm font-medium">
                        View All Due Vaccinations &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="fixed bottom-6 right-6 z-50">
        <div class="relative" x-data="{ open: false }">
            <!-- Main FAB -->
            <button @click="open = !open" class="bg-green-600 hover:bg-green-700 text-white rounded-full p-4 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-110">
                <i class="fas fa-plus text-xl"></i>
            </button>
            
            <!-- FAB Menu -->
            <div x-show="open" @click.away="open = false" x-transition class="absolute bottom-16 right-0 space-y-2">
                <a href="{{ route('admin.vaccination-records.create') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-syringe text-green-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700">Record Vaccination</span>
                </a>
                <a href="{{ route('admin.medical-records.create') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-stethoscope text-blue-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700">Log Consultation</span>
                </a>
                <a href="{{ route('admin.health-center-activities.create') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-calendar-plus text-orange-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700">Schedule Activity</span>
                </a>
                <a href="{{ route('admin.medicines.create') }}" class="flex items-center bg-white rounded-lg shadow-lg p-3 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-pills text-purple-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700">Add Medicine</span>
                </a>
            </div>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const sk = document.getElementById('healthDashboardSkeleton');
        if (sk) sk.style.display = 'none';
        const content = document.getElementById('healthContent');
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Ensure charts don't extend infinitely */
    .chart-container {
        max-height: 200px;
        overflow: hidden;
    }
</style>

<!-- Chart initialization scripts -->
<script>
    // Initialize charts with error handling and cleanup
    function initializeHealthCharts() {
        // Destroy existing charts if they exist
        if (window.healthStatusChartInstance) {
            window.healthStatusChartInstance.destroy();
            window.healthStatusChartInstance = null;
        }
        if (window.consultationTrendsChartInstance) {
            window.consultationTrendsChartInstance.destroy();
            window.consultationTrendsChartInstance = null;
        }
        if (window.topRequestedMedicinesChartInstance) {
            window.topRequestedMedicinesChartInstance.destroy();
            window.topRequestedMedicinesChartInstance = null;
        }

        // Health Status Distribution Chart
        try {
            const healthStatusData = JSON.parse(`@json($healthStatusDistribution)`);
            if (healthStatusData && healthStatusData.length > 0) {
                const labels = healthStatusData.map(item => item.health_status || 'Unknown');
                const counts = healthStatusData.map(item => parseInt(item.count) || 0);

                const healthCtx = document.getElementById('healthStatusChart').getContext('2d');
                window.healthStatusChartInstance = new Chart(healthCtx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: counts,
                            backgroundColor: [
                                '#3B82F6', // blue-500
                                '#10B981', // green-500
                                '#8B5CF6', // purple-500
                                '#F59E0B', // amber-500
                                '#EF4444', // red-500
                                '#EC4899', // pink-500
                                '#6366F1', // indigo-500
                                '#14B8A6'  // teal-500
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 15
                                }
                            }
                        }
                    }
                });
            } else {
                // Create empty chart with placeholder data
                const healthCtx = document.getElementById('healthStatusChart').getContext('2d');
                window.healthStatusChartInstance = new Chart(healthCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['No Data Available'],
                        datasets: [{
                            data: [1],
                            backgroundColor: ['#E5E7EB'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 15
                                }
                            }
                        }
                    }
                });
            }
        } catch (error) {
            console.error('Error initializing health status chart:', error);
        }

        // Monthly Consultation Trends Chart
        try {
            const consultationData = JSON.parse(`@json($monthlyConsultations)`);
            if (consultationData && consultationData.length > 0) {
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const labels = consultationData.map(item => {
                    const [year, month] = item.month.split('-');
                    return monthNames[parseInt(month) - 1] + ' ' + year;
                });
                const counts = consultationData.map(item => parseInt(item.count) || 0);

                const consultationCtx = document.getElementById('consultationTrendsChart').getContext('2d');
                window.consultationTrendsChartInstance = new Chart(consultationCtx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Consultations',
                            data: counts,
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 3,
                            pointRadius: 4,
                            pointBackgroundColor: '#10B981',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            } else {
                // Create empty chart with placeholder data
                const consultationCtx = document.getElementById('consultationTrendsChart').getContext('2d');
                window.consultationTrendsChartInstance = new Chart(consultationCtx, {
                    type: 'line',
                    data: {
                        labels: ['No Data'],
                        datasets: [{
                            label: 'Consultations',
                            data: [0],
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        } catch (error) {
            console.error('Error initializing consultation trends chart:', error);
        }

        // Top Requested Medicines Chart
        try {
            const topRequestedData = JSON.parse(`@json($topRequestedMedicines ?? [])`);
            if (topRequestedData && topRequestedData.length > 0) {
                const labels = topRequestedData.map(item => {
                    const medicineName = item.medicine && item.medicine.name ? item.medicine.name : 'Unknown';
                    return medicineName.length > 15 ? medicineName.substring(0, 15) + '...' : medicineName;
                });
                const requests = topRequestedData.map(item => parseInt(item.requests || 0));

                const topRequestedCtx = document.getElementById('topRequestedMedicinesChart').getContext('2d');
                window.topRequestedMedicinesChartInstance = new Chart(topRequestedCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Requests',
                            data: requests,
                            backgroundColor: [
                                '#3B82F6', // blue-500
                                '#10B981', // green-500
                                '#8B5CF6', // purple-500
                                '#F59E0B', // amber-500
                                '#EF4444'  // red-500
                            ],
                            borderColor: [
                                '#2563EB', // blue-600
                                '#059669', // green-600
                                '#7C3AED', // purple-600
                                '#D97706', // amber-600
                                '#DC2626'  // red-600
                            ],
                            borderWidth: 2,
                            borderRadius: 6,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const originalName = topRequestedData[context.dataIndex]?.medicine?.name || 'Unknown';
                                        return `${originalName}: ${context.parsed.y} requests`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    stepSize: 1
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            } else {
                // Create empty chart with placeholder data
                const topRequestedCtx = document.getElementById('topRequestedMedicinesChart').getContext('2d');
                window.topRequestedMedicinesChartInstance = new Chart(topRequestedCtx, {
                    type: 'bar',
                    data: {
                        labels: ['No Data'],
                        datasets: [{
                            label: 'Requests',
                            data: [0],
                            backgroundColor: ['#E5E7EB'],
                            borderColor: ['#D1D5DB'],
                            borderWidth: 2,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        } catch (error) {
            console.error('Error initializing top requested medicines chart:', error);
        }
    }

    // Initialize charts when the page is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeHealthCharts);
    } else {
        // DOM is already loaded
        initializeHealthCharts();
    }

    // Re-initialize charts if the window is resized
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            initializeHealthCharts();
        }, 100);
    });
</script>

