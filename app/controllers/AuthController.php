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
        session_start(); 
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail = $_POST['mail'] ?? '';
            $pswd = $_POST['pswd'] ?? '';
            $result = $this->model->login($mail, $pswd);
            if ($result) {
                $_SESSION['user'] = $result;
                header('Location: index.php');
                exit;
            } else {
                echo "<p>Identifiants incorrects.</p>";
            }
        }
        $this->view->render(); // Affiche la vue même après une tentative échouée
    }
}
?>
