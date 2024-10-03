<?php
use Controllers\KarenController;
use Controllers\AuthController;
use Controllers\AdminController;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Determine if it's a JSON request
    $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
    
    // Only parse JSON if it's a chatbot request, otherwise use traditional form handling
    if (strpos($contentType, 'application/json') !== false) {
        $data = json_decode(file_get_contents("php://input"), true);
        $requestType = $data['request_type'] ?? 'unknown';

        switch ($requestType) {
            case 'chat':
                $controller = new KarenController();
                $controller->handleChat(); // Handle chat requests
                break;

            default:
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Invalid request type for JSON input.']);
                break;
        }

        exit; // Exit after handling the POST request
    } else {
        // Handle form-based POST requests (e.g., authentication)
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $controller = new AuthController();
            $controller->handleLogin($_POST); // Traditional form-based login
            exit;
        }
    }
}

// For GET requests, continue as usual
$action = $_REQUEST['action'] ?? null;
$logged = $_SESSION['user'] ?? null;

switch ($action) {
    default:
        if ($logged) {
            $controller = new KarenController();
            $controller->renderView(); // Render chat view for logged users
        } else {
            $controller = new AuthController();
            $controller->login(); // Display login view
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
                default:
                    $controller->index();
                    break;
                case 'create':
                    $controller->create();
                    break;
                case 'edit':
                    if ($id && filter_var($id, FILTER_VALIDATE_INT)) {
                        $controller->edit($id);
                    } else {
                        header('Location: /karenbot/admin');
                    }
                    break;
                case 'delete':
                    if ($id && filter_var($id, FILTER_VALIDATE_INT)) {
                        $controller->delete($id);
                    } else {
                        header('Location: /karenbot/admin');
                    }
                    break;
            }
        }
        break;
}