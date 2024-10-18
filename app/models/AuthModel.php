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
        $query = $this->db->getConnection()->prepare($sql);
        $query->bindParam(':mail', $mail, PDO::PARAM_STR);
        $query->bindParam(':pswd', $pswd, PDO::PARAM_STR);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC); 
    }
}
?>
