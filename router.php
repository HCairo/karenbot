<?php
// Charger les dépendances de Composer / Load Composer dependencies
require_once('vendor/autoload.php');

// Importer les classes nécessaires / Import necessary classes
use Dotenv\Dotenv;
use Controllers\KarenController;
use Controllers\AuthController;
use Controllers\AdminController;

// Charger les variables d'environnement depuis le fichier .env / Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Obtenir l'action de la requête / Get the action from the request
$action = $_REQUEST['action'] ?? null;
$logged = $_SESSION['user'] ?? null;

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

    // Administration accessible à tous les utilisateurs connectés pour le moment je dois pas oublier de modifier 
    case 'admin':
        if ($logged) {
            $controller = new AdminController();
            // Gérer les actions spécifiques du CRUD dans AdminController
            $adminAction = $_GET['admin_action'] ?? 'index'; 
            $id = $_GET['id'] ?? null;
            switch ($adminAction) {
                case 'create':
                    $controller->create(); 
                    break;
                case 'edit':
                    if ($id) {
                        $controller->edit($id); 
                    } else {
                        header('Location: http://localhost/karenbot/admin');
                    }
                    break;
                case 'delete':
                    if ($id) {
                        $controller->delete($id);
                    } else {
                        header('Location: http://localhost/karenbot/admin');
                    }
                    break;
                default:
                    $controller->index(); 
                    break;
            }
        // } else {
        //     // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
        //     header('Location: login');
        //     exit;
        }
        break;
}