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
                        <div class="bg-white rounded-lg p-3 shadow-sm cursor-pointer hover:shadow-md transition duration-200 incident-card"
                             role="button"
                             tabindex="0"
                             data-cluster-id="{{ $cluster['id'] }}"
                             data-cluster-label="{{ addslashes($cluster['label']) }}"
                             onclick="if(typeof window.openIncidentModal === 'function') { window.openIncidentModal({{ $cluster['id'] }}, '{{ addslashes($cluster['label']) }}'); } else { console.error('openIncidentModal function not found'); }"
                             onkeydown="if(event.key==='Enter' || event.key===' ') { event.preventDefault(); if(typeof window.openIncidentModal === 'function') { window.openIncidentModal({{ $cluster['id'] }}, '{{ addslashes($cluster['label']) }}'); } }">
                            <p class="text-xs text-gray-600 mb-1">Incidents</p>
                            <p class="text-xl font-bold {{ $iconColor }}">
                                {{ number_format($cluster['total_blotter'] ?? 0) }}
                            </p>
                        </div>
                        <div class="bg-white rounded-lg p-3 shadow-sm cursor-pointer hover:shadow-md transition duration-200 medical-card"
                             role="button"
                             tabindex="0"
                             data-cluster-id="{{ $cluster['id'] }}"
                             data-cluster-label="{{ addslashes($cluster['label']) }}"
                             onclick="if(typeof window.openMedicalModal === 'function') { window.openMedicalModal({{ $cluster['id'] }}, '{{ addslashes($cluster['label']) }}'); } else { console.error('openMedicalModal function not found'); }"
                             onkeydown="if(event.key==='Enter' || event.key===' ') { event.preventDefault(); if(typeof window.openMedicalModal === 'function') { window.openMedicalModal({{ $cluster['id'] }}, '{{ addslashes($cluster['label']) }}'); } }">
                            <p class="text-xs text-gray-600 mb-1">Medical Visits</p>
                            <p class="text-xl font-bold {{ $iconColor }}">
                                {{ number_format($cluster['total_medical'] ?? 0) }}
                            </p>
                        </div>
                        <div class="bg-white rounded-lg p-3 shadow-sm cursor-pointer hover:shadow-md transition duration-200 medicine-card"
                             role="button"
                             tabindex="0"
                             data-cluster-id="{{ $cluster['id'] }}"
                             data-cluster-label="{{ addslashes($cluster['label']) }}"
                             onclick="if(typeof window.openMedicineModal === 'function') { window.openMedicineModal({{ $cluster['id'] }}, '{{ addslashes($cluster['label']) }}'); } else { console.error('openMedicineModal function not found'); }"
                             onkeydown="if(event.key==='Enter' || event.key===' ') { event.preventDefault(); if(typeof window.openMedicineModal === 'function') { window.openMedicineModal({{ $cluster['id'] }}, '{{ addslashes($cluster['label']) }}'); } }">
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
                    
                    <!-- Per-Purok Details Button -->
                    <button type="button" 
                            class="mt-4 w-full text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-lg px-3 py-2 transition duration-200 flex items-center justify-center shadow-md"
                            onclick="openPurokDetailsModal({{ $cluster['id'] }}, '{{ addslashes($cluster['label']) }}')">
                        <i class="fas fa-info-circle mr-2"></i>
                        View Purok Details
                    </button>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Incident Analytics Modal -->
<div id="incidentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-[9999]">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[80vh] overflow-hidden flex flex-col" onclick="event.stopPropagation()">
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
<div id="medicalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-[9999]">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[80vh] overflow-hidden flex flex-col" onclick="event.stopPropagation()">
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
<div id="medicineModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-[9999]">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[80vh] overflow-hidden flex flex-col" onclick="event.stopPropagation()">
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

<!-- Purok Details Modal -->
<div id="purokDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-[9999]">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 max-h-[85vh] overflow-hidden flex flex-col" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900" id="purokDetailsModalTitle">Purok Details</h3>
            <button type="button" id="closePurokDetailsModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <div id="purokDetailsModalContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
        <div class="flex justify-end px-6 py-4 border-t border-gray-200">
            <button type="button" id="dismissPurokDetailsModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Cluster data from PHP - ensure it's always an array
const clusterDataRaw = @json($clusters ?? []);
// Convert to array if it's an object (happens when PHP array has non-sequential keys)
let clusterData = [];
if (Array.isArray(clusterDataRaw)) {
    clusterData = clusterDataRaw;
} else if (clusterDataRaw && typeof clusterDataRaw === 'object') {
    // Convert object to array
    clusterData = Object.values(clusterDataRaw);
} else {
    clusterData = [];
}

console.log('Cluster data loaded:', clusterData);
console.log('Cluster data type:', typeof clusterDataRaw, 'isArray:', Array.isArray(clusterDataRaw));
console.log('First cluster sample:', clusterData[0]);
if (clusterData[0]) {
    console.log('First cluster medicine_analytics:', clusterData[0].medicine_analytics);
}

// Make functions globally accessible
window.openIncidentModal = function(clusterId, clusterLabel) {
    console.log('openIncidentModal called with:', clusterId, clusterLabel);
    console.log('clusterData type:', typeof clusterData, 'isArray:', Array.isArray(clusterData));
    
    const modal = document.getElementById('incidentModal');
    const title = document.getElementById('incidentModalTitle');
    const content = document.getElementById('incidentModalContent');

    if (!modal) {
        console.error('Modal element not found');
        return;
    }
    if (!title || !content) {
        console.error('Modal title or content not found');
        return;
    }

    if (!Array.isArray(clusterData)) {
        console.error('clusterData is not an array:', clusterData);
        content.innerHTML = '<p class="text-red-600">Error: Cluster data is not available.</p>';
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        return;
    }

    title.textContent = `Incident Analytics - ${clusterLabel}`;

    // Find cluster by ID (handle both string and number comparisons)
    const cluster = clusterData.find(c => c.id == clusterId || c.id === clusterId);
    const caseTypes = cluster?.incident_analytics?.case_types || [];
    
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

    // Show modal
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
    console.log('Modal should be visible now');
}

window.openMedicalModal = function(clusterId, clusterLabel) {
    console.log('openMedicalModal called with:', clusterId, clusterLabel);
    console.log('clusterData type:', typeof clusterData, 'isArray:', Array.isArray(clusterData));
    
    const modal = document.getElementById('medicalModal');
    const title = document.getElementById('medicalModalTitle');
    const content = document.getElementById('medicalModalContent');

    if (!modal) {
        console.error('Modal element not found');
        return;
    }
    if (!title || !content) {
        console.error('Modal title or content not found');
        return;
    }

    if (!Array.isArray(clusterData)) {
        console.error('clusterData is not an array:', clusterData);
        content.innerHTML = '<p class="text-red-600">Error: Cluster data is not available.</p>';
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        return;
    }

    title.textContent = `Medical Visit Analytics - ${clusterLabel}`;

    // Find cluster by ID (handle both string and number comparisons)
    const cluster = clusterData.find(c => c.id == clusterId || c.id === clusterId);
    const visitsByPurok = cluster?.medical_analytics?.visits_by_purok || [];
    const illnesses = cluster?.medical_analytics?.illnesses || [];
    
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

    // Show modal
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
    console.log('Modal should be visible now');
}

window.openMedicineModal = function(clusterId, clusterLabel) {
    console.log('openMedicineModal called with:', clusterId, clusterLabel);
    console.log('clusterData type:', typeof clusterData, 'isArray:', Array.isArray(clusterData));
    
    const modal = document.getElementById('medicineModal');
    const title = document.getElementById('medicineModalTitle');
    const content = document.getElementById('medicineModalContent');

    if (!modal) {
        console.error('Modal element not found');
        return;
    }
    if (!title || !content) {
        console.error('Modal title or content not found');
        return;
    }

    if (!Array.isArray(clusterData)) {
        console.error('clusterData is not an array:', clusterData);
        content.innerHTML = '<p class="text-red-600">Error: Cluster data is not available.</p>';
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        return;
    }

    title.textContent = `Medicine Dispense Analytics - ${clusterLabel}`;

    // Find cluster by ID (handle both string and number comparisons)
    const cluster = clusterData.find(c => {
        const cId = c.id;
        const searchId = clusterId;
        return cId == searchId || cId === searchId || String(cId) === String(searchId);
    });
    
    console.log('Searching for clusterId:', clusterId, 'type:', typeof clusterId);
    console.log('Available cluster IDs:', clusterData.map(c => ({ id: c.id, type: typeof c.id, label: c.label })));
    console.log('Found cluster:', cluster);
    
    if (!cluster) {
        content.innerHTML = '<p class="text-red-600">Error: Cluster not found. Cluster ID: ' + clusterId + '</p>';
    } else {
        // Debug: Log the full cluster structure
        console.log('Full cluster structure:', JSON.stringify(cluster, null, 2));
        console.log('Cluster medicine_analytics:', cluster.medicine_analytics);
        console.log('Medicine analytics type:', typeof cluster.medicine_analytics);
        
        // Try multiple ways to access medicine data
        let medicinesData = cluster.medicine_analytics?.medicines || 
                           cluster.medicine_analytics || 
                           cluster.medicines || 
                           [];
        
        // If medicine_analytics is an object but not an array, try to extract medicines
        if (medicinesData && typeof medicinesData === 'object' && !Array.isArray(medicinesData)) {
            if (medicinesData.medicines && Array.isArray(medicinesData.medicines)) {
                medicinesData = medicinesData.medicines;
            } else {
                medicinesData = [];
            }
        }
        
        const medicines = Array.isArray(medicinesData) ? medicinesData : [];
        console.log('Final medicines array:', medicines);
        console.log('Medicines length:', medicines.length);
        
        if (medicines.length === 0) {
            content.innerHTML = '<div class="space-y-2">' +
                '<p class="text-gray-600">No medicine data available for this cluster.</p>' +
                '<p class="text-xs text-gray-500">Debug: medicine_analytics = ' + JSON.stringify(cluster.medicine_analytics) + '</p>' +
                '</div>';
        } else {
        let html = '<div class="space-y-4">';
        html += '<h4 class="font-semibold text-gray-900 mb-3">Medicines Dispensed (Most Common to Least Common)</h4>';
        html += '<div class="space-y-2">';
        
        medicines.forEach((item, index) => {
            console.log('Processing medicine item:', item);
            const name = item.name || item.medicine_name || 'Unknown Medicine';
            const dispensed = item.dispensed || item.quantity || item.total || 0;
            
            html += `
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-500 mr-3">#${index + 1}</span>
                            <span class="text-sm font-semibold text-gray-900">${escapeHtml(name)}</span>
                        </div>
                        <span class="text-sm font-bold text-blue-600">
                            <i class="fas fa-pills mr-1"></i>${dispensed} dispensed
                        </span>
                    </div>
                </div>
            `;
        });
        
        html += '</div></div>';
        content.innerHTML = html;
        }
    }

    // Show modal
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
    console.log('Modal should be visible now');
}

window.closeIncidentModal = function() {
    const modal = document.getElementById('incidentModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = ''; // Restore scrolling
    }
}

window.closeMedicalModal = function() {
    const modal = document.getElementById('medicalModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = ''; // Restore scrolling
    }
}

window.closeMedicineModal = function() {
    const modal = document.getElementById('medicineModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = ''; // Restore scrolling
    }
}

window.openPurokDetailsModal = function(clusterId, clusterLabel) {
    const modal = document.getElementById('purokDetailsModal');
    const title = document.getElementById('purokDetailsModalTitle');
    const content = document.getElementById('purokDetailsModalContent');

    if (!modal || !title || !content) {
        console.error('Purok details modal elements not found');
        return;
    }

    if (!Array.isArray(clusterData)) {
        console.error('clusterData is not an array:', clusterData);
        content.innerHTML = '<p class="text-red-600">Error: Cluster data is not available.</p>';
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        return;
    }

    const cluster = clusterData.find(c => c.id == clusterId || c.id === clusterId);
    if (!cluster || !cluster.puroks) {
        content.innerHTML = '<p class="text-gray-600">No purok data available for this cluster.</p>';
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        return;
    }

    title.textContent = `Purok Details - ${clusterLabel}`;

    let html = '<div class="space-y-4">';
    
    cluster.puroks.forEach((purok, index) => {
        html += `<div class="bg-gray-50 rounded-lg p-4 border border-gray-200">`;
        html += `<h4 class="font-semibold text-lg text-gray-900 mb-3">${escapeHtml(purok.purok_display || 'N/A')}</h4>`;
        
        // Summary Statistics
        html += '<div class="grid grid-cols-2 gap-3 mb-4">';
        html += `<div class="bg-white rounded p-2"><span class="text-gray-600 text-xs">Residents:</span> <span class="font-semibold">${purok.resident_count || 0}</span></div>`;
        html += `<div class="bg-white rounded p-2"><span class="text-gray-600 text-xs">Incidents:</span> <span class="font-semibold">${purok.blotter_count || 0}</span></div>`;
        html += `<div class="bg-white rounded p-2"><span class="text-gray-600 text-xs">Med. Visits:</span> <span class="font-semibold">${purok.medical_count || 0}</span></div>`;
        html += `<div class="bg-white rounded p-2"><span class="text-gray-600 text-xs">Medicine Dispensed:</span> <span class="font-semibold">${purok.medicine_count || 0}</span></div>`;
        html += '</div>';

        // Detailed Breakdown
        html += '<div class="mt-4 pt-4 border-t border-gray-300 space-y-3">';
        
        // Incidents Breakdown
        const incidents = purok.incident_analytics?.case_types || [];
        if (incidents.length > 0) {
            html += '<div class="text-sm">';
            html += '<p class="font-semibold text-gray-700 mb-2">Top Case Types:</p>';
            html += '<ul class="list-disc list-inside text-gray-600 space-y-1">';
            incidents.slice(0, 5).forEach(incident => {
                html += `<li>${escapeHtml(incident.type)} (${incident.count})</li>`;
            });
            html += '</ul></div>';
        }

        // Medical Visits Breakdown
        const visits = purok.medical_analytics?.visits_by_purok || [];
        const illnesses = purok.medical_analytics?.illnesses || [];
        if (visits.length > 0 || illnesses.length > 0) {
            html += '<div class="text-sm">';
            if (visits.length > 0) {
                html += `<p class="font-semibold text-gray-700 mb-1">Visits: ${visits[0].count || 0}</p>`;
            }
            if (illnesses.length > 0) {
                html += '<p class="font-semibold text-gray-700 mb-2 mt-2">Top Diagnoses:</p>';
                html += '<ul class="list-disc list-inside text-gray-600 space-y-1">';
                illnesses.slice(0, 5).forEach(illness => {
                    html += `<li>${escapeHtml(illness.illness)} (${illness.count})</li>`;
                });
                html += '</ul>';
            }
            html += '</div>';
        }

        // Medicine Breakdown (Dispensed only)
        let medicines = purok.medicine_analytics?.medicines || [];
        
        // Debug logging
        if (purok.medicine_count > 0 && medicines.length === 0) {
            console.warn('Purok has medicine_count > 0 but empty medicines array:', {
                purok: purok.purok_display,
                medicine_count: purok.medicine_count,
                medicine_analytics: purok.medicine_analytics
            });
        }
        
        if (medicines.length > 0) {
            html += '<div class="text-sm">';
            html += '<p class="font-semibold text-gray-700 mb-2">Top Dispensed Medicines:</p>';
            html += '<div class="space-y-2 mb-2">';
            const topMedicines = medicines.slice(0, 3);
            topMedicines.forEach((medicine, medIndex) => {
                const dispensed = medicine.dispensed || medicine.quantity || medicine.total || 0;
                html += `<div class="flex items-center justify-between bg-white rounded p-2">`;
                html += `<span class="text-gray-900">${escapeHtml(medicine.name)}</span>`;
                html += `<span class="text-xs font-semibold text-blue-600"><i class="fas fa-pills mr-1"></i>${dispensed} dispensed</span>`;
                html += '</div>';
            });
            html += '</div>';
            
            if (medicines.length > 3) {
                const totalDispensed = medicines.reduce((sum, m) => sum + (m.dispensed || m.quantity || m.total || 0), 0);
                const remainingMedicines = medicines.slice(3);
                const uniqueId = `medicines-${clusterId}-${index}`;
                html += `<details class="mt-2">`;
                html += `<summary class="text-blue-600 hover:text-blue-800 hover:underline text-xs italic cursor-pointer font-medium">`;
                html += `+ ${medicines.length - 3} more medicine(s). Total dispensed: ${totalDispensed}`;
                html += `</summary>`;
                html += `<div class="mt-2 space-y-2 pl-4 border-l-2 border-blue-200">`;
                remainingMedicines.forEach((medicine) => {
                    const dispensed = medicine.dispensed || medicine.quantity || medicine.total || 0;
                    html += `<div class="flex items-center justify-between bg-white rounded p-2">`;
                    html += `<span class="text-gray-900">${escapeHtml(medicine.name)}</span>`;
                    html += `<span class="text-xs font-semibold text-blue-600"><i class="fas fa-pills mr-1"></i>${dispensed} dispensed</span>`;
                    html += '</div>';
                });
                html += '</div>';
                html += '</details>';
            }
            html += '</div>';
        } else if (purok.medicine_count > 0) {
            // Show message if medicine_count > 0 but no detailed analytics
            html += '<div class="text-sm">';
            html += '<p class="font-semibold text-gray-700 mb-2">Medicine Dispense:</p>';
            html += `<p class="text-gray-600 text-xs">Total ${purok.medicine_count} medicine(s) dispensed, but detailed breakdown not available.</p>`;
            html += '</div>';
        }

        html += '</div></div>';
    });

    html += '</div>';
    content.innerHTML = html;

    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

window.openPurokMedicineModal = function(clusterId, clusterLabel, purokDisplay) {
    const modal = document.getElementById('medicineModal');
    const title = document.getElementById('medicineModalTitle');
    const content = document.getElementById('medicineModalContent');

    if (!modal || !title || !content) {
        console.error('Modal elements not found');
        return;
    }

    if (!Array.isArray(clusterData)) {
        console.error('clusterData is not an array:', clusterData);
        content.innerHTML = '<p class="text-red-600">Error: Cluster data is not available.</p>';
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        return;
    }

    const cluster = clusterData.find(c => c.id == clusterId || c.id === clusterId);
    if (!cluster || !cluster.puroks) {
        content.innerHTML = '<p class="text-gray-600">No purok data available.</p>';
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        return;
    }

    // Find the specific purok
    const purok = cluster.puroks.find(p => p.purok_display === purokDisplay);
    if (!purok) {
        content.innerHTML = '<p class="text-gray-600">Purok not found.</p>';
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        return;
    }

    title.textContent = `Medicine Dispense Analytics - ${purokDisplay} (${clusterLabel})`;

    const medicines = purok.medicine_analytics?.medicines || [];
    
    if (!purok) {
        content.innerHTML = '<p class="text-red-600">Error: Purok not found.</p>';
    } else if (medicines.length === 0) {
        content.innerHTML = '<p class="text-gray-600">No medicine data available for this purok.</p>';
    } else {
        let html = '<div class="space-y-4">';
        html += '<h4 class="font-semibold text-gray-900 mb-3">Medicines Dispensed (Most Common to Least Common)</h4>';
        html += '<div class="space-y-2">';
        
        medicines.forEach((item, index) => {
            const name = item.name || 'Unknown Medicine';
            const dispensed = item.dispensed || item.quantity || item.total || 0;
            
            html += `
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-500 mr-3">#${index + 1}</span>
                            <span class="text-sm font-semibold text-gray-900">${escapeHtml(name)}</span>
                        </div>
                        <span class="text-sm font-bold text-blue-600">
                            <i class="fas fa-pills mr-1"></i>${dispensed} dispensed
                        </span>
                    </div>
                </div>
            `;
        });
        
        html += '</div></div>';
        content.innerHTML = html;
    }

    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

window.closePurokDetailsModal = function() {
    const modal = document.getElementById('purokDetailsModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = ''; // Restore scrolling
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Event listeners - wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
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

    document.getElementById('closePurokDetailsModal')?.addEventListener('click', closePurokDetailsModal);
    document.getElementById('dismissPurokDetailsModal')?.addEventListener('click', closePurokDetailsModal);

    document.getElementById('purokDetailsModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closePurokDetailsModal();
        }
    });
});

</script>
@endpush
@endsection
