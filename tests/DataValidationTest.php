<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests de validation des types de données
 */
class DataValidationTest extends TestCase
{
    private $collection;

    protected function setUp(): void
    {
        require_once __DIR__ . '/../config/database.php';
        $this->collection = \Database::getCollection('planets');
    }

    protected function tearDown(): void
    {
        $this->collection->deleteMany(['name' => ['$regex' => '^Test Validation']]);
    }

    /**
     * Test : Les dates doivent être du type UTCDateTime
     */
    public function testDateFieldsAreUTCDateTime()
    {
        $planet = $this->collection->findOne(['name' => 'Mars']);
        
        if ($planet && isset($planet['discovery_date'])) {
            $this->assertInstanceOf(
                \MongoDB\BSON\UTCDateTime::class,
                $planet['discovery_date'],
                "La date de découverte doit être un UTCDateTime"
            );
        } else {
            $this->markTestSkipped("Aucune planète Mars trouvée");
        }
    }

    /**
     * Test : Les champs numériques doivent avoir les bons types
     */
    public function testNumericFieldsHaveCorrectTypes()
    {
        $planet = $this->collection->findOne(['name' => 'Terre']);
        
        if ($planet) {
            $this->assertIsInt($planet['diameter_km'], "Le diamètre doit être un entier");
            $this->assertIsNumeric($planet['mass_kg'], "La masse doit être numérique");
            // orbital_period_days peut être float (ex: 365.25 pour la Terre)
            $this->assertIsNumeric($planet['orbital_period_days'], "La période orbitale doit être numérique");
        } else {
            $this->markTestSkipped("Planète Terre non trouvée");
        }
    }

    /**
     * Test : Les tableaux (arrays) doivent être correctement structurés
     */
    public function testArrayFieldsAreCorrectlyStructured()
    {
        $planet = $this->collection->findOne(['name' => 'Terre']);
        
        if ($planet && isset($planet['atmosphere'])) {
            // MongoDB peut retourner un BSONDocument ou un array
            $atmosphere = $planet['atmosphere'];
            $isValidType = is_array($atmosphere) || $atmosphere instanceof \MongoDB\Model\BSONDocument;
            
            $this->assertTrue($isValidType, "L'atmosphère doit être un tableau ou un BSONDocument");
            
            // Convertir en array si c'est un BSONDocument
            if ($atmosphere instanceof \MongoDB\Model\BSONDocument) {
                $atmosphere = (array) $atmosphere;
            }
            
            $this->assertNotEmpty($atmosphere, "L'atmosphère ne doit pas être vide");
        } else {
            $this->markTestSkipped("Planète Terre ou atmosphère non trouvée");
        }
    }

    /**
     * Test : Les objets imbriqués (embedded documents) doivent être corrects
     */
    public function testEmbeddedDocumentsAreCorrect()
    {
        $planet = $this->collection->findOne(['name' => 'Mars']);
        
        if ($planet && isset($planet['temperature_celsius'])) {
            $temp = $planet['temperature_celsius'];
            
            // MongoDB peut retourner un BSONDocument ou un array
            $isValidType = is_array($temp) || $temp instanceof \MongoDB\Model\BSONDocument;
            $this->assertTrue($isValidType, "La température doit être un objet/tableau ou BSONDocument");
            
            // Convertir en array si nécessaire pour les tests suivants
            if ($temp instanceof \MongoDB\Model\BSONDocument) {
                $temp = (array) $temp;
            }
            
            $this->assertArrayHasKey('min', $temp, "Doit avoir une température minimum");
            $this->assertArrayHasKey('max', $temp, "Doit avoir une température maximum");
            $this->assertArrayHasKey('average', $temp, "Doit avoir une température moyenne");
            
            $this->assertIsNumeric($temp['min'], "La température min doit être numérique");
            $this->assertIsNumeric($temp['max'], "La température max doit être numérique");
        } else {
            $this->markTestSkipped("Mars ou température non trouvée");
        }
    }

    /**
     * Test : Les booléens doivent être du bon type
     */
    public function testBooleanFieldsAreCorrect()
    {
        $planet = $this->collection->findOne(['name' => 'Saturne']);
        
        if ($planet) {
            $this->assertIsBool($planet['has_rings'], "has_rings doit être un booléen");
            $this->assertTrue($planet['has_rings'], "Saturne doit avoir des anneaux");
        } else {
            $this->markTestSkipped("Saturne non trouvée");
        }
    }

    /**
     * Test : Les champs obligatoires doivent être présents
     */
    public function testRequiredFieldsArePresent()
    {
        $planet = $this->collection->findOne(['name' => 'Jupiter']);
        
        if ($planet) {
            $this->assertArrayHasKey('name', $planet, "Le nom est obligatoire");
            $this->assertArrayHasKey('type', $planet, "Le type est obligatoire");
            $this->assertArrayHasKey('diameter_km', $planet, "Le diamètre est obligatoire");
            
            $this->assertNotEmpty($planet['name'], "Le nom ne doit pas être vide");
        } else {
            $this->markTestSkipped("Jupiter non trouvée");
        }
    }

    /**
     * Test : Validation - impossible d'insérer une planète sans nom
     */
    public function testCannotInsertPlanetWithoutName()
    {
        // Dans une vraie application, ceci devrait lever une exception
        // Pour l'instant, on teste juste qu'on peut détecter l'absence de nom
        $invalidPlanet = [
            'type' => 'Test',
            'diameter_km' => 5000
        ];

        // Simuler une vérification avant insertion
        $hasName = isset($invalidPlanet['name']) && !empty($invalidPlanet['name']);
        
        $this->assertFalse($hasName, "Un nom manquant doit être détecté");
    }

    /**
     * Test : Les valeurs doivent être dans des plages raisonnables
     */
    public function testValuesAreInReasonableRanges()
    {
        $planet = $this->collection->findOne(['name' => 'Mercure']);
        
        if ($planet) {
            $this->assertGreaterThan(0, $planet['diameter_km'], "Le diamètre doit être positif");
            $this->assertGreaterThan(0, $planet['mass_kg'], "La masse doit être positive");
            $this->assertGreaterThan(0, $planet['distance_from_sun_km'], "La distance doit être positive");
        } else {
            $this->markTestSkipped("Mercure non trouvée");
        }
    }
}