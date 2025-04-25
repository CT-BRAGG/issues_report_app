<!--
Edit Person Script
author: Carson Bragg; chatgpt
desc: This script is responisble for managing updates to the
       database in regards to the data of the given person. 
-->

<?php
session_start();
require_once '/var/www/database/issDB/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $admin = $_SESSION['admin'] ? ($_POST['admin'] ?? 0) : 0; // Only admins can set admin status

    // Validate password match
    if (!empty($password) && $password !== $confirm_password) {
        die("Passwords do not match.");
    }

    $fields = [
        'fname' => $fname,
        'lname' => $lname,
        'email' => $email,
        'admin' => $admin,
    ];

    // Hash password if provided
    if (!empty($password)) {
        $salt = bin2hex(random_bytes(8));
        $hash = hash('sha256', $password . $salt);
        $fields['pwd_hash'] = $hash;
        $fields['pwd_salt'] = $salt;
    }

    // Build SET clause dynamically
    $set_clause = join(', ', array_map(fn($k) => "$k = :$k", array_keys($fields)));
    $fields['id'] = $id;

    $stmt = $pdo->prepare("UPDATE iss_persons SET $set_clause WHERE id = :id");
    if ($stmt->execute($fields)) {
        header("Location: view_person_detail.php?id=$id");
        exit();
    } else {
        die("Failed to update record.");
    }
} else {
    header("Location: view_people.php");
    exit();
}

