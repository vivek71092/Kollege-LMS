<?php
// /classes/Database.php

// This class requires the constants from config.php
require_once __DIR__ . '/../config.php';

class Database {
    
    // Holds the single instance of the class
    private static $instance = null;
    
    // Holds the PDO connection object
    private $pdo;
    
    /**
     * The constructor is private to prevent direct instantiation.
     * It establishes the database connection.
     */
    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
        } catch (PDOException $e) {
            // Use the global error handler
            log_error($e->getMessage(), __FILE__, __LINE__);
            die("Database connection failed. Please check configuration.");
        }
    }
    
    /**
     * Gets the single instance of the Database class.
     * @return Database The singleton Database instance.
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    /**
     * Gets the PDO connection object.
     * @return PDO The PDO database connection.
     */
    public function getConnection() {
        return $this->pdo;
    }
    
    // Prevent cloning of the instance
    private function __clone() {}
    
    // Prevent unserialization of the instance
    public function __wakeup() {}
}
?>