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
        $sql = "SELECT * FROM users WHERE mail = :mail AND pswd = :pswd";
        $pdo = $this->db->getConnection()->prepare($sql);
        $pdo->bindParam(':mail', $mail, PDO::PARAM_STR);
        $pdo->bindParam(':pswd', $pswd, PDO::PARAM_STR);
        $pdo->execute();
        return $pdo->fetch(PDO::FETCH_ASSOC); 
    }

    public function updateUserToken($userId, $token, $ip_address) {
        $sql = "UPDATE users SET token = :token, ip_address = :ip_address, token_creation_time = NOW() WHERE id = :id";
        $pdo = $this->db->getConnection()->prepare($sql);
        $pdo->bindParam(':token', $token, PDO::PARAM_STR);
        $pdo->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
        $pdo->bindParam(':id', $userId, PDO::PARAM_INT);
        $pdo->execute();
    }

    public function checkSession($ip_address) {
        // Check for an active token for the current IP
        $sql = "SELECT * FROM users WHERE ip_address = :ip_address AND token_creation_time > DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        $pdo = $this->db->getConnection()->prepare($sql);
        $pdo->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
        $pdo->execute();
        return $pdo->fetch(PDO::FETCH_ASSOC); 
    }

    public function logout($userId) {
        $sql = "UPDATE users SET token = NULL, ip_address = NULL WHERE id = :id";
        $pdo = $this->db->getConnection()->prepare($sql);
        $pdo->bindParam(':id', $userId, PDO::PARAM_INT);
        $pdo->execute();
    }
}
?>
