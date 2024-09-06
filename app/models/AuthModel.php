<?php
namespace Models;

use App\Database;
use PDO;

class AuthModel {
    protected $db;

    public function __construct() {
        $this->db = new Database;
    }
}