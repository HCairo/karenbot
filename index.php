<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Démarrer une nouvelle session ou reprendre la session existante
session_start();

// Charger les dépendances de Composer
require_once 'vendor/autoload.php';

// Définir des constantes pour divers chemins de répertoires
const CONT = 'app/controllers/';
const MOD = 'app/models/';
const VIEW = 'app/views/';
const JS  = 'assets/js/';
const CSS = 'assets/css/';
const IMG = 'assets/img/';
const TMP = 'assets/templates/';

// Charger les variables d'environnement depuis le fichier .env
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Charger le routeur pour les actions du contrôleur
require_once 'router.php';
// Inclure les templates de base
require_once TMP . 'top.php';    // Template de haut de page
require_once TMP . 'menu.php';   // Template de menu
// Inclure le template de bas de page
require_once TMP . 'bottom.php';