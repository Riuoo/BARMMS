@extends('admin.main.layout')

@section('title', 'Comprehensive Health Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Comprehensive Health Report</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.health-reports') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-print mr-2"></i>Print Report
            </a>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" 
                       class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" id="end_date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" 
                       class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </form>
    </div>

    <!-- Report Period -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h2 class="text-lg font-semibold text-blue-900 mb-2">Report Period</h2>
        <p class="text-blue-700">{{ $startDate->format('F d, Y') }} - {{ $endDate->format('F d, Y') }}</p>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                    <i class="fas fa-user-injured text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Patient Records</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $patientSummary['total'] }}</p>
                </div>
            </div>
        </div>


        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                    <i class="fas fa-stethoscope text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Consultations</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $consultationSummary['total'] }}</p>
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
                    <p class="text-2xl font-semibold text-gray-900">{{ $activitySummary['total'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Reports -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Patient Records Analysis -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Patient Records Analysis</h3>
            
            <!-- Risk Level Distribution -->
            <div class="mb-6">
                <h4 class="text-md font-medium text-gray-700 mb-3">Risk Level Distribution</h4>
                <div class="space-y-2">
                    @foreach($patientSummary['by_risk_level'] as $risk => $count)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ ucfirst($risk) }}</span>
                        <div class="flex items-center">
                            <div class="w-24 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-blue-500 h-2 rounded-full progress-bar" data-width="{{ ($patientSummary['total'] > 0) ? round(($count / $patientSummary['total']) * 100, 2) : 0 }}"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Blood Type Distribution -->
            @if($patientSummary['by_blood_type']->count() > 0)
            <div>
                <h4 class="text-md font-medium text-gray-700 mb-3">Blood Type Distribution</h4>
                <div class="space-y-2">
                    @foreach($patientSummary['by_blood_type'] as $bloodType => $count)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ $bloodType }}</span>
                        <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

    <!-- Consultation Analysis -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Consultation Analysis</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Consultation Types -->
            <div>
                <h4 class="text-md font-medium text-gray-700 mb-3">Consultation Types</h4>
                <div class="space-y-2">
                    @foreach($consultationSummary['by_type'] as $type => $count)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ ucfirst($type) }}</span>
                        <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Consultation Status -->
            <div>
                <h4 class="text-md font-medium text-gray-700 mb-3">Consultation Status</h4>
                <div class="space-y-2">
                    @foreach($consultationSummary['by_status'] as $status => $count)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ ucfirst($status) }}</span>
                        <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Common Complaints -->
        @if($consultationSummary['common_complaints']->count() > 0)
        <div class="mt-6">
            <h4 class="text-md font-medium text-gray-700 mb-3">Top 10 Common Complaints</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($consultationSummary['common_complaints'] as $complaint => $count)
                <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                    <span class="text-sm text-gray-600">{{ $complaint }}</span>
                    <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Health Center Activities Analysis -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Health Center Activities Analysis</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $activitySummary['total'] }}</p>
                <p class="text-sm text-gray-600">Total Activities</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-green-600">{{ $activitySummary['total_participants'] }}</p>
                <p class="text-sm text-gray-600">Total Participants</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-purple-600">₱{{ number_format($activitySummary['total_budget'], 2) }}</p>
                <p class="text-sm text-gray-600">Total Budget</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-red-600">{{ $activitySummary['total'] > 0 ? round($activitySummary['total_participants'] / $activitySummary['total'], 1) : 0 }}</p>
                <p class="text-sm text-gray-600">Avg. Participants</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Activity Types -->
            <div>
                <h4 class="text-md font-medium text-gray-700 mb-3">Activity Types</h4>
                <div class="space-y-2">
                    @foreach($activitySummary['by_type'] as $type => $count)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ ucfirst($type) }}</span>
                        <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Activity Status -->
            <div>
                <h4 class="text-md font-medium text-gray-700 mb-3">Activity Status</h4>
                <div class="space-y-2">
                    @foreach($activitySummary['by_status'] as $status => $count)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ ucfirst($status) }}</span>
                        <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-yellow-900 mb-4">Key Insights & Recommendations</h3>
        <div class="space-y-3 text-yellow-800">
            @if($patientSummary['total'] > 0)
            <div class="flex items-start">
                <i class="fas fa-info-circle mt-1 mr-2"></i>
                <p class="text-sm">Patient records show {{ $patientSummary['by_risk_level']->get('high', 0) }} high-risk patients requiring immediate attention.</p>
            </div>
            @endif
            
            @if($consultationSummary['total'] > 0)
            <div class="flex items-start">
                <i class="fas fa-info-circle mt-1 mr-2"></i>
                <p class="text-sm">Consultation trends indicate {{ $consultationSummary['by_status']->get('completed', 0) }} completed consultations.</p>
            </div>
            @endif
            
            @if($activitySummary['total'] > 0)
            <div class="flex items-start">
                <i class="fas fa-info-circle mt-1 mr-2"></i>
                <p class="text-sm">Health center activities reached {{ $activitySummary['total_participants'] }} participants with ₱{{ number_format($activitySummary['total_budget'], 2) }} total budget utilization.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.progress-bar').forEach(function(el) {
        const width = parseFloat(el.getAttribute('data-width')) || 0;
        el.style.width = width + '%';
    });
});
</script>

<style>
@media print {
    .container { 
        max-width: none; 
    }
    .bg-white { 
        background-color: white !important; 
    }
    .text-gray-900 { 
        color: black !important; 
    }
    .text-gray-600 { 
        color: #666 !important; 
    }
    .text-gray-500 { 
        color: #666 !important; 
    }
    .text-gray-700 { 
        color: #333 !important; 
    }
    .shadow { 
        box-shadow: none !important; 
    }
    .border { 
        border: 1px solid #ddd !important; 
    }
}
</style>
@endsection 