# Solar System Explorer

Application web de gestion et visualisation du système solaire développée avec PHP et MongoDB.

## Contexte du projet

Ce projet a été réalisé dans le cadre de ma formation pour apprendre à manipuler MongoDB avec PHP. J'ai choisi de créer une application de gestion de données astronomiques (planètes, lunes, missions spatiales) avec une visualisation 3D du système solaire.

Le thème spatial m'a permis de travailler avec différents types de données complexes comme les dates, les coordonnées, les tableaux et les objets imbriqués.

## Fonctionnalités

- Visualisation 3D du système solaire avec Three.js
- Gestion complète des planètes (ajout, modification, suppression)
- Gestion des missions spatiales
- Recherche et filtrage des données
- Graphiques de comparaison entre planètes
- Système de simulation de catastrophes (juste pour le fun)

## Technologies utilisées

- **Backend** : PHP 8.3
- **Base de données** : MongoDB
- **Frontend** : HTML, CSS, JavaScript
- **Bibliothèques** : 
  - Three.js pour la visualisation 3D
  - MongoDB PHP Library
  - Chart.js pour les graphiques
- **Tests** : PHPUnit 9

## Structure du projet

```
mon-projet-mongodb/
├── config/
│   └── database.php          # Configuration MongoDB
├── data/
│   └── seed.php              # Script d'initialisation des données
├── public/
│   ├── api/                  # Endpoints API pour le CRUD
│   ├── css/                  # Feuilles de style
│   ├── js/                   # Scripts JavaScript
│   ├── index.php             # Page d'accueil avec visualisation 3D
│   ├── planets.php           # Gestion des planètes
│   ├── missions.php          # Gestion des missions
│   └── charts.php            # Graphiques comparatifs
├── tests/                    # Tests unitaires PHPUnit
└── vendor/                   # Dépendances Composer
```

## Collections MongoDB

Le projet utilise 4 collections liées entre elles :

- **planets** : Les 8 planètes du système solaire avec leurs caractéristiques
- **moons** : Les satellites naturels (liés aux planètes via `planet_id`)
- **missions** : Les missions spatiales (liées aux planètes via `target_planet_id`)
- **events** : Les événements astronomiques (liés aux planètes via `planets_involved`)

## Installation

### Prérequis

- PHP 8.0 ou supérieur
- MongoDB installé et en cours d'exécution
- Composer

### Étapes d'installation

1. Cloner le projet
```bash
git clone https://github.com/antho4242/Projet-NoSQL.git
cd Projet-NoSQL
```

2. Installer les dépendances
```bash
composer install
```

3. Configuration de MongoDB

Modifier le fichier `config/database.php` si nécessaire pour adapter la connexion à votre installation MongoDB :
```php
private static $connectionString = 'mongodb://localhost:27017';
private static $databaseName = 'solar_system';
```

4. Initialiser la base de données

Lancer le script pour créer les collections et insérer les données :
```bash
php data/seed.php
```

Ou via navigateur si vous utilisez un serveur web :
```
http://localhost/votre-dossier/data/seed.php
```

5. Lancer le serveur PHP

```bash
php -S localhost:8000 -t public
```

6. Accéder à l'application

Ouvrir votre navigateur à l'adresse :
```
http://localhost:8000
```

## Utilisation

### Page d'accueil (Visualisation 3D)
- Cliquer sur une planète pour voir ses détails
- Utiliser les sliders pour simuler des catastrophes
- Contrôler la vitesse de rotation du système

### Page Planètes
- Voir la liste complète des planètes
- Rechercher une planète par nom
- Ajouter, modifier ou supprimer des planètes

### Page Missions
- Consulter les missions spatiales passées et en cours
- Filtrer par statut ou agence
- Gérer les missions

### Page Graphiques
- Comparer les tailles des planètes
- Visualiser les distances au Soleil
- Analyser les périodes orbitales

## Tests

Le projet contient une suite de tests unitaires pour vérifier le bon fonctionnement des différentes fonctionnalités.

### Lancer tous les tests

```bash
php vendor/bin/phpunit
```

Sur Windows :
```bash
php vendor\bin\phpunit
```

### Lancer un fichier de test spécifique

```bash
php vendor/bin/phpunit tests/DatabaseConnectionTest.php
```

### Couverture des tests

Les tests couvrent :
- La connexion à MongoDB
- Les opérations CRUD sur les collections
- Les relations entre collections
- La validation des types de données
- Les recherches et filtres


## API

Les endpoints API sont disponibles dans le dossier `public/api/` :

### Planètes
- `POST /api/add-planet.php` - Ajouter une planète
- `PUT /api/update-planet.php` - Modifier une planète
- `DELETE /api/delete-planet.php` - Supprimer une planète
- `GET /api/get-planets.php` - Récupérer les planètes

### Missions
- `POST /api/add-mission.php` - Ajouter une mission
- `PUT /api/update-mission.php` - Modifier une mission
- `DELETE /api/delete-mission.php` - Supprimer une mission

### Utilitaires
- `POST /api/reset-data.php` - Réinitialiser les données

## Types de données utilisés

Le projet utilise différents types de données MongoDB pour optimiser le stockage :

- **UTCDateTime** : Pour les dates (discovery_date, launch_date, etc.)
- **ObjectId** : Pour les références entre collections
- **Arrays** : Pour les listes (atmosphere, objectives, crew)
- **Embedded Documents** : Pour les objets imbriqués (temperature_celsius)
- **Boolean** : Pour les flags (has_rings, has_ocean)
- **Int32/Int64** : Pour les nombres entiers
- **Double** : Pour les nombres décimaux
- **String** : Pour les textes

## Points techniques intéressants

- Utilisation de références entre collections pour maintenir l'intégrité des données
- Gestion des types de données complexes MongoDB (UTCDateTime, BSONDocument)
- Visualisation 3D avec calculs de trajectoires orbitales
- API REST pour les opérations CRUD
- Tests unitaires avec gestion des cas particuliers MongoDB

## Difficultés rencontrées

Quelques problèmes que j'ai du résoudre pendant le développement :
- Comprendre les différences de types entre MongoDB et PHP (notamment les BSONDocument qui ne sont pas des tableaux classiques)
- Synchroniser l'animation 3D avec les données récupérées depuis la base
- Optimiser les requêtes pour éviter de faire trop d'appels à la base
- Gérer les relations entre collections et maintenir la cohérence des données

## Auteur

Anthony Valour - 2025

## Note

Projet réalisé dans le cadre de ma formation. Les données astronomiques sont approximatives et utilisées uniquement dans un but pédagogique.