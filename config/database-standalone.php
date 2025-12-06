<?php

if (!class_exists('Database')) {
class Database {
    
    private static $instance = null;
    private $connection;
    private $host;
    private $port;
    private $dbname;
    private $username;
    private $password;
    
    private function __construct() {
        $this->loadCredentials();
        
        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
        
        if (!extension_loaded('pdo_mysql')) {
            throw new Exception("Estensione PDO MySQL non disponibile. Abilita 'extension=pdo_mysql' in php.ini");
        }
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8mb4";
        }
        
        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            throw new Exception("Errore di connessione al database: " . $e->getMessage());
        }
    }
    
    private function loadCredentials() {
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $env = parse_ini_file($envFile);
            $this->host = $env['DB_HOST'] ?? 'localhost';
            $this->port = $env['DB_PORT'] ?? '3307';
            $this->dbname = $env['DB_NAME'] ?? 'blog_db';
            $this->username = $env['DB_USER'] ?? 'root';
            $this->password = $env['DB_PASSWORD'] ?? '';
        } else {
            $this->host = 'localhost';
            $this->port = '3307';
            $this->dbname = 'blog_db';
            $this->username = 'root';
            $this->password = '';
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    private function __clone() {}
    
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
}

