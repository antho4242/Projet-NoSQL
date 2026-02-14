let scene, camera, renderer, controls;
let planets = [];
let sun;
let animationId;
let isPaused = false;
let speed = 1;

let planetHealthBars = {};
let lastSyncTime = Date.now();
const SYNC_INTERVAL = 2000;

planetsData.forEach(planet => {
    if (!planet.health) {
        planet.health = 100;
        planet.max_health = 100;
    }
    planet.destroyed = planet.destroyed || false;
});

function init() {
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x000510);

    const container = document.getElementById('canvas-container');
    camera = new THREE.PerspectiveCamera(
        75,
        container.clientWidth / container.clientHeight,
        0.1,
        10000
    );
    camera.position.set(0, 200, 400);
    camera.lookAt(0, 0, 0);

    const canvas = document.getElementById('solar-system-canvas');
    renderer = new THREE.WebGLRenderer({ 
        canvas: canvas, 
        antialias: true 
    });
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.setPixelRatio(window.devicePixelRatio);

    const ambientLight = new THREE.AmbientLight(0x333333);
    scene.add(ambientLight);

    const sunLight = new THREE.PointLight(0xffffff, 2, 2000);
    sunLight.position.set(0, 0, 0);
    scene.add(sunLight);

    createStars();
    createSun();
    createPlanets();
    setupEventListeners();
    animate();
}

function createStars() {
    const starsGeometry = new THREE.BufferGeometry();
    const starsMaterial = new THREE.PointsMaterial({
        color: 0xffffff,
        size: 1,
        sizeAttenuation: true
    });

    const starsVertices = [];
    for (let i = 0; i < 10000; i++) {
        const x = (Math.random() - 0.5) * 4000;
        const y = (Math.random() - 0.5) * 4000;
        const z = (Math.random() - 0.5) * 4000;
        starsVertices.push(x, y, z);
    }

    starsGeometry.setAttribute('position', new THREE.Float32BufferAttribute(starsVertices, 3));
    const stars = new THREE.Points(starsGeometry, starsMaterial);
    scene.add(stars);
}

function createSun() {
    const sunGeometry = new THREE.SphereGeometry(30, 32, 32);
    const sunMaterial = new THREE.MeshBasicMaterial({
        color: 0xFDB813,
        emissive: 0xFDB813,
        emissiveIntensity: 1
    });
    sun = new THREE.Mesh(sunGeometry, sunMaterial);
    scene.add(sun);

    const glowGeometry = new THREE.SphereGeometry(35, 32, 32);
    const glowMaterial = new THREE.MeshBasicMaterial({
        color: 0xFDB813,
        transparent: true,
        opacity: 0.3
    });
    const glow = new THREE.Mesh(glowGeometry, glowMaterial);
    sun.add(glow);
}


function createPlanets() {
    planetsData.forEach((planetData, index) => {
        if (planetData.destroyed) {
            console.log(`${planetData.name} detruite, ignoree`);
            return;
        }
        
        const distance = 60 + (index * 40);
        const size = Math.max(3, planetData.diameter_km / 10000);

        const geometry = new THREE.SphereGeometry(size, 32, 32);
        const material = new THREE.MeshStandardMaterial({
            color: planetData.color || 0xffffff,
            roughness: 0.7,
            metalness: 0.3
        });
        const planet = new THREE.Mesh(geometry, material);

        const angle = Math.random() * Math.PI * 2;
        planet.position.x = Math.cos(angle) * distance;
        planet.position.z = Math.sin(angle) * distance;

        const orbitGeometry = new THREE.RingGeometry(distance - 0.5, distance + 0.5, 64);
        const orbitMaterial = new THREE.MeshBasicMaterial({
            color: 0x444444,
            side: THREE.DoubleSide,
            transparent: true,
            opacity: 0.3
        });
        const orbit = new THREE.Mesh(orbitGeometry, orbitMaterial);
        orbit.rotation.x = Math.PI / 2;
        scene.add(orbit);

        // Créer la barre de vie
        const healthBarGroup = createHealthBar(planetData.name, planetData.health);
        planet.add(healthBarGroup);
        planetHealthBars[planetData.name] = healthBarGroup;

        // Ajouter la planète à la scène
        scene.add(planet);

        // Stocker les infos
        planets.push({
            mesh: planet,
            data: planetData,
            distance: distance,
            angle: angle,
            speed: 0.001 / (planetData.orbital_period_days / 365),
            orbit: orbit
        });
    });
}


function animate() {
    animationId = requestAnimationFrame(animate);

    if (!isPaused) {
        // Rotation du soleil
        sun.rotation.y += 0.001;

        // Déplacement des planètes (avec gravité)
        planets.forEach(planet => {
            planet.angle += planet.speed * speed * gravityMultiplier;
            const distance = planet.distance / gravityMultiplier;
            planet.mesh.position.x = Math.cos(planet.angle) * distance;
            planet.mesh.position.z = Math.sin(planet.angle) * distance;
            planet.mesh.rotation.y += 0.01;
            
            // Effet de température
            if (sunTemperature > 120) {
                const heatRatio = (sunTemperature - 120) / 80;
                planet.mesh.material.emissive = new THREE.Color(0xff0000);
                planet.mesh.material.emissiveIntensity = heatRatio * 0.5;
            } else {
                planet.mesh.material.emissiveIntensity = 0;
            }
        });
        
        // Mettre à jour les astéroïdes
        updateAsteroids();
        // Synchronisation périodique
        periodicSync();
    }

    renderer.render(scene, camera);
}


function setupEventListeners() {
    // Redimensionnement de la fenêtre
    window.addEventListener('resize', () => {
        const container = document.getElementById('canvas-container');
        camera.aspect = container.clientWidth / container.clientHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(container.clientWidth, container.clientHeight);
    });

    // Contrôle de la caméra avec la souris
    let isDragging = false;
    let previousMousePosition = { x: 0, y: 0 };

    const canvas = document.getElementById('solar-system-canvas');
    
    canvas.addEventListener('mousedown', (e) => {
        isDragging = true;
        previousMousePosition = { x: e.clientX, y: e.clientY };
    });

    canvas.addEventListener('mousemove', (e) => {
        if (isDragging) {
            const deltaX = e.clientX - previousMousePosition.x;
            const deltaY = e.clientY - previousMousePosition.y;

            camera.position.x += deltaX * 0.5;
            camera.position.y -= deltaY * 0.5;
            camera.lookAt(0, 0, 0);

            previousMousePosition = { x: e.clientX, y: e.clientY };
        }
    });

    canvas.addEventListener('mouseup', () => {
        isDragging = false;
    });

    // Zoom avec la molette
    canvas.addEventListener('wheel', (e) => {
        e.preventDefault();
        const zoomSpeed = 10;
        camera.position.z += e.deltaY * zoomSpeed * 0.01;
        camera.position.z = Math.max(100, Math.min(1000, camera.position.z));
    });

    // Boutons de contrôle
    document.getElementById('play-pause-btn').addEventListener('click', () => {
        isPaused = !isPaused;
        document.getElementById('play-pause-btn').textContent = isPaused ? 'Play' : 'Pause';
    });

    document.getElementById('reset-btn').addEventListener('click', () => {
        camera.position.set(0, 200, 400);
        camera.lookAt(0, 0, 0);
        speed = 1;
        document.getElementById('speed-slider').value = 1;
        document.getElementById('speed-value').textContent = '1x';
    });

    document.getElementById('speed-slider').addEventListener('input', (e) => {
        speed = parseFloat(e.target.value);
        document.getElementById('speed-value').textContent = speed + 'x';
    });

    // Clic sur les planètes de la liste
    document.querySelectorAll('.planet-item').forEach((item, index) => {
        item.addEventListener('click', () => {
            showPlanetInfo(planets[index].data);
            focusOnPlanet(planets[index]);
        });
    });


    // Catastrophes
    document.getElementById('sun-temp-slider').addEventListener('input', (e) => {
        updateSunTemperature(parseInt(e.target.value));
    });

    document.getElementById('asteroid-slider').addEventListener('input', (e) => {
        updateAsteroidProbability(parseInt(e.target.value));
    });

    document.getElementById('gravity-slider').addEventListener('input', (e) => {
        updateGravity(parseInt(e.target.value));
    });

    document.getElementById('supernova-btn').addEventListener('click', triggerSupernova);
    document.getElementById('reset-catastrophe-btn').addEventListener('click', resetCatastrophes);



}


function showPlanetInfo(planetData) {
    const infoDiv = document.getElementById('planet-info');
    infoDiv.innerHTML = `
        <h3 style="color: ${planetData.color}">${planetData.name}</h3>
        <p><strong>Type:</strong> ${planetData.type}</p>
        <p><strong>Diametre:</strong> ${planetData.diameter_km.toLocaleString()} km</p>
        <p><strong>Distance du Soleil:</strong> ${(planetData.distance_from_sun_km / 1000000).toFixed(2)} millions km</p>
        <p><strong>Periode orbitale:</strong> ${planetData.orbital_period_days} jours</p>
        <p><strong>Temperature moy:</strong> ${planetData.temperature_celsius.average}°C</p>
        <p><strong>Lunes:</strong> ${planetData.moons_count}</p>
        <p><strong>Anneaux:</strong> ${planetData.has_rings ? 'Oui' : 'Non'}</p>
        <hr>
        <p style="font-size: 0.9em; color: #ccc;">${planetData.description}</p>
    `;
}


function focusOnPlanet(planet) {
    const targetPosition = planet.mesh.position.clone();
    targetPosition.z += planet.distance * 0.5;
    targetPosition.y += 50;

    // Animation simple vers la planète
    const duration = 1000;
    const start = Date.now();
    const startPos = camera.position.clone();

    function animateCamera() {
        const elapsed = Date.now() - start;
        const progress = Math.min(elapsed / duration, 1);

        camera.position.lerpVectors(startPos, targetPosition, progress);
        camera.lookAt(planet.mesh.position);

        if (progress < 1) {
            requestAnimationFrame(animateCamera);
        }
    }

    animateCamera();
}


window.addEventListener('load', init);


let sunTemperature = 100;
let asteroidProbability = 0;
let gravityMultiplier = 1;
let supernovaActive = false;
let asteroids = [];

// Mise à jour de la température du Soleil
function updateSunTemperature(value) {
    sunTemperature = value;
    document.getElementById('sun-temp-value').textContent = value + '%';
    
    // Changer la couleur du soleil
    const normalColor = 0xFDB813;
    const hotColor = 0xFF0000;
    const ratio = (value - 100) / 100;
    
    if (value > 100) {
        sun.material.color.setHex(lerpColor(normalColor, hotColor, Math.min(ratio, 1)));
        sun.material.emissive.setHex(lerpColor(normalColor, hotColor, Math.min(ratio, 1)));
        
        // Augmenter la taille du soleil
        const scale = 1 + (ratio * 0.5);
        sun.scale.set(scale, scale, scale);
        
        // Afficher un avertissement
        if (value > 150 && !document.getElementById('temp-warning')) {
            showWarning('⚠️ TEMPÉRATURE SOLAIRE CRITIQUE ! Les planètes brûlent !', 'temp-warning');
        }
    } else {
        sun.material.color.setHex(normalColor);
        sun.material.emissive.setHex(normalColor);
        sun.scale.set(1, 1, 1);
        removeWarning('temp-warning');
    }
}

// Mettre à jour la probabilité d'astéroïdes
function updateAsteroidProbability(value) {
    asteroidProbability = value;
    document.getElementById('asteroid-value').textContent = value + '%';
}

// Mettre à jour la gravité
function updateGravity(value) {
    gravityMultiplier = value / 100;
    document.getElementById('gravity-value').textContent = value + '%';
    
    if (value < 50) {
        showWarning('⚠️ GRAVITÉ FAIBLE ! Les planètes dérivent dans l\'espace !', 'gravity-warning');
    } else if (value > 150) {
        showWarning('⚠️ GRAVITÉ ÉLEVÉE ! Les planètes sont attirées vers le Soleil !', 'gravity-warning');
    } else {
        removeWarning('gravity-warning');
    }
}

// Déclencher une supernova
function triggerSupernova() {
    if (supernovaActive) return;
    
    supernovaActive = true;
    showWarning('🌞 SUPERNOVA EN COURS ! LE SYSTÈME SOLAIRE EST DÉTRUIT !', 'supernova-warning');
    
    // Animation d'explosion
    let explosionSize = 30;
    const explosionInterval = setInterval(() => {
        explosionSize += 20;
        sun.scale.set(explosionSize / 30, explosionSize / 30, explosionSize / 30);
        sun.material.opacity = Math.max(0, 1 - (explosionSize / 500));
        sun.material.transparent = true;
        
        // Faire exploser les planètes
        planets.forEach(planet => {
            const direction = planet.mesh.position.clone().normalize();
            planet.mesh.position.add(direction.multiplyScalar(5));
        });
        
        if (explosionSize > 500) {
            clearInterval(explosionInterval);
        }
    }, 50);
}

// Créer un astéroïde
function createAsteroid() {
    const size = Math.random() * 3 + 1;
    const geometry = new THREE.SphereGeometry(size, 8, 8);
    const material = new THREE.MeshStandardMaterial({
        color: 0x8B4513,
        roughness: 1
    });
    const asteroid = new THREE.Mesh(geometry, material);
    
    // Position aléatoire en dehors du système
    const angle = Math.random() * Math.PI * 2;
    const distance = 500;
    asteroid.position.set(
        Math.cos(angle) * distance,
        (Math.random() - 0.5) * 100,
        Math.sin(angle) * distance
    );
    
    // Vélocité vers le centre
    const velocity = new THREE.Vector3(
        -Math.cos(angle) * 2,
        0,
        -Math.sin(angle) * 2
    );
    
    scene.add(asteroid);
    asteroids.push({ mesh: asteroid, velocity: velocity });
}

// Mettre à jour les astéroïdes
function updateAsteroids() {
    if (Math.random() * 100 < asteroidProbability / 10) {
        createAsteroid();
    }
    
    asteroids.forEach((asteroid, index) => {
        asteroid.mesh.position.add(asteroid.velocity);
        asteroid.mesh.rotation.x += 0.1;
        asteroid.mesh.rotation.y += 0.1;
        
        // Vérifier les collisions avec les planètes
        // Vérifier les collisions avec les planètes
        planets.forEach(planet => {
            const distance = asteroid.mesh.position.distanceTo(planet.mesh.position);
            if (distance < 20) {
                // Collision !
                createExplosion(planet.mesh.position);
                showWarning(`💥 COLLISION ! ${planet.data.name} a été touchée par un astéroïde !`, 'collision-warning-' + Date.now());
                
                // Infliger des dégâts
                damagePlanet(planet.data.name, 15);
                
                // Supprimer l'astéroïde
                scene.remove(asteroid.mesh);
                asteroids.splice(asteroids.indexOf(asteroid), 1);
            }
        });
        
        // Supprimer si trop loin
        if (asteroid.mesh.position.length() > 1000) {
            scene.remove(asteroid.mesh);
            asteroids.splice(index, 1);
        }
    });
}

// Créer une explosion
function createExplosion(position) {
    const particlesGeometry = new THREE.BufferGeometry();
    const particlesMaterial = new THREE.PointsMaterial({
        color: 0xff6600,
        size: 3,
        transparent: true,
        opacity: 1
    });
    
    const particlesCount = 100;
    const positions = [];
    
    for (let i = 0; i < particlesCount; i++) {
        positions.push(
            position.x + (Math.random() - 0.5) * 20,
            position.y + (Math.random() - 0.5) * 20,
            position.z + (Math.random() - 0.5) * 20
        );
    }
    
    particlesGeometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));
    const particles = new THREE.Points(particlesGeometry, particlesMaterial);
    scene.add(particles);
    
    // Faire disparaître les particules
    setTimeout(() => {
        scene.remove(particles);
    }, 2000);
}

// Réinitialiser les catastrophes
function resetCatastrophes() {
    // Réinitialiser les données dans MongoDB
    fetch('/api/reset-data.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Données réinitialisées dans la base');
            // Recharger la page pour afficher les données fraîches
            setTimeout(() => location.reload(), 500);
        }
    });
    
    sunTemperature = 100;
    asteroidProbability = 0;
    gravityMultiplier = 1;
    supernovaActive = false;
    sunTemperature = 100;
    asteroidProbability = 0;
    gravityMultiplier = 1;
    supernovaActive = false;
    
    document.getElementById('sun-temp-slider').value = 100;
    document.getElementById('asteroid-slider').value = 0;
    document.getElementById('gravity-slider').value = 100;
    
    updateSunTemperature(100);
    updateAsteroidProbability(0);
    updateGravity(100);
    
    // Nettoyer les astéroïdes
    asteroids.forEach(asteroid => scene.remove(asteroid.mesh));
    asteroids = [];
    
    // Réinitialiser les planètes
    planets.forEach((planet, index) => {
        planet.angle = Math.random() * Math.PI * 2;
        planet.mesh.position.x = Math.cos(planet.angle) * planet.distance;
        planet.mesh.position.z = Math.sin(planet.angle) * planet.distance;
    });
    
    // Réinitialiser le soleil
    sun.scale.set(1, 1, 1);
    sun.material.opacity = 1;
    sun.material.transparent = false;
    
    // Supprimer tous les avertissements
    document.querySelectorAll('.catastrophe-warning').forEach(w => w.remove());
}

// Afficher un avertissement
function showWarning(message, id) {
    if (document.getElementById(id)) return;
    
    const existingWarnings = document.querySelectorAll('.catastrophe-warning');
    const offset = existingWarnings.length * 120;
    
    const warning = document.createElement('div');
    warning.className = 'catastrophe-warning';
    warning.id = id;
    warning.style.top = `${20 + offset}px`;
    warning.innerHTML = `<strong>⚠️ ${message}</strong>`;
    document.body.appendChild(warning);
    
    setTimeout(() => {
        if (warning.parentNode) {
            warning.style.opacity = '0';
            warning.style.transform = 'translateX(450px)';
            setTimeout(() => warning.remove(), 300);
        }
    }, 3000);
}

// Supprimer un avertissement
function removeWarning(id) {
    const warning = document.getElementById(id);
    if (warning) warning.remove();
}

// Interpolation de couleurs
function lerpColor(color1, color2, ratio) {
    const r1 = (color1 >> 16) & 0xff;
    const g1 = (color1 >> 8) & 0xff;
    const b1 = color1 & 0xff;
    
    const r2 = (color2 >> 16) & 0xff;
    const g2 = (color2 >> 8) & 0xff;
    const b2 = color2 & 0xff;
    
    const r = Math.floor(r1 + (r2 - r1) * ratio);
    const g = Math.floor(g1 + (g2 - g1) * ratio);
    const b = Math.floor(b1 + (b2 - b1) * ratio);
    
    return (r << 16) | (g << 8) | b;
}



function createHealthBar(planetName, health) {
    const group = new THREE.Group();
    
    // Fond de la barre
    const bgGeometry = new THREE.PlaneGeometry(20, 2);
    const bgMaterial = new THREE.MeshBasicMaterial({ color: 0x333333 });
    const bg = new THREE.Mesh(bgGeometry, bgMaterial);
    bg.position.y = 15;
    group.add(bg);
    
    // Barre de vie
    const healthGeometry = new THREE.PlaneGeometry(20 * (health / 100), 2);
    const healthMaterial = new THREE.MeshBasicMaterial({ 
        color: health > 50 ? 0x00ff00 : health > 25 ? 0xffaa00 : 0xff0000 
    });
    const healthBar = new THREE.Mesh(healthGeometry, healthMaterial);
    healthBar.position.y = 15;
    healthBar.position.x = -10 + (20 * (health / 100) / 2);
    group.add(healthBar);
    
    group.userData.healthBar = healthBar;
    group.userData.planetName = planetName;
    
    return group;
}


function updateHealthBar(planetName, health) {
    const healthBarGroup = planetHealthBars[planetName];
    if (!healthBarGroup) return;
    
    const healthBar = healthBarGroup.userData.healthBar;
    
    // Mettre à jour la taille
    healthBar.geometry.dispose();
    healthBar.geometry = new THREE.PlaneGeometry(20 * (health / 100), 2);
    
    // Mettre à jour la position
    healthBar.position.x = -10 + (20 * (health / 100) / 2);
    
    // Mettre à jour la couleur
    healthBar.material.color.setHex(
        health > 50 ? 0x00ff00 : health > 25 ? 0xffaa00 : 0xff0000
    );
}


function damagePlanet(planetName, damage) {
    const planet = planets.find(p => p.data.name === planetName);
    if (!planet || planet.data.destroyed) return;
    
    planet.data.health = Math.max(0, planet.data.health - damage);
    updateHealthBar(planetName, planet.data.health);
    
    // Sauvegarder dans la base de données
    syncPlanetToDatabase(planetName, { health: planet.data.health });
    
    // Vérifier si la planète est detruite
    if (planet.data.health <= 0) {
        destroyPlanet(planetName);
    }
}


function destroyPlanet(planetName) {
    const planetIndex = planets.findIndex(p => p.data.name === planetName);
    if (planetIndex === -1) return;
    
    const planet = planets[planetIndex];
    
    // Animation d'explosion
    createExplosion(planet.mesh.position);
    
    // Marquer comme detruite
    planet.data.destroyed = true;
    
    // Sauvegarder dans la base de données
    fetch('/api/delete-planet.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: planetName })
    });
    
    // Supprimer de la scène
    scene.remove(planet.mesh);
    scene.remove(planet.orbit);
    
    // Supprimer du tableau
    planets.splice(planetIndex, 1);
    
    // Afficher avertissement
    showWarning(`💥 ${planetName} a été DÉTRUITE ! Le système solaire est déséquilibré !`, 'destroy-' + planetName);
    
    // Impact sur les autres planètes
    affectNeighboringPlanets(planetName);
}


function affectNeighboringPlanets(destroyedPlanetName) {
    console.log(`${destroyedPlanetName} detruite - impact sur les planètes voisines`);
    
    // Chaque planète restante perd un peu de santé
    planets.forEach(planet => {
        damagePlanet(planet.data.name, 10);
    });
    
    // Modifier légèrement les orbites
    planets.forEach(planet => {
        planet.speed *= 1.1; // Accélération
    });
}


function syncPlanetToDatabase(planetName, updates) {
    fetch('/api/update-planet.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            name: planetName,
            updates: updates
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log(`✅ ${planetName} synchronisée avec la base de données`);
        }
    })
    .catch(error => console.error('Erreur de synchronisation:', error));
}


function periodicSync() {
    if (Date.now() - lastSyncTime < SYNC_INTERVAL) return;
    
    lastSyncTime = Date.now();
    
    // Synchroniser la température du Soleil
    if (sunTemperature !== 100) {
        // Calculer l'impact sur chaque planète
        planets.forEach(planet => {
            const tempIncrease = (sunTemperature - 100) * 0.5;
            const newTemp = planet.data.temperature_celsius.average + tempIncrease;
            
            syncPlanetToDatabase(planet.data.name, {
                'temperature_celsius.average': Math.round(newTemp)
            });
            
            planet.data.temperature_celsius.average = Math.round(newTemp);
        });
    }
}