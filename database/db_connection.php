<!-- 
DATABASE CONNECTION
desc: manages the database connection
author: Carson Bragg; chatgpt 
-->

<?php
    // db_connection.php

    $host = 'localhost';
    $db = 'iss'; // The database name
    $user = 'root'; // Your MySQL username
    $pass = ''; // Your MySQL password

    // Set up the DSN (Data Source Name)
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

    // PDO options for error handling and emulating prepared statements
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        // Create a PDO instance
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        // If there is an error in connecting to the database, show an error message
        die("Connection failed: " . $e->getMessage());
    }
?>

