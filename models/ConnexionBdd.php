<?php

class Database {
    
    private static $instance = null;
    private $pdo;
    
    private function __construct() 
    {
        $host = 'localhost';
        $database = 'api_mydigitalgpt';
        $username = 'root';
        $password = '';
        
        $this->pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    }

    public static function getInstance() 
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() 
    {
        return $this->pdo;
    }

}