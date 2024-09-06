<?php
namespace App;

use PDO;
use PDOException;

class Database {
    protected $cnx;   // PDO connection object / Objet de connexion PDO
    protected $host;  // Database host / Hôte de la base de données
    protected $db;    // Database name / Nom de la base de données
    protected $login; // Database username / Nom d'utilisateur de la base de données
    protected $pw;    // Database password / Mot de passe de la base de données
    
    public function __construct() {
        $this->host = $_ENV['DB_HOST'];   // Retrieve database host from environment variables / Récupère l'hôte de la base de données depuis les variables d'environnement
        $this->db = $_ENV['DB_NAME'];     // Retrieve database name from environment variables / Récupère le nom de la base de données depuis les variables d'environnement
        $this->login = $_ENV['DB_USER'];  // Retrieve database username from environment variables / Récupère le nom d'utilisateur de la base de données depuis les variables d'environnement
        $this->pw = $_ENV['DB_PASS'];     // Retrieve database password from environment variables / Récupère le mot de passe de la base de données depuis les variables d'environnement

        try {
            // Attempt to establish a PDO connection to the database / Tente d'établir une connexion PDO à la base de données
            $this->cnx = new \PDO("mysql:host={$this->host};dbname={$this->db}", $this->login, $this->pw);
            // Set PDO error mode to throw exceptions on errors / Configure le mode d'erreur PDO pour lancer des exceptions en cas d'erreur
            $this->cnx->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            // Catch any PDOException and display an error message / Capture toute PDOException et affiche un message d'erreur
            echo 'Connection failed: ' . $e->getMessage();
            exit;
        }
    }

    public function getConnection() {
        // Returns the PDO connection object / Retourne l'objet de connexion PDO
        return $this->cnx;
    }
}
?>