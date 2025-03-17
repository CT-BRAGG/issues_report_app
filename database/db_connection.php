<!-- 
DATABASE CONNECTION
desc: manages the database connection
author: Carson Bragg; chatgpt 
-->

<?php
    // db_connection.php

    // Database connection details
    $host = 'localhost';
    $db = 'iss'; // The database name
    $user = 'root'; // Your MySQL username
    $pass = ''; // Your MySQL password

    // Create a connection to the database
    $conn = new mysqli($host, $user, $pass, $db);

    // Check for database connection errors
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>
