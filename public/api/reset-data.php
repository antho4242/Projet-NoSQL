<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();
    $planetsCollection = Database::getCollection('planets');
    
    // Réinitialise toutes les planètes
    $planetsCollection->updateMany(
        [],
        ['$set' => [
            'health' => 100,
            'max_health' => 100,
            'destroyed' => false
        ]],
        ['$unset' => ['destruction_date' => '']]
    );
    
    // Réinitialise les températures aux valeurs d'origine
    
    $originalTemps = [
        'Mercure' => 167,
        'Vénus' => 462,
        'Terre' => 15,
        'Mars' => -63,
        'Jupiter' => -110,
        'Saturne' => -140,
        'Uranus' => -195,
        'Neptune' => -200
    ];
    
    foreach ($originalTemps as $planetName => $temp) {
        $planetsCollection->updateOne(
            ['name' => $planetName],
            ['$set' => ['temperature_celsius.average' => $temp]]
        );
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Données réinitialisées avec succès'
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>