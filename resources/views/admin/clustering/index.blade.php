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
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Residents</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalResidents ?? 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">High Risk Puroks</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $highRiskPuroks ?? 0 }}</p>
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
                        <div class="bg-white rounded-lg p-3 shadow-sm cursor-pointer hover:shadow-md transition duration-200"
                             role="button"
                             tabindex="0"
                             onclick="openIncidentModal({{ $cluster['id'] }}, '{{ addslashes($cluster['label']) }}')"
                             onkeydown="if(event.key==='Enter' || event.key===' ') { event.preventDefault(); openIncidentModal({{ $cluster['id'] }}, '{{ addslashes($cluster['label']) }}'); }">
                            <p class="text-xs text-gray-600 mb-1">Incidents</p>
                            <p class="text-xl font-bold {{ $iconColor }}">
                                {{ number_format($cluster['total_blotter'] ?? 0) }}
                            </p>
                        </div>
                        <div class="bg-white rounded-lg p-3 shadow-sm cursor-pointer hover:shadow-md transition duration-200"
                             role="button"
                             tabindex="0"
                             onclick="openMedicalModal({{ $cluster['id'] }}, '{{ addslashes($cluster['label']) }}')"
                             onkeydown="if(event.key==='Enter' || event.key===' ') { event.preventDefault(); openMedicalModal({{ $cluster['id'] }}, '{{ addslashes($cluster['label']) }}'); }">
                            <p class="text-xs text-gray-600 mb-1">Medical Visits</p>
                            <p class="text-xl font-bold {{ $iconColor }}">
                                {{ number_format($cluster['total_medical'] ?? 0) }}
                            </p>
                        </div>
                        <div class="bg-white rounded-lg p-3 shadow-sm cursor-pointer hover:shadow-md transition duration-200"
                             role="button"
                             tabindex="0"
                             onclick="openMedicineModal({{ $cluster['id'] }}, '{{ addslashes($cluster['label']) }}')"
                             onkeydown="if(event.key==='Enter' || event.key===' ') { event.preventDefault(); openMedicineModal({{ $cluster['id'] }}, '{{ addslashes($cluster['label']) }}'); }">
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
                    <details class="mt-4" id="details-{{ $cluster['id'] }}">
                        <summary class="cursor-pointer text-sm font-semibold text-gray-700 hover:text-gray-900">
                            <i class="fas fa-chevron-down mr-1"></i>
                            View Purok Details
                        </summary>
                        <div class="mt-3 space-y-2">
                            @foreach(($cluster['puroks'] ?? []) as $purokIndex => $purok)
                                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                                    <h5 class="font-semibold text-gray-900 mb-2">
                                        {{ $purok['purok_display'] ?? 'N/A' }}
                                    </h5>
                                    <div class="grid grid-cols-2 gap-2 text-xs mb-3">
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
                                    
                                    <!-- Detailed Breakdown -->
                                    <div class="mt-3 pt-3 border-t border-gray-200 space-y-2">
                                        <!-- Incidents Breakdown -->
                                        @php
                                            $purokIncidents = collect($purok['incident_analytics']['case_types'] ?? [])
                                                ->take(3);
                                        @endphp
                                        @if($purokIncidents->isNotEmpty())
                                            <div class="text-xs">
                                                <p class="font-semibold text-gray-700 mb-1">Top Case Types:</p>
                                                <ul class="list-disc list-inside text-gray-600">
                                                    @foreach($purokIncidents as $incident)
                                                        <li>{{ $incident['type'] }} ({{ $incident['count'] }})</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        
                                        <!-- Medical Visits Breakdown -->
                                        @php
                                            $purokVisits = collect($purok['medical_analytics']['visits_by_purok'] ?? [])
                                                ->first();
                                            $purokIllnesses = collect($purok['medical_analytics']['illnesses'] ?? [])
                                                ->take(3);
                                        @endphp
                                        @if($purokVisits || $purokIllnesses->isNotEmpty())
                                            <div class="text-xs">
                                                @if($purokVisits)
                                                    <p class="font-semibold text-gray-700 mb-1">Visits: {{ $purokVisits['count'] }}</p>
                                                @endif
                                                @if($purokIllnesses->isNotEmpty())
                                                    <p class="font-semibold text-gray-700 mb-1 mt-2">Top Diagnoses:</p>
                                                    <ul class="list-disc list-inside text-gray-600">
                                                        @foreach($purokIllnesses as $illness)
                                                            <li>{{ $illness['illness'] }} ({{ $illness['count'] }})</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        <!-- Medicine Breakdown -->
                                        @php
                                            $purokMedicines = collect($purok['medicine_analytics']['medicines'] ?? [])
                                                ->take(3);
                                        @endphp
                                        @if($purokMedicines->isNotEmpty())
                                            <div class="text-xs">
                                                <p class="font-semibold text-gray-700 mb-1">Top Medicines:</p>
                                                <ul class="list-disc list-inside text-gray-600">
                                                    @foreach($purokMedicines as $medicine)
                                                        <li>{{ $medicine['name'] }} (Total: {{ $medicine['total'] }})</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
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

<!-- Incident Analytics Modal -->
<div id="incidentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[80vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900" id="incidentModalTitle">Incident Analytics</h3>
            <button type="button" id="closeIncidentModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <div id="incidentModalContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
        <div class="flex justify-end px-6 py-4 border-t border-gray-200">
            <button type="button" id="dismissIncidentModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Medical Analytics Modal -->
<div id="medicalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[80vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900" id="medicalModalTitle">Medical Visit Analytics</h3>
            <button type="button" id="closeMedicalModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <div id="medicalModalContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
        <div class="flex justify-end px-6 py-4 border-t border-gray-200">
            <button type="button" id="dismissMedicalModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Medicine Analytics Modal -->
<div id="medicineModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[80vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900" id="medicineModalTitle">Medicine Dispense Analytics</h3>
            <button type="button" id="closeMedicineModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <div id="medicineModalContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
        <div class="flex justify-end px-6 py-4 border-t border-gray-200">
            <button type="button" id="dismissMedicineModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Cluster data from PHP
const clusterData = @json($clusters ?? []);

function openIncidentModal(clusterId, clusterLabel) {
    const cluster = clusterData.find(c => c.id === clusterId);
    if (!cluster || !cluster.incident_analytics) {
        return;
    }

    const modal = document.getElementById('incidentModal');
    const title = document.getElementById('incidentModalTitle');
    const content = document.getElementById('incidentModalContent');

    title.textContent = `Incident Analytics - ${clusterLabel}`;

    const caseTypes = cluster.incident_analytics.case_types || [];
    
    if (caseTypes.length === 0) {
        content.innerHTML = '<p class="text-gray-600">No incident data available for this cluster.</p>';
    } else {
        let html = '<div class="space-y-4">';
        html += '<h4 class="font-semibold text-gray-900 mb-3">Case Types (Most Common to Least Common)</h4>';
        html += '<div class="space-y-2">';
        
        caseTypes.forEach((item, index) => {
            html += `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-500 mr-3">#${index + 1}</span>
                        <span class="text-sm font-medium text-gray-900">${escapeHtml(item.type)}</span>
                    </div>
                    <span class="text-sm font-bold text-gray-700">${item.count}</span>
                </div>
            `;
        });
        
        html += '</div></div>';
        content.innerHTML = html;
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function openMedicalModal(clusterId, clusterLabel) {
    const cluster = clusterData.find(c => c.id === clusterId);
    if (!cluster || !cluster.medical_analytics) {
        return;
    }

    const modal = document.getElementById('medicalModal');
    const title = document.getElementById('medicalModalTitle');
    const content = document.getElementById('medicalModalContent');

    title.textContent = `Medical Visit Analytics - ${clusterLabel}`;

    const visitsByPurok = cluster.medical_analytics.visits_by_purok || [];
    const illnesses = cluster.medical_analytics.illnesses || [];
    
    let html = '<div class="space-y-6">';
    
    // Visits by Purok
    if (visitsByPurok.length > 0) {
        html += '<div>';
        html += '<h4 class="font-semibold text-gray-900 mb-3">Visits by Purok</h4>';
        html += '<div class="space-y-2">';
        visitsByPurok.forEach((item, index) => {
            html += `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-900">${escapeHtml(item.purok)}</span>
                    <span class="text-sm font-bold text-gray-700">${item.count} visits</span>
                </div>
            `;
        });
        html += '</div></div>';
    }
    
    // Illnesses
    if (illnesses.length > 0) {
        html += '<div>';
        html += '<h4 class="font-semibold text-gray-900 mb-3">Illnesses Diagnosed (Most Common to Least Common)</h4>';
        html += '<div class="space-y-2">';
        illnesses.forEach((item, index) => {
            html += `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-500 mr-3">#${index + 1}</span>
                        <span class="text-sm font-medium text-gray-900">${escapeHtml(item.illness)}</span>
                    </div>
                    <span class="text-sm font-bold text-gray-700">${item.count}</span>
                </div>
            `;
        });
        html += '</div></div>';
    }
    
    if (visitsByPurok.length === 0 && illnesses.length === 0) {
        html = '<p class="text-gray-600">No medical visit data available for this cluster.</p>';
    } else {
        html += '</div>';
    }
    
    content.innerHTML = html;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function openMedicineModal(clusterId, clusterLabel) {
    const cluster = clusterData.find(c => c.id === clusterId);
    if (!cluster || !cluster.medicine_analytics) {
        return;
    }

    const modal = document.getElementById('medicineModal');
    const title = document.getElementById('medicineModalTitle');
    const content = document.getElementById('medicineModalContent');

    title.textContent = `Medicine Dispense Analytics - ${clusterLabel}`;

    const medicines = cluster.medicine_analytics.medicines || [];
    
    if (medicines.length === 0) {
        content.innerHTML = '<p class="text-gray-600">No medicine data available for this cluster.</p>';
    } else {
        let html = '<div class="space-y-4">';
        html += '<h4 class="font-semibold text-gray-900 mb-3">Medicines Requested/Dispensed (Most Common to Least Common)</h4>';
        html += '<div class="space-y-2">';
        
        medicines.forEach((item, index) => {
            html += `
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-500 mr-3">#${index + 1}</span>
                            <span class="text-sm font-medium text-gray-900">${escapeHtml(item.name)}</span>
                        </div>
                        <span class="text-sm font-bold text-gray-700">Total: ${item.total}</span>
                    </div>
                    <div class="flex gap-4 text-xs text-gray-600 ml-8">
                        <span>Requested: ${item.requested || 0}</span>
                        <span>Dispensed: ${item.dispensed || 0}</span>
                    </div>
                </div>
            `;
        });
        
        html += '</div></div>';
        content.innerHTML = html;
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeIncidentModal() {
    const modal = document.getElementById('incidentModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function closeMedicalModal() {
    const modal = document.getElementById('medicalModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function closeMedicineModal() {
    const modal = document.getElementById('medicineModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Event listeners
document.getElementById('closeIncidentModal')?.addEventListener('click', closeIncidentModal);
document.getElementById('dismissIncidentModal')?.addEventListener('click', closeIncidentModal);
document.getElementById('closeMedicalModal')?.addEventListener('click', closeMedicalModal);
document.getElementById('dismissMedicalModal')?.addEventListener('click', closeMedicalModal);
document.getElementById('closeMedicineModal')?.addEventListener('click', closeMedicineModal);
document.getElementById('dismissMedicineModal')?.addEventListener('click', closeMedicineModal);

// Close modals on outside click
document.getElementById('incidentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeIncidentModal();
    }
});

document.getElementById('medicalModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeMedicalModal();
    }
});

document.getElementById('medicineModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeMedicineModal();
    }
});
</script>
@endpush
@endsection
