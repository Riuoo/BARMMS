/**
 * Clustering Charts JavaScript
 * Advanced chart initialization and analytics for clustering analysis
 */

// Global chart instances
let analyticsCharts = {};

// Initialize all analytics charts
function initializeAnalyticsCharts() {
    // Prepare chart data from the characteristics
    const chartData = prepareAnalyticsChartData();
    
    if (chartData) {
        initializeIncomeChart(chartData);
        initializeEmploymentChart(chartData);
        initializeAgeChart(chartData);
        initializeHealthChart(chartData);
        initializeComparativeChart(chartData);
    }
}

// Prepare comprehensive chart data from clustering characteristics
function prepareAnalyticsChartData() {
    if (!window.clusteringData || !window.clusteringData.characteristics) {
        console.warn('No clustering data available for charts');
        return null;
    }

    const characteristics = window.clusteringData.characteristics;
    const colors = (window.clusteringConfig && window.clusteringConfig.colors && window.clusteringConfig.colors.clusters)
        ? window.clusteringConfig.colors.clusters
        : ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#F97316'];
    
    return characteristics.map((cluster, index) => {
        // Extract data from cluster characteristics
        const incomeDistribution = cluster.income_distribution || {};
        const employmentDistribution = cluster.employment_distribution || {};
        const healthDistribution = cluster.health_distribution || {};
        
        // Prepare age data (simulate from average age)
        const avgAge = cluster.avg_age || 35;
        const ages = generateAgeArray(avgAge, cluster.size || 0);
        
        // Prepare family size data
        const avgFamilySize = cluster.avg_family_size || 4;
        const familySizes = generateFamilySizeArray(avgFamilySize, cluster.size || 0);
        
        return {
            label: `Cluster ${index + 1}`,
            color: colors[index % colors.length],
            size: cluster.size || 0,
            total: cluster.size || 0,
            ages: ages,
            familySizes: familySizes,
            incomes: {
                'Low': incomeDistribution['Low'] || 0,
                'Lower Middle': incomeDistribution['Lower Middle'] || 0,
                'Middle': incomeDistribution['Middle'] || 0,
                'Upper Middle': incomeDistribution['Upper Middle'] || 0,
                'High': incomeDistribution['High'] || 0
            },
            employments: {
                'Unemployed': employmentDistribution['Unemployed'] || 0,
                'Part-time': employmentDistribution['Part-time'] || 0,
                'Self-employed': employmentDistribution['Self-employed'] || 0,
                'Full-time': employmentDistribution['Full-time'] || 0
            },
            healths: {
                'Critical': healthDistribution['Critical'] || 0,
                'Poor': healthDistribution['Poor'] || 0,
                'Fair': healthDistribution['Fair'] || 0,
                'Good': healthDistribution['Good'] || 0,
                'Excellent': healthDistribution['Excellent'] || 0
            }
        };
    });
}

// Generate realistic age array based on average
function generateAgeArray(avgAge, size) {
    const ages = [];
    const stdDev = 15; // Standard deviation for age distribution
    
    for (let i = 0; i < size; i++) {
        // Generate normally distributed age
        const age = Math.max(0, Math.round(avgAge + (Math.random() - 0.5) * stdDev * 2));
        ages.push(age);
    }
    
    return ages;
}

// Generate realistic family size array
function generateFamilySizeArray(avgFamilySize, size) {
    const familySizes = [];
    const stdDev = 2;
    
    for (let i = 0; i < size; i++) {
        const size = Math.max(1, Math.round(avgFamilySize + (Math.random() - 0.5) * stdDev * 2));
        familySizes.push(size);
    }
    
    return familySizes;
}

// Income Level Distribution Chart
function initializeIncomeChart(chartData) {
    const ctx = document.getElementById('incomeChart');
    if (!ctx) return;

    const incomeLabels = ['Low', 'Lower Middle', 'Middle', 'Upper Middle', 'High'];
    
    const datasets = chartData.map((data) => ({
        label: data.label,
        data: incomeLabels.map(label => data.incomes[label] || 0),
        backgroundColor: data.color + '80',
        borderColor: data.color,
        borderWidth: 2,
        borderRadius: 4,
        borderSkipped: false
    }));
    
    analyticsCharts.incomeChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: incomeLabels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'nearest', intersect: true },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { 
                        font: { size: 11 },
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    enabled: true,
                    mode: 'nearest',
                    intersect: true,
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            const value = context.parsed.y ?? context.parsed;
                            const datasetLabel = context.dataset.label;
                            return `${datasetLabel}: ${value}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Number of Residents' },
                    grid: { color: 'rgba(0,0,0,0.1)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
}

// Employment Status Distribution Chart
function initializeEmploymentChart(chartData) {
    const ctx = document.getElementById('employmentChart');
    if (!ctx) return;

    const employmentLabels = ['Unemployed', 'Part-time', 'Self-employed', 'Full-time'];
    const employmentColors = (window.clusteringConfig && window.clusteringConfig.colors && window.clusteringConfig.colors.employment)
        ? window.clusteringConfig.colors.employment
        : ['#EF4444', '#F59E0B', '#8B5CF6', '#10B981'];
    
    // Create pie chart for overall employment distribution
    const totalEmployments = { 'Unemployed': 0, 'Part-time': 0, 'Self-employed': 0, 'Full-time': 0 };
    chartData.forEach(data => {
        employmentLabels.forEach(label => {
            totalEmployments[label] += data.employments[label] || 0;
        });
    });
    
    analyticsCharts.employmentChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: employmentLabels,
            datasets: [{
                data: employmentLabels.map(label => totalEmployments[label]),
                backgroundColor: employmentColors,
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        font: { size: 11 },
                        padding: 15
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

// Age Distribution Chart
function initializeAgeChart(chartData) {
    const ctx = document.getElementById('ageChart');
    if (!ctx) return;

    const ageRanges = ['0-20', '21-40', '41-60', '61-80', '80+'];
    const ageColors = ['#8B5CF6', '#3B82F6', '#10B981', '#F59E0B', '#EF4444'];
    
    const datasets = chartData.map((data, idx) => {
        const ageCounts = [0, 0, 0, 0, 0];
        data.ages.forEach(age => {
            if (age <= 20) ageCounts[0]++;
            else if (age <= 40) ageCounts[1]++;
            else if (age <= 60) ageCounts[2]++;
            else if (age <= 80) ageCounts[3]++;
            else ageCounts[4]++;
        });
        
        return {
            label: data.label,
            data: ageCounts,
            backgroundColor: data.color + '80',
            borderColor: data.color,
            borderWidth: 2,
            borderRadius: 4,
            borderSkipped: false
        };
    });
    
    analyticsCharts.ageChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ageRanges,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { 
                        font: { size: 11 },
                        usePointStyle: true,
                        padding: 15
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Number of Residents' },
                    grid: { color: 'rgba(0,0,0,0.1)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
}

// Health Status Distribution Chart
function initializeHealthChart(chartData) {
    const ctx = document.getElementById('healthChart');
    if (!ctx) return;

    const healthLabels = ['Critical', 'Poor', 'Fair', 'Good', 'Excellent'];
    const healthColors = (window.clusteringConfig && window.clusteringConfig.colors && window.clusteringConfig.colors.health)
        ? window.clusteringConfig.colors.health
        : ['#EF4444', '#F97316', '#F59E0B', '#3B82F6', '#10B981'];
    
    // Create stacked bar chart for health distribution
    const datasets = healthLabels.map((label, idx) => ({
        label: label,
        data: chartData.map(data => data.healths[label] || 0),
        backgroundColor: healthColors[idx],
        borderColor: healthColors[idx],
        borderWidth: 1
    }));
    
    analyticsCharts.healthChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.map(data => data.label),
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { 
                        font: { size: 11 },
                        usePointStyle: true,
                        padding: 15
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    grid: { display: false }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    title: { display: true, text: 'Number of Residents' },
                    grid: { color: 'rgba(0,0,0,0.1)' }
                }
            }
        }
    });
}

// Comparative Chart (Line Chart)
function initializeComparativeChart(chartData) {
    const ctx = document.getElementById('comparativeChart');
    if (!ctx) return;

    const labels = chartData.map(data => data.label);
    
    // Calculate metrics for comparison
    const avgAges = chartData.map(data => {
        if (data.ages.length === 0) return 0;
        return Math.round(data.ages.reduce((a, b) => a + b, 0) / data.ages.length);
    });
    
    const avgFamilySizes = chartData.map(data => {
        if (data.familySizes.length === 0) return 0;
        return Math.round((data.familySizes.reduce((a, b) => a + b, 0) / data.familySizes.length) * 10) / 10;
    });
    
    analyticsCharts.comparativeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Average Age',
                data: avgAges,
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                yAxisID: 'y'
            }, {
                label: 'Average Family Size',
                data: avgFamilySizes,
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                yAxisID: 'y1'
            }, {
                label: 'Total Residents',
                data: chartData.map(data => data.total),
                borderColor: '#F59E0B',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                yAxisID: 'y2'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { size: 11 } }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Average Age' },
                    grid: { color: 'rgba(0,0,0,0.1)' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'Average Family Size' },
                    grid: { drawOnChartArea: false }
                },
                y2: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'Total Residents' },
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });
}

// Download all charts as images
function downloadAllCharts() {
    const chartIds = ['incomeChart', 'employmentChart', 'ageChart', 'healthChart', 'comparativeChart'];
    chartIds.forEach(chartId => {
        const chart = analyticsCharts[chartId];
        if (chart) {
            const link = document.createElement('a');
            link.download = `${chartId}.png`;
            link.href = chart.toBase64Image();
            link.click();
        }
    });
}

// Initialize charts when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Small delay to ensure clustering.js has loaded the data
    setTimeout(() => {
        initializeAnalyticsCharts();
    }, 100);
});
