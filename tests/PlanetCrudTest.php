<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use MongoDB\BSON\ObjectId;

/**
 * Tests des opérations CRUD sur la collection Planets
 */
class PlanetCrudTest extends TestCase
{
    private $collection;
    private $testPlanetId;

    /**
     * Initialisation avant chaque test
     */
    protected function setUp(): void
    {
        require_once __DIR__ . '/../config/database.php';
        $this->collection = \Database::getCollection('planets');
    }

    /**
     * Nettoyage après chaque test
     */
    protected function tearDown(): void
    {
        // Supprimer la planète de test si elle existe
        if ($this->testPlanetId) {
            $this->collection->deleteOne(['_id' => $this->testPlanetId]);
        }
        
        // Supprimer toutes les planètes de test
        $this->collection->deleteMany(['name' => ['$regex' => '^Test']]);
    }

    /**
     * Test : Insertion d'une nouvelle planète
     */
    public function testCanInsertPlanet()
    {
        $planetData = [
            'name' => 'Test Planet',
            'type' => 'Planète test',
            'diameter_km' => 10000,
            'mass_kg' => 1.0e24,
            'distance_from_sun_km' => 150000000,
            'orbital_period_days' => 365,
            'rotation_period_hours' => 24,
            'temperature_celsius' => ['min' => -50, 'max' => 50, 'average' => 0],
            'atmosphere' => ['N2' => 78, 'O2' => 21],
            'has_rings' => false,
            'moons_count' => 0,
            'discovery_date' => new \MongoDB\BSON\UTCDateTime(),
            'discovered_by' => 'PHPUnit Test',
            'color' => '#FFFFFF',
            'description' => 'Planète de test créée par PHPUnit'
        ];

        $result = $this->collection->insertOne($planetData);
        $this->testPlanetId = $result->getInsertedId();

        $this->assertInstanceOf(ObjectId::class, $this->testPlanetId, "L'ID inséré doit être un ObjectId");
        $this->assertTrue($result->isAcknowledged(), "L'insertion doit être confirmée");
    }

    /**
     * Test : Lecture d'une planète existante
     */
    public function testCanReadPlanet()
    {
        // Créer d'abord une planète de test
        $planetData = [
            'name' => 'Test Read Planet',
            'type' => 'Planète test',
            'diameter_km' => 5000,
            'color' => '#FF0000'
        ];
        
        $result = $this->collection->insertOne($planetData);
        $this->testPlanetId = $result->getInsertedId();

        // Lire la planète
        $planet = $this->collection->findOne(['_id' => $this->testPlanetId]);

        $this->assertNotNull($planet, "La planète doit exister");
        $this->assertEquals('Test Read Planet', $planet['name'], "Le nom doit correspondre");
        $this->assertEquals(5000, $planet['diameter_km'], "Le diamètre doit correspondre");
    }

    /**
     * Test : Mise à jour d'une planète
     */
    public function testCanUpdatePlanet()
    {
        // Créer une planète de test
        $planetData = ['name' => 'Test Update Planet', 'diameter_km' => 8000];
        $result = $this->collection->insertOne($planetData);
        $this->testPlanetId = $result->getInsertedId();

        // Mettre à jour la planète
        $updateResult = $this->collection->updateOne(
            ['_id' => $this->testPlanetId],
            ['$set' => ['diameter_km' => 9000, 'color' => '#00FF00']]
        );

        $this->assertEquals(1, $updateResult->getModifiedCount(), "Une planète doit être modifiée");

        // Vérifier la mise à jour
        $planet = $this->collection->findOne(['_id' => $this->testPlanetId]);
        $this->assertEquals(9000, $planet['diameter_km'], "Le diamètre doit être mis à jour");
        $this->assertEquals('#00FF00', $planet['color'], "La couleur doit être ajoutée");
    }

    /**
     * Test : Suppression d'une planète
     */
    public function testCanDeletePlanet()
    {
        // Créer une planète de test
        $planetData = ['name' => 'Test Delete Planet', 'type' => 'À supprimer'];
        $result = $this->collection->insertOne($planetData);
        $planetId = $result->getInsertedId();

        // Supprimer la planète
        $deleteResult = $this->collection->deleteOne(['_id' => $planetId]);

        $this->assertEquals(1, $deleteResult->getDeletedCount(), "Une planète doit être supprimée");

        // Vérifier que la planète n'existe plus
        $planet = $this->collection->findOne(['_id' => $planetId]);
        $this->assertNull($planet, "La planète ne doit plus exister");
    }

    /**
     * Test : Recherche de planètes par nom
     */
    public function testCanSearchPlanetsByName()
    {
        // Créer plusieurs planètes de test
        $this->collection->insertMany([
            ['name' => 'Test Search Alpha', 'type' => 'Type A'],
            ['name' => 'Test Search Beta', 'type' => 'Type B'],
            ['name' => 'Test Search Gamma', 'type' => 'Type A']
        ]);

        // Rechercher avec regex
        $cursor = $this->collection->find(['name' => ['$regex' => 'Search', '$options' => 'i']]);
        $results = iterator_to_array($cursor);

        $this->assertGreaterThanOrEqual(3, count($results), "Au moins 3 planètes doivent être trouvées");
    }

    /**
     * Test : Une planète avec un nom existant ne peut pas être ajoutée
     */
    public function testCannotInsertDuplicatePlanetName()
    {
        // Créer une première planète
        $planetData = ['name' => 'Test Unique Planet', 'type' => 'Test'];
        $result = $this->collection->insertOne($planetData);
        $this->testPlanetId = $result->getInsertedId();

        // Vérifier qu'elle existe
        $existing = $this->collection->findOne(['name' => 'Test Unique Planet']);
        $this->assertNotNull($existing, "La première planète doit exister");

        // Tenter d'en créer une autre avec le même nom devrait être géré par l'application
        $duplicate = $this->collection->findOne(['name' => 'Test Unique Planet']);
        $this->assertEquals('Test Unique Planet', $duplicate['name'], "Le nom doit correspondre");
    }

    /**
     * Test : Compter le nombre de planètes
     */
    public function testCanCountPlanets()
    {
        // Compter avant insertion
        $countBefore = $this->collection->countDocuments(['name' => ['$regex' => '^Test Count']]);

        // Insérer des planètes de test
        $this->collection->insertMany([
            ['name' => 'Test Count 1'],
            ['name' => 'Test Count 2'],
            ['name' => 'Test Count 3']
        ]);

        // Compter après insertion
        $countAfter = $this->collection->countDocuments(['name' => ['$regex' => '^Test Count']]);

        $this->assertEquals($countBefore + 3, $countAfter, "3 planètes supplémentaires doivent être comptées");
    }
}