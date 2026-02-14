<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$missionName = $data['name'] ?? null;

if (!$missionName) {
    echo json_encode(['error' => 'Nom de mission manquant']);
    exit;
}

try {
    $missionsCollection = Database::getCollection('missions');
    
    $result = $missionsCollection->deleteOne(['name' => $missionName]);
    
    echo json_encode([
        'success' => true,
        'deleted' => $result->getDeletedCount()
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>