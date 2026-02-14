let charts = {};
let selectedPlanets = planetsData.map(p => p.name);



function createSizeChart() {
    const ctx = document.getElementById('sizeChart').getContext('2d');
    const filteredData = planetsData.filter(p => selectedPlanets.includes(p.name));
    
    if (charts.sizeChart) charts.sizeChart.destroy();
    
    charts.sizeChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: filteredData.map(p => p.name),
            datasets: [{
                label: 'Diametre (km)',
                data: filteredData.map(p => p.diameter_km),
                backgroundColor: filteredData.map(p => p.color),
                borderColor: filteredData.map(p => p.color),
                borderWidth: 2
            }]
        },  
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            return context.parsed.y.toLocaleString() + ' km';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: '#fff' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                },
                x: {
                    ticks: { color: '#fff' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                }
            }
        }
    });
} 

function createTemperatureChart() {
    const ctx = document.getElementById('temperatureChart').getContext('2d');
    const filteredData = planetsData.filter(p => selectedPlanets.includes(p.name));
    
    if (charts.temperatureChart) charts.temperatureChart.destroy();
    

    charts.temperatureChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: filteredData.map(p => p.name),
            datasets: [{
                label: 'Temperature moyenne (°C)',
                data: filteredData.map(p => p.temperature_celsius.average),
                borderColor: '#ff6b6b',
                backgroundColor: 'rgba(255, 107, 107, 0.2)',
                tension: 0.4,
                fill: true,
                pointRadius: 6,
                pointBackgroundColor: filteredData.map(p => p.color)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: '#fff' } }
            },
            scales: {
                y: {
                    ticks: { color: '#fff' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                },
                x: { 
                    ticks: { color: '#fff' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                }
            }
        }
    });
}

function createDistanceChart() {
    const ctx = document.getElementById('distanceChart').getContext('2d');
    const filteredData = planetsData.filter(p => selectedPlanets.includes(p.name));
    
    if (charts.distanceChart) charts.distanceChart.destroy();
    
    charts.distanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: filteredData.map(p => p.name),
            datasets: [{
                label: 'Distance du Soleil (millions km)',
                data: filteredData.map(p => p.distance_from_sun_km / 1000000),
                backgroundColor: 'rgba(102, 126, 234, 0.6)',
                borderColor: '#667eea',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { labels: { color: '#fff' } }
            },
            scales: {
                x: {
                    ticks: { color: '#fff' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                },
                y: {
                    ticks: { color: '#fff' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                }
            }
        }
    });
}

function createAtmosphereChart() {
    const ctx = document.getElementById('atmosphereChart').getContext('2d');
    
    const earthAtmosphere = {
        'Azote (N₂)': 78,
        'Oxygene (O₂)': 21,
        'Argon (Ar)': 0.9,
        'Autres': 0.1
    };
    
    if (charts.atmosphereChart) charts.atmosphereChart.destroy();
    
    charts.atmosphereChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(earthAtmosphere),
            datasets: [{
                data: Object.values(earthAtmosphere),
                backgroundColor: [
                    '#667eea',
                    '#4fd1c5',
                    '#f6ad55',
                    '#fc8181'
                ],
                borderWidth: 2,
                borderColor: '#1a202c'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: { color: '#fff', font: { size: 14 } }
                },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
}

function createMissionsChart() {
    const ctx = document.getElementById('missionsChart').getContext('2d');
    
    const agencies = {};
    missionsData.forEach(mission => {
        agencies[mission.agency] = (agencies[mission.agency] || 0) + 1;
    });
    
    if (charts.missionsChart) charts.missionsChart.destroy();
    
    charts.missionsChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: Object.keys(agencies),
            datasets: [{
                data: Object.values(agencies),
                backgroundColor: [
                    '#667eea',
                    '#764ba2',
                    '#f093fb',
                    '#4facfe'
                ],
                borderWidth: 2,
                borderColor: '#1a202c'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#fff', font: { size: 14 } }
                }
            }
        }
    });
}

function createTimelineChart() {
    const ctx = document.getElementById('timelineChart').getContext('2d');
    
    const sortedMissions = missionsData
        .filter(m => m.launch_date)
        .sort((a, b) => new Date(a.launch_date.$date) - new Date(b.launch_date.$date));
    
    if (charts.timelineChart) charts.timelineChart.destroy();
    
    charts.timelineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: sortedMissions.map(m => {
                const date = new Date(m.launch_date.$date);
                return date.getFullYear();
            }),
            datasets: [{
                label: 'Missions lancees',
                data: sortedMissions.map((m, i) => i + 1),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.2)',
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointBackgroundColor: '#667eea'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: '#fff' } },
                tooltip: {
                    callbacks: {
                        afterLabel: (context) => {
                            const mission = sortedMissions[context.dataIndex];
                            return mission.name;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: '#fff', stepSize: 1 },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                },
                x: {
                    ticks: { color: '#fff' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                }
            }
        }
    });
}

function setupFilters() {
    const checkboxes = document.querySelectorAll('.planet-filter');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            selectedPlanets = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            updateCharts();
        });
    });
}

function updateCharts() {
    createSizeChart();
    createTemperatureChart();
    createDistanceChart();
}

window.addEventListener('load', () => {
    createSizeChart();
    createTemperatureChart();
    createDistanceChart();
    createAtmosphereChart();
    createMissionsChart();
    createTimelineChart();
    setupFilters();

function refreshData() {
    fetch('/api/get-planets.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.planetsData = data.planets.filter(p => !p.destroyed);
                updateCharts();
                alert('Donnees actualisees !');
            }
        })
        .catch(error => console.error('Erreur:', error));
}

document.getElementById('refresh-data-btn').addEventListener('click', refreshData);
setInterval(refreshData, 5000);
});