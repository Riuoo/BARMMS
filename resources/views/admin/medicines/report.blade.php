@extends('admin.main.layout')

@section('title', 'Medicine Dispense Report')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <div class="mb-3">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Medicine Dispense Report</h1>
        <p class="text-sm md:text-base text-gray-600">Analyze medicine requests and dispensing trends</p>
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

    <!-- Charts Row 1: Top Requested / Top Dispensed -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-900">Top Requested (30 days)</h3>
                <span class="text-xs text-gray-500">Count</span>
            </div>
            <div class="h-48">
                <canvas id="chartTopRequested"></canvas>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-900">Top Dispensed (30 days)</h3>
                <span class="text-xs text-gray-500">Qty</span>
            </div>
            <div class="h-48">
                <canvas id="chartTopDispensed"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 2: Category / Monthly / Age Bracket -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-900">Category Distribution</h3>
            </div>
            <div class="h-56">
                <canvas id="chartCategory"></canvas>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-900">Monthly Dispensed</h3>
                <span class="text-xs text-gray-500">Last 6 months</span>
            </div>
            <div class="h-56">
                <canvas id="chartMonthly"></canvas>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-900">Requests by Age Bracket</h3>
            </div>
            <div class="h-56">
                <canvas id="chartAge"></canvas>
            </div>
        </div>
    </div>

    @if(!empty($clusterTopMedicines))
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
        <h3 class="font-semibold mb-4 text-gray-900">Cluster-based Top Medicines</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($clusterTopMedicines as $clusterId => $rows)
            <div class="border border-gray-200 rounded-lg p-3">
                <h4 class="font-medium mb-2 text-gray-800">Cluster {{ $clusterId+1 }}</h4>
                <ol class="list-decimal ml-5 text-sm space-y-1">
                    @forelse(($rows ?? []) as $row)
                        <li class="flex items-center justify-between">
                            <span class="truncate pr-2" title="{{ data_get($row, 'medicine.name', 'Unknown') }}">{{ data_get($row, 'medicine.name', 'Unknown') }}</span>
                            <span class="text-gray-500">{{ (int) data_get($row, 'requests', 0) }}</span>
                        </li>
                    @empty
                        <li class="text-gray-500">No data</li>
                    @endforelse
                </ol>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const palette = [
            '#16a34a', '#059669', '#14b8a6', '#22c55e', '#4ade80', '#86efac', '#10b981', '#34d399', '#2dd4bf', '#a7f3d0'
        ];

        @php
            $__tr = collect($topRequested ?? [])->map(function ($r) { return [data_get($r, 'medicine.name', 'Unknown'), (int) data_get($r, 'requests', 0)]; })->values();
            $__td = collect($topDispensed ?? [])->map(function ($r) { return [data_get($r, 'medicine.name', 'Unknown'), (int) data_get($r, 'total_qty', 0)]; })->values();
            $__cats = collect($categoryCounts ?? [])->map(function ($r) { return [data_get($r, 'category', 'Unknown'), (int) data_get($r, 'count', 0)]; })->values();
            $__mons = collect($monthlyDispensed ?? [])->map(function ($r) { return [data_get($r, 'month', 'Unknown'), (int) data_get($r, 'qty', 0)]; })->values();
            $__ages = collect($requestsByAgeBracket ?? [])->map(function ($r) { return [data_get($r, 'bracket', 'Unknown'), (int) data_get($r, 'count', 0)]; })->values();
        @endphp
        const topRequested = @json($__tr);
        const topDispensed = @json($__td);
        const categoryCounts = @json($__cats);
        const monthlyDispensed = @json($__mons);
        const requestsByAge = @json($__ages);

        function makeBarChart(canvas, labels, values, label) {
            if (!canvas) return;
            new Chart(canvas, {
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
                    }
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
        makeBarChart(document.getElementById('chartTopRequested'), tr.map(r => r[0]), tr.map(r => r[1]), 'Requests');

        const td = topDispensed || [];
        makeBarChart(document.getElementById('chartTopDispensed'), td.map(r => r[0]), td.map(r => r[1]), 'Quantity');

        const cat = categoryCounts || [];
        makeDoughnut(document.getElementById('chartCategory'), cat.map(r => r[0]), cat.map(r => r[1]));

        const m = monthlyDispensed || [];
        makeBarChart(document.getElementById('chartMonthly'), m.map(r => r[0]), m.map(r => r[1]), 'Qty');

        const age = requestsByAge || [];
        makeDoughnut(document.getElementById('chartAge'), age.map(r => r[0]), age.map(r => r[1]));
    });
</script>
