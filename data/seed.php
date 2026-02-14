<?php

require_once __DIR__ . '/../config/database.php';

echo "<h1>Initialisation base de donnees Solar System</h1>";

$db = Database::getConnection();
echo "<h2>Nettoyage anciennes donnees...</h2>";
$db->dropCollection('planets');
$db->dropCollection('moons');
$db->dropCollection('missions');
$db->dropCollection('events');
echo "Collections nettoyees<br><br>";

echo "<h2>Insertion planetes...</h2>";
$planetsCollection = Database::getCollection('planets');

$planets = [
    [
        'name' => 'Mercure',
        'type' => 'Planète tellurique',
        'diameter_km' => 4879,
        'mass_kg' => 3.285e23,
        'distance_from_sun_km' => 57909050,
        'orbital_period_days' => 88,
        'rotation_period_hours' => 1407.6,
        'temperature_celsius' => ['min' => -173, 'max' => 427, 'average' => 167],
        'atmosphere' => ['traces'],
        'has_rings' => false,
        'moons_count' => 0,
        'discovery_date' => new MongoDB\BSON\UTCDateTime(strtotime('-3000 years') * 1000),
        'discovered_by' => 'Connu depuis l\'Antiquité',
        'image_url' => 'mercury.jpg',
        'color' => '#8C7853',
        'description' => 'La plus petite planète du système solaire et la plus proche du Soleil.'
    ],
    [
        'name' => 'Vénus',
        'type' => 'Planète tellurique',
        'diameter_km' => 12104,
        'mass_kg' => 4.867e24,
        'distance_from_sun_km' => 108208000,
        'orbital_period_days' => 225,
        'rotation_period_hours' => -5832.5,
        'temperature_celsius' => ['min' => 462, 'max' => 462, 'average' => 462],
        'atmosphere' => ['CO2' => 96.5, 'N2' => 3.5],
        'has_rings' => false,
        'moons_count' => 0,
        'discovery_date' => new MongoDB\BSON\UTCDateTime(strtotime('-3000 years') * 1000),
        'discovered_by' => 'Connu depuis l\'Antiquité',
        'image_url' => 'venus.jpg',
        'color' => '#FFC649',
        'description' => 'La planète la plus chaude du système solaire avec un effet de serre extrême.'
    ],
    [
        'name' => 'Terre',
        'type' => 'Planète tellurique',
        'diameter_km' => 12742,
        'mass_kg' => 5.972e24,
        'distance_from_sun_km' => 149598023,
        'orbital_period_days' => 365.25,
        'rotation_period_hours' => 24,
        'temperature_celsius' => ['min' => -89, 'max' => 58, 'average' => 15],
        'atmosphere' => ['N2' => 78, 'O2' => 21, 'Ar' => 0.9],
        'has_rings' => false,
        'moons_count' => 1,
        'discovery_date' => new MongoDB\BSON\UTCDateTime(0),
        'discovered_by' => 'N/A',
        'image_url' => 'earth.jpg',
        'color' => '#4169E1',
        'description' => 'Notre planète bleue, la seule connue pour abriter la vie.'
    ],
    [
        'name' => 'Mars',
        'type' => 'Planète tellurique',
        'diameter_km' => 6779,
        'mass_kg' => 6.39e23,
        'distance_from_sun_km' => 227900000,
        'orbital_period_days' => 687,
        'rotation_period_hours' => 24.6,
        'temperature_celsius' => ['min' => -140, 'max' => 20, 'average' => -63],
        'atmosphere' => ['CO2' => 95, 'N2' => 2.7, 'Ar' => 1.6],
        'has_rings' => false,
        'moons_count' => 2,
        'discovery_date' => new MongoDB\BSON\UTCDateTime(strtotime('1610-01-01') * 1000),
        'discovered_by' => 'Galilée',
        'image_url' => 'mars.jpg',
        'color' => '#CD5C5C',
        'description' => 'La planète rouge, cible principale de l\'exploration spatiale.'
    ],
    [
        'name' => 'Jupiter',
        'type' => 'Géante gazeuse',
        'diameter_km' => 139820,
        'mass_kg' => 1.898e27,
        'distance_from_sun_km' => 778500000,
        'orbital_period_days' => 4333,
        'rotation_period_hours' => 9.9,
        'temperature_celsius' => ['min' => -145, 'max' => -108, 'average' => -110],
        'atmosphere' => ['H2' => 89, 'He' => 10, 'CH4' => 0.3],
        'has_rings' => true,
        'moons_count' => 95,
        'discovery_date' => new MongoDB\BSON\UTCDateTime(strtotime('-3000 years') * 1000),
        'discovered_by' => 'Connu depuis l\'Antiquité',
        'image_url' => 'jupiter.jpg',
        'color' => '#DAA520',
        'description' => 'La plus grande planète du système solaire avec sa Grande Tache Rouge.'
    ],
    [
        'name' => 'Saturne',
        'type' => 'Géante gazeuse',
        'diameter_km' => 116460,
        'mass_kg' => 5.683e26,
        'distance_from_sun_km' => 1433500000,
        'orbital_period_days' => 10759,
        'rotation_period_hours' => 10.7,
        'temperature_celsius' => ['min' => -178, 'max' => -138, 'average' => -140],
        'atmosphere' => ['H2' => 96, 'He' => 3, 'CH4' => 0.4],
        'has_rings' => true,
        'moons_count' => 146,
        'discovery_date' => new MongoDB\BSON\UTCDateTime(strtotime('-3000 years') * 1000),
        'discovered_by' => 'Connu depuis l\'Antiquité',
        'image_url' => 'saturn.jpg',
        'color' => '#FAD5A5',
        'description' => 'Célèbre pour ses magnifiques anneaux, composés de glace et de roche.'
    ],
    [
        'name' => 'Uranus',
        'type' => 'Géante de glace',
        'diameter_km' => 50724,
        'mass_kg' => 8.681e25,
        'distance_from_sun_km' => 2872500000,
        'orbital_period_days' => 30687,
        'rotation_period_hours' => -17.2,
        'temperature_celsius' => ['min' => -224, 'max' => -197, 'average' => -195],
        'atmosphere' => ['H2' => 83, 'He' => 15, 'CH4' => 2],
        'has_rings' => true,
        'moons_count' => 28,
        'discovery_date' => new MongoDB\BSON\UTCDateTime(strtotime('1781-03-13') * 1000),
        'discovered_by' => 'William Herschel',
        'image_url' => 'uranus.jpg',
        'color' => '#4FD0E0',
        'description' => 'Une géante de glace qui tourne sur le côté.'
    ],
    [
        'name' => 'Neptune',
        'type' => 'Géante de glace',
        'diameter_km' => 49244,
        'mass_kg' => 1.024e26,
        'distance_from_sun_km' => 4495100000,
        'orbital_period_days' => 60190,
        'rotation_period_hours' => 16.1,
        'temperature_celsius' => ['min' => -218, 'max' => -200, 'average' => -200],
        'atmosphere' => ['H2' => 80, 'He' => 19, 'CH4' => 1],
        'has_rings' => true,
        'moons_count' => 16,
        'discovery_date' => new MongoDB\BSON\UTCDateTime(strtotime('1846-09-23') * 1000),
        'discovered_by' => 'Johann Galle',
        'image_url' => 'neptune.jpg',
        'color' => '#4169E1',
        'description' => 'La planète la plus éloignée, avec les vents les plus rapides du système solaire.'
    ]
];

$planetIds = [];
foreach ($planets as $planet) {
    $result = $planetsCollection->insertOne($planet);
    $planetIds[$planet['name']] = $result->getInsertedId();
    echo $planet['name'] . " ajoutee<br>";
}

echo "<br>";

echo "<h2>Insertion lunes principales...</h2>";
$moonsCollection = Database::getCollection('moons');

$moons = [
    [
        'name' => 'Lune',
        'planet_id' => $planetIds['Terre'],
        'planet_name' => 'Terre',
        'diameter_km' => 3474,
        'orbital_period_days' => 27.3,
        'discovery_date' => new MongoDB\BSON\UTCDateTime(0),
        'discovered_by' => 'N/A',
        'surface_type' => 'Rocheux',
        'has_ocean' => false,
        'description' => 'Le seul satellite naturel de la Terre.'
    ],
    [
        'name' => 'Phobos',
        'planet_id' => $planetIds['Mars'],
        'planet_name' => 'Mars',
        'diameter_km' => 22,
        'orbital_period_days' => 0.3,
        'discovery_date' => new MongoDB\BSON\UTCDateTime(strtotime('1877-08-18') * 1000),
        'discovered_by' => 'Asaph Hall',
        'surface_type' => 'Rocheux',
        'has_ocean' => false,
        'description' => 'La plus grande lune de Mars, avec une orbite très proche.'
    ],
    [
        'name' => 'Deimos',
        'planet_id' => $planetIds['Mars'],
        'planet_name' => 'Mars',
        'diameter_km' => 12,
        'orbital_period_days' => 1.3,
        'discovery_date' => new MongoDB\BSON\UTCDateTime(strtotime('1877-08-12') * 1000),
        'discovered_by' => 'Asaph Hall',
        'surface_type' => 'Rocheux',
        'has_ocean' => false,
        'description' => 'La plus petite lune de Mars.'
    ],
    [
        'name' => 'Europa',
        'planet_id' => $planetIds['Jupiter'],
        'planet_name' => 'Jupiter',
        'diameter_km' => 3121,
        'orbital_period_days' => 3.55,
        'discovery_date' => new MongoDB\BSON\UTCDateTime(strtotime('1610-01-08') * 1000),
        'discovered_by' => 'Galilée',
        'surface_type' => 'Glace',
        'has_ocean' => true,
        'description' => 'Une lune glacée avec un océan sous-terrain potentiellement habitable.'
    ],
    [
        'name' => 'Ganymède',
        'planet_id' => $planetIds['Jupiter'],
        'planet_name' => 'Jupiter',
        'diameter_km' => 5262,
        'orbital_period_days' => 7.15,
        'discovery_date' => new MongoDB\BSON\UTCDateTime(strtotime('1610-01-07') * 1000),
        'discovered_by' => 'Galilée',
        'surface_type' => 'Glace et roche',
        'has_ocean' => true,
        'description' => 'La plus grande lune du système solaire, plus grande que Mercure.'
    ],
    [
        'name' => 'Titan',
        'planet_id' => $planetIds['Saturne'],
        'planet_name' => 'Saturne',
        'diameter_km' => 5150,
        'orbital_period_days' => 15.95,
        'discovery_date' => new MongoDB\BSON\UTCDateTime(strtotime('1655-03-25') * 1000),
        'discovered_by' => 'Christiaan Huygens',
        'surface_type' => 'Glace et roche',
        'has_ocean' => true,
        'description' => 'La seule lune avec une atmosphère dense, des lacs de méthane.'
    ]
];

foreach ($moons as $moon) {
    $moonsCollection->insertOne($moon);
    echo $moon['name'] . " ajoutee<br>";
}

echo "<br>";

echo "<h2>Insertion missions spatiales...</h2>";
$missionsCollection = Database::getCollection('missions');

$missions = [
    [
        'name' => 'Apollo 11',
        'agency' => 'NASA',
        'launch_date' => new MongoDB\BSON\UTCDateTime(strtotime('1969-07-16') * 1000),
        'arrival_date' => new MongoDB\BSON\UTCDateTime(strtotime('1969-07-20') * 1000),
        'status' => 'Terminée',
        'target_planet_id' => $planetIds['Terre'],
        'target_planet_name' => 'Terre',
        'target_moon' => 'Lune',
        'mission_type' => 'Habité',
        'objectives' => ['Premier alunissage habité', 'Collecte d\'échantillons'],
        'crew' => ['Neil Armstrong', 'Buzz Aldrin', 'Michael Collins'],
        'budget_usd' => 25400000000,
        'achievements' => ['Premier pas sur la Lune', '21.5 kg d\'échantillons lunaires']
    ],
    [
        'name' => 'Mars Perseverance',
        'agency' => 'NASA',
        'launch_date' => new MongoDB\BSON\UTCDateTime(strtotime('2020-07-30') * 1000),
        'arrival_date' => new MongoDB\BSON\UTCDateTime(strtotime('2021-02-18') * 1000),
        'status' => 'Active',
        'target_planet_id' => $planetIds['Mars'],
        'target_planet_name' => 'Mars',
        'mission_type' => 'Rover',
        'objectives' => ['Recherche de vie passée', 'Collecte d\'échantillons', 'Test de production d\'oxygène'],
        'crew' => [],
        'budget_usd' => 2700000000,
        'achievements' => ['Premier vol d\'hélicoptère sur Mars (Ingenuity)', 'Production d\'oxygène réussie']
    ],
    [
        'name' => 'Voyager 1',
        'agency' => 'NASA',
        'launch_date' => new MongoDB\BSON\UTCDateTime(strtotime('1977-09-05') * 1000),
        'arrival_date' => null,
        'status' => 'Active',
        'target_planet_id' => null,
        'target_planet_name' => 'Espace interstellaire',
        'mission_type' => 'Sonde',
        'objectives' => ['Explorer le système solaire externe', 'Atteindre l\'espace interstellaire'],
        'crew' => [],
        'budget_usd' => 865000000,
        'achievements' => ['Premier objet humain en espace interstellaire', 'Photos de Jupiter et Saturne']
    ],
    [
        'name' => 'Juno',
        'agency' => 'NASA',
        'launch_date' => new MongoDB\BSON\UTCDateTime(strtotime('2011-08-05') * 1000),
        'arrival_date' => new MongoDB\BSON\UTCDateTime(strtotime('2016-07-04') * 1000),
        'status' => 'Active',
        'target_planet_id' => $planetIds['Jupiter'],
        'target_planet_name' => 'Jupiter',
        'mission_type' => 'Orbiteur',
        'objectives' => ['Étudier la composition de Jupiter', 'Cartographier le champ magnétique'],
        'crew' => [],
        'budget_usd' => 1100000000,
        'achievements' => ['Images détaillées des pôles de Jupiter', 'Mesure du champ gravitationnel']
    ]
];

foreach ($missions as $mission) {
    $missionsCollection->insertOne($mission);
    echo $mission['name'] . " ajoutee<br>";
}

echo "<br>";

echo "<h2>Insertion evenements astronomiques...</h2>";
$eventsCollection = Database::getCollection('events');

$events = [
    [
        'name' => 'Éclipse solaire totale 2024',
        'event_type' => 'Éclipse solaire',
        'date' => new MongoDB\BSON\UTCDateTime(strtotime('2024-04-08') * 1000),
        'visibility' => 'Amérique du Nord',
        'planets_involved' => [$planetIds['Terre']],
        'description' => 'Éclipse solaire totale visible en Amérique du Nord.',
        'next_occurrence' => new MongoDB\BSON\UTCDateTime(strtotime('2026-08-12') * 1000)
    ],
    [
        'name' => 'Opposition de Mars 2025',
        'event_type' => 'Opposition planétaire',
        'date' => new MongoDB\BSON\UTCDateTime(strtotime('2025-01-15') * 1000),
        'visibility' => 'Globale',
        'planets_involved' => [$planetIds['Mars'], $planetIds['Terre']],
        'description' => 'Mars au plus proche de la Terre, idéal pour l\'observation.',
        'next_occurrence' => new MongoDB\BSON\UTCDateTime(strtotime('2027-02-19') * 1000)
    ],
    [
        'name' => 'Pluie de météores Perséides 2025',
        'event_type' => 'Pluie de météores',
        'date' => new MongoDB\BSON\UTCDateTime(strtotime('2025-08-12') * 1000),
        'visibility' => 'Hémisphère Nord',
        'planets_involved' => [$planetIds['Terre']],
        'description' => 'Une des pluies de météores les plus spectaculaires de l\'année.',
        'next_occurrence' => new MongoDB\BSON\UTCDateTime(strtotime('2026-08-12') * 1000)
    ]
];

foreach ($events as $event) {
    $eventsCollection->insertOne($event);
    echo $event['name'] . " ajoute<br>";
}

echo "<br>";
echo "<h1>Base de donnees initialisee avec succes !</h1>";
echo "<p>" . count($planets) . " planetes</p>";
echo "<p>" . count($moons) . " lunes</p>";
echo "<p>" . count($missions) . " missions</p>";
echo "<p>" . count($events) . " evenements</p>";
?>