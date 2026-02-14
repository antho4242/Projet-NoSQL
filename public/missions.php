<?php
require_once __DIR__ . '/../config/database.php';

// Récupérer toutes les missions
$missionsCollection = Database::getCollection('missions');
$missions = $missionsCollection->find()->toArray();

// Récupérer les planètes pour le select
$planetsCollection = Database::getCollection('planets');
$planets = $planetsCollection->find(['destroyed' => ['$ne' => true]])->toArray();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Missions - Solar System Explorer</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .crud-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: rgba(26, 32, 44, 0.6);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .action-bar h2 {
            color: #667eea;
            margin: 0;
        }
        
        .btn-add {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .missions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 2rem;
        }
        
        .mission-card {
            background: rgba(26, 32, 44, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s;
        }
        
        .mission-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .mission-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }
        
        .mission-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .mission-agency {
            background: rgba(102, 126, 234, 0.2);
            color: #667eea;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .mission-info {
            margin: 1rem 0;
        }
        
        .mission-info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .mission-info-label {
            color: #a0aec0;
            font-size: 0.9rem;
        }
        
        .mission-info-value {
            color: #fff;
            font-weight: 600;
        }
        
        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-active {
            background: rgba(72, 187, 120, 0.2);
            color: #48bb78;
        }
        
        .status-completed {
            background: rgba(66, 153, 225, 0.2);
            color: #4299e1;
        }
        
        .mission-objectives {
            margin: 1rem 0;
        }
        
        .mission-objectives h4 {
            color: #cbd5e0;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .objectives-list {
            list-style: none;
            padding: 0;
        }
        
        .objectives-list li {
            padding: 0.3rem 0;
            color: #a0aec0;
            font-size: 0.9rem;
        }
        
        .objectives-list li:before {
            content: "✓ ";
            color: #48bb78;
            font-weight: bold;
            margin-right: 0.5rem;
        }
        
        .mission-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .btn-edit, .btn-delete {
            flex: 1;
            padding: 0.6rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-edit {
            background: #4299e1;
            color: white;
        }
        
        .btn-edit:hover {
            background: #3182ce;
        }
        
        .btn-delete {
            background: #fc8181;
            color: white;
        }
        
        .btn-delete:hover {
            background: #f56565;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: rgba(26, 32, 44, 0.95);
            border-radius: 16px;
            padding: 2rem;
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .modal-header h2 {
            color: #667eea;
            margin: 0;
        }
        
        .btn-close {
            background: none;
            border: none;
            font-size: 2rem;
            color: #fff;
            cursor: pointer;
            line-height: 1;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #cbd5e0;
            font-weight: 600;
        }
        
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
        }
        
        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .objectives-input {
            margin-bottom: 0.5rem;
        }
        
        .btn-add-objective {
            background: rgba(102, 126, 234, 0.2);
            color: #667eea;
            border: 1px dashed #667eea;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
        }
        
        .btn-submit {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
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
                <li><a href="missions.php" class="active">Missions</a></li>
                <li><a href="charts.php">Graphiques</a></li>
            </ul>
        </div>
    </nav>

    <div class="crud-container">
        
        <div class="action-bar">
            <h2>Gestion Missions Spatiales</h2>
            <button class="btn-add" onclick="openAddModal()">➕ Ajouter une mission</button>
        </div>

        
        <div class="missions-grid">
            <?php foreach ($missions as $mission): ?>
                <div class="mission-card">
                    <div class="mission-header">
                        <div>
                            <div class="mission-title"><?= htmlspecialchars($mission['name']) ?></div>
                            <span class="mission-agency"><?= htmlspecialchars($mission['agency']) ?></span>
                        </div>
                        <span class="status-badge status-<?= strtolower($mission['status']) === 'active' ? 'active' : 'completed' ?>">
                            <?= htmlspecialchars($mission['status']) ?>
                        </span>
                    </div>
                    
                    <div class="mission-info">
                        <div class="mission-info-item">
                            <span class="mission-info-label">🎯 Cible</span>
                            <span class="mission-info-value"><?= htmlspecialchars($mission['target_planet_name']) ?></span>
                        </div>
                        <div class="mission-info-item">
                            <span class="mission-info-label">📅 Lancement</span>
                            <span class="mission-info-value"><?= date('d/m/Y', $mission['launch_date']->toDateTime()->getTimestamp()) ?></span>
                        </div>
                        <?php if (isset($mission['arrival_date']) && $mission['arrival_date']): ?>
                        <div class="mission-info-item">
                            <span class="mission-info-label">🛬 Arrivée</span>
                            <span class="mission-info-value"><?= date('d/m/Y', $mission['arrival_date']->toDateTime()->getTimestamp()) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="mission-info-item">
                            <span class="mission-info-label">🔧 Type</span>
                            <span class="mission-info-value"><?= htmlspecialchars($mission['mission_type']) ?></span>
                        </div>
                    </div>
                    
                    <?php if (!empty($mission['objectives'])): ?>
                    <div class="mission-objectives">
                        <h4>Objectifs :</h4>
                        <ul class="objectives-list">
                            <?php 
                            $objectives = $mission['objectives'];
                            // Convertir BSONArray en array PHP
                            if ($objectives instanceof MongoDB\Model\BSONArray) {
                                $objectives = $objectives->getArrayCopy();
                            }
                            $count = 0;
                            foreach ($objectives as $objective): 
                                if ($count >= 3) break;
                                $count++;
                            ?>
                                <li><?= htmlspecialchars($objective) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mission-actions">
                        <button class="btn-edit" data-mission="<?= base64_encode(json_encode($mission)) ?>">✏️ Modifier</button>
                        <button class="btn-delete" onclick="deleteMission('<?= addslashes($mission['name']) ?>')">🗑️ Supprimer</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    
    <div id="missionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">➕ Ajouter une mission</h2>
                <button class="btn-close" onclick="closeModal()">&times;</button>
            </div>
            
            <form id="missionForm">
                <input type="hidden" id="missionId" name="missionId">
                <input type="hidden" id="isEdit" name="isEdit" value="false">
                
                <div class="form-group">
                    <label>Nom de la mission *</label>
                    <input type="text" id="name" name="name" placeholder="Ex: Mars 2030" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Agence spatiale *</label>
                        <select id="agency" name="agency" required>
                            <option value="NASA">NASA</option>
                            <option value="ESA">ESA</option>
                            <option value="SpaceX">SpaceX</option>
                            <option value="Roscosmos">Roscosmos</option>
                            <option value="CNSA">CNSA</option>
                            <option value="ISRO">ISRO</option>
                            <option value="JAXA">JAXA</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Statut *</label>
                        <select id="status" name="status" required>
                            <option value="Active">Active</option>
                            <option value="Terminée">Terminée</option>
                            <option value="Planifiée">Planifiée</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Planète cible *</label>
                        <select id="target_planet" name="target_planet" required>
                            <?php foreach ($planets as $planet): ?>
                                <option value="<?= htmlspecialchars($planet['name']) ?>"><?= htmlspecialchars($planet['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Type de mission *</label>
                        <select id="mission_type" name="mission_type" required>
                            <option value="Orbiteur">Orbiteur</option>
                            <option value="Rover">Rover</option>
                            <option value="Sonde">Sonde</option>
                            <option value="Habité">Habité</option>
                            <option value="Atterrisseur">Atterrisseur</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Date de lancement *</label>
                        <input type="date" id="launch_date" name="launch_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Date d'arrivée</label>
                        <input type="date" id="arrival_date" name="arrival_date">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Budget (USD)</label>
                    <input type="number" id="budget_usd" name="budget_usd" placeholder="Ex: 2700000000">
                </div>
                
                <div class="form-group">
                    <label>Objectifs (un par ligne)</label>
                    <textarea id="objectives" name="objectives" placeholder="Recherche de vie&#10;Collecte d'échantillons&#10;Cartographie"></textarea>
                </div>
                
                <button type="submit" class="btn-submit">💾 Enregistrer</button>
            </form>
        </div>
    </div>

    <script>
        const planetsData = <?= json_encode($planets) ?>;
    </script>
    <script src="js/missions-crud.js"></script>
</body>
</html>