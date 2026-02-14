<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

// Validation du nom (obligatoire)
if (!$data || !isset($data['name']) || trim($data['name']) === '') {
    echo json_encode(['error' => 'Le nom est obligatoire']);
    exit;
}

// Validation et conversion des champs numeriques
$numericFields = [
    'diameter_km' => 'int',
    'mass_kg' => 'float',
    'distance_from_sun_km' => 'int',
    'orbital_period_days' => 'float',
    'rotation_period_hours' => 'float',
    'moons_count' => 'int'
];

foreach ($numericFields as $field => $type) {
    if (isset($data[$field])) {
        // Verifier que c'est bien un nombre
        if (!is_numeric($data[$field])) {
            echo json_encode(['error' => "Le champ $field doit etre un nombre"]);
            exit;
        }
        
        // Verifier que c'est positif
        if ($data[$field] < 0) {
            echo json_encode(['error' => "Le champ $field doit etre positif"]);
            exit;
        }
        
        // Convertir au bon type
        if ($type === 'int') {
            $data[$field] = (int)$data[$field];
        } else {
            $data[$field] = (float)$data[$field];
        }
    }
}

// Validation du booleen has_rings
if (isset($data['has_rings'])) {
    $data['has_rings'] = filter_var($data['has_rings'], FILTER_VALIDATE_BOOLEAN);
}

try {
    $planetsCollection = Database::getCollection('planets');
    
    // Verifie si la planete existe deja
    $existing = $planetsCollection->findOne(['name' => $data['name']]);
    if ($existing) {
        echo json_encode(['error' => 'Une planete avec ce nom existe deja']);
        exit;
    }
    
    // Ajoute des champs par defaut
    $data['discovery_date'] = new MongoDB\BSON\UTCDateTime();
    $data['discovered_by'] = 'Utilisateur';
    $data['image_url'] = 'default.jpg';
    
    // Insere la planete
    $result = $planetsCollection->insertOne($data);
    
    echo json_encode([
        'success' => true,
        'insertedId' => (string)$result->getInsertedId()
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>