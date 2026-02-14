<?php

require_once __DIR__ . '/../vendor/autoload.php';

class Database {
    private static $client = null;
    private static $db = null;
    
    public static function getConnection() {
        if (self::$client === null) {
            try {
                self::$client = new MongoDB\Client('mongodb://localhost:27017');
                self::$db = self::$client->solar_system;
            } catch (Exception $e) {
                die("Erreur connexion MongoDB: " . $e->getMessage());
            }
        }
        return self::$db;
    }
    
    public static function getCollection($collectionName) {
        $db = self::getConnection();
        return $db->$collectionName;
    }
}