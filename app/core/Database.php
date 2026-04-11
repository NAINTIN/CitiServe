<?php

// Simple object-oriented database connection class.
class Database
{
    private $connection;

    public function __construct()
    {
        $config = require __DIR__ . '/../config/database.php';

        $dsn = $config['driver'] . ':host=' . $config['host']
             . ';port=' . $config['port']
             . ';dbname=' . $config['database']
             . ';charset=' . $config['charset'];

        try {
            $this->connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
