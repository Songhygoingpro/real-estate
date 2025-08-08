<?php
/**
 * Database Configuration
 * 
 * Secure database connection configuration with environment variable support
 */

class DatabaseConfig
{
    private static $instance = null;
    private $connection = null;
    
    // Database configuration constants
    private const DEFAULT_HOST = 'localhost';
    private const DEFAULT_PORT = 3307;
    private const DEFAULT_CHARSET = 'utf8mb4';
    
    private function __construct()
    {
        // Private constructor to prevent direct instantiation
    }
    
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection(): mysqli
    {
        if ($this->connection === null) {
            $this->connection = $this->createConnection();
        }
        
        return $this->connection;
    }
    
    private function createConnection(): mysqli
    {
        $host = $_ENV['DB_HOST'] ?? self::DEFAULT_HOST;
        $port = (int)($_ENV['DB_PORT'] ?? self::DEFAULT_PORT);
        $database = $_ENV['DB_NAME'] ?? 'real_estate';
        $username = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASS'] ?? '';
        
        try {
            $connection = new mysqli($host, $username, $password, $database, $port);
            
            if ($connection->connect_error) {
                throw new Exception("Database connection failed: " . $connection->connect_error);
            }
            
            // Set charset for security
            $connection->set_charset(self::DEFAULT_CHARSET);
            
            return $connection;
            
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Database connection failed. Please try again later.");
        }
    }
    
    public function closeConnection(): void
    {
        if ($this->connection !== null) {
            $this->connection->close();
            $this->connection = null;
        }
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}