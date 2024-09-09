<?php
namespace Models;

use App\Database;
use PDO;

class AdminModel {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Récupérer tous les utilisateurs
    public function getUsers() {
        $sql = "SELECT * FROM users";
        $query = $this->db->getConnection()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un utilisateur par son ID
    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $query = $this->db->getConnection()->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // Créer un nouvel utilisateur
    public function createUser($firstname, $lastname, $mail, $pswd, $level_id) {
        try {
            $sql = "INSERT INTO users (firstname, lastname, mail, pswd, level_id) VALUES (:firstname, :lastname, :mail, :pswd, :level_id)";
            $query = $this->db->getConnection()->prepare($sql);
            $query->bindParam(':firstname', $firstname, PDO::PARAM_STR);
            $query->bindParam(':lastname', $lastname, PDO::PARAM_STR);
            $query->bindParam(':mail', $mail, PDO::PARAM_STR);
            $query->bindParam(':pswd', $pswd, PDO::PARAM_STR);
            $query->bindParam(':level_id', $level_id, PDO::PARAM_INT);

            // Exécution et vérification
            if ($query->execute()) {
                return true;
            } else {
                // Afficher les erreurs SQL si l'exécution échoue
                $errorInfo = $query->errorInfo();
                echo "Erreur SQL : " . $errorInfo[2]; // Afficher le message d'erreur SQL
                return false;
            }
        } catch (PDOException $e) {
            // Attraper et afficher les erreurs de la base de données
            echo "Erreur SQL : " . $e->getMessage();
            return false;
        }
    }

    // Mettre à jour un utilisateur
    public function updateUser($id, $firstname, $lastname, $mail, $pswd, $level_id) {
        $sql = "UPDATE users SET firstname = :firstname, lastname = :lastname, mail = :mail, pswd = :pswd, level_id = :level_id WHERE id = :id";
        $query = $this->db->getConnection()->prepare($sql);
        $query->bindParam(':firstname', $firstname, PDO::PARAM_STR);
        $query->bindParam(':lastname', $lastname, PDO::PARAM_STR);
        $query->bindParam(':mail', $mail, PDO::PARAM_STR);
        $query->bindParam(':pswd', $pswd, PDO::PARAM_STR);
        $query->bindParam(':level_id', $level_id, PDO::PARAM_INT);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
    }

    // Supprimer un utilisateur
    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $query = $this->db->getConnection()->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
    }
}
?>
