@extends('admin.main.layout')

@section('title', 'Purok Risk Assessment Clustering')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-chart-network mr-2"></i>
            Purok Risk Assessment Clustering
        </h1>
        <p class="text-gray-600">
            Puroks grouped by combined risk factors: incidents, medical visits, and medicine needs
        </p>
    </div>

    @if(isset($error))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ $error }}</p>
                </div>
            </div>
        </div>
    @else
        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-map-marker-alt text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Puroks</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalPuroks ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-layer-group text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Clusters Formed</p>
                        <p class="text-2xl font-bold text-gray-900">{{ count($clusters ?? []) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-star text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Quality Score</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($silhouette ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Processing Time</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $processingTime ?? 0 }}ms</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Clusters -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @foreach(($clusters ?? []) as $cluster)
                @php
                    $riskLevel = $cluster['label'] ?? 'Unknown Risk';
                    $bgColor = 'bg-gray-50';
                    $borderColor = 'border-gray-300';
                    $iconColor = 'text-gray-600';
                    $badgeColor = 'bg-gray-200 text-gray-800';
                    
                    if (str_contains($riskLevel, 'Low')) {
                        $bgColor = 'bg-green-50';
                        $borderColor = 'border-green-300';
                        $iconColor = 'text-green-600';
                        $badgeColor = 'bg-green-200 text-green-900';
                    } elseif (str_contains($riskLevel, 'High')) {
                        $bgColor = 'bg-red-50';
                        $borderColor = 'border-red-300';
                        $iconColor = 'text-red-600';
                        $badgeColor = 'bg-red-200 text-red-900';
                    } elseif (str_contains($riskLevel, 'Moderate')) {
                        $bgColor = 'bg-yellow-50';
                        $borderColor = 'border-yellow-300';
                        $iconColor = 'text-yellow-600';
                        $badgeColor = 'bg-yellow-200 text-yellow-900';
                    }
                @endphp

                <div class="{{ $bgColor }} border-2 {{ $borderColor }} rounded-xl shadow-lg p-6">
                    <!-- Cluster Header -->
                    <div class="mb-4">
                        <h3 class="text-2xl font-bold {{ $iconColor }} mb-2">
                            <i class="fas fa-shield-alt mr-2"></i>
                            {{ $riskLevel }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ $cluster['purok_count'] ?? 0 }} purok(s) in this cluster
                        </p>
                    </div>

                    <!-- Aggregate Metrics -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <p class="text-xs text-gray-600 mb-1">Total Residents</p>
                            <p class="text-xl font-bold {{ $iconColor }}">
                                {{ number_format($cluster['total_residents'] ?? 0) }}
                            </p>
                        </div>
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <p class="text-xs text-gray-600 mb-1">Incidents</p>
                            <p class="text-xl font-bold {{ $iconColor }}">
                                {{ number_format($cluster['total_blotter'] ?? 0) }}
                            </p>
                        </div>
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <p class="text-xs text-gray-600 mb-1">Medical Visits</p>
                            <p class="text-xl font-bold {{ $iconColor }}">
                                {{ number_format($cluster['total_medical'] ?? 0) }}
                            </p>
                        </div>
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <p class="text-xs text-gray-600 mb-1">Medicine Disp.</p>
                            <p class="text-xl font-bold {{ $iconColor }}">
                                {{ number_format($cluster['total_medicine'] ?? 0) }}
                            </p>
                        </div>
                    </div>

                    <!-- Puroks in this Cluster -->
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-map-marked-alt mr-1"></i>
                            Puroks in this Cluster
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach(($cluster['puroks'] ?? []) as $purok)
                                <span class="{{ $badgeColor }} px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $purok['purok_display'] ?? 'N/A' }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Per-Purok Details (Expandable) -->
                    <details class="mt-4">
                        <summary class="cursor-pointer text-sm font-semibold text-gray-700 hover:text-gray-900">
                            <i class="fas fa-chevron-down mr-1"></i>
                            View Purok Details
                        </summary>
                        <div class="mt-3 space-y-2">
                            @foreach(($cluster['puroks'] ?? []) as $purok)
                                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                                    <h5 class="font-semibold text-gray-900 mb-2">
                                        {{ $purok['purok_display'] ?? 'N/A' }}
                                    </h5>
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div>
                                            <span class="text-gray-600">Residents:</span>
                                            <span class="font-semibold">{{ $purok['resident_count'] ?? 0 }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Incidents:</span>
                                            <span class="font-semibold">{{ $purok['blotter_count'] ?? 0 }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Med. Visits:</span>
                                            <span class="font-semibold">{{ $purok['medical_count'] ?? 0 }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Medicine:</span>
                                            <span class="font-semibold">{{ $purok['medicine_count'] ?? 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </details>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
