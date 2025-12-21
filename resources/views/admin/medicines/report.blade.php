@extends('admin.main.layout')

@section('title', 'Dispensing Report')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Main Skeleton -->
    <div id="medicineReportSkeleton">
        @include('components.loading.medicine-skeleton', ['type' => 'report'])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="medicineReportContent" style="display: none;">
        <div class="mb-3">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Dispensing Report</h1>
                    <p class="text-sm md:text-base text-gray-600">Medicine request analysis</p>
                </div>
                <a href="{{ route('admin.medicines.index') }}" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Inventory
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-3 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-3 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form method="GET" class="mb-3 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                <div>
                    <label class="text-sm text-gray-600">Start</label>
                    <input type="date" name="start_date" value="{{ request('start_date', $start->format('Y-m-d')) }}" class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="text-sm text-gray-600">End</label>
                    <input type="date" name="end_date" value="{{ request('end_date', $end->format('Y-m-d')) }}" class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div class="flex space-x-2">
                    <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Apply</button>
                    <a href="{{ route('admin.medicines.report') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Reset</a>
                </div>
            </div>
        </form>

        <!-- Charts Row 1: Top Requested by Purok (Chart) -->
        <div class="grid grid-cols-1 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-gray-900">Top Requests by Purok</h3>
                    <span class="text-xs text-gray-500">People</span>
                </div>
                <div class="h-64">
                    @if(!empty($topRequestedPeopleByPurok) && count($topRequestedPeopleByPurok) > 0)
                        <canvas id="chartTopRequestedByPurok"></canvas>
                    @else
                        <div class="text-center text-gray-500 mt-8">
                            <p>No purok-based data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Charts Row 2: Category / Age Bracket -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-gray-900">Most Requested</h3>
                    <span class="text-xs text-gray-500">Count</span>
                </div>
                <div class="h-56">
                    <canvas id="chartTopRequested"></canvas>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-gray-900">Requests by Age</h3>
                </div>
                <div class="h-56">
                    <canvas id="chartAge"></canvas>
                </div>
            </div>
        </div>

        <!-- Note: Cluster-based section removed to eliminate redundancy with purok-based grouping -->

        <!-- Category Distribution Chart -->
        <div class="grid grid-cols-1 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-gray-900">Category Distribution</h3>
                </div>
                <div class="h-56">
                    <canvas id="chartCategory"></canvas>
                </div>
            </div>
        </div>

        <!-- Modal: Request Distribution Pie -->
        <div id="requestPieModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="requestPieChartTitle" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeRequestPieChart()"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-gray-900" id="requestPieChartTitle">
                                <i class="fas fa-chart-pie text-green-600 mr-2"></i>
                                Request Distribution
                            </h3>
                            <button onclick="closeRequestPieChart()" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition-colors" title="Close modal">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="chart-container" style="position: relative; height: 450px;">
                            <canvas id="requestPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Insights & Guidance -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6 shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-lightbulb text-yellow-500 mt-1"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-gray-900">Insights & Guidance</h3>
                    <div class="mt-2 text-sm text-gray-700 space-y-1">
                        <p>• Click any purok bar to see its most requested medicines in a pie view.</p>
                        <p>• Use age distribution to confirm coverage across all brackets starting at age 1.</p>
                        <p>• Category distribution highlights stock planning needs—watch for categories approaching limits.</p>
                        <p>• For anomalies or spikes, narrow the date range above and re-check the request mix.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const sk = document.getElementById('medicineReportSkeleton');
        if (sk) sk.style.display = 'none';
        const content = document.getElementById('medicineReportContent');
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    /* eslint-disable */
    document.addEventListener('DOMContentLoaded', function() {
        const palette = [
            '#16a34a', '#059669', '#14b8a6', '#22c55e', '#4ade80', '#86efac', '#10b981', '#34d399', '#2dd4bf', '#a7f3d0',
            '#f59e42', '#f43f5e', '#6366f1', '#eab308', '#f472b6', '#a21caf', '#0ea5e9', '#facc15', '#64748b', '#e11d48'
        ];

        const topRequested = JSON.parse(`{!! json_encode(
            collect($requestAnalytics['overall'] ?? [])->map(function ($r) {
                return [data_get($r, "medicine.name", "Unknown"), (int) data_get($r, "requests", 0)];
            })->values()
        ) !!}`);
        const purokMedicineBreakdown = JSON.parse(`{!! json_encode($requestAnalytics['by_purok'] ?? []) !!}`);
        const categoryCounts = JSON.parse(`{!! json_encode(
            collect($categoryCounts ?? [])->map(function ($r) {
                return [data_get($r, "category", "Unknown"), (int) data_get($r, "count", 0)];
            })->values()
        ) !!}`);
        const requestsByAge = JSON.parse(`{!! json_encode(
            collect($requestsByAgeBracket ?? [])->map(function ($r) {
                return [data_get($r, "bracket", "Unknown"), (int) data_get($r, "count", 0)];
            })->values()
        ) !!}`);
        // For purok people chart
        const purokPeopleLabels = JSON.parse(`{!! json_encode(collect($topRequestedPeopleByPurok ?? [])->pluck("purok")->values()) !!}`);
        const purokPeopleData = JSON.parse(`{!! json_encode(collect($topRequestedPeopleByPurok ?? [])->pluck("people")->values()) !!}`);

        function makeBarChart(canvas, labels, values, label, onClick) {
            if (!canvas) return;
            return new Chart(canvas, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label,
                        data: values,
                        backgroundColor: labels.map((_, i) => palette[i % palette.length]),
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { color: '#6b7280' } },
                        y: { ticks: { color: '#6b7280' }, beginAtZero: true }
                    },
                    onClick
                }
            });
        }



        function makeDoughnut(canvas, labels, values) {
            if (!canvas) return;
            new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: labels.map((_, i) => palette[i % palette.length]),
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { color: '#374151' } } }
                }
            });
        }

        // Build charts
        const tr = topRequested || [];
        makeBarChart(
            document.getElementById('chartTopRequested'),
            tr.map(r => r[0]),
            tr.map(r => r[1]),
            'Requests'
        );



        const cat = categoryCounts || [];
        makeDoughnut(document.getElementById('chartCategory'), cat.map(r => r[0]), cat.map(r => r[1]));

        const defaultAgeBrackets = [
            ['1-5 years old', 0],
            ['6-12 years old', 0],
            ['13-17 years old', 0],
            ['18-59 years old', 0],
            ['60+ years old', 0],
        ];
        const age = (requestsByAge || []).map(([label, value]) => {
            let processedLabel = label || 'Unknown';
            // Add "years old" if not already present
            if (processedLabel !== 'Unknown' && !processedLabel.includes('years old')) {
                processedLabel = processedLabel + ' years old';
            }
            return [processedLabel, value || 0];
        });
        const mergedAge = defaultAgeBrackets.map(([label, _]) => {
            // Try to find matching age bracket (with or without "years old")
            const found = age.find(a => {
                const ageLabel = a[0];
                // Match by removing "years old" from both for comparison
                const cleanLabel = label.replace(' years old', '');
                const cleanAgeLabel = ageLabel.replace(' years old', '');
                return cleanAgeLabel === cleanLabel;
            });
            return [label, found ? found[1] : 0];
        });
        const remaining = age.filter(a => {
            const cleanAgeLabel = a[0].replace(' years old', '');
            return !defaultAgeBrackets.find(([label]) => {
                const cleanLabel = label.replace(' years old', '');
                return cleanLabel === cleanAgeLabel;
            });
        });
        const finalAge = [...mergedAge, ...remaining];
        makeDoughnut(document.getElementById('chartAge'), finalAge.map(r => r[0]), finalAge.map(r => r[1]));

        // Draw the purok people chart with modal pie on click
        if (purokPeopleLabels.length && purokPeopleData.length) {
            makeBarChart(
                document.getElementById('chartTopRequestedByPurok'),
                purokPeopleLabels,
                purokPeopleData,
                'People',
                function(event, elements) {
                    if (elements && elements.length) {
                        const index = elements[0].index;
                        const purok = purokPeopleLabels[index];
                        showRequestPieChartForPurok(purok);
                    }
                }
            );
        }

        let requestPieChart = null;
        function showRequestPieChartForPurok(purok) {
            const breakdown = purokMedicineBreakdown[purok] || [];
            if (!breakdown.length) {
                alert('No medicine data available for ' + purok);
                return;
            }

            const labels = breakdown.map(r => r.medicine_name || 'Unknown');
            const values = breakdown.map(r => r.requests || 0);

            const modal = document.getElementById('requestPieModal');
            const title = document.getElementById('requestPieChartTitle');
            title.innerHTML = `<i class="fas fa-chart-pie text-green-600 mr-2"></i>Request Distribution - ${purok}`;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            const pieCtx = document.getElementById('requestPieChart');
            if (requestPieChart) {
                requestPieChart.destroy();
            }

            const colors = labels.map((_, i) => palette[i % palette.length]);

            requestPieChart = new Chart(pieCtx.getContext('2d'), {
                type: 'pie',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderColor: colors,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function closeRequestPieChart() {
            const modal = document.getElementById('requestPieModal');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            if (requestPieChart) {
                requestPieChart.destroy();
                requestPieChart = null;
            }
        }

        // Expose modal handlers for inline triggers
        window.closeRequestPieChart = closeRequestPieChart;
        window.showRequestPieChartForPurok = showRequestPieChartForPurok;

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('requestPieModal');
                if (!modal.classList.contains('hidden')) {
                    closeRequestPieChart();
                }
            }
        });
    });
</script>
