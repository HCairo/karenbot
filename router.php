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
$logged = $_SESSION['user'];

// Sélectionner le contrôleur approprié en fonction de l'action / Select the appropriate controller based on the action
switch ($action) {
    // Par défaut, afficher la page d'accueil / By default, display the home page
    default:
        if ($logged) {
            $controller = new KarenController();
            $controller->handleChat();
        } else {
            $controller = new AuthController();
            $controller->login();
        }
        break;
    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;
}