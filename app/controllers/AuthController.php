<?php
namespace Controllers;

use Models\AuthModel;
use Views\AuthView;

class AuthController {
    protected $model;
    protected $view;

    public function __construct() {
        $this->model = new AuthModel();
        $this->view = new AuthView();
    }

    public function login() {
        // Check if there's a valid token in the session tied to the IP address
        $user = $this->model->checkSession($_SERVER['REMOTE_ADDR']);
        if ($user) {
            $_SESSION['user'] = $user;
            header('Location: http://localhost/karenbot/');
            exit;
        }

        // Handle login via POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail = $_POST['mail'] ?? '';
            $pswd = $_POST['pswd'] ?? '';
            $user = $this->model->login($mail, $pswd);

            if ($user) {
                // Generate token and bind it to IP address
                $token = bin2hex(random_bytes(16)); 
                $ip_address = $_SERVER['REMOTE_ADDR'];

                // Store token and IP address on the server-side only
                $this->model->updateUserToken($user['id'], $token, $ip_address);

                // Set session
                $_SESSION['user'] = $user;
                $_SESSION['token'] = $token;

                // Redirect to the main page
                header('Location: http://localhost/karenbot/');
                exit;
            } else {
                echo "<p>Incorrect login credentials.</p>";
            }
        }

        $this->view->render(); // Render login form if not logged in
    }

    public function logout() {
        if (isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['id'];
            // Clear token and IP address on the server-side
            $this->model->logout($userId);
        }

        // Destroy session
        session_destroy();

        // Redirect to login page
        header('Location: http://localhost/karenbot/login');
        exit;
    }
}
?>
