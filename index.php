<?php
// Enable displaying errors for debugging
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start a new session or resume the existing session
// Démarrer une nouvelle session ou reprendre la session existante
session_start();

// Define constants for various directory paths
// Définir des constantes pour divers chemins de répertoires
const CONT = 'app/controllers/'; // Controller directory / Répertoire des contrôleurs
const MOD = 'app/models/';       // Model directory / Répertoire des modèles
const VIEW = 'app/views/';       // View directory / Répertoire des vues
const JS  = 'assets/js/';        // JavaScript directory / Répertoire des fichiers JavaScript
const CSS = 'assets/css/';       // CSS directory / Répertoire des fichiers CSS
const IMG = 'assets/img/';       // Images directory / Répertoire des images
const TMP = 'assets/templates/'; // Templates directory / Répertoire des templates

// Include template and routing files
// Inclure les fichiers de template et de routage
require_once TMP . 'menu.php';   // Include the menu template / Inclure le template de menu
require_once TMP . 'top.php';    // Include the top template / Inclure le template de haut de page
require_once 'router.php';       // Include the router script / Inclure le script de routage
require_once TMP . 'bottom.php'; // Include the bottom template / Inclure le template de bas de page