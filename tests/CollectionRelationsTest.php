<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use MongoDB\BSON\ObjectId;

/**
 * Tests des relations entre les collections
 */
class CollectionRelationsTest extends TestCase
{
    private $planetsCollection;
    private $moonsCollection;
    private $missionsCollection;
    private $testPlanetId;

    protected function setUp(): void
    {
        require_once __DIR__ . '/../config/database.php';
        $this->planetsCollection = \Database::getCollection('planets');
        $this->moonsCollection = \Database::getCollection('moons');
        $this->missionsCollection = \Database::getCollection('missions');
    }

    protected function tearDown(): void
    {
        // Nettoyage des données de test
        if ($this->testPlanetId) {
            $this->planetsCollection->deleteOne(['_id' => $this->testPlanetId]);
            $this->moonsCollection->deleteMany(['planet_id' => $this->testPlanetId]);
            $this->missionsCollection->deleteMany(['target_planet_id' => $this->testPlanetId]);
        }
    }

    /**
     * Test : Une lune doit être liée à une planète
     */
    public function testMoonIsLinkedToPlanet()
    {
        // Créer une planète de test
        $planetResult = $this->planetsCollection->insertOne([
            'name' => 'Test Planet With Moon',
            'type' => 'Test'
        ]);
        $this->testPlanetId = $planetResult->getInsertedId();

        // Créer une lune liée à cette planète
        $moonResult = $this->moonsCollection->insertOne([
            'name' => 'Test Moon',
            'planet_id' => $this->testPlanetId,
            'planet_name' => 'Test Planet With Moon',
            'diameter_km' => 1000
        ]);

        // Vérifier que la lune est bien liée
        $moon = $this->moonsCollection->findOne(['_id' => $moonResult->getInsertedId()]);
        
        $this->assertNotNull($moon, "La lune doit exister");
        $this->assertEquals($this->testPlanetId, $moon['planet_id'], "L'ID de la planète doit correspondre");
        $this->assertEquals('Test Planet With Moon', $moon['planet_name'], "Le nom de la planète doit correspondre");
    }

    /**
     * Test : Une mission doit être liée à une planète cible
     */
    public function testMissionIsLinkedToPlanet()
    {
        // Créer une planète de test
        $planetResult = $this->planetsCollection->insertOne([
            'name' => 'Test Target Planet',
            'type' => 'Test'
        ]);
        $this->testPlanetId = $planetResult->getInsertedId();

        // Créer une mission vers cette planète
        $missionResult = $this->missionsCollection->insertOne([
            'name' => 'Test Mission to Planet',
            'agency' => 'PHPUnit Space Agency',
            'target_planet_id' => $this->testPlanetId,
            'target_planet_name' => 'Test Target Planet',
            'status' => 'Test',
            'launch_date' => new \MongoDB\BSON\UTCDateTime()
        ]);

        // Vérifier la relation
        $mission = $this->missionsCollection->findOne(['_id' => $missionResult->getInsertedId()]);
        
        $this->assertEquals($this->testPlanetId, $mission['target_planet_id'], "L'ID de la planète cible doit correspondre");
        $this->assertEquals('Test Target Planet', $mission['target_planet_name'], "Le nom de la planète cible doit correspondre");
    }

    /**
     * Test : Récupérer toutes les lunes d'une planète
     */
    public function testCanGetAllMoonsOfPlanet()
    {
        // Créer une planète
        $planetResult = $this->planetsCollection->insertOne([
            'name' => 'Test Planet Multiple Moons',
            'type' => 'Test'
        ]);
        $this->testPlanetId = $planetResult->getInsertedId();

        // Créer plusieurs lunes pour cette planète
        $this->moonsCollection->insertMany([
            ['name' => 'Test Moon 1', 'planet_id' => $this->testPlanetId, 'planet_name' => 'Test Planet Multiple Moons'],
            ['name' => 'Test Moon 2', 'planet_id' => $this->testPlanetId, 'planet_name' => 'Test Planet Multiple Moons'],
            ['name' => 'Test Moon 3', 'planet_id' => $this->testPlanetId, 'planet_name' => 'Test Planet Multiple Moons']
        ]);

        // Récupérer toutes les lunes de cette planète
        $moons = $this->moonsCollection->find(['planet_id' => $this->testPlanetId])->toArray();

        $this->assertCount(3, $moons, "La planète doit avoir 3 lunes");
    }

    /**
     * Test : Récupérer toutes les missions vers une planète
     */
    public function testCanGetAllMissionsToPlanet()
    {
        // Créer une planète
        $planetResult = $this->planetsCollection->insertOne([
            'name' => 'Test Popular Planet',
            'type' => 'Test'
        ]);
        $this->testPlanetId = $planetResult->getInsertedId();

        // Créer plusieurs missions vers cette planète
        $this->missionsCollection->insertMany([
            [
                'name' => 'Test Mission 1',
                'target_planet_id' => $this->testPlanetId,
                'target_planet_name' => 'Test Popular Planet',
                'status' => 'Test'
            ],
            [
                'name' => 'Test Mission 2',
                'target_planet_id' => $this->testPlanetId,
                'target_planet_name' => 'Test Popular Planet',
                'status' => 'Test'
            ]
        ]);

        // Récupérer toutes les missions
        $missions = $this->missionsCollection->find(['target_planet_id' => $this->testPlanetId])->toArray();

        $this->assertCount(2, $missions, "La planète doit avoir 2 missions");
    }

    /**
     * Test : Vérifier l'intégrité référentielle (une planète existe pour une lune)
     */
    public function testReferentialIntegrityMoonToPlanet()
    {
        // Récupérer une lune existante (de la base de données seed)
        $moon = $this->moonsCollection->findOne(['name' => 'Lune']);
        
        if ($moon && isset($moon['planet_id'])) {
            // Vérifier que la planète référencée existe
            $planet = $this->planetsCollection->findOne(['_id' => $moon['planet_id']]);
            
            $this->assertNotNull($planet, "La planète référencée par la lune doit exister");
            $this->assertEquals($moon['planet_name'], $planet['name'], "Le nom de la planète doit correspondre");
        } else {
            $this->markTestSkipped("Aucune lune trouvée dans la base de données");
        }
    }
}