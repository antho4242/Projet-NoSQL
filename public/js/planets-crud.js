function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Ajouter une planete';
    document.getElementById('isEdit').value = 'false';
    document.getElementById('planetForm').reset();
    document.getElementById('planetModal').classList.add('active');
}

function openEditModal(planet) {
    document.getElementById('modalTitle').textContent = 'Modifier planete';
    document.getElementById('isEdit').value = 'true';
    document.getElementById('planetId').value = planet._id.$oid;
    
    document.getElementById('name').value = planet.name;
    document.getElementById('type').value = planet.type;
    document.getElementById('color').value = planet.color;
    document.getElementById('diameter_km').value = planet.diameter_km;
    document.getElementById('mass_kg').value = planet.mass_kg;
    document.getElementById('distance_from_sun_km').value = planet.distance_from_sun_km;
    document.getElementById('orbital_period_days').value = planet.orbital_period_days;
    document.getElementById('rotation_period_hours').value = planet.rotation_period_hours;
    document.getElementById('temp_min').value = planet.temperature_celsius.min;
    document.getElementById('temp_max').value = planet.temperature_celsius.max;
    document.getElementById('temp_avg').value = planet.temperature_celsius.average;
    document.getElementById('moons_count').value = planet.moons_count;
    document.getElementById('has_rings').value = planet.has_rings.toString();
    document.getElementById('description').value = planet.description || '';
    
    document.getElementById('planetModal').classList.add('active');
}

function closeModal() {
    document.getElementById('planetModal').classList.remove('active');
}

document.getElementById('planetForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const isEdit = formData.get('isEdit') === 'true';
    
    const planetData = {
        name: formData.get('name'),
        type: formData.get('type'),
        color: formData.get('color'),
        diameter_km: parseFloat(formData.get('diameter_km')),
        mass_kg: parseFloat(formData.get('mass_kg')),
        distance_from_sun_km: parseFloat(formData.get('distance_from_sun_km')),
        orbital_period_days: parseFloat(formData.get('orbital_period_days')),
        rotation_period_hours: parseFloat(formData.get('rotation_period_hours')),
        temperature_celsius: {
            min: parseFloat(formData.get('temp_min')),
            max: parseFloat(formData.get('temp_max')),
            average: parseFloat(formData.get('temp_avg'))
        },
        moons_count: parseInt(formData.get('moons_count')),
        has_rings: formData.get('has_rings') === 'true',
        description: formData.get('description'),
        atmosphere: [],
        health: 100,
        max_health: 100,
        destroyed: false
    };
    
    try {
        const endpoint = isEdit ? '/api/update-planet.php' : '/api/add-planet.php';
        
        if (isEdit) {
        planetData._id = formData.get('planetId');
                }
        
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(planetData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(isEdit ? 'Planete modifiee !' : 'Planete ajoutee !');
            location.reload();
        } else {
            alert('Erreur: ' + (result.error || 'Erreur inconnue'));
        }
    } catch (error) {
        alert('Erreur connexion: ' + error.message);
    }
});

async function deletePlanet(planetName) {
    if (!confirm(`Supprimer ${planetName} ?\n\nAction irreversible !`)) {
        return;
    }
    
    try {
        const response = await fetch('/api/delete-planet.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name: planetName })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Planete supprimee !');
            location.reload();
        } else {
            alert('Erreur: ' + (result.error || 'Erreur inconnue'));
        }
    } catch (error) {
        alert('Erreur connexion: ' + error.message);
    }
}

document.getElementById('planetModal').addEventListener('click', (e) => {
    if (e.target.id === 'planetModal') {
        closeModal();
    }
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            const planetBase64 = btn.getAttribute('data-planet');
            const planetJson = atob(planetBase64);
            const planetData = JSON.parse(planetJson);
            openEditModal(planetData);
        });
    });
});