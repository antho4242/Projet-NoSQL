<?php
require_once __DIR__ . '/../config/database.php';

$planetsCollection = Database::getCollection('planets');
$planets = $planetsCollection->find()->toArray();

foreach ($planets as &$planet) {
    if (!isset($planet['health'])) {
        $planetsCollection->updateOne(
            ['_id' => $planet['_id']],
            ['$set' => ['health' => 100, 'max_health' => 100]]
        );
        $planet['health'] = 100;
        $planet['max_health'] = 100;
    }
}
unset($planet);
?>
<!DOCTYPE html>
<html lang="fr">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solar System Explorer</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="logo">🚀 Solar System Explorer</h1>
            <ul class="nav-menu">
                <li><a href="index.php" class="active">Systeme Solaire 3D</a></li>
                <li><a href="planets.php">Planetes</a></li>
                <li><a href="missions.php">Missions</a></li>
                <li><a href="charts.php">Graphiques</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <aside class="sidebar">
            <h2>📊 Informations</h2>
            <div id="planet-info">
                <p>Cliquez sur une planete pour voir details</p>
            </div>
            
            <h3>🌍 Liste planetes</h3>
            <ul id="planet-list">
                <?php foreach ($planets as $planet): ?>
                    <li class="planet-item" data-planet="<?= $planet['name'] ?>" style="border-left: 4px solid <?= $planet['color'] ?>">
                        <strong><?= $planet['name'] ?></strong>
                        <br>
                        <small><?= $planet['type'] ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
            <hr style="margin: 2rem 0; border-color: rgba(255,255,255,0.1);">
            
            <h3>⚠️ Simulations Catastrophes</h3>
            <div id="catastrophe-panel">
                <div class="catastrophe-control">
                    <label>🔥 Temperature Soleil</label>
                    <input type="range" id="sun-temp-slider" min="0" max="200" value="100" step="1">
                    <span id="sun-temp-value">100%</span>
                </div>
                
                <div class="catastrophe-control">
                    <label>💥 Probabilite asteroide</label>
                    <input type="range" id="asteroid-slider" min="0" max="100" value="0" step="1">
                    <span id="asteroid-value">0%</span>
                </div>
                
                <div class="catastrophe-control">
                    <label>🌌 Gravite systeme</label>
                    <input type="range" id="gravity-slider" min="10" max="200" value="100" step="5">
                    <span id="gravity-value">100%</span>
                </div>
                
                <button id="supernova-btn" class="catastrophe-btn">🌞 Declencher Supernova</button>
                <button id="reset-catastrophe-btn" class="catastrophe-btn reset">🔄 Reinitialiser</button>
            </div>
        </aside>

        <main class="main-content">
            <div id="canvas-container">
                <canvas id="solar-system-canvas"></canvas>
            </div>
            
            <div class="controls">
                <button id="play-pause-btn">⏸️ Pause</button>
                <button id="reset-btn">🔄 Reset</button>
                <label>
                    Vitesse: <input type="range" id="speed-slider" min="0" max="5" value="1" step="0.1">
                    <span id="speed-value">1x</span>
                </label>
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        const planetsData = <?= json_encode($planets) ?>;
    </script>
    <script src="js/solar-system-3d.js"></script>
</body>
</html>