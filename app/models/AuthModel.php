<?php
namespace Models;

use App\Database;
use PDO;

class AuthModel {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function login($mail, $pswd) {
        // Requête pour récupérer l'utilisateur par email uniquement
        $sql = "SELECT id, firstname, lastname, mail, pswd, is_admin FROM users WHERE mail = :mail";
        $query = $this->db->getConnection()->prepare($sql);
        $query->bindParam(':mail', $mail, PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);
    
        // Vérifier si l'utilisateur existe
        if ($user) {
            // Debug pour vérifier le mot de passe en clair et le hashé
            // echo "Mot de passe fourni : $pswd, Hash dans la base de données : " . $user['pswd'];

            // Vérifier si le mot de passe correspond au hash stocké
            if (password_verify($pswd, $user['pswd'])) {
                return $user;  // Mot de passe correct
            } else {
                echo "Mot de passe incorrect.";
            }
        } else {
            echo "Utilisateur non trouvé.";
        }
    
        return false; // Si le mot de passe ne correspond pas ou si l'utilisateur n'existe pas
    }
}

?>
