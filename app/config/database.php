<?php
// Database connection settings
// Change the username and password to match your MySQL/phpMyAdmin setup

return [
    'driver'   => 'mysql',              // We are using MySQL (or MariaDB)
    'host'     => '127.0.0.1',          // Database server address (localhost)
    'port'     => 3306,                 // Default MySQL port
    'database' => 'citiserve_db',       // Name of our database
    'username' => 'root',         // YOUR phpMyAdmin username
    'password' => '', // YOUR phpMyAdmin password
    'charset'  => 'utf8mb4',           // Character set (supports emojis and special chars)
    'collation'=> 'utf8mb4_unicode_ci', // How text is sorted and compared
];
