<?php
// Database connection settings
// Copy this file to "database.php" and fill in your real MySQL username and password

return [
    'driver'   => 'mysql',                      // We are using MySQL (or MariaDB)
    'host'     => '127.0.0.1',                  // Database server address (localhost)
    'port'     => 3306,                         // Default MySQL port
    'database' => 'citiserve_db',               // Name of our database
    'username' => 'your_mysql_username_here',    // REPLACE with your actual MySQL username
    'password' => 'your_mysql_password_here',    // REPLACE with your actual MySQL password
    'charset'  => 'utf8mb4',                    // Character set (supports emojis and special chars)
    'collation'=> 'utf8mb4_unicode_ci',         // How text is sorted and compared
];