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

// Validation de l'agence si presente
if (isset($data['agency']) && trim($data['agency']) === '') {
    echo json_encode(['error' => 'L\'agence ne peut pas etre vide']);
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
    
    // Extraire l'ID
    $id = $data['_id'];
    unset($data['_id']);
    
    // Convertir les dates si presentes
    if (isset($data['launch_date'])) {
        $timestamp = strtotime($data['launch_date']);
        if ($timestamp === false) {
            echo json_encode(['error' => 'Date de lancement invalide']);
            exit;
        }
        $data['launch_date'] = new MongoDB\BSON\UTCDateTime($timestamp * 1000);
    }
    
    if (isset($data['arrival_date'])) {
        if ($data['arrival_date']) {
            $timestamp = strtotime($data['arrival_date']);
            if ($timestamp === false) {
                echo json_encode(['error' => 'Date d\'arrivee invalide']);
                exit;
            }
            $data['arrival_date'] = new MongoDB\BSON\UTCDateTime($timestamp * 1000);
        } else {
            $data['arrival_date'] = null;
        }
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
    
    // Mettre a jour
    $result = $missionsCollection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($id)],
        ['$set' => $data]
    );
    
    if ($result->getModifiedCount() > 0 || $result->getMatchedCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Mission non trouvee']);
    }
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>