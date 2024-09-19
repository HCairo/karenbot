<?php
use Controllers\KarenController;
use Controllers\AuthController;
use Controllers\AdminController;

// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtenir l'action de la requête
$action = $_REQUEST['action'] ?? null;
$logged = $_SESSION['user'] ?? null; // Vérifier si l'utilisateur est connecté

// Gestion des requêtes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new KarenController();
    $controller->handleChat(); // Traiter uniquement les requêtes POST
    exit; // Stop further execution since we handled the POST request
}

// Gestion des actions GET (affichage de la vue ou autres actions)
switch ($action) {
    default:
        if ($logged) {
            $controller = new KarenController();
            $controller->renderView(); // Afficher la vue HTML
        } else {
            $controller = new AuthController();
            $controller->login();
        }
        break;

    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;

    case 'admin':
        if ($logged) {
            $controller = new AdminController();
            $adminAction = $_GET['admin_action'] ?? 'index';
            $id = $_GET['id'] ?? null;

            switch ($adminAction) {
                case 'create':
                    $controller->create();
                    break;

                case 'edit':
                    if ($id && filter_var($id, FILTER_VALIDATE_INT)) {
                        $controller->edit($id);
                    } else {
                        header('Location: /karenbot/admin');
                        exit;
                    }
                    break;

                case 'delete':
                    if ($id && filter_var($id, FILTER_VALIDATE_INT)) {
                        $controller->delete($id);
                    } else {
                        header('Location: /karenbot/admin');
                        exit;
                    }
                    break;

                default:
                    $controller->index();
                    break;
            }
        } else {
            header('Location: /login');
            exit;
        }
        break;
}
