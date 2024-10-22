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
    
    public function createUser($firstname, $lastname, $mail, $pswd, $level_id, $is_admin) {
        try {
            $hashedPassword = password_hash($pswd, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (firstname, lastname, mail, pswd, level_id, is_admin) 
                    VALUES (:firstname, :lastname, :mail, :pswd, :level_id, :is_admin)";
            $query = $this->db->getConnection()->prepare($sql);
            $query->bindParam(':firstname', $firstname, PDO::PARAM_STR);
            $query->bindParam(':lastname', $lastname, PDO::PARAM_STR);
            $query->bindParam(':mail', $mail, PDO::PARAM_STR);
            $query->bindParam(':pswd', $hashedPassword, PDO::PARAM_STR); // Utilisation du mot de passe haché
            $query->bindParam(':level_id', $level_id, PDO::PARAM_INT);
            $query->bindParam(':is_admin', $is_admin, PDO::PARAM_INT);
    
            if ($query->execute()) {
                return true;
            } else {
                $errorInfo = $query->errorInfo();
                echo "Erreur SQL : " . $errorInfo[2];
                return false;
            }
        } catch (PDOException $e) {
            echo "Erreur SQL : " . $e->getMessage();
            return false;
        }
    }
    

    // Mettre à jour un utilisateur
    public function updateUser($id, $firstname, $lastname, $mail, $pswd, $level_id, $is_admin) {
        $sql = "UPDATE users SET firstname = :firstname, lastname = :lastname, mail = :mail, pswd = :pswd, level_id = :level_id, is_admin = :is_admin WHERE id = :id";
        $query = $this->db->getConnection()->prepare($sql);
        $query->bindParam(':firstname', $firstname, PDO::PARAM_STR);
        $query->bindParam(':lastname', $lastname, PDO::PARAM_STR);
        $query->bindParam(':mail', $mail, PDO::PARAM_STR);
        $query->bindParam(':pswd', $pswd, PDO::PARAM_STR);  // Le mot de passe est soit haché soit l'ancien mot de passe
        $query->bindParam(':level_id', $level_id, PDO::PARAM_INT);
        $query->bindParam(':is_admin', $is_admin, PDO::PARAM_INT);
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
