<?php
namespace Controllers;

use Models\AdminModel;
use Views\AdminView;

class AdminController {
    protected $model;
    protected $view;

    public function __construct() {
        $this->model = new AdminModel();
        $this->view = new AdminView();
    }

    // Afficher la liste des utilisateurs
    public function index() {
        // Récupérer tous les utilisateurs depuis le modèle
        $users = $this->model->getUsers();
        // Afficher la liste des utilisateurs via la vue
        $this->view->renderUserList($users);
    }

    // Créer un nouvel utilisateur
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstname = $_POST['firstname'] ?? '';
            $lastname = $_POST['lastname'] ?? '';
            $mail = $_POST['mail'] ?? '';
            $pswd = $_POST['pswd'] ?? '';
            $level_id = $_POST['level_id'] ?? 0;
            $is_admin = $_POST['is_admin'] ?? 0;
    
            // Vérification des conditions du mot de passe
            $passwordValid = $this->validatePassword($pswd);
    
            // Vérifiez que toutes les valeurs sont présentes et valides
            if (!empty($firstname) && !empty($lastname) && !empty($mail) && $passwordValid && !empty($level_id)) {
                if ($this->model->createUser($firstname, $lastname, $mail, $pswd, $level_id, $is_admin)) {
                    header('Location: ?action=admin');
                    exit;
                } else {
                    echo "Erreur lors de la création de l'utilisateur.";
                }
            } else {
                if (!$passwordValid) {
                    echo "Le mot de passe doit contenir au moins 12 caractères, une majuscule et un chiffre.";
                } else {
                    echo "Veuillez remplir tous les champs.";
                }
            }
        } else {
            $this->view->renderCreateForm();
        }
    }
    
    private function validatePassword($password) {
        if (strlen($password) < 12) {
            return false;
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }
        if (!preg_match('/\d/', $password)) {
            return false;
        }
        return true;
    }
    

    // Modifier un utilisateur
    public function edit($id) {
        $user = $this->model->getUserById($id);
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $mail = $_POST['mail'];
            $pswd = !empty($_POST['pswd']) ? password_hash($_POST['pswd'], PASSWORD_DEFAULT) : $user['pswd'];  // Hacher le nouveau mot de passe ou garder l'ancien
            $level_id = $_POST['level_id'];
            $is_admin = $_POST['is_admin'];
    
            $this->model->updateUser($id, $firstname, $lastname, $mail, $pswd, $level_id, $is_admin);
            header('Location: http://localhost/karenbot/admin');
            exit;
        } else {
            $this->view->renderEditForm($user);
        }
    }
    

    // Supprimer un utilisateur
    public function delete($id) {
        $this->model->deleteUser($id);
        header('Location: http://localhost/karenbot/admin');
        exit;
    }
}
?>
