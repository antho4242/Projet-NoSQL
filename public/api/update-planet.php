<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['error' => 'Données invalides']);
    exit;
}

try {
    $planetsCollection = Database::getCollection('planets');
    
  
    if (isset($data['planetId'])) {
        $planetId = new MongoDB\BSON\ObjectId($data['planetId']);
        unset($data['planetId']);
        
        // Convertie les valeurs numériques
        if (isset($data['mass_kg'])) {
            $data['mass_kg'] = floatval($data['mass_kg']);
        }
        if (isset($data['rotation_period_hours'])) {
            $data['rotation_period_hours'] = floatval($data['rotation_period_hours']);
        }
        
        $result = $planetsCollection->updateOne(
            ['_id' => $planetId],
            ['$set' => $data]
        );
        
        $updatedPlanet = $planetsCollection->findOne(['_id' => $planetId]);
        
        echo json_encode([
            'success' => true,
            'modified' => $result->getModifiedCount(),
            'planet' => $updatedPlanet
        ]);
    }
     
    else {
        $planetName = $data['name'] ?? null;
        $updates = $data['updates'] ?? [];
        
        if (!$planetName) {
            echo json_encode(['error' => 'Nom de planète manquant']);
            exit;
        }
          
        $result = $planetsCollection->updateOne(
            ['name' => $planetName],
            ['$set' => $updates]
        );
        
        $updatedPlanet = $planetsCollection->findOne(['name' => $planetName]);
        
        echo json_encode([
            'success' => true,
            'modified' => $result->getModifiedCount(),
            'planet' => $updatedPlanet
        ]);
    } 
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>