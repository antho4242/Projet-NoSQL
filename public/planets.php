<?php
require_once __DIR__ . '/../config/database.php';

// Récupérer toutes les planètes
$planetsCollection = Database::getCollection('planets');
$planets = $planetsCollection->find()->toArray();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Planetes - Solar System Explorer</title>
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
        
        .planets-table {
            background: rgba(26, 32, 44, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: rgba(102, 126, 234, 0.2);
        }
        
        th {
            padding: 1rem;
            text-align: left;
            color: #667eea;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
        
        td {
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        tbody tr {
            transition: background 0.3s;
        }
        
        tbody tr:hover {
            background: rgba(102, 126, 234, 0.1);
        }
        
        .planet-name {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .planet-color {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 0.5rem;
            vertical-align: middle;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .health-bar {
            width: 100px;
            height: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            overflow: hidden;
            display: inline-block;
        }
        
        .health-fill {
            height: 100%;
            transition: width 0.3s;
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
        
        .status-destroyed {
            background: rgba(245, 101, 101, 0.2);
            color: #f56565;
        }
        
        .btn-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-edit, .btn-delete {
            padding: 0.5rem 1rem;
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
            min-height: 100px;
            resize: vertical;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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
                <li><a href="planets.php" class="active">Planètes</a></li>
                <li><a href="missions.php">Missions</a></li>
                <li><a href="charts.php">Graphiques</a></li>
            </ul>
        </div>
    </nav>

    <div class="crud-container">
        
        <div class="action-bar">
            <h2>Gestion Planetes</h2>
            <button class="btn-add" onclick="openAddModal()">➕ Ajouter une planète</button>
        </div>

        
        <div class="planets-table">
            <table>
                <thead>
                    <tr>
                        <th>Planète</th>
                        <th>Type</th>
                        <th>Diamètre (km)</th>
                        <th>Distance (M km)</th>
                        <th>Température (°C)</th>
                        <th>Santé</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($planets as $planet): ?>
                        <tr data-planet-id="<?= $planet['_id'] ?>">
                            <td>
                                <span class="planet-color" style="background-color: <?= $planet['color'] ?>"></span>
                                <span class="planet-name"><?= $planet['name'] ?></span>
                            </td>
                            <td><?= $planet['type'] ?></td>
                            <td><?= number_format($planet['diameter_km']) ?></td>
                            <td><?= number_format($planet['distance_from_sun_km'] / 1000000, 2) ?></td>
                            <td><?= $planet['temperature_celsius']['average'] ?>°C</td>
                            <td>
                                <div class="health-bar">
                                    <div class="health-fill" style="width: <?= $planet['health'] ?? 100 ?>%; background: <?= ($planet['health'] ?? 100) > 50 ? '#48bb78' : (($planet['health'] ?? 100) > 25 ? '#ed8936' : '#f56565') ?>"></div>
                                </div>
                                <span style="margin-left: 0.5rem;"><?= $planet['health'] ?? 100 ?>%</span>
                            </td>
                            <td>
                                <?php if (isset($planet['destroyed']) && $planet['destroyed']): ?>
                                    <span class="status-badge status-destroyed">Détruite</span>
                                <?php else: ?>
                                    <span class="status-badge status-active">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-actions">
                                <button class="btn-edit" data-planet="<?= base64_encode(json_encode($planet)) ?>">✏️ Modifier</button>
                                <button class="btn-delete" onclick="deletePlanet('<?= addslashes($planet['name']) ?>')">🗑️ Supprimer</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div id="planetModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">➕ Ajouter une planète</h2>
                <button class="btn-close" onclick="closeModal()">&times;</button>
            </div>
            
            <form id="planetForm">
                <input type="hidden" id="planetId" name="planetId">
                <input type="hidden" id="isEdit" name="isEdit" value="false">
                
                <div class="form-group">
                    <label>Nom de la planète *</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Type *</label>
                        <select id="type" name="type" required>
                            <option value="Planète tellurique">Planète tellurique</option>
                            <option value="Géante gazeuse">Géante gazeuse</option>
                            <option value="Géante de glace">Géante de glace</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Couleur *</label>
                        <input type="color" id="color" name="color" value="#4169E1" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Diamètre (km) *</label>
                        <input type="number" id="diameter_km" name="diameter_km" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Masse (kg) *</label>
                        <input type="text" id="mass_kg" name="mass_kg" placeholder="Ex: 5.972e24" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Distance du Soleil (km) *</label>
                    <input type="number" id="distance_from_sun_km" name="distance_from_sun_km" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Periode orbitale (jours) *</label>
                        <input type="number" id="orbital_period_days" name="orbital_period_days" step="any" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Periode de rotation (heures) *</label>
                        <input type="number" id="rotation_period_hours" name="rotation_period_hours" step="any" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Température min (°C) *</label>
                        <input type="number" id="temp_min" name="temp_min" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Température max (°C) *</label>
                        <input type="number" id="temp_max" name="temp_max" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Température moyenne (°C) *</label>
                    <input type="number" id="temp_avg" name="temp_avg" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre de lunes</label>
                        <input type="number" id="moons_count" name="moons_count" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label>Anneaux ?</label>
                        <select id="has_rings" name="has_rings">
                            <option value="false">Non</option>
                            <option value="true">Oui</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="description" name="description" placeholder="Description de la planète..."></textarea>
                </div>
                
                <button type="submit" class="btn-submit">💾 Enregistrer</button>
            </form>
        </div>
    </div>

    <script src="js/planets-crud.js"></script>
</body>
</html>