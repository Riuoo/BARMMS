/**
 * Clustering Analysis JavaScript
 * Core functionality for resident clustering analysis
 */

// Global variables
let clusteringData = {};
let charts = {};

function destroyChartIfExists(key) {
    if (charts[key] && typeof charts[key].destroy === 'function') {
        charts[key].destroy();
        charts[key] = null;
    }
}

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Chart initialization functions
function initializeClusterChart() {
    const ctx = document.getElementById('clusterChart');
    if (!ctx) return;

    const data = prepareClusterChartData();
    if (!data) return;

    destroyChartIfExists('clusterChart');
    charts.clusterChart = new Chart(ctx, {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: 12 },
                        padding: 20
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function prepareClusterChartData() {
    if (!clusteringData.characteristics || clusteringData.characteristics.length === 0) {
        return null;
    }

    const labels = [];
    const data = [];
    const colors = (window.clusteringConfig && window.clusteringConfig.colors && window.clusteringConfig.colors.clusters) 
        ? window.clusteringConfig.colors.clusters 
        : ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#F97316'];

    clusteringData.characteristics.forEach((cluster, index) => {
        labels.push(`Cluster ${index + 1}`);
        data.push(cluster.size || 0);
    });

    return {
        labels: labels,
        datasets: [{
            data: data,
            backgroundColor: colors.slice(0, labels.length),
            borderColor: '#fff',
            borderWidth: 3,
            hoverOffset: 4
        }]
    };
}

// Chart utility functions
function downloadChart() {
    const chart = charts.clusterChart;
    if (chart) {
        const link = document.createElement('a');
        link.download = 'clusterChart.png';
        link.href = chart.toBase64Image();
        link.click();
    }
}

function fullscreenChart() {
    const chart = charts.clusterChart;
    if (chart) {
        // Create fullscreen modal
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-lg p-6 max-w-4xl max-h-full overflow-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Chart Fullscreen</h3>
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div style="width: 800px; height: 600px;">
                    <canvas id="fullscreenChart"></canvas>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Create new chart instance
        const ctx = document.getElementById('fullscreenChart').getContext('2d');
        const data = prepareClusterChartData();
        new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 14 },
                            padding: 20
                        }
                    }
                }
            }
        });
    }
}

// Show Resident Details Modal
function showResidentDetailsModal() {
    const modal = document.getElementById('residentDetailsModal');
    const modalContent = document.getElementById('residentDetailsModalContent');
    const detailsSection = document.getElementById('detailedAnalysisSection');
    
    if (modal && modalContent && detailsSection) {
        // Get the inner content from the hidden section
        const innerContent = detailsSection.querySelector('div');
        if (innerContent) {
            // Clone the content
            const contentClone = innerContent.cloneNode(true);
            
            // Clear and populate modal
            modalContent.innerHTML = '';
            modalContent.appendChild(contentClone);
            
            // Show modal
            modal.classList.remove('hidden');
            
            // Initialize table features in the modal after a short delay
            setTimeout(() => {
                initializeTableFeatures();
            }, 150);
        }
    }
}

// Close Resident Details Modal
function closeResidentDetailsModal() {
    const modal = document.getElementById('residentDetailsModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Show Cluster Details Modal
function showClusterDetailsModal(clusterId, expandedView) {
    const modal = document.getElementById('clusterDetailsModal');
    const modalContent = document.getElementById('clusterDetailsModalContent');
    const modalTitle = document.getElementById('clusterDetailsModalTitle');
    
    if (modal && modalContent && expandedView) {
        // Get the cluster label from the card
        const clusterCard = expandedView.closest('.cluster-card');
        const clusterLabel = clusterCard?.querySelector('.bg-purple-600')?.textContent?.trim() || `Cluster ${parseInt(clusterId) + 1}`;
        
        // Clone the expanded view content
        const contentClone = expandedView.cloneNode(true);
        contentClone.classList.remove('hidden');
        
        // Update modal title
        if (modalTitle) {
            modalTitle.innerHTML = `<i class="fas fa-layer-group text-purple-600 mr-2"></i>${clusterLabel} - Details`;
        }
        
        // Clear and populate modal
        modalContent.innerHTML = '';
        modalContent.appendChild(contentClone);
        
        // Show modal
        modal.classList.remove('hidden');
    }
}

// Close Cluster Details Modal
function closeClusterDetailsModal() {
    const modal = document.getElementById('clusterDetailsModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Refresh analysis
function refreshAnalysis() {
    window.location.reload();
}

// Modal functions
function closeModal() {
    const modal = document.getElementById('analysisModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Event handlers for expandable sections
document.addEventListener('click', function(e) {
    if (e.target.closest('[data-action="show-cluster-details-modal"]')) {
        const button = e.target.closest('[data-action="show-cluster-details-modal"]');
        const clusterId = button.dataset.clusterId;
        const expandedView = document.getElementById(`expanded-cluster-${clusterId}`);
        
        if (expandedView) {
            showClusterDetailsModal(clusterId, expandedView);
        }
    }
    
    if (e.target.closest('[data-action="show-cluster-modal"]')) {
        const payload = JSON.parse(e.target.closest('[data-action="show-cluster-modal"]').dataset.payload);
        showClusterModal(payload);
    }
});

function showClusterModal(payload) {
    const modal = document.getElementById('analysisModal');
    const title = document.getElementById('modalTitle');
    const content = document.getElementById('modalContent');
    
    if (modal && title && content) {
        title.textContent = `Cluster ${payload.cluster?.label || 'Analysis'}`;
        content.innerHTML = generateClusterModalContent(payload);
        modal.classList.remove('hidden');
    }
}

function generateClusterModalContent(payload) {
    const cluster = payload.cluster || {};
    const traits = payload.traits || [];
    const insights = payload.insights || {};
    
    function getTopWithConfidence(counts, total) {
        if (!counts || typeof counts !== 'object') return null;
        let topKey = null;
        let topVal = 0;
        Object.keys(counts).forEach(k => {
            const v = counts[k] || 0;
            if (v > topVal) { topVal = v; topKey = k; }
        });
        if (!topKey) return null;
        const pct = total > 0 ? Math.round((topVal / total) * 100) : 0;
        return { key: topKey, count: topVal, pct };
    }

    function formatOneDecimal(value) {
        const num = Number(value);
        if (!isFinite(num)) return 'N/A';
        return Math.round(num).toString();
    }

    const total = (typeof insights.total === 'number') ? insights.total : (cluster.size || 0);
    const topIncome = getTopWithConfidence(cluster.income_distribution, total);
    const topEmployment = getTopWithConfidence(cluster.employment_distribution, total);
    const topHealth = getTopWithConfidence(cluster.health_distribution, total);

    const topProgram = getTopWithConfidence(insights.program, insights.total || total);
    const topEligibility = getTopWithConfidence(insights.eligibility, insights.total || total);
    const topRisk = getTopWithConfidence(insights.risk, insights.total || total);

    return `
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 p-3 rounded">
                    <h4 class="font-semibold text-gray-700">Size</h4>
                    <p class="text-lg font-bold text-blue-600">${cluster.size || 0} residents</p>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <h4 class="font-semibold text-gray-700">Average Age</h4>
                    <p class="text-lg font-bold text-green-600">${formatOneDecimal(cluster.avg_age)}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <h4 class="font-semibold text-gray-700">Average Family Size</h4>
                    <p class="text-lg font-bold text-indigo-600">${formatOneDecimal(cluster.avg_family_size)}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <h4 class="font-semibold text-gray-700">Most Common Purok</h4>
                    <p class="text-lg font-bold text-rose-600">${cluster.most_common_purok || 'N/A'}</p>
                </div>
            </div>

            ${(topIncome || topEmployment || topHealth) ? `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="bg-white border border-gray-200 rounded p-3">
                        <h4 class="text-xs font-semibold text-gray-600">Income</h4>
                        <p class="text-sm text-gray-800">${topIncome ? `${topIncome.key} (${topIncome.pct}%)` : 'N/A'}</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded p-3">
                        <h4 class="text-xs font-semibold text-gray-600">Employment</h4>
                        <p class="text-sm text-gray-800">${topEmployment ? `${topEmployment.key} (${topEmployment.pct}%)` : 'N/A'}</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded p-3">
                        <h4 class="text-xs font-semibold text-gray-600">Health</h4>
                        <p class="text-sm text-gray-800">${topHealth ? `${topHealth.key} (${topHealth.pct}%)` : 'N/A'}</p>
                    </div>
                </div>
            ` : ''}

            ${traits.length > 0 ? `
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Top Traits</h4>
                    <div class="flex flex-wrap gap-2">
                        ${traits.map(trait => `
                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                ${trait.label}
                            </span>
                        `).join('')}
                    </div>
                </div>
            ` : ''}

            ${(topProgram || topEligibility || topRisk) ? `
                <div class="flex flex-wrap gap-2">
                    ${topProgram ? `<span class=\"inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800\"><i class=\"fas fa-magic mr-1\"></i> Program: ${topProgram.key} (${topProgram.pct}%)</span>` : ''}
                    ${topEligibility ? `<span class=\"inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800\"><i class=\"fas fa-check mr-1\"></i> Eligibility: ${topEligibility.key} (${topEligibility.pct}%)</span>` : ''}
                    ${topRisk ? `<span class=\"inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800\"><i class=\"fas fa-heartbeat mr-1\"></i> Risk: ${topRisk.key} (${topRisk.pct}%)</span>` : ''}
                </div>
            ` : ''}
        </div>
    `;
}

// Table features initialization
function initializeTableFeatures() {
    const searchInput = document.getElementById('searchTable');
    const rowsPerPageSelect = document.getElementById('rowsPerPage');
    const table = document.getElementById('residentsTable');
    
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    const allRows = Array.from(tbody.querySelectorAll('tr'));
    const emptyState = document.getElementById('emptyState');
    const loadingDiv = document.getElementById('tableLoading');
    const paginationControls = document.getElementById('paginationControls');
    const paginationInfo = document.getElementById('paginationInfo');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const currentPageSpan = document.getElementById('currentPage');
    
    if (!allRows.length) return;
    
    let currentPage = 1;
    let rowsPerPage = parseInt(rowsPerPageSelect.value);
    let filteredRows = allRows;

    function showLoading(show) {
        if (loadingDiv) loadingDiv.classList.toggle('hidden', !show);
        if (table) table.classList.toggle('opacity-50', show);
    }

    function updateTable() {
        showLoading(true);
        setTimeout(() => {
            // Hide all rows
            allRows.forEach(row => row.style.display = 'none');
            // Show filtered rows for current page
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            let anyVisible = false;
            filteredRows.slice(start, end).forEach(row => {
                row.style.display = '';
                anyVisible = true;
            });
            // Empty state
            if (emptyState) emptyState.classList.toggle('hidden', anyVisible);
            // Pagination info
            const total = filteredRows.length;
            const totalPages = Math.ceil(total / rowsPerPage) || 1;
            if (paginationInfo) paginationInfo.textContent = `Showing ${total ? start + 1 : 0} to ${Math.min(end, total)} of ${total} residents`;
            if (currentPageSpan) currentPageSpan.textContent = `${currentPage} / ${totalPages}`;
            if (prevPageBtn) prevPageBtn.disabled = currentPage === 1;
            if (nextPageBtn) nextPageBtn.disabled = currentPage === totalPages;
            showLoading(false);
        }, 200); // Simulate loading
    }

    function filterRows() {
        showLoading(true);
        setTimeout(() => {
            const searchTerm = searchInput.value.toLowerCase();
            filteredRows = allRows.filter(row => {
                let match = true;
                if (searchTerm) {
                    match = row.textContent.toLowerCase().includes(searchTerm);
                }
                return match;
            });
            currentPage = 1;
            updateTable();
        }, 200);
    }

    // Debounced search
    if (searchInput) searchInput.addEventListener('input', debounce(filterRows, 300));
    if (rowsPerPageSelect) {
        rowsPerPageSelect.addEventListener('change', function() {
            rowsPerPage = parseInt(this.value);
            currentPage = 1;
            updateTable();
        });
    }
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', function() {
            if (currentPage > 1) { currentPage--; updateTable(); }
        });
    }
    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', function() {
            const totalPages = Math.ceil(filteredRows.length / rowsPerPage) || 1;
            if (currentPage < totalPages) { currentPage++; updateTable(); }
        });
    }

    // Initial render
    filterRows();
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize clustering data from embedded JSON
    try {
        const el = document.getElementById('clustering-data');
        if (el) {
            clusteringData = JSON.parse(el.textContent || '{}');
        }
    } catch (e) {
        console.error('Error parsing clustering data:', e);
        clusteringData = {};
    }
    
    // Initialize charts based on mode
    initializeClusterChart();
});
