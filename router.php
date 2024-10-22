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

// Obtenir l'action de la requête
$action = $_REQUEST['action'] ?? null;
$logged = isset($_SESSION['user_id']);  // Vérifier si l'utilisateur est connecté
$is_admin = $_SESSION['is_admin'] ?? 0; // Récupérer le statut admin depuis la session (0 ou 1)

// Sélectionner le contrôleur approprié en fonction de l'action
switch ($action) {
    // Par défaut, afficher la page d'accueil
    default:
        if ($logged) {
            $controller = new KarenController();
            $controller->handleChat();
        } else {
            $controller = new AuthController();
            $controller->login();
        }
        break;

    // Page de connexion
    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;

    // Page d'administration, accessible uniquement aux administrateurs
    case 'admin':
        if ($logged && $is_admin == 1) {
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
                        exit;
                    }
                    break;
                case 'delete':
                    if ($id) {
                        $controller->delete($id);
                    } else {
                        header('Location: http://localhost/karenbot/admin');
                        exit;
                    }
                    break;
                default:
                    $controller->index();
                    break;
            }
        } else {
            // Si l'utilisateur n'est pas admin, rediriger vers la page d'accueil
            header('Location: http://localhost/karenbot/');
            exit;
        }
        break;
}
