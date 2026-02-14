function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Ajouter mission';
    document.getElementById('isEdit').value = 'false';
    document.getElementById('missionForm').reset();
    document.getElementById('missionModal').classList.add('active');
}

function openEditModal(mission) {
    const getOid = (id) => {
        if (!id) return '';
        if (typeof id === 'string') return id;
        return id.$oid || id.oid || '';
    };

    const objectivesToArray = (obj) => {
        if (!obj) return [];
        if (Array.isArray(obj)) return obj;
        if (typeof obj === 'object') return Object.values(obj);
        return [];
    };

    const mongoDateToInput = (d) => {
        if (!d) return '';
        const raw = (typeof d === 'string') ? d : (d.$date ?? d.date ?? d);
        const dt = new Date(raw);
        if (isNaN(dt)) return '';
        return dt.toISOString().split('T')[0];
    };

    const normalizeStatus = (s) => {
        if (!s) return 'Active';
        const low = String(s).toLowerCase();
        if (low === 'active') return 'Active';
        if (low.includes('termin')) return 'Terminée';
        if (low.includes('plan')) return 'Planifiée';
        return s;
    };

    document.getElementById('modalTitle').textContent = 'Modifier mission';
    document.getElementById('isEdit').value = 'true';
    document.getElementById('missionId').value = getOid(mission._id);
    document.getElementById('name').value = mission.name ?? '';
    document.getElementById('agency').value = mission.agency ?? 'NASA';
    document.getElementById('status').value = normalizeStatus(mission.status);
    document.getElementById('target_planet').value = mission.target_planet_name ?? mission.target_planet ?? '';
    document.getElementById('mission_type').value = mission.mission_type ?? 'Orbiteur';
    document.getElementById('launch_date').value = mongoDateToInput(mission.launch_date);
    document.getElementById('arrival_date').value = mongoDateToInput(mission.arrival_date);
    document.getElementById('budget_usd').value = mission.budget_usd ?? '';

    const objectivesArr = objectivesToArray(mission.objectives);
    document.getElementById('objectives').value = objectivesArr.join('\n');

    document.getElementById('missionModal').classList.add('active');
}

function closeModal() {
    document.getElementById('missionModal').classList.remove('active');
}

document.getElementById('missionForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const isEdit = formData.get('isEdit') === 'true';
    
    const objectivesText = formData.get('objectives');
    const objectives = objectivesText ? objectivesText.split('\n').filter(o => o.trim()) : [];
    
    const targetPlanetName = formData.get('target_planet');
    const targetPlanet = planetsData.find(p => p.name === targetPlanetName);
    
    const missionData = {
        name: formData.get('name'),
        agency: formData.get('agency'),
        status: formData.get('status'),
        target_planet_name: targetPlanetName,
        target_planet_id: targetPlanet ? (targetPlanet._id.$oid || targetPlanet._id) : null,
        mission_type: formData.get('mission_type'),
        launch_date: formData.get('launch_date'),
        arrival_date: formData.get('arrival_date') || null,
        budget_usd: formData.get('budget_usd') ? parseInt(formData.get('budget_usd')) : 0,
        objectives: objectives,
        crew: [],
        achievements: []
    };
    
    try {
        const endpoint = isEdit ? '/api/update-mission.php' : '/api/add-mission.php';
        if (isEdit) missionData.missionId = formData.get('missionId');
        
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(missionData)
        });
        
        const result = await response.json();
        if (result.success) {
            alert(isEdit ? 'Mission modifiee !' : 'Mission ajoutee !');
            location.reload();
        } else {
            alert('Erreur: ' + (result.error || 'Erreur inconnue'));
        }
    } catch (error) {
        alert('Erreur connexion: ' + error.message);
    }
});

async function deleteMission(missionName) {
    if (!confirm(`Supprimer mission "${missionName}" ?\n\nAction irreversible !`)) return;
    
    try {
        const response = await fetch('/api/delete-mission.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name: missionName })
        });
        
        const result = await response.json();
        if (result.success) {
            alert('Mission supprimee !');
            location.reload();
        } else {
            alert('Erreur: ' + (result.error || 'Erreur inconnue'));
        }
    } catch (error) {
        alert('Erreur connexion: ' + error.message);
    }
}

document.getElementById('missionModal').addEventListener('click', (e) => {
    if (e.target.id === 'missionModal') closeModal();
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            const missionBase64 = btn.getAttribute('data-mission');
            const missionJson = atob(missionBase64);
            const missionData = JSON.parse(missionJson);
            console.log('Mission data:', missionData);
            openEditModal(missionData);
        });
    });
});