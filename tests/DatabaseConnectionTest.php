<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

/**
 * Test de connexion à la base de données MongoDB
 */
class DatabaseConnectionTest extends TestCase
{
    private $db;

    /**
     * Initialisation avant chaque test
     */
    protected function setUp(): void
    {
        require_once __DIR__ . '/../config/database.php';
        $this->db = \Database::getConnection();
    }

    /**
     * Test : La connexion à MongoDB doit être établie
     */
    public function testDatabaseConnectionIsEstablished()
    {
        $this->assertNotNull($this->db, "La connexion à la base de données ne doit pas être null");
        $this->assertInstanceOf(\MongoDB\Database::class, $this->db, "L'objet doit être une instance de MongoDB\Database");
    }

    /**
     * Test : Le nom de la base de données doit être correct
     */
    public function testDatabaseNameIsCorrect()
    {
        $dbName = $this->db->getDatabaseName();
        $this->assertEquals('solar_system', $dbName, "Le nom de la base de données devrait être 'solar_system'");
    }

    /**
     * Test : Récupération d'une collection doit fonctionner
     */
    public function testCanGetCollection()
    {
        $collection = \Database::getCollection('planets');
        $this->assertNotNull($collection, "La collection ne doit pas être null");
        $this->assertInstanceOf(\MongoDB\Collection::class, $collection, "L'objet doit être une instance de MongoDB\Collection");
    }

    /**
     * Test : Le nom de la collection doit être correct
     */
    public function testCollectionNameIsCorrect()
    {
        $collection = \Database::getCollection('planets');
        $collectionName = $collection->getCollectionName();
        $this->assertEquals('planets', $collectionName, "Le nom de la collection devrait être 'planets'");
    }
}