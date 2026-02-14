<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

// Validation du nom (obligatoire)
if (!$data || !isset($data['name']) || trim($data['name']) === '') {
    echo json_encode(['error' => 'Le nom est obligatoire']);
    exit;
}

// Validation de l'agence (obligatoire)
if (!isset($data['agency']) || trim($data['agency']) === '') {
    echo json_encode(['error' => 'L\'agence est obligatoire']);
    exit;
}

// Validation du budget (si present)
if (isset($data['budget_usd'])) {
    if (!is_numeric($data['budget_usd'])) {
        echo json_encode(['error' => 'Le budget doit etre un nombre']);
        exit;
    }
    if ($data['budget_usd'] < 0) {
        echo json_encode(['error' => 'Le budget doit etre positif']);
        exit;
    }
    $data['budget_usd'] = (float)$data['budget_usd'];
}

try {
    $missionsCollection = Database::getCollection('missions');
    
    // Convertir les dates
    if (isset($data['launch_date'])) {
        $timestamp = strtotime($data['launch_date']);
        if ($timestamp === false) {
            echo json_encode(['error' => 'Date de lancement invalide']);
            exit;
        }
        $data['launch_date'] = new MongoDB\BSON\UTCDateTime($timestamp * 1000);
    }
    
    if (isset($data['arrival_date']) && $data['arrival_date']) {
        $timestamp = strtotime($data['arrival_date']);
        if ($timestamp === false) {
            echo json_encode(['error' => 'Date d\'arrivee invalide']);
            exit;
        }
        $data['arrival_date'] = new MongoDB\BSON\UTCDateTime($timestamp * 1000);
    } else {
        $data['arrival_date'] = null;
    }
    
    // Convertir target_planet_id si present
    if (isset($data['target_planet_id']) && $data['target_planet_id']) {
        try {
            $data['target_planet_id'] = new MongoDB\BSON\ObjectId($data['target_planet_id']);
        } catch (Exception $e) {
            echo json_encode(['error' => 'ID de planete invalide']);
            exit;
        }
    }
    
    // Inserer la mission
    $result = $missionsCollection->insertOne($data);
    
    echo json_encode([
        'success' => true,
        'insertedId' => (string)$result->getInsertedId()
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>