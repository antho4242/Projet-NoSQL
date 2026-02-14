<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['name'])) {
    echo json_encode(['error' => 'Données invalides']);
    exit;
}

try {
    $planetsCollection = Database::getCollection('planets');
    
    // Vérifie si la planete existe deja
    $existing = $planetsCollection->findOne(['name' => $data['name']]);
    if ($existing) {
        echo json_encode(['error' => 'Une planète avec ce nom existe déjà']);
        exit;
    }
    
    // Ajoute des champs par défaut
    $data['discovery_date'] = new MongoDB\BSON\UTCDateTime();
    $data['discovered_by'] = 'Utilisateur';
    $data['image_url'] = 'default.jpg';
    
    // Insére la planète
    $result = $planetsCollection->insertOne($data);
    
    echo json_encode([
        'success' => true,
        'insertedId' => (string)$result->getInsertedId()
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>