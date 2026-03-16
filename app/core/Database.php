<?php

// This class handles our database connection.
// We use a "singleton" pattern so we only connect to the database once.
class Database
{
    // This stores the single instance of this class
    private static $instance = null;

    // This stores the PDO database connection
    private $connection;

    // The constructor is private so nobody can create a new Database() from outside
    private function __construct()
    {
        // Load the database settings from our config file
        $config = require __DIR__ . '/../config/database.php';

        // Build the DSN (Data Source Name) string that PDO needs to connect
        $dsn = $config['driver'] . ':host=' . $config['host']
             . ';port=' . $config['port']
             . ';dbname=' . $config['database']
             . ';charset=' . $config['charset'];

        // Try to connect to the database
        try {
            $this->connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    // Throw exceptions when there's a database error
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    // Return rows as associative arrays (e.g. $row['name'])
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // Use real prepared statements for security
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            // If connection fails, stop everything and show an error
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    // Get the single instance of this class (create it if it doesn't exist yet)
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Get the PDO connection so we can run queries
    public function getConnection()
    {
        return $this->connection;
    }
}