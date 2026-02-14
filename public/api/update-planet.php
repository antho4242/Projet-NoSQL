<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

// Validation de l'ID (obligatoire pour update)
if (!$data || !isset($data['_id'])) {
    echo json_encode(['error' => 'ID manquant']);
    exit;
}

// Validation du nom si present
if (isset($data['name']) && trim($data['name']) === '') {
    echo json_encode(['error' => 'Le nom ne peut pas etre vide']);
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
        
        // Verifier que c'est positif (sauf rotation_period qui peut etre negatif)
        if ($field !== 'rotation_period_hours' && $data[$field] < 0) {
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
    
    // Extraire l'ID et le supprimer des donnees a mettre a jour
    $id = $data['_id'];
    unset($data['_id']);
    
    // Mettre a jour
    $result = $planetsCollection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($id)],
        ['$set' => $data]
    );
    
    if ($result->getModifiedCount() > 0 || $result->getMatchedCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Planete non trouvee']);
    }
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>