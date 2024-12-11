<?php
require_once __DIR__ . '/../config/db_connection.php';

class Database {
    private $conn;

    public function __construct() {
        $config = DatabaseConfig::getConnectionParams();
        try {
            $this->conn = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']}", 
                $config['username'], 
                $config['password']
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>