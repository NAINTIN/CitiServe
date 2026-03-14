<?php
// Simple PHP + MySQL test for citiserve_db

$host = '127.0.0.1';      // or 'localhost'
$db   = 'citiserve_db';   // your database name
$user = 'phpmyadmin';     // your MySQL username from phpMyAdmin
$pass = 'YourStrongPassword'; // your MySQL password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>✅ Connected to database: $db</h2>";

    // Fetch all document services
    $stmt = $pdo->query("SELECT id, name, description, price, processing_time_days FROM document_services");

    echo "<h3>Available Document Services</h3>";
    echo "<table border='1' cellpadding='6' cellspacing='0'>";
    echo "<tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Processing Time (days)</th>
          </tr>";

    foreach ($stmt as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
        echo "<td>" . htmlspecialchars($row['processing_time_days']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";

} catch (PDOException $e) {
    echo "<h2>❌ Database connection failed</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}