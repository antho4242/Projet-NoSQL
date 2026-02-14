<?php
require_once __DIR__ . '/../config/database.php';

// Recupere toutes les planetes Non detruites
$planetsCollection = Database::getCollection('planets');
$planets = $planetsCollection->find([
    '$or' => [
        ['destroyed' => ['$exists' => false]],
        ['destroyed' => false]
    ]
])->toArray();

// Récupére toutes les missions 
$missionsCollection = Database::getCollection('missions');
$missions = $missionsCollection->find()->toArray();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graphiques - Solar System Explorer</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .charts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 2rem;
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        
        .chart-card {
            background: rgba(26, 32, 44, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .chart-card h2 {
            color: #667eea;
            margin-bottom: 1.5rem;
            font-size: 1.4rem;
        }
        
        .chart-wrapper {
            position: relative;
            height: 300px;
        }
        
        .filters {
            background: rgba(26, 32, 44, 0.6);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 12px;
            margin: 2rem auto;
            max-width: 1400px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .filters h3 {
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .filter-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .filter-group label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        
        .filter-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="logo">🚀 Solar System Explorer</h1>
            <ul class="nav-menu">
                <li><a href="index.php">Système Solaire 3D</a></li>
                <li><a href="planets.php">Planètes</a></li>
                <li><a href="missions.php">Missions</a></li>
                <li><a href="charts.php" class="active">Graphiques</a></li>
            </ul>
        </div>
    </nav>

    
    <div class="filters">
        <h3>🔍 Filtrer les planètes affichées</h3>
        <div class="filter-group" id="planet-filters">
            <?php foreach ($planets as $planet): ?>
                <label>
                    <input type="checkbox" class="planet-filter" value="<?= $planet['name'] ?>" checked>
                    <?= $planet['name'] ?>
                </label>
            <?php endforeach; ?>
        </div>
        <button id="refresh-data-btn" class="catastrophe-btn" style="width: auto; padding: 0.8rem 2rem;">🔄 Actualiser les données</button>
    </div>

    
    <div class="charts-container">
        
        <div class="chart-card">
            <h2>🌍 Comparaison des diamètres des planètes</h2>
            <div class="chart-wrapper">
                <canvas id="sizeChart"></canvas>
            </div>
        </div>

        
        <div class="chart-card">
            <h2>🌡️ Températures moyennes</h2>
            <div class="chart-wrapper">
                <canvas id="temperatureChart"></canvas>
            </div>
        </div>

        
        <div class="chart-card">
            <h2>☀️ Distance au Soleil</h2>
            <div class="chart-wrapper">
                <canvas id="distanceChart"></canvas>
            </div>
        </div>

        
        <div class="chart-card">
            <h2>💨 Composition de l'atmosphère terrestre</h2>
            <div class="chart-wrapper">
                <canvas id="atmosphereChart"></canvas>
            </div>
        </div>

        
        <div class="chart-card">
            <h2>🚀 Missions spatiales par agence</h2>
            <div class="chart-wrapper">
                <canvas id="missionsChart"></canvas>
            </div>
        </div>

        
        <div class="chart-card">
            <h2>📅 Timeline des missions spatiales</h2>
            <div class="chart-wrapper">
                <canvas id="timelineChart"></canvas>
            </div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Données des planètes depuis PHP
        const planetsData = <?= json_encode($planets) ?>;
        const missionsData = <?= json_encode($missions) ?>;
    </script>
    <script src="js/charts.js"></script>
</body>
</html>