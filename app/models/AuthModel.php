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
    
        // Si l'utilisateur existe et que le mot de passe correspond
        if ($user && password_verify($pswd, $user['pswd'])) {
            return $user;  // Retourner les informations de l'utilisateur
        }
    
        return false; // Si le mot de passe ne correspond pas ou si l'utilisateur n'existe pas
    }
}
?>
