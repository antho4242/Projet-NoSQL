<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['name'])) {
    echo json_encode(['error' => 'Données invalides']);
    exit;
}

try {
    $missionsCollection = Database::getCollection('missions');
    
    // Convertie les dates
    if (isset($data['launch_date'])) {
        $data['launch_date'] = new MongoDB\BSON\UTCDateTime(strtotime($data['launch_date']) * 1000);
    }
    
    if (isset($data['arrival_date']) && $data['arrival_date']) {
        $data['arrival_date'] = new MongoDB\BSON\UTCDateTime(strtotime($data['arrival_date']) * 1000);
    } else {
        $data['arrival_date'] = null;
    }
    
    // Insére la mission
    $result = $missionsCollection->insertOne($data);
    
    echo json_encode([
        'success' => true,
        'insertedId' => (string)$result->getInsertedId()
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>