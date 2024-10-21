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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail = $_POST['mail'] ?? '';
            $pswd = $_POST['pswd'] ?? '';
            
            // Récupérer les informations de l'utilisateur depuis le modèle
            $user = $this->model->login($mail, $pswd);
            if ($user) {
                // Stocker les informations de l'utilisateur dans la session
                $_SESSION['user_id'] = $user['id'];             // ID utilisateur
                $_SESSION['user_name'] = $user['firstname'];    // Nom de l'utilisateur
                $_SESSION['user_email'] = $user['mail'];        // Email utilisateur
                $_SESSION['is_admin'] = $user['is_admin'];      // Statut administrateur (0 ou 1)

                // Redirection vers la page d'accueil ou la page admin
                header('Location: http://localhost/karenbot/');
                exit;
            } else {
                echo "<p>Identifiants incorrects.</p>";
            }
        }

        // Affiche la vue même après une tentative échouée
        $this->view->render();
    }
}

?>

