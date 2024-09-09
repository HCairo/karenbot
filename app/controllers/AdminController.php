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

    // Afficher la liste des utilisateurs (Méthode index manquante)
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

            // Vérifiez que toutes les valeurs sont présentes et valides
            if (!empty($firstname) && !empty($lastname) && !empty($mail) && !empty($pswd) && !empty($level_id)) {
                if ($this->model->createUser($firstname, $lastname, $mail, $pswd, $level_id)) {
                    header('Location: ?action=admin');
                    exit;
                } else {
                    echo "Erreur lors de la création de l'utilisateur.";
                }
            } else {
                echo "Veuillez remplir tous les champs.";
            }
        } else {
            $this->view->renderCreateForm();
        }
    }

    // Modifier un utilisateur
    public function edit($id) {
        $user = $this->model->getUserById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $mail = $_POST['mail'];
            $pswd = !empty($_POST['pswd']) ? $_POST['pswd'] : $user['pswd'];
            $level_id = $_POST['level_id'];

            $this->model->updateUser($id, $firstname, $lastname, $mail, $pswd, $level_id);
            header('Location: http://localhost/karenbot/?action=admin');
            exit;
        } else {
            $this->view->renderEditForm($user);
        }
    }

    // Supprimer un utilisateur
    public function delete($id) {
        $this->model->deleteUser($id);
        header('Location: http://localhost/karenbot/?action=admin');
        exit;
    }
}
?>
