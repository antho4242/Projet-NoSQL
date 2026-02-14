<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['missionId'])) {
    echo json_encode(['error' => 'Données invalides']);
    exit;
}

try {
    $missionsCollection = Database::getCollection('missions');
    $missionId = new MongoDB\BSON\ObjectId($data['missionId']);
    unset($data['missionId']);
    
    // Convertie les dates
    if (isset($data['launch_date'])) {
        $data['launch_date'] = new MongoDB\BSON\UTCDateTime(strtotime($data['launch_date']) * 1000);
    }
    
    if (isset($data['arrival_date']) && $data['arrival_date']) {
        $data['arrival_date'] = new MongoDB\BSON\UTCDateTime(strtotime($data['arrival_date']) * 1000);
    } else {
        $data['arrival_date'] = null;
    }
    
    $result = $missionsCollection->updateOne(
        ['_id' => $missionId],
        ['$set' => $data]
    );
    
    echo json_encode([
        'success' => true,
        'modified' => $result->getModifiedCount()
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>