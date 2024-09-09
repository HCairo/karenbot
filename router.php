<?php
// Charger les dépendances de Composer / Load Composer dependencies
require_once('vendor/autoload.php');

// Importer les classes nécessaires / Import necessary classes
use Dotenv\Dotenv;
use Controllers\KarenController;
use Controllers\AuthController;

// Charger les variables d'environnement depuis le fichier .env / Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Obtenir l'action de la requête / Get the action from the request
$action = $_REQUEST['action'] ?? null;

// Sélectionner le contrôleur approprié en fonction de l'action / Select the appropriate controller based on the action
switch ($action) {
    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;

    // Par défaut, afficher la page d'accueil / By default, display the home page
    default:
        $controller = new KarenController();
        $controller->handleChat();
        break;

    // Afficher la page d'accueil / Display the home page

}