<?php
class Database {
    private PDO $pdo;

    public function __construct() {
        // Paramètres de connexion
        $host = 'mysql-db';
        $user = 'root';
        $pass = 'rootpassword';
        $dbname = 'questarena';
        $port = 3306;

        try {
            // DSN (Data Source Name)
            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";

            // Connexion à la base de données
            $this->pdo = new PDO($dsn, $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            die('❌ Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }

    public function getConnection(): PDO {
        return $this->pdo;
    }
}
?>
