// Clustering Analysis JavaScript Functions
// This file contains all the JavaScript functionality for the clustering analysis view

// Utility Functions
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// Helper function to check if value is numeric
function is_numeric(value) {
    return !isNaN(parseFloat(value)) && isFinite(value);
}

// Toggle Functions
function togglePurokGroup(purok) {
    const clustersDiv = document.getElementById('purok-clusters-' + purok);
    const chevron = document.getElementById('chevron-' + purok);
    if (clustersDiv.classList.contains('hidden')) {
        clustersDiv.classList.remove('hidden');
        chevron.classList.remove('rotate-180');
    } else {
        clustersDiv.classList.add('hidden');
        chevron.classList.add('rotate-180');
    }
}

function toggleDetails() {
    const section = document.getElementById('detailedAnalysisSection');
    const btn = document.getElementById('toggleDetailsBtn');
    if (!section || !btn) return;
    const icon = btn.querySelector('i');
    const state = btn.getAttribute('data-state') || 'hidden';
    if (state === 'hidden') {
        section.classList.remove('hidden');
        if (icon) { icon.classList.remove('fa-chevron-down'); icon.classList.add('fa-chevron-up'); }
        btn.setAttribute('data-state', 'shown');
        btn.querySelector('span.btn-text').textContent = ' Hide Details';
    } else {
        section.classList.add('hidden');
        if (icon) { icon.classList.remove('fa-chevron-up'); icon.classList.add('fa-chevron-down'); }
        btn.setAttribute('data-state', 'hidden');
        btn.querySelector('span.btn-text').textContent = ' Show Details';
    }
}

function togglePurokDetails(purok) {
    const expandedView = document.getElementById(`expanded-${purok}`);
    const icon = document.getElementById(`icon-${purok}`);
    const button = icon.parentElement;
    
    if (expandedView.classList.contains('hidden')) {
        expandedView.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
        button.innerHTML = button.innerHTML.replace('Show Details', 'Hide Details');
    } else {
        expandedView.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
        button.innerHTML = button.innerHTML.replace('Hide Details', 'Show Details');
    }
}

function toggleClusterDetails(clusterId) {
    const expandedView = document.getElementById(`expanded-cluster-${clusterId}`);
    const icon = document.getElementById(`cluster-icon-${clusterId}`);
    const button = icon.parentElement;
    
    if (expandedView.classList.contains('hidden')) {
        expandedView.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
        button.innerHTML = button.innerHTML.replace('Show Details', 'Hide Details');
    } else {
        expandedView.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
        button.innerHTML = button.innerHTML.replace('Hide Details', 'Show Details');
    }
}

// Action Functions
function refreshAnalysis() {
    location.reload();
}

function exportData() {
    // Create download link for CSV
    const link = document.createElement('a');
    link.href = '/admin/clustering/export?format=csv';
    link.download = 'clustering_results.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function downloadChart() {
    const canvas = document.getElementById('clusterChart');
    if (canvas) {
        const link = document.createElement('a');
        link.download = 'cluster_chart.png';
        link.href = canvas.toDataURL();
        link.click();
    }
}

function fullscreenChart() {
    const chartContainer = document.querySelector('.chart-container');
    if (chartContainer.requestFullscreen) {
        chartContainer.requestFullscreen();
    }
}

function viewResident(residentId) {
    // Open resident details in modal or redirect
    window.open(`/admin/residents/${residentId}`, '_blank');
}

function downloadAllCharts() {
    // Download all charts as images
    const charts = ['incomeChart', 'employmentChart', 'ageChart', 'healthChart', 'comparativeChart'];
    charts.forEach(chartId => {
        const canvas = document.getElementById(chartId);
        if (canvas) {
            const link = document.createElement('a');
            link.download = `${chartId}.png`;
            link.href = canvas.toDataURL();
            link.click();
        }
    });
}

// Modal Functions
function showPurokModal(purok, data) {
    const modal = document.getElementById('analysisModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    
    modalTitle.textContent = `Purok ${purok} - Complete Analysis`;
    
    let content = `
        <div class="space-y-4">
            <div class="bg-indigo-50 p-4 rounded-lg">
                <h4 class="font-semibold text-indigo-900 mb-2">Overview</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="font-medium">Total Residents:</span> ${data.total}</div>
                    <div><span class="font-medium">Label:</span> ${data.label}</div>
                </div>
            </div>
            
            <div class="bg-green-50 p-4 rounded-lg">
                <h4 class="font-semibold text-green-900 mb-2">Demographics</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="font-medium">Average Age:</span> ${data.avgAge} ± ${data.stdAge}</div>
                    <div><span class="font-medium">Average Family Size:</span> ${data.avgFamilySize} ± ${data.stdFamilySize}</div>
                </div>
            </div>
            
            <div class="bg-yellow-50 p-4 rounded-lg">
                <h4 class="font-semibold text-yellow-900 mb-2">Most Common Values</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="font-medium">Age:</span> ${data.mostCommonAge}</div>
                    <div><span class="font-medium">Family Size:</span> ${data.mostCommonFamilySize}</div>
                    <div><span class="font-medium">Income:</span> ${data.mostCommonIncome}</div>
                    <div><span class="font-medium">Employment:</span> ${data.mostCommonEmployment}</div>
                    <div><span class="font-medium">Health:</span> ${data.mostCommonHealth}</div>
                </div>
            </div>
    `;
    
    if (data.pins) {
        content += `
            <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-semibold text-blue-900 mb-2">AI Predictions</h4>
                <div class="space-y-2 text-sm">
                    ${data.pins.program ? `<div><span class="font-medium">Recommended Program:</span> ${data.pins.program} (${data.pins.program_confidence}%)</div>` : ''}
                    ${data.pins.eligibility ? `<div><span class="font-medium">Service Eligibility:</span> ${data.pins.eligibility} (${data.pins.eligibility_confidence}%)</div>` : ''}
                    ${data.pins.risk ? `<div><span class="font-medium">Health Risk:</span> ${data.pins.risk} (${data.pins.risk_confidence}%)</div>` : ''}
                </div>
            </div>
        `;
    }
    
    content += `
        </div>
    `;
    
    modalContent.innerHTML = content;
    modal.classList.remove('hidden');
}

function showClusterModal(clusterId, data) {
    const modal = document.getElementById('analysisModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    
    modalTitle.textContent = `Cluster ${parseInt(clusterId) + 1} - Complete Analysis`;
    
    let content = `
        <div class="space-y-4">
            <div class="bg-purple-50 p-4 rounded-lg">
                <h4 class="font-semibold text-purple-900 mb-2">Overview</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="font-medium">Size:</span> ${data.cluster.size} (${data.cluster.percent_of_total || '?'}%)</div>
                    <div><span class="font-medium">Label:</span> ${data.cluster.label || 'N/A'}</div>
                    ${data.cluster.outlier_count ? `<div><span class="font-medium">Outliers:</span> ${data.cluster.outlier_count}</div>` : ''}
                </div>
            </div>
            
            <div class="bg-green-50 p-4 rounded-lg">
                <h4 class="font-semibold text-green-900 mb-2">Demographics</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="font-medium">Average Age:</span> ${data.cluster.avg_age || 'N/A'} ± ${data.cluster.std_age || 'N/A'}</div>
                    <div><span class="font-medium">Average Family Size:</span> ${data.cluster.avg_family_size || 'N/A'} ± ${data.cluster.std_family_size || 'N/A'}</div>
                </div>
            </div>
            
            <div class="bg-red-50 p-4 rounded-lg">
                <h4 class="font-semibold text-red-900 mb-2">Top Traits</h4>
                <div class="flex flex-wrap gap-2">
                    ${Array.isArray(data.traits) ? data.traits.map(trait => {
                        const label = (trait && typeof trait === 'object') ? (trait.label || JSON.stringify(trait)) : String(trait ?? '');
                        return `<span class=\"inline-block px-2 py-1 rounded-full bg-red-100 text-red-800 text-xs font-bold\">${label}</span>`;
                    }).join('') : ''}
                </div>
            </div>
    `;
    
    if (data.insights) {
        content += `
            <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-semibold text-blue-900 mb-2">AI Predictions</h4>
                <div class="space-y-2 text-sm">
                    ${Object.keys(data.insights.program || {}).length > 0 ? `<div><span class="font-medium">Programs:</span> ${Object.entries(data.insights.program).map(([k,v]) => `${k}: ${v}`).join(', ')}</div>` : ''}
                    ${Object.keys(data.insights.eligibility || {}).length > 0 ? `<div><span class="font-medium">Eligibility:</span> ${Object.entries(data.insights.eligibility).map(([k,v]) => `${k}: ${v}`).join(', ')}</div>` : ''}
                    ${Object.keys(data.insights.risk || {}).length > 0 ? `<div><span class="font-medium">Risk:</span> ${Object.entries(data.insights.risk).map(([k,v]) => `${k}: ${v}`).join(', ')}</div>` : ''}
                </div>
            </div>
        `;
    }
    
    content += `
        </div>
    `;
    
    modalContent.innerHTML = content;
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('analysisModal').classList.add('hidden');
}

// Export functions to global scope
window.togglePurokGroup = togglePurokGroup;
window.toggleDetails = toggleDetails;
window.togglePurokDetails = togglePurokDetails;
window.toggleClusterDetails = toggleClusterDetails;
window.refreshAnalysis = refreshAnalysis;
window.exportData = exportData;
window.downloadChart = downloadChart;
window.fullscreenChart = fullscreenChart;
window.viewResident = viewResident;
window.downloadAllCharts = downloadAllCharts;
window.showPurokModal = showPurokModal;
window.showClusterModal = showClusterModal;
window.closeModal = closeModal;

// Event delegation for buttons migrated from inline handlers
document.addEventListener('click', function(e) {
    const btn = e.target.closest('button');
    if (!btn) return;
    const action = btn.getAttribute('data-action');
    if (!action) return;
    function parsePayload(raw) {
        if (!raw) return null;
        try { return JSON.parse(raw); } catch (_) {}
        try {
            // Decode HTML entities then parse
            const ta = document.createElement('textarea');
            ta.innerHTML = raw;
            const decoded = ta.value;
            return JSON.parse(decoded);
        } catch (_) {
            return null;
        }
    }
    if (action === 'view-resident') {
        const id = btn.getAttribute('data-resident-id');
        if (id) viewResident(id);
    } else if (action === 'toggle-purok-details') {
        const purok = btn.getAttribute('data-purok');
        if (purok) togglePurokDetails(purok);
    } else if (action === 'toggle-cluster-details') {
        const cid = btn.getAttribute('data-cluster-id');
        if (cid !== null) toggleClusterDetails(cid);
    } else if (action === 'show-purok-modal') {
        const purok = btn.getAttribute('data-purok');
        const payloadRaw = btn.getAttribute('data-payload');
        const payload = parsePayload(payloadRaw);
        if (payload) showPurokModal(purok, payload);
    } else if (action === 'show-cluster-modal') {
        const cid = btn.getAttribute('data-cluster-id');
        const payloadRaw = btn.getAttribute('data-payload');
        const payload = parsePayload(payloadRaw);
        if (payload) showClusterModal(cid, payload);
    }
});
