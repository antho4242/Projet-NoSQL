<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$planetName = $data['name'] ?? null;

if (!$planetName) {
    echo json_encode(['error' => 'Nom de planète manquant']);
    exit;
}

try {
    $planetsCollection = Database::getCollection('planets');
    
    // Marquer la planète comme detruite 
    $result = $planetsCollection->updateOne(
        ['name' => $planetName],
        ['$set' => [
            'destroyed' => true,
            'destruction_date' => new MongoDB\BSON\UTCDateTime()
        ]]
    );
    
    echo json_encode([
        'success' => true,
        'deleted' => $result->getModifiedCount()
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>