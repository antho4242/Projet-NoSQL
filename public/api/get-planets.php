<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

try {
    $planetsCollection = Database::getCollection('planets');
    
    // Récupérer toutes les planètes  
    
    $planets = $planetsCollection->find()->toArray();
    
    echo json_encode([
        'success' => true,
        'planets' => $planets
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>