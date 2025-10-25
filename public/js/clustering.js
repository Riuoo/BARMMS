/**
 * Clustering Analysis JavaScript
 * Core functionality for resident clustering analysis
 */

// Global variables
let clusteringData = {};
let charts = {};

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

function initializePurokChart() {
    const ctx = document.getElementById('purokChart');
    if (!ctx) return;

    const data = preparePurokChartData();
    if (!data) return;

    charts.purokChart = new Chart(ctx, {
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
    const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#F97316'];

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

function preparePurokChartData() {
    if (!clusteringData.grouped || Object.keys(clusteringData.grouped).length === 0) {
        return null;
    }

    const labels = [];
    const data = [];
    const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#F97316', '#EC4899', '#06B6D4'];

    Object.keys(clusteringData.grouped).forEach((purok, index) => {
        if (purok !== 'N/A') {
            labels.push(`Purok ${purok}`);
            data.push(clusteringData.grouped[purok].length || 0);
        }
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
    const chartId = clusteringData.isHier ? 'purokChart' : 'clusterChart';
    const chart = charts[chartId];
    if (chart) {
        const link = document.createElement('a');
        link.download = `${chartId}.png`;
        link.href = chart.toBase64Image();
        link.click();
    }
}

function fullscreenChart() {
    const chartId = clusteringData.isHier ? 'purokChart' : 'clusterChart';
    const chart = charts[chartId];
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
        const data = clusteringData.isHier ? preparePurokChartData() : prepareClusterChartData();
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

// Toggle functions
function toggleDetails() {
    const details = document.getElementById('detailedAnalysisSection');
    const toggleBtn = document.getElementById('toggleDetailsBtn');
    const icon = toggleBtn.querySelector('i');
    const btnText = toggleBtn.querySelector('.btn-text') || toggleBtn.querySelector('span');

    if (details.classList.contains('hidden')) {
        details.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
        if (btnText) btnText.textContent = ' Hide Details';
    } else {
        details.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
        if (btnText) btnText.textContent = ' Show Details';
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
    if (e.target.closest('[data-action="toggle-cluster-details"]')) {
        const clusterId = e.target.closest('[data-action="toggle-cluster-details"]').dataset.clusterId;
        const expandedView = document.getElementById(`expanded-cluster-${clusterId}`);
        const icon = document.getElementById(`cluster-icon-${clusterId}`);
        
        if (expandedView && icon) {
            if (expandedView.classList.contains('hidden')) {
                expandedView.classList.remove('hidden');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                expandedView.classList.add('hidden');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
    }
    
    if (e.target.closest('[data-action="toggle-purok-details"]')) {
        const purok = e.target.closest('[data-action="toggle-purok-details"]').dataset.purok;
        const expandedView = document.getElementById(`expanded-${purok}`);
        const icon = document.getElementById(`icon-${purok}`);
        
        if (expandedView && icon) {
            if (expandedView.classList.contains('hidden')) {
                expandedView.classList.remove('hidden');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                expandedView.classList.add('hidden');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
    }
    
    if (e.target.closest('[data-action="show-cluster-modal"]')) {
        const payload = JSON.parse(e.target.closest('[data-action="show-cluster-modal"]').dataset.payload);
        showClusterModal(payload);
    }
    
    if (e.target.closest('[data-action="show-purok-modal"]')) {
        const payload = JSON.parse(e.target.closest('[data-action="show-purok-modal"]').dataset.payload);
        showPurokModal(payload);
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

function showPurokModal(payload) {
    const modal = document.getElementById('analysisModal');
    const title = document.getElementById('modalTitle');
    const content = document.getElementById('modalContent');
    
    if (modal && title && content) {
        title.textContent = `Purok ${payload.purok || 'Analysis'}`;
        content.innerHTML = generatePurokModalContent(payload);
        modal.classList.remove('hidden');
    }
}

function generateClusterModalContent(payload) {
    const cluster = payload.cluster || {};
    const traits = payload.traits || [];
    const insights = payload.insights || {};
    
    return `
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 p-3 rounded">
                    <h4 class="font-semibold text-gray-700">Size</h4>
                    <p class="text-lg font-bold text-blue-600">${cluster.size || 0} residents</p>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <h4 class="font-semibold text-gray-700">Average Age</h4>
                    <p class="text-lg font-bold text-green-600">${cluster.avg_age || 'N/A'}</p>
                </div>
            </div>
            
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
            
            ${insights.program ? `
                <div class="bg-purple-50 p-3 rounded">
                    <h4 class="font-semibold text-purple-700">Recommended Program</h4>
                    <p class="text-purple-600">${insights.program}</p>
                </div>
            ` : ''}
        </div>
    `;
}

function generatePurokModalContent(payload) {
    return `
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 p-3 rounded">
                    <h4 class="font-semibold text-gray-700">Total Residents</h4>
                    <p class="text-lg font-bold text-blue-600">${payload.total || 0}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <h4 class="font-semibold text-gray-700">Average Age</h4>
                    <p class="text-lg font-bold text-green-600">${payload.avgAge || 'N/A'}</p>
                </div>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Most Common Characteristics</h4>
                <div class="space-y-2">
                    <p><strong>Income:</strong> ${payload.mostCommonIncome || 'N/A'}</p>
                    <p><strong>Employment:</strong> ${payload.mostCommonEmployment || 'N/A'}</p>
                    <p><strong>Health:</strong> ${payload.mostCommonHealth || 'N/A'}</p>
                </div>
            </div>
        </div>
    `;
}

// Table features initialization
function initializeTableFeatures() {
    const searchInput = document.getElementById('searchTable');
    const filterPurok = document.getElementById('filterPurok');
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

    // Purok is always the 7th column (1-based index)
    const purokColIdx = 7;

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
            const selectedPurok = filterPurok.value.trim().toUpperCase();
            filteredRows = allRows.filter(row => {
                let match = true;
                if (searchTerm) {
                    match = row.textContent.toLowerCase().includes(searchTerm);
                }
                if (match && selectedPurok) {
                    // Use correct column index for purok
                    const purokCell = row.querySelector(`td:nth-child(${purokColIdx})`);
                    const val = (purokCell ? purokCell.textContent : '').trim().toUpperCase();
                    match = val.includes(selectedPurok);
                }
                return match;
            });
            currentPage = 1;
            updateTable();
        }, 200);
    }

    // Debounced search
    if (searchInput) searchInput.addEventListener('input', debounce(filterRows, 300));
    if (filterPurok) filterPurok.addEventListener('change', filterRows);
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
    if (clusteringData.isHier) {
        initializePurokChart();
    } else {
        initializeClusterChart();
    }
});
